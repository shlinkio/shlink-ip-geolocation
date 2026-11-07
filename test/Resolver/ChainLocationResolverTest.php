<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Geolocation\Resolver;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Geolocation\Exception\LocationResolutionException;
use Shlinkio\Shlink\Geolocation\Model\Location;
use Shlinkio\Shlink\Geolocation\Resolver\ChainLocationResolver;
use Shlinkio\Shlink\Geolocation\Resolver\LocationResolverInterface;

use function ShlinkioTest\Shlink\Geolocation\dummyRequestWithIp;

class ChainLocationResolverTest extends TestCase
{
    private ChainLocationResolver $resolver;
    private MockObject $firstInnerResolver;
    private MockObject $secondInnerResolver;

    public function setUp(): void
    {
        $this->firstInnerResolver = $this->createMock(LocationResolverInterface::class);
        $this->secondInnerResolver = $this->createMock(LocationResolverInterface::class);

        $this->resolver = new ChainLocationResolver($this->firstInnerResolver, $this->secondInnerResolver);
    }

    #[Test]
    public function throwsExceptionWhenNoInnerResolverCanHandleTheResolution(): void
    {
        $request = dummyRequestWithIp('1.2.3.4');

        $this->firstInnerResolver
            ->expects($this->once())
            ->method('resolveRequestLocation')
            ->with($this->equalTo($request))
            ->willThrowException(LocationResolutionException::anonymous());
        $this->secondInnerResolver
            ->expects($this->once())
            ->method('resolveRequestLocation')
            ->with($this->equalTo($request))
            ->willThrowException(LocationResolutionException::anonymous());

        $this->expectException(LocationResolutionException::class);

        $this->resolver->resolveRequestLocation($request);
    }

    #[Test]
    public function returnsResultOfFirstInnerResolver(): void
    {
        $request = dummyRequestWithIp('1.2.3.4');

        $this->firstInnerResolver
            ->expects($this->once())
            ->method('resolveRequestLocation')
            ->with($this->equalTo($request))
            ->willReturn(Location::empty());
        $this->secondInnerResolver->expects($this->never())->method('resolveRequestLocation');

        $this->resolver->resolveRequestLocation($request);
    }

    #[Test]
    public function returnsResultOfSecondInnerResolver(): void
    {
        $request = dummyRequestWithIp('1.2.3.4');

        $this->firstInnerResolver
            ->expects($this->once())
            ->method('resolveRequestLocation')
            ->with($this->equalTo($request))
            ->willThrowException(LocationResolutionException::anonymous());
        $this->secondInnerResolver
            ->expects($this->once())
            ->method('resolveRequestLocation')
            ->with($this->equalTo($request))
            ->willReturn(Location::empty());

        $this->resolver->resolveRequestLocation($request);
    }
}
