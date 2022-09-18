<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\IpGeolocation\Exception;

class MissingLicenseException extends RuntimeException
{
    public static function forMissingLicense(): self
    {
        return new self('Impossible to download GeoLite2 db file. A license key was not provided.');
    }
}
