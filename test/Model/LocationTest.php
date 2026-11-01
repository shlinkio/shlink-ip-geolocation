<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Geolocation\Model;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Geolocation\Model\Location;

class LocationTest extends TestCase
{
    #[Test]
    #[TestWith(['12.3', 12.3])]
    #[TestWith([87.5, 87.5])]
    #[TestWith(['-12.3', -12.3])]
    #[TestWith([-87.5, -87.5])]
    #[TestWith(['-90.0', -90.0])]
    #[TestWith(['90.0', 90.0])]
    #[TestWith(['-91.0', -90.0])]
    #[TestWith(['91.0', 90.0])]
    #[TestWith(['not a number', 0.0])]
    public function expectedLatitudeIsSet(string|float $latitude, float $expectedLatitude): void
    {
        self::assertEquals($expectedLatitude, new Location(latitude: $latitude)->latitude);
    }

    #[Test]
    #[TestWith(['12.3', 12.3])]
    #[TestWith([87.5, 87.5])]
    #[TestWith(['-12.3', -12.3])]
    #[TestWith([-87.5, -87.5])]
    #[TestWith(['-180.0', -180.0])]
    #[TestWith(['180.0', 180.0])]
    #[TestWith(['-181.0', -180.0])]
    #[TestWith(['181.0', 180.0])]
    #[TestWith(['not a number', 0.0])]
    public function expectedLongitudeIsSet(string|float $longitude, float $expectedLongitude): void
    {
        self::assertEquals($expectedLongitude, new Location(longitude: $longitude)->longitude);
    }
}
