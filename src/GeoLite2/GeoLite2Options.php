<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\IpGeolocation\GeoLite2;

use function str_replace;

class GeoLite2Options
{
    private const DOWNLOAD_FROM_PATTERN = 'https://download.maxmind.com/app/geoip_download'
        . '?edition_id=GeoLite2-City&license_key={license_key}&suffix=tar.gz';

    public readonly string $downloadFrom;

    public function __construct(
        private readonly ?string $licenseKey = null,
        public readonly string $dbLocation = '',
        public readonly string $tempDir = '',
        public readonly float $connectionTimeout = 15.0,
    ) {
        $this->downloadFrom = str_replace('{license_key}', $this->licenseKey ?? '', self::DOWNLOAD_FROM_PATTERN);
    }

    public function hasLicenseKey(): bool
    {
        return ! empty($this->licenseKey);
    }
}
