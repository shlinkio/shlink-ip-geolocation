<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\IpGeolocation\Model;

final readonly class Location
{
    public function __construct(
        public string $countryCode = '',
        public string $countryName = '',
        public string $regionName = '',
        public string $city = '',
        public float $latitude = 0.0,
        public float $longitude = 0.0,
        public string $timeZone = '',
    ) {
    }

    /** @deprecated Use self:.empty() instead */
    public static function emptyInstance(): self
    {
        return self::empty();
    }

    public static function empty(): self
    {
        return new self();
    }
}
