<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\IpGeolocation\Resolver;

use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use GeoIp2\Model\City;
use MaxMind\Db\Reader\InvalidDatabaseException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\IpGeolocation\Exception\WrongIpException;
use Shlinkio\Shlink\IpGeolocation\Model\Location;
use Shlinkio\Shlink\IpGeolocation\Resolver\GeoLite2LocationResolver;
use Throwable;

class GeoLite2LocationResolverTest extends TestCase
{
    private GeoLite2LocationResolver $resolver;
    private MockObject $reader;

    public function setUp(): void
    {
        $this->reader = $this->createMock(Reader::class);
        $this->resolver = new GeoLite2LocationResolver($this->reader);
    }

    /**
     * @test
     * @dataProvider provideReaderExceptions
     */
    public function exceptionIsThrownIfReaderThrowsException(Throwable $e, string $message): void
    {
        $ipAddress = '1.2.3.4';

        $this->reader
            ->expects($this->once())
            ->method('city')
            ->with($this->equalTo($ipAddress))
            ->willThrowException($e);

        $this->expectException(WrongIpException::class);
        $this->expectExceptionMessage($message);
        $this->expectExceptionCode(0);

        $this->resolver->resolveIpLocation($ipAddress);
    }

    public function provideReaderExceptions(): iterable
    {
        yield 'invalid IP address' => [new AddressNotFoundException(), 'Provided IP "1.2.3.4" is invalid'];
        yield 'invalid geolite DB' => [new InvalidDatabaseException(), 'Provided GeoLite2 db file is invalid'];
    }

    /** @test */
    public function resolvedCityIsProperlyMapped(): void
    {
        $ipAddress = '1.2.3.4';
        $city = new City([]);

        $this->reader->expects($this->once())->method('city')->with($this->equalTo($ipAddress))->willReturn($city);

        $result = $this->resolver->resolveIpLocation($ipAddress);

        self::assertEquals(Location::emptyInstance(), $result);
    }
}
