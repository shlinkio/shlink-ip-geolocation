<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Geolocation\Model;

use function is_numeric;
use function max;
use function min;

final readonly class Location
{
    public float $latitude;
    public float $longitude;

    public function __construct(
        public string $countryCode = '',
        public string $countryName = '',
        public string $regionName = '',
        public string $city = '',
        float|string $latitude = 0.0,
        float|string $longitude = 0.0,
        public string $timeZone = '',
        /** Full ISO 3166-2 code of the first-level subdivision (e.g. US-CA), or empty string if unknown */
        public string $regionCode = '',
    ) {
        $this->latitude = $this->cleanupCoordinateValue($latitude, minMax: 90.0);
        $this->longitude = $this->cleanupCoordinateValue($longitude, minMax: 180.0);
    }

    private function cleanupCoordinateValue(float|string $value, float $minMax): float
    {
        if (!is_numeric($value)) {
            return 0.0;
        }

        $coordinate = (float) $value;
        return max(min($coordinate, $minMax), -$minMax);
    }

    public static function empty(): self
    {
        return new self();
    }
}
