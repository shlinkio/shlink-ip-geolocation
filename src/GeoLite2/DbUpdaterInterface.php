<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\IpGeolocation\GeoLite2;

use Shlinkio\Shlink\IpGeolocation\Exception\DbUpdateException;
use Shlinkio\Shlink\IpGeolocation\Exception\MissingLicenseException;

interface DbUpdaterInterface
{
    public function databaseFileExists(): bool;

    /**
     * @param (callable(int $total, int $downloaded): void) | null $handleProgress
     * @throws DbUpdateException
     * @throws MissingLicenseException
     */
    public function downloadFreshCopy(callable|null $handleProgress = null): void;
}
