<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\IpGeolocation\Exception;

use Exception;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\IpGeolocation\Exception\WrongIpException;

class WrongIpExceptionTest extends TestCase
{
    #[Test]
    public function fromIpAddressProperlyCreatesExceptionWithoutPrev(): void
    {
        $e = WrongIpException::fromIpAddress('1.2.3.4');

        self::assertEquals('Provided IP "1.2.3.4" is invalid', $e->getMessage());
        self::assertEquals(0, $e->getCode());
        self::assertNull($e->getPrevious());
    }

    #[Test]
    public function fromIpAddressProperlyCreatesExceptionWithPrev(): void
    {
        $prev = new Exception('Previous error');
        $e = WrongIpException::fromIpAddress('1.2.3.4', $prev);

        self::assertEquals('Provided IP "1.2.3.4" is invalid', $e->getMessage());
        self::assertEquals(0, $e->getCode());
        self::assertSame($prev, $e->getPrevious());
    }
}
