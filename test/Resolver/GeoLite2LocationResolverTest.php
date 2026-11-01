<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\IpGeolocation\Resolver;

use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use GeoIp2\Model\City;
use MaxMind\Db\Reader\InvalidDatabaseException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\IpGeolocation\Exception\LocationResolutionException;
use Shlinkio\Shlink\IpGeolocation\Model\Location;
use Shlinkio\Shlink\IpGeolocation\Resolver\GeoLite2LocationResolver;
use Throwable;

use function ShlinkioTest\Shlink\IpGeolocation\requestWithIp;

class GeoLite2LocationResolverTest extends TestCase
{
    private GeoLite2LocationResolver $resolver;
    private MockObject&Reader $reader;

    public function setUp(): void
    {
        $this->reader = $this->createMock(Reader::class);
        $this->resolver = new GeoLite2LocationResolver(fn () => $this->reader);
    }

    #[Test, DataProvider('provideReaderExceptions')]
    public function exceptionIsThrownIfReaderThrowsException(Throwable $e, string $message): void
    {
        $ipAddress = '1.2.3.4';

        $this->reader
            ->expects($this->once())
            ->method('city')
            ->with($this->equalTo($ipAddress))
            ->willThrowException($e);

        $this->expectException(LocationResolutionException::class);
        $this->expectExceptionMessageIs($message);

        $this->resolver->resolveRequestLocation(requestWithIp($ipAddress));
    }

    public static function provideReaderExceptions(): iterable
    {
        yield 'invalid IP address' => [
            new AddressNotFoundException(),
            'Could not resolve location for IP address 1.2.3.4',
        ];
        yield 'invalid geolite DB' => [new InvalidDatabaseException(), 'Provided GeoLite2 db file is invalid'];
    }

    #[Test]
    public function resolvedCityIsProperlyMapped(): void
    {
        $ipAddress = '1.2.3.4';
        $city = new City([]);

        $this->reader->expects($this->once())->method('city')->with($this->equalTo($ipAddress))->willReturn($city);

        $result = $this->resolver->resolveRequestLocation(requestWithIp($ipAddress));

        self::assertEquals(Location::empty(), $result);
    }
}
