<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Geolocation;

use Psr\Http\Message\ServerRequestInterface;

use function is_string;
use function preg_match;
use function strtoupper;

const IP_ADDRESS_REQUEST_ATTRIBUTE = 'remote_address';

function ipAddressFromRequest(ServerRequestInterface $request): string|null
{
    $ipAddress = $request->getAttribute(IP_ADDRESS_REQUEST_ATTRIBUTE);
    return is_string($ipAddress) ? $ipAddress : null;
}

/**
 * Builds a full ISO 3166-2 subdivision code (e.g. US-CA) from a country code (US) and the subdivision part of the code
 * (CA), which is how both GeoLite2 and Cloudflare provide it.
 * An empty string is returned if any of them is missing or invalid, as a subdivision code is ambiguous without its
 * country.
 */
function isoSubdivisionCode(string $countryCode, string $subdivisionCode): string
{
    $countryCode = strtoupper($countryCode);
    $subdivisionCode = strtoupper($subdivisionCode);

    return preg_match('/^[A-Z]{2}$/', $countryCode) === 1 && preg_match('/^[A-Z0-9]{1,3}$/', $subdivisionCode) === 1
        ? $countryCode . '-' . $subdivisionCode
        : '';
}
