<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\IpGeolocation\GeoLite2;

use Shlinkio\Shlink\IpGeolocation\Exception\DbUpdateException;
use Shlinkio\Shlink\IpGeolocation\Exception\MissingLicenseException;

interface DbUpdaterInterface
{
    public function databaseFileExists(): bool;

    /**
     * @throws DbUpdateException
     * @throws MissingLicenseException
     */
    public function downloadFreshCopy(?callable $handleProgress = null): void;
}
