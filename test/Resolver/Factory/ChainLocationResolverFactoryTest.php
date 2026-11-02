<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Geolocation\Resolver\Factory;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Shlinkio\Shlink\Geolocation\Exception\LocationResolutionException;
use Shlinkio\Shlink\Geolocation\Exception\RuntimeException;
use Shlinkio\Shlink\Geolocation\Model\Location;
use Shlinkio\Shlink\Geolocation\Resolver\CloudflareLocationResolver;
use Shlinkio\Shlink\Geolocation\Resolver\EmptyLocationResolver;
use Shlinkio\Shlink\Geolocation\Resolver\Factory\ChainLocationResolverFactory;
use Shlinkio\Shlink\Geolocation\Resolver\GeoLite2LocationResolver;
use Shlinkio\Shlink\Geolocation\Resolver\GeolocationStrategy;
use Shlinkio\Shlink\Geolocation\Resolver\LocationResolverInterface;
use stdClass;

use function ShlinkioTest\Shlink\Geolocation\dummyRequest;

class ChainLocationResolverFactoryTest extends TestCase
{
    private ChainLocationResolverFactory $factory;
    private ContainerInterface&Stub $container;

    protected function setUp(): void
    {
        $this->container = $this->createStub(ContainerInterface::class);
        $this->factory = new ChainLocationResolverFactory();
    }

    #[Test]
    #[TestWith(['foo'])]
    #[TestWith([45])]
    #[TestWith([new stdClass()])]
    public function exceptionIsThrownIfNonArrayValueIsSet(mixed $value): void
    {
        $this->container->method('get')->willReturn(['geolocation_resolvers' => $value]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageIs(
            'geolocation_resolvers config is invalid. Expected an array of GeolocationStrategy values',
        );

        ($this->factory)($this->container);
    }

    #[Test]
    public function strategiesAreConvertedToResolvers(): void
    {
        $geoliteResolver = $this->createMock(LocationResolverInterface::class);
        $geoliteResolver
            ->expects($this->once())
            ->method('resolveRequestLocation')
            ->willThrowException(
                LocationResolutionException::anonymous(),
            );

        $cloudflareResolver = $this->createMock(LocationResolverInterface::class);
        $cloudflareResolver
            ->expects($this->once())
            ->method('resolveRequestLocation')
            ->willThrowException(
                LocationResolutionException::anonymous(),
            );

        $emptyResolver = $this->createMock(LocationResolverInterface::class);
        $emptyResolver->expects($this->once())->method('resolveRequestLocation')->willReturn(new Location());

        $this->container
            ->method('get')
            ->willReturnMap([
                [
                    'config',
                    ['geolocation_resolvers' => [
                        GeolocationStrategy::GEOLITE2,
                        GeolocationStrategy::CLOUDFLARE,
                        GeolocationStrategy::GEOLITE2,
                    ]],
                ],
                [GeoLite2LocationResolver::class, $geoliteResolver],
                [CloudflareLocationResolver::class, $cloudflareResolver],
                [EmptyLocationResolver::class, $emptyResolver],
            ]);

        $chainResolver = ($this->factory)($this->container);
        $chainResolver->resolveRequestLocation(dummyRequest());
    }
}
