<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\IpGeolocation\GeoLite2;

use Shlinkio\Shlink\IpGeolocation\Exception\DbUpdateException;

interface DbUpdaterInterface
{
    public function databaseFileExists(): bool;

    /**
     * @throws DbUpdateException
     */
    public function downloadFreshCopy(?callable $handleProgress = null): void;
}
