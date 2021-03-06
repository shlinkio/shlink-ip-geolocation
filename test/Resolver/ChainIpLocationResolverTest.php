<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\IpGeolocation\Resolver;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Shlinkio\Shlink\IpGeolocation\Exception\WrongIpException;
use Shlinkio\Shlink\IpGeolocation\Model\Location;
use Shlinkio\Shlink\IpGeolocation\Resolver\ChainIpLocationResolver;
use Shlinkio\Shlink\IpGeolocation\Resolver\IpLocationResolverInterface;

class ChainIpLocationResolverTest extends TestCase
{
    use ProphecyTrait;

    private ChainIpLocationResolver $resolver;
    private ObjectProphecy $firstInnerResolver;
    private ObjectProphecy $secondInnerResolver;

    public function setUp(): void
    {
        $this->firstInnerResolver = $this->prophesize(IpLocationResolverInterface::class);
        $this->secondInnerResolver = $this->prophesize(IpLocationResolverInterface::class);

        $this->resolver = new ChainIpLocationResolver(
            $this->firstInnerResolver->reveal(),
            $this->secondInnerResolver->reveal(),
        );
    }

    /** @test */
    public function throwsExceptionWhenNoInnerResolverCanHandleTheResolution(): void
    {
        $ipAddress = '1.2.3.4';

        $firstResolve = $this->firstInnerResolver->resolveIpLocation($ipAddress)->willThrow(WrongIpException::class);
        $secondResolve = $this->secondInnerResolver->resolveIpLocation($ipAddress)->willThrow(WrongIpException::class);

        $this->expectException(WrongIpException::class);
        $firstResolve->shouldBeCalledOnce();
        $secondResolve->shouldBeCalledOnce();

        $this->resolver->resolveIpLocation($ipAddress);
    }

    /** @test */
    public function returnsResultOfFirstInnerResolver(): void
    {
        $ipAddress = '1.2.3.4';

        $firstResolve = $this->firstInnerResolver->resolveIpLocation($ipAddress)->willReturn(Location::emptyInstance());
        $secondResolve = $this->secondInnerResolver->resolveIpLocation($ipAddress)->willThrow(WrongIpException::class);

        $this->resolver->resolveIpLocation($ipAddress);

        $firstResolve->shouldHaveBeenCalledOnce();
        $secondResolve->shouldNotHaveBeenCalled();
    }

    /** @test */
    public function returnsResultOfSecondInnerResolver(): void
    {
        $ipAddress = '1.2.3.4';

        $firstResolve = $this->firstInnerResolver->resolveIpLocation($ipAddress)->willThrow(WrongIpException::class);
        $secondResolve = $this->secondInnerResolver->resolveIpLocation($ipAddress)->willReturn(
            Location::emptyInstance(),
        );

        $this->resolver->resolveIpLocation($ipAddress);

        $firstResolve->shouldHaveBeenCalledOnce();
        $secondResolve->shouldHaveBeenCalledOnce();
    }
}
