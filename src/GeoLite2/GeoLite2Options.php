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

    protected function setDbLocation(string $dbLocation): self
    {
        $this->dbLocation = $dbLocation;
        return $this;
    }

    public function getTempDir(): string
    {
        return $this->tempDir;
    }

    protected function setTempDir(string $tempDir): self
    {
        $this->tempDir = $tempDir;
        return $this;
    }

    public function getDownloadFrom(): string
    {
        return str_replace('{license_key}', $this->licenseKey ?? '', self::DOWNLOAD_FROM_PATTERN);
    }

    protected function setLicenseKey(?string $licenseKey): self
    {
        $this->licenseKey = $licenseKey;
        return $this;
    }

    public function hasLicenseKey(): bool
    {
        return $this->licenseKey !== null;
    }

    protected function setConnectionTimeout(float $connectionTimeout): self
    {
        $this->connectionTimeout = $connectionTimeout;
        return $this;
    }

    public function getConnectionTimeout(): float
    {
        return $this->connectionTimeout;
    }
}
