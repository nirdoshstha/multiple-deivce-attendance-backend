<?php

namespace App\Console\Commands;

use App\Models\CompanyDevice;
use App\Services\FingerprintDeviceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class SyncDeviceAttendance extends Command
{
    protected $signature = 'attendance:sync {companyDeviceId? : Sync just one device by its companies_devices.id}';

    protected $description = 'Pull attendance punches from active fingerprint devices and store them';

    public function handle(): int
    {
        $devices = CompanyDevice::query()
            ->where('status', '!=', '1')
            ->when($this->argument('companyDeviceId'), fn($q, $id) => $q->where('id', $id))
            ->get();

        if ($devices->isEmpty()) {
            $this->warn('No devices to sync.');

            return self::SUCCESS;
        }

        foreach ($devices as $device) {
            $this->info("Syncing device #{$device->id} ({$device->serial_no})...");

            try {
                $summary = (new FingerprintDeviceService($device))->sync();
                $this->info("  -> read {$summary['logs_read']}, inserted {$summary['logs_inserted']}, recomputed {$summary['days_recomputed']} day(s)");
            } catch (Throwable $e) {
                // One unreachable device should never stop the rest of the batch.
                $this->error("  -> failed: {$e->getMessage()}");
                Log::error('Fingerprint device sync failed', [
                    'company_device_id' => $device->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return self::SUCCESS;
    }
}
