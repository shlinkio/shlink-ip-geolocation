<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Geolocation\Resolver;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Geolocation\Model\Location;
use Shlinkio\Shlink\Geolocation\Resolver\EmptyLocationResolver;

use function array_map;
use function range;
use function ShlinkioTest\Shlink\Geolocation\dummyRequestWithIp;

class EmptyLocationResolverTest extends TestCase
{
    private EmptyLocationResolver $resolver;

    public function setUp(): void
    {
        $this->resolver = new EmptyLocationResolver();
    }

    #[Test, DataProvider('provideEmptyResponses')]
    public function alwaysReturnsAnEmptyLocation(string $ipAddress): void
    {
        self::assertEquals(Location::empty(), $this->resolver->resolveRequestLocation(dummyRequestWithIp($ipAddress)));
    }

    public static function provideEmptyResponses(): array
    {
        return array_map(static fn () => ['foobar'], range(0, 5));
    }
}
