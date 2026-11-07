<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Geolocation;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;

use function Shlinkio\Shlink\Geolocation\ipAddressFromRequest;
use function Shlinkio\Shlink\Geolocation\requestWithIpAddress;

use const Shlinkio\Shlink\Geolocation\IP_ADDRESS_REQUEST_ATTRIBUTE;

class FunctionsTest extends TestCase
{
    #[Test, DataProvider('provideRequests')]
    public function ipAddressCanBeExtractedFromRequest(
        ServerRequestInterface $request,
        string|null $expectedIpAddress,
    ): void {
        self::assertEquals($expectedIpAddress, ipAddressFromRequest($request));
    }

    public static function provideRequests(): iterable
    {
        $baseRequest = dummyRequest();

        yield 'no attribute' => [$baseRequest, null];
        yield 'no string attribute' => [
            $baseRequest->withAttribute(IP_ADDRESS_REQUEST_ATTRIBUTE, new stdClass()),
            null,
        ];
        yield 'string attribute' => [$baseRequest->withAttribute(IP_ADDRESS_REQUEST_ATTRIBUTE, '1.2.3.4'), '1.2.3.4'];
    }

    #[Test]
    public function ipAddressCanBeSetInRequest(): void
    {
        $baseRequest = dummyRequest();
        $request = requestWithIpAddress($baseRequest, '5.6.7.8');

        self::assertNotSame($request, $baseRequest);
        self::assertEquals('5.6.7.8', $request->getAttribute(IP_ADDRESS_REQUEST_ATTRIBUTE));
    }
}
