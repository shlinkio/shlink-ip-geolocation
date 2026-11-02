<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Geolocation\Exception;

use Exception;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Geolocation\Exception\LocationResolutionException;
use Throwable;

class LocationResolutionExceptionTest extends TestCase
{
    #[Test, DataProvider('providePrev')]
    public function anonymousProperlyCreatesExceptionWithPrev(Throwable|null $prev): void
    {
        $e = LocationResolutionException::anonymous($prev);

        self::assertEquals('Could not resolve request location', $e->getMessage());
        self::assertEquals($prev, $e->getPrevious());
    }

    #[Test, DataProvider('providePrev')]
    public function fromIpAddressProperlyCreatesExceptionWithPrev(Throwable|null $prev): void
    {
        $e = LocationResolutionException::fromIpAddress('1.2.3.4', $prev);

        self::assertEquals('Could not resolve location for IP address 1.2.3.4', $e->getMessage());
        self::assertEquals($prev, $e->getPrevious());
    }

    public static function providePrev(): iterable
    {
        yield 'no prev' => [null];
        yield 'with prev' => [new Exception('Previous error')];
    }
}
