<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Geolocation\GeoLite2;

use Shlinkio\Shlink\Geolocation\Exception\DbUpdateException;
use Shlinkio\Shlink\Geolocation\Exception\MissingLicenseException;

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
