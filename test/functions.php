<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Geolocation;

use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ServerRequestInterface;

use const Shlinkio\Shlink\Geolocation\IP_ADDRESS_REQUEST_ATTRIBUTE;

function requestWithIp(string $ipAddress): ServerRequestInterface
{
    return new ServerRequest('GET', '/')->withAttribute(IP_ADDRESS_REQUEST_ATTRIBUTE, $ipAddress);
}
