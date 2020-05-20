<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\IpGeolocation\GeoLite2;

use Laminas\Stdlib\AbstractOptions;

use function str_replace;

class GeoLite2Options extends AbstractOptions
{
    private string $dbLocation = '';
    private string $tempDir = '';
    private string $licenseKey = 'G4Lm0C60yJsnkdPi';
    private string $downloadFrom = 'https://download.maxmind.com/app/geoip_download'
        . '?edition_id=GeoLite2-City&license_key={license_key}&suffix=tar.gz';
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
        return str_replace('{license_key}', $this->licenseKey, $this->downloadFrom);
    }

    protected function setDownloadFrom(string $downloadFrom): self
    {
        $this->downloadFrom = $downloadFrom;
        return $this;
    }

    protected function setLicenseKey(string $licenseKey): self
    {
        $this->licenseKey = $licenseKey;
        return $this;
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
