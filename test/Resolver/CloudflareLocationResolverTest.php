<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Geolocation\Resolver;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Shlinkio\Shlink\Geolocation\Exception\LocationResolutionException;
use Shlinkio\Shlink\Geolocation\Resolver\CloudflareLocationResolver;

use function ShlinkioTest\Shlink\Geolocation\dummyRequest;

class CloudflareLocationResolverTest extends TestCase
{
    private CloudflareLocationResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new CloudflareLocationResolver();
    }

    #[Test, DataProvider('provideInvalidRequest')]
    public function exceptionIsThrowsForInvalidOrMissingCountryCode(ServerRequestInterface $request): void
    {
        $this->expectException(LocationResolutionException::class);
        $this->expectExceptionMessageIs('Missing or invalid country code in cf-ipcountry header');

        $this->resolver->resolveRequestLocation($request);
    }

    public static function provideInvalidRequest(): iterable
    {
        yield 'missing header' => [dummyRequest()];
        yield 'invalid country code' => [dummyRequest()->withHeader('cf-ipcountry', 'invalid')];
    }

    #[Test]
    public function allValuesAreReadFromHeaders(): void
    {
        $request = dummyRequest()
            ->withHeader('cf-ipcountry', 'US')
            ->withHeader('cf-region', 'region')
            ->withHeader('cf-region-code', 'CA')
            ->withHeader('cf-ipcity', 'city')
            ->withHeader('cf-iplatitude', '10')
            ->withHeader('cf-iplongitude', '20')
            ->withHeader('cf-timezone', 'America/Los_Angeles');

        $location = $this->resolver->resolveRequestLocation($request);

        self::assertEquals('region', $location->regionName);
        self::assertEquals('US-CA', $location->regionCode);
        self::assertEquals('city', $location->city);
        self::assertEquals('America/Los_Angeles', $location->timeZone);
        self::assertEquals(10.0, $location->latitude);
        self::assertEquals(20.0, $location->longitude);
    }

    #[Test]
    #[TestWith(['XX', '', ''])]
    #[TestWith(['T1', '', ''])]
    #[TestWith(['US', 'US', 'United States'])]
    #[TestWith(['ES', 'ES', 'Spain'])]
    #[TestWith(['TH', 'TH', 'Thailand'])]
    public function expectedCountryCodeAndNameAreResolved(
        string $countryCode,
        string $expectedCode,
        string $expectedName,
    ): void {
        $request = dummyRequest()->withHeader('cf-ipcountry', $countryCode);

        $location = $this->resolver->resolveRequestLocation($request);

        self::assertEquals($expectedCode, $location->countryCode);
        self::assertEquals($expectedName, $location->countryName);
    }

    #[Test]
    #[TestWith(['US', 'TX', 'US-TX'])]
    #[TestWith(['US', '', ''])]
    #[TestWith(['XX', 'CA', ''])]
    #[TestWith(['T1', 'CA', ''])]
    public function regionCodeIsResolvedOnlyWhenCountryIsKnown(
        string $countryCode,
        string $regionCode,
        string $expectedRegionCode,
    ): void {
        $request = dummyRequest()->withHeader('cf-ipcountry', $countryCode)->withHeader('cf-region-code', $regionCode);

        $location = $this->resolver->resolveRequestLocation($request);

        self::assertEquals($expectedRegionCode, $location->regionCode);
    }
}
