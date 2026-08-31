<?php

namespace App\Services\Devices;

use App\Models\CompanyDevice;

/**
 * Thin adapter around a ZKTeco UDP-protocol PHP package.
 *
 * ZKTeco (and clones) fingerprint terminals speak a UDP protocol on port 4370
 * and don't have a native PHP SDK — you talk to them through a community
 * package. A few well-maintained ones on Packagist:
 *   - rats/zkteco
 *   - jmrashed/zkteco
 *   - mehedijaman/laravel-zkteco
 *
 * `composer require rats/zkteco` and adjust the `use` statement + class name
 * below to whichever one you install — the connect()/getAttendance()/
 * disconnect() call shape is consistent across all of them, but exact method
 * names for reading the serial number differ by fork/version, so check that
 * package's README and adjust getSerialNumber() accordingly.
 */
class ZktecoDriver implements FingerprintDeviceDriverInterface
{
    protected \Rats\Zkteco\Lib\ZKTeco $client;

    protected bool $connected = false;

    public function __construct(protected CompanyDevice $companyDevice)
    {
        $this->client = new \Rats\Zkteco\Lib\ZKTeco(
            $this->companyDevice->ip,
            $this->companyDevice->port ?: 4370
        );
    }

    public function connect(): bool
    {
        $this->connected = (bool) $this->client->connect();

        return $this->connected;
    }

    public function disconnect(): void
    {
        if ($this->connected) {
            $this->client->disconnect();
            $this->connected = false;
        }
    }

    public function getSerialNumber(): ?string
    {
        // Method name varies by package/firmware — common options are
        // getSerialNumber(), serialNumber(), or reading it out of deviceInfo().
        // Wrap in try/catch: some firmwares/libraries simply don't expose it,
        // in which case you fall back to trusting ip+port pairing alone and
        // just log a warning (see FingerprintDeviceService::connect()).
        if (method_exists($this->client, 'getSerialNumber')) {
            return $this->client->getSerialNumber() ?: null;
        }

        return null;
    }

    public function getAttendanceLogs(): array
    {
        $raw = $this->client->getAttendance() ?: [];

        return collect($raw)->map(fn(array $log) => [
            'device_user_id' => (string) ($log['id'] ?? $log['uid']),
            'timestamp' => $log['timestamp'],
            'verify_type' => $log['state'] ?? null,
            'state' => $log['type'] ?? null,
        ])->all();
    }
}
