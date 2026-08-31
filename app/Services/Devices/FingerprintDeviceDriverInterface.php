<?php

namespace App\Services\Devices;

interface FingerprintDeviceDriverInterface
{
    public function connect(): bool;

    public function disconnect(): void;

    /**
     * Return the serial number burned into the device's firmware, so it can be
     * checked against what you have on file in companies_devices.serial_no.
     * Not every low-level library exposes this the same way — see the note in
     * ZktecoDriver.
     */
    public function getSerialNumber(): ?string;

    /**
     * Return raw punches as an array of:
     * ['device_user_id' => string, 'timestamp' => string (Y-m-d H:i:s), 'verify_type' => int|null, 'state' => int|null]
     */
    public function getAttendanceLogs(): array;
}
