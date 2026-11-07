<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Geolocation;

use Psr\Http\Message\ServerRequestInterface;

use function is_string;

const IP_ADDRESS_REQUEST_ATTRIBUTE = 'remote_address';

function ipAddressFromRequest(ServerRequestInterface $request): string|null
{
    $ipAddress = $request->getAttribute(IP_ADDRESS_REQUEST_ATTRIBUTE);
    return is_string($ipAddress) ? $ipAddress : null;
}

function requestWithIpAddress(ServerRequestInterface $request, string $ipAddress): ServerRequestInterface
{
    return $request->withAttribute(IP_ADDRESS_REQUEST_ATTRIBUTE, $ipAddress);
}
