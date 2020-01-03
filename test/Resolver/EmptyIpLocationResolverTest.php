<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\IpGeolocation\Resolver;

use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Common\Util\StringUtilsTrait;
use Shlinkio\Shlink\IpGeolocation\Model\Location;
use Shlinkio\Shlink\IpGeolocation\Resolver\EmptyIpLocationResolver;

use function Functional\map;
use function range;

class EmptyIpLocationResolverTest extends TestCase
{
    use StringUtilsTrait;

    private EmptyIpLocationResolver $resolver;

    public function setUp(): void
    {
        $this->resolver = new EmptyIpLocationResolver();
    }

    /**
     * @test
     * @dataProvider provideEmptyResponses
     */
    public function alwaysReturnsAnEmptyLocation(string $ipAddress): void
    {
        $this->assertEquals(Location::emptyInstance(), $this->resolver->resolveIpLocation($ipAddress));
    }

    public function provideEmptyResponses(): array
    {
        return map(range(0, 5), fn () => [$this->generateRandomString(15)]);
    }
}
