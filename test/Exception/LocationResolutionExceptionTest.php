<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\IpGeolocation\Exception;

use Exception;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\IpGeolocation\Exception\LocationResolutionException;
use Throwable;

class LocationResolutionExceptionTest extends TestCase
{
    #[Test]
    #[TestWith([null])]
    #[TestWith([new Exception('Previous error')])]
    public function anonymousProperlyCreatesExceptionWithPrev(Throwable|null $prev): void
    {
        $e = LocationResolutionException::anonymous($prev);

        self::assertEquals('Could not resolve request location', $e->getMessage());
        self::assertEquals($prev, $e->getPrevious());
    }

    #[Test]
    #[TestWith([null])]
    #[TestWith([new Exception('Previous error')])]
    public function fromIpAddressProperlyCreatesExceptionWithPrev(Throwable|null $prev): void
    {
        $e = LocationResolutionException::fromIpAddress('1.2.3.4', $prev);

        self::assertEquals('Could not resolve location for IP address 1.2.3.4', $e->getMessage());
        self::assertEquals($prev, $e->getPrevious());
    }
}
