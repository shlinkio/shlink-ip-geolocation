<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Geolocation;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;

use function Shlinkio\Shlink\Geolocation\ipAddressFromRequest;
use function Shlinkio\Shlink\Geolocation\isoSubdivisionCode;

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
    #[TestWith(['US', 'CA', 'US-CA'])]
    #[TestWith(['de', 'by', 'DE-BY'])]
    #[TestWith(['GB', 'ENG', 'GB-ENG'])]
    #[TestWith(['FR', '75', 'FR-75'])]
    #[TestWith(['', 'CA', ''])]
    #[TestWith(['US', '', ''])]
    #[TestWith(['', '', ''])]
    #[TestWith(['USA', 'CA', ''])]
    #[TestWith(['US', 'CALI', ''])]
    #[TestWith(['US', 'US-CA', ''])]
    public function isoSubdivisionCodeIsBuiltOnlyWhenBothPartsAreValid(
        string $countryCode,
        string $subdivisionCode,
        string $expectedResult,
    ): void {
        self::assertEquals($expectedResult, isoSubdivisionCode($countryCode, $subdivisionCode));
    }
}
