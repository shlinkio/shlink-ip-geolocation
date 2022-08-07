<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\IpGeolocation\GeoLite2;

use Laminas\Stdlib\AbstractOptions;

use function str_replace;

class GeoLite2Options extends AbstractOptions
{
    private const DOWNLOAD_FROM_PATTERN = 'https://download.maxmind.com/app/geoip_download'
        . '?edition_id=GeoLite2-City&license_key={license_key}&suffix=tar.gz';

    private string $dbLocation = '';
    private string $tempDir = '';
    private ?string $licenseKey = null;
    private float $connectionTimeout = 15.0;

    public function getDbLocation(): string
    {
        return $this->dbLocation;
    }

    protected function setDbLocation(string $dbLocation): void
    {
        $this->dbLocation = $dbLocation;
    }

    public function getTempDir(): string
    {
        return $this->tempDir;
    }

    protected function setTempDir(string $tempDir): void
    {
        $this->tempDir = $tempDir;
    }

    public function getDownloadFrom(): string
    {
        return str_replace('{license_key}', $this->licenseKey ?? '', self::DOWNLOAD_FROM_PATTERN);
    }

    protected function setLicenseKey(?string $licenseKey): void
    {
        $this->licenseKey = $licenseKey;
    }

    public function hasLicenseKey(): bool
    {
        return $this->licenseKey !== null;
    }

    protected function setConnectionTimeout(float $connectionTimeout): void
    {
        $this->connectionTimeout = $connectionTimeout;
    }

    public function getConnectionTimeout(): float
    {
        return $this->connectionTimeout;
    }
}
