<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\IpGeolocation\Resolver;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\IpGeolocation\Exception\WrongIpException;
use Shlinkio\Shlink\IpGeolocation\Model\Location;
use Shlinkio\Shlink\IpGeolocation\Resolver\ChainIpLocationResolver;
use Shlinkio\Shlink\IpGeolocation\Resolver\IpLocationResolverInterface;

class ChainIpLocationResolverTest extends TestCase
{
    private ChainIpLocationResolver $resolver;
    private MockObject $firstInnerResolver;
    private MockObject $secondInnerResolver;

    public function setUp(): void
    {
        $this->firstInnerResolver = $this->createMock(IpLocationResolverInterface::class);
        $this->secondInnerResolver = $this->createMock(IpLocationResolverInterface::class);

        $this->resolver = new ChainIpLocationResolver($this->firstInnerResolver, $this->secondInnerResolver);
    }

    #[Test]
    public function throwsExceptionWhenNoInnerResolverCanHandleTheResolution(): void
    {
        $ipAddress = '1.2.3.4';

        $this->firstInnerResolver
            ->expects($this->once())
            ->method('resolveIpLocation')
            ->with($this->equalTo($ipAddress))
            ->willThrowException(WrongIpException::fromIpAddress(''));
        $this->secondInnerResolver
            ->expects($this->once())
            ->method('resolveIpLocation')
            ->with($this->equalTo($ipAddress))
            ->willThrowException(WrongIpException::fromIpAddress(''));

        $this->expectException(WrongIpException::class);

        $this->resolver->resolveIpLocation($ipAddress);
    }

    #[Test]
    public function returnsResultOfFirstInnerResolver(): void
    {
        $ipAddress = '1.2.3.4';

        $this->firstInnerResolver
            ->expects($this->once())
            ->method('resolveIpLocation')
            ->with($this->equalTo($ipAddress))
            ->willReturn(Location::empty());
        $this->secondInnerResolver->expects($this->never())->method('resolveIpLocation');

        $this->resolver->resolveIpLocation($ipAddress);
    }

    #[Test]
    public function returnsResultOfSecondInnerResolver(): void
    {
        $ipAddress = '1.2.3.4';

        $this->firstInnerResolver
            ->expects($this->once())
            ->method('resolveIpLocation')
            ->with($this->equalTo($ipAddress))
            ->willThrowException(WrongIpException::fromIpAddress(''));
        $this->secondInnerResolver
            ->expects($this->once())
            ->method('resolveIpLocation')
            ->with($this->equalTo($ipAddress))
            ->willReturn(Location::empty());

        $this->resolver->resolveIpLocation($ipAddress);
    }
}
