<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CompanyDevice;
use App\Models\DeviceStaffLink;
use App\Services\AttendanceAggregator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Implements the subset of ZKTeco's ADMS ("iClock") protocol needed to
 * receive attendance pushes. Plain text in/out, not JSON — that's the
 * protocol, not a shortcut. Configure this URL as the device's "Cloud
 * Server Address" (COMM > Cloud Server Setting / ADMS on the device menu).
 *
 * These routes are intentionally NOT behind auth:sanctum — the device can't
 * do OAuth/token auth. Identity is instead established per-request via the
 * serial number (SN) query param, checked against a known, enabled
 * CompanyDevice row. See routes_and_schedule_snippets.php for the throttle
 * + route registration.
 */

use Symfony\Component\Console\Output\ConsoleOutput;



class IclockController extends Controller
{

    protected ConsoleOutput $console;

    public function __construct(ConsoleOutput $console)
    {
        $this->console = $console;
    }
    // GET/POST /iclock/cdata
    public function cdata(Request $request)
    {
        $device = $this->resolveDevice($request);

        $this->printRequestData();

        if (! $device) {
            return $this->plain('ERROR: Unregistered SN', 401);
        }

        $device->forceFill([
            'status' => 'online',
            'last_seen_at' => now(),
        ])->save();

        if ($request->isMethod('get')) {
            return $this->handshake($device);
        }

        $table = $request->query('table');

        if ($table === 'ATTLOG') {
            return $this->storeAttendanceLogs($device, $request->getContent());
        }

        // OPERLOG / USERINFO / other tables the device may push — accepted
        // and acknowledged so the device doesn't keep retrying, but not
        // parsed here. Extend this branch if you need auto user-provisioning
        // from device-side enrollment (table === 'USERINFO').
        Log::info('iClock cdata: unhandled table type received', [
            'company_device_id' => $device->id,
            'table' => $table,
        ]);

        return $this->plain('OK');
    }

    // GET /iclock/getrequest — device polling for queued commands.
    public function getRequest(Request $request)
    {
        $device = $this->resolveDevice($request);

        $this->printRequestData();

        if (! $device) {
            return $this->plain('ERROR: Unregistered SN', 401);
        }

        $device->forceFill(['last_seen_at' => now()])->save();

        // No command queue wired up yet — respond blank/OK, which the
        // protocol treats as "nothing pending." To push commands (e.g.
        // "USER ADD PIN=1003 Name=..."), build a device_commands table and
        // return one pending command per line here instead.
        return $this->plain('OK');
    }

    // POST /iclock/devicecmd — device confirming it executed a command.
    public function deviceCmd(Request $request)
    {
        $device = $this->resolveDevice($request);

        if (! $device) {
            return $this->plain('ERROR: Unregistered SN', 401);
        }

        Log::info('iClock devicecmd result received', [
            'company_device_id' => $device->id,
            'body' => $request->getContent(),
        ]);

        return $this->plain('OK');
    }

    protected function resolveDevice(Request $request): ?CompanyDevice
    {
        $sn = $request->query('SN');

        if (! $sn) {
            return null;
        }

        return CompanyDevice::query()
            ->where('serial_no', $sn)
            ->where('status', '!=', 'disabled')
            ->first();
    }

    protected function handshake(CompanyDevice $device): Response
    {
        // Minimal handshake config. Real firmware tolerates a fairly small
        // response; expand with TransTimes/TransInterval/Encrypt lines if a
        // specific device model insists on them (check your model's ADMS
        // spec sheet if handshake fails silently).
        $body = implode("\n", [
            'GET OPTION FROM: ' . $device->serial_no,
            'Stamp=9999',
            'OpStamp=9999',
            'ErrorDelay=60',
            'Delay=30',
            'Realtime=1',
            'Encrypt=0',
        ]);

        return $this->plain($body);
    }

    protected function storeAttendanceLogs(CompanyDevice $device, string $body): Response
    {
        $lines = preg_split('/\r\n|\r|\n/', trim($body));
        $inserted = 0;
        $affected = [];

        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }

            // ADMS ATTLOG format: PIN, time, status, verify-type, ...(more
            // columns some firmwares add — we only need the first four).
            $parts = preg_split('/\t/', $line);
            $pin = $parts[0] ?? null;
            $deviceTime = $parts[1] ?? null;
            $status = $parts[2] ?? null;
            $verify = $parts[3] ?? null;

            if (! $pin || ! $deviceTime) {
                continue;
            }

            $dateTime = explode(' ', $deviceTime);

            $nepDate = adToBs($dateTime[0]);
            $time = $dateTime[1];

            $staffId = DeviceStaffLink::query()
                ->where('company_device_id', $device->id)
                ->where('device_user_id', $pin)
                ->value('staff_id');

            if (!$staffId) continue;

            $punchTime = Carbon::parse($time);

            $wasInserted = DB::table('device_attendance_logs')->insertOrIgnore([
                'company_device_id' => $device->id ?? null,
                'staff_id' => $staffId,
                'device_user_id' => $pin,
                'date' => $nepDate,
                'time' => $time,
                'attendance_type' => "auto",
                'verify_type' => is_numeric($verify) ? (int) $verify : null,
                'punch_state' => is_numeric($status) ? (int) $status : null,
                'processed' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($wasInserted && $staffId) {
                $inserted++;
                $affected[$staffId . '-' . $punchTime->toDateString()] = [
                    'staff_id' => $staffId,
                    'date' => $nepDate,
                    'time' => $time,
                ];
            }
        }

        $aggregator = new AttendanceAggregator();

        foreach ($affected as $pair) {
            $aggregator->rebuildFor($pair['staff_id'], $pair['date'], $device->id);
        }

        // The device expects exactly this "OK: <count>" shape to know the
        // batch was accepted — anything else and some firmwares re-send.
        return $this->plain('OK: ' . $inserted);
    }

    protected function plain(string $body, int $status = 200): Response
    {
        return response($body, $status)->header('Content-Type', 'text/plain');
    }

    private function printRequestData()
    {
        $this->console->writeln('###### REQUEST ########');

        foreach (request()->all() as $key => $value) {
            $this->console->writeln($key . ':' . $value);
        }

        $this->console->writeln('###### REQUEST ######## ' . request()->method());
    }
}
