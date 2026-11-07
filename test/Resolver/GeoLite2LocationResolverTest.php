<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Geolocation\Resolver;

use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use GeoIp2\Model\City;
use MaxMind\Db\Reader\InvalidDatabaseException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Geolocation\Exception\LocationResolutionException;
use Shlinkio\Shlink\Geolocation\GeoLite2\DbUpdaterInterface;
use Shlinkio\Shlink\Geolocation\Model\Location;
use Shlinkio\Shlink\Geolocation\Resolver\GeoLite2LocationResolver;
use Throwable;

use function ShlinkioTest\Shlink\Geolocation\dummyRequest;
use function ShlinkioTest\Shlink\Geolocation\requestWithIp;

class GeoLite2LocationResolverTest extends TestCase
{
    private GeoLite2LocationResolver $resolver;
    private MockObject&Reader $reader;
    private MockObject&DbUpdaterInterface $dbUpdater;

    public function setUp(): void
    {
        $this->reader = $this->createMock(Reader::class);
        $this->dbUpdater = $this->createMock(DbUpdaterInterface::class);

        $this->resolver = new GeoLite2LocationResolver(fn () => $this->reader, $this->dbUpdater);
    }

    #[Test]
    public function exceptionIsThrowsIfIpAddressIsMissing(): void
    {
        $this->dbUpdater->expects($this->never())->method('databaseFileExists');
        $this->reader->expects($this->never())->method('city');

        $this->expectException(LocationResolutionException::class);
        $this->expectExceptionMessageIs('Provided request does not contain an IP address');

        $this->resolver->resolveRequestLocation(dummyRequest());
    }

    #[Test]
    public function exceptionIsThrowsIfIpAddressIsLocalhost(): void
    {
        $this->dbUpdater->expects($this->never())->method('databaseFileExists');
        $this->reader->expects($this->never())->method('city');

        $this->expectException(LocationResolutionException::class);
        $this->expectExceptionMessageIs('Could not resolve location for IP address 127.0.0.1');

        $this->resolver->resolveRequestLocation(requestWithIp('127.0.0.1'));
    }

    #[Test]
    public function exceptionIsThrowsIfDatabaseFileDoesNotExist(): void
    {
        $this->dbUpdater->expects($this->once())->method('databaseFileExists')->willReturn(false);
        $this->reader->expects($this->never())->method('city');

        $this->expectException(LocationResolutionException::class);
        $this->expectExceptionMessageIs('Tried to geolocate IP address, but a GeoLite2 db was not found');

        $this->resolver->resolveRequestLocation(requestWithIp('1.2.3.4'));
    }

    #[Test, DataProvider('provideReaderExceptions')]
    public function exceptionIsThrownIfReaderThrowsException(Throwable $e, string $message): void
    {
        $ipAddress = '1.2.3.4';

        $this->dbUpdater->expects($this->once())->method('databaseFileExists')->willReturn(true);
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

        $this->dbUpdater->expects($this->once())->method('databaseFileExists')->willReturn(true);
        $this->reader->expects($this->once())->method('city')->with($this->equalTo($ipAddress))->willReturn($city);

        $result = $this->resolver->resolveRequestLocation(requestWithIp($ipAddress));

        self::assertEquals(Location::empty(), $result);
    }
}
