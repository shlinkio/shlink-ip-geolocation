<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\IpGeolocation\GeoLite2;

use GeoIp2\Database\Reader;
use MaxMind\Db\Reader\InvalidDatabaseException;

readonly class GeoLite2ReaderFactory
{
    public function __construct(private GeoLite2Options $options)
    {
    }

    /**
     * @throws InvalidDatabaseException
     */
    public function __invoke(): Reader
    {
        return new Reader($this->options->dbLocation);
    }
}
