<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Geolocation\Resolver;

use Locale;
use Psr\Http\Message\ServerRequestInterface;
use Shlinkio\Shlink\Geolocation\Exception\LocationResolutionException;
use Shlinkio\Shlink\Geolocation\Model;

use function in_array;
use function preg_match;
use function Shlinkio\Shlink\Geolocation\isoSubdivisionCode;

class CloudflareLocationResolver implements LocationResolverInterface
{
    /**
     * XX: Country is unknown
     * T1: Visitor is using the Tor network
     * @see https://developers.cloudflare.com/fundamentals/reference/http-headers/#cf-ipcountry
     */
    private const array SPECIAL_COUNTRY_CODES = ['XX', 'T1'];

    /** @inheritDoc */
    public function resolveRequestLocation(ServerRequestInterface $request): Model\Location
    {
        // Cloudflare headers are documented here:
        // https://developers.cloudflare.com/rules/transform/managed-transforms/reference/#add-visitor-location-headers

        $countryCode = $request->getHeaderLine('cf-ipcountry');
        $countryCodeIsSpecialValue = in_array($countryCode, self::SPECIAL_COUNTRY_CODES, strict: true);

        if (!$countryCodeIsSpecialValue && preg_match('/^[A-Z]{2}$/', $countryCode) !== 1) {
            throw new LocationResolutionException('Missing or invalid country code in cf-ipcountry header');
        }

        $countryCode = $countryCodeIsSpecialValue ? '' : $countryCode;
        $countryName = $countryCode === '' ? '' : Locale::getDisplayRegion('-' . $countryCode);

        return new Model\Location(
            countryCode: $countryCode,
            countryName: $countryName ?: '',
            regionName: $request->getHeaderLine('cf-region'),
            city: $request->getHeaderLine('cf-ipcity'),
            latitude: $request->getHeaderLine('cf-iplatitude'),
            longitude: $request->getHeaderLine('cf-iplongitude'),
            timeZone: $request->getHeaderLine('cf-timezone'),
            regionCode: isoSubdivisionCode($countryCode, $request->getHeaderLine('cf-region-code')),
        );
    }
}
