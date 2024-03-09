<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\IpGeolocation\GeoLite2;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\IpGeolocation\GeoLite2\GeoLite2Options;
use Shlinkio\Shlink\IpGeolocation\GeoLite2\GeoLite2ReaderFactory;

class GeoLite2ReaderFactoryTest extends TestCase
{
    private GeoLite2ReaderFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new GeoLite2ReaderFactory(new GeoLite2Options(
            dbLocation: __DIR__ . '/../../test-resources/GeoLite2-City.mmdb',
        ));
    }

    #[Test]
    public function newInstancesAreCreatedEveryTime(): void
    {
        $reader1 = ($this->factory)();
        $reader2 = ($this->factory)();
        $reader3 = ($this->factory)();

        self::assertNotSame($reader1, $reader2);
        self::assertNotSame($reader1, $reader3);
        self::assertNotSame($reader2, $reader3);
    }
}
