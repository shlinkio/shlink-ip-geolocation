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
    ) {}

    public static function empty(): self
    {
        return new self();
    }
}
