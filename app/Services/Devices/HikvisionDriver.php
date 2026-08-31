<?php

namespace App\Services\Devices;

use App\Models\CompanyDevice;
use Illuminate\Support\Facades\Http;

/**
 * Example second driver. Unlike ZKTeco's raw UDP protocol, Hikvision-style
 * terminals are usually reachable over HTTP (ISAPI) using the api_url/api_key
 * columns you already have on companies_devices — that's exactly why those
 * columns exist alongside ip/port: different brands need different transport.
 *
 * This is a skeleton to show the shape; fill in real ISAPI endpoints/auth
 * for your specific hardware before using it.
 */
class HikvisionDriver implements FingerprintDeviceDriverInterface
{
    protected bool $connected = false;

    public function __construct(protected CompanyDevice $companyDevice)
    {
    }

    public function connect(): bool
    {
        $response = Http::withBasicAuth('admin', $this->companyDevice->api_key)
            ->timeout(5)
            ->get(rtrim($this->companyDevice->api_url, '/').'/ISAPI/System/deviceInfo');

        $this->connected = $response->successful();

        return $this->connected;
    }

    public function disconnect(): void
    {
        // Stateless HTTP API — nothing to tear down.
        $this->connected = false;
    }

    public function getSerialNumber(): ?string
    {
        $response = Http::withBasicAuth('admin', $this->companyDevice->api_key)
            ->timeout(5)
            ->get(rtrim($this->companyDevice->api_url, '/').'/ISAPI/System/deviceInfo');

        // Real response is XML/JSON depending on device firmware config —
        // parse accordingly. Left as a stub.
        return $response->json('serialNumber');
    }

    public function getAttendanceLogs(): array
    {
        // Pull from the device's event/access-control-record endpoint and map
        // it into the same shape ZktecoDriver returns, e.g.:
        // ['device_user_id' => ..., 'timestamp' => ..., 'verify_type' => ..., 'state' => ...]
        return [];
    }
}
