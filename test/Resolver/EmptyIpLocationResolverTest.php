<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\IpGeolocation\Resolver;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\IpGeolocation\Model\Location;
use Shlinkio\Shlink\IpGeolocation\Resolver\EmptyIpLocationResolver;

use function array_map;
use function range;

class EmptyIpLocationResolverTest extends TestCase
{
    private EmptyIpLocationResolver $resolver;

    public function setUp(): void
    {
        $this->resolver = new EmptyIpLocationResolver();
    }

    #[Test, DataProvider('provideEmptyResponses')]
    public function alwaysReturnsAnEmptyLocation(string $ipAddress): void
    {
        self::assertEquals(Location::empty(), $this->resolver->resolveIpLocation($ipAddress));
    }

    public static function provideEmptyResponses(): array
    {
        return array_map(fn () => ['foobar'], range(0, 5));
    }
}
