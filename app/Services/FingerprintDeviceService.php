<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\CompanyDevice;
use App\Models\DeviceAttendanceLog;
use App\Models\DeviceStaffLink;
use App\Services\Devices\DeviceDriverFactory;
use App\Services\Devices\FingerprintDeviceDriverInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class FingerprintDeviceService
{
    protected FingerprintDeviceDriverInterface $driver;

    public function __construct(protected CompanyDevice $companyDevice)
    {
        // Resolves ZktecoDriver, HikvisionDriver, etc. based on this specific
        // device's brand — so devices from different companies AND different
        // brands are each handled correctly in the same sync run.
        $this->driver = DeviceDriverFactory::make($companyDevice);
    }

    /**
     * Open a connection and confirm this is genuinely the device you think it
     * is, using the serial number on file — not just its IP (IPs change on
     * DHCP networks; a serial number doesn't).
     */
    public function connect(): void
    {
        if (! $this->driver->connect()) {
            $this->markStatus('offline');

            throw new RuntimeException("Unable to reach device [{$this->companyDevice->serial_no}] at {$this->companyDevice->ip}:{$this->companyDevice->port}");
        }

        $reportedSerial = $this->driver->getSerialNumber();

        if ($reportedSerial === null) {
            // Library/firmware doesn't expose it — proceed, but make it loud
            // in the logs rather than silently trusting the IP.
            Log::warning('Fingerprint device did not report a serial number; skipping identity check.', [
                'company_device_id' => $this->companyDevice->id,
                'expected_serial' => $this->companyDevice->serial_no,
            ]);
        } elseif ($reportedSerial !== $this->companyDevice->serial_no) {
            $this->driver->disconnect();
            $this->markStatus('mismatch');

            throw new RuntimeException(
                "Serial number mismatch for device #{$this->companyDevice->id}: " .
                    "expected [{$this->companyDevice->serial_no}], device reported [{$reportedSerial}]. " .
                    'Refusing to import data — check whether the IP was reassigned to a different unit.'
            );
        }

        $this->markStatus('online');
    }

    public function disconnect(): void
    {
        $this->driver->disconnect();
    }

    /**
     * Pull raw punches from the device, store them idempotently, then fold
     * newly-received punches into the daily `attendances` rows.
     *
     * Returns a small summary you can hand back to the API/UI.
     */
    public function sync(): array
    {
        $this->connect();

        try {
            $rawLogs = $this->driver->getAttendanceLogs();
        } finally {
            $this->disconnect();
        }

        $inserted = 0;
        $affected = []; // ['staff_id-date' => true]

        foreach ($rawLogs as $log) {
            $staffId = $this->resolveStaffId($log['device_user_id']);

            // insertOrIgnore + the unique index from the migration is what
            // makes re-running a sync safe: duplicate punches are silently
            // skipped instead of erroring or double-counting.
            $wasInserted = DB::table('device_attendance_logs')->insertOrIgnore([
                'company_device_id' => $this->companyDevice->id,
                'staff_id' => $staffId,
                'device_user_id' => $log['device_user_id'],
                'punch_time' => Carbon::parse($log['timestamp']),
                'verify_type' => $log['verify_type'],
                'punch_state' => $log['state'],
                'processed' => false,
                'created_at' => now(),
                'updated_at' => now(),

            ]);

            if ($wasInserted && $staffId) {
                $inserted++;
                $affected[$staffId . '-' . Carbon::parse($log['timestamp'])->toDateString()] = [
                    'staff_id' => $staffId,
                    'date' => Carbon::parse($log['timestamp'])->toDateString(),
                ];
            }
        }

        foreach ($affected as $pair) {
            $this->rebuildAttendanceFor($pair['staff_id'], $pair['date']);
        }

        return [
            'device' => $this->companyDevice->serial_no,
            'logs_read' => count($rawLogs),
            'logs_inserted' => $inserted,
            'days_recomputed' => count($affected),
        ];
    }

    protected function resolveStaffId(string $deviceUserId): ?int
    {
        return DeviceStaffLink::query()
            ->where('company_device_id', $this->companyDevice->id)
            ->where('device_user_id', $deviceUserId)
            ->value('staff_id');
    }

    /**
     * Rebuild the aggregated Attendance row for one staff member on one date
     * from every stored punch that day: earliest punch = check_in, latest =
     * check_out. Wrapped in a transaction + row lock so two overlapping syncs
     * (e.g. the scheduled job and a manual "Sync now" click) can't corrupt it.
     */
    protected function rebuildAttendanceFor(int $staffId, string $date): void
    {
        DB::transaction(function () use ($staffId, $date) {
            $punches = DeviceAttendanceLog::query()
                ->where('staff_id', $staffId)
                ->whereDate('punch_time', $date)
                ->orderBy('punch_time')
                ->lockForUpdate()
                ->get();

            if ($punches->isEmpty()) {
                return;
            }

            $checkIn = $punches->first()->punch_time;
            $checkOut = $punches->count() > 1 ? $punches->last()->punch_time : null;

            $attendance = Attendance::updateOrCreate(
                ['staff_id' => $staffId, 'date' => $date],
                [
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'working_minutes' => $checkOut ? $checkIn->diffInMinutes($checkOut) : null,
                    'company_device_id' => $this->companyDevice->id,
                    'status' => 'present',
                    // late_minutes / early_leave_minutes / overtime_minutes depend on
                    // each staff member's shift schedule, which isn't in scope here —
                    // compute those in a dedicated ShiftCalculator once you have shift
                    // data, then set them alongside the fields above.
                ]
            );

            DeviceAttendanceLog::query()
                ->whereIn('id', $punches->pluck('id'))
                ->update(['processed' => true]);
        });
    }

    protected function markStatus(string $status): void
    {
        $this->companyDevice->forceFill(['status' => $status])->save();
    }
}
