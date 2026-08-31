<?php

namespace App\Services\Devices;

use App\Models\CompanyDevice;
use RuntimeException;

class DeviceDriverFactory
{
    /**
     * Add one line per brand you support. Keys should match whatever you use
     * to tag brand/protocol on a device — either companies_devices.device_code
     * (already on your model) or device_brand_id via $companyDevice->brand->code,
     * depending on how you're populating those columns today. Pick one source
     * of truth and stick to it; mixing both is how you end up sending a
     * ZKTeco payload to a Hikvision unit.
     */
    public static function make(CompanyDevice $companyDevice): FingerprintDeviceDriverInterface
    {
        $brandCode = strtolower($companyDevice->device_code ?? $companyDevice->brand?->code ?? '');

        return match ($brandCode) {
            'zkteco', 'zk' => new ZktecoDriver($companyDevice),
            'hikvision' => new HikvisionDriver($companyDevice),
            // 'suprema' => new SupremaDriver($companyDevice),
            // 'anviz'   => new AnvizDriver($companyDevice),
            default => throw new RuntimeException(
                "No driver registered for device brand [{$brandCode}] on device #{$companyDevice->id}. " .
                    'Add a case in DeviceDriverFactory::make() for this brand.'
            ),
        };
    }
}
