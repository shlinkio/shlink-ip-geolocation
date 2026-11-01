<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Geolocation\Resolver;

use Locale;
use Psr\Http\Message\ServerRequestInterface;
use Shlinkio\Shlink\Geolocation\Exception\LocationResolutionException;
use Shlinkio\Shlink\Geolocation\Model;

class CloudflareLocationResolver implements LocationResolverInterface
{
    /** @inheritDoc */
    public function resolveRequestLocation(ServerRequestInterface $request): Model\Location
    {
        // Cloudflare headers are documented here:
        // https://developers.cloudflare.com/rules/transform/managed-transforms/reference/#add-visitor-location-headers

        $countryCode = $request->getHeaderLine('cf-ipcountry');
        if (preg_match('/^[A-Z]{2}$/', $countryCode) !== 1) {
            throw new LocationResolutionException('Missing or invalid country code in cf-ipcountry header');
        }

        // Cleanup country code and resolve country name
        // See https://developers.cloudflare.com/fundamentals/reference/http-headers/#cf-ipcountry
        $countryCode = $countryCode === 'XX' || $countryCode === 'T1' ? '' : $countryCode;
        $countryName = $countryCode === '' ? '' : Locale::getDisplayRegion('-' . $countryCode);

        return new Model\Location(
            countryCode: $countryCode,
            countryName: $countryName ?: '',
            regionName: $request->getHeaderLine('cf-region'),
            city: $request->getHeaderLine('cf-ipcity'),
            latitude: $request->getHeaderLine('cf-iplatitude'),
            longitude: $request->getHeaderLine('cf-iplongitude'),
            timeZone: $request->getHeaderLine('cf-timezone'),
        );
    }
}
