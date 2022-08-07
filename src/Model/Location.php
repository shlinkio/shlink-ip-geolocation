<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\IpGeolocation\Model;

final class Location
{
    public function __construct(
        public readonly string $countryCode,
        public readonly string $countryName,
        public readonly string $regionName,
        public readonly string $city,
        public readonly float $latitude,
        public readonly float $longitude,
        public readonly string $timeZone,
    ) {
    }

    public static function emptyInstance(): self
    {
        return new self('', '', '', '', 0.0, 0.0, '');
    }
}
