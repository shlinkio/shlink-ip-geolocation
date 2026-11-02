<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Geolocation;

use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ServerRequestInterface;

use const Shlinkio\Shlink\Geolocation\IP_ADDRESS_REQUEST_ATTRIBUTE;

function dummyRequest(): ServerRequestInterface
{
    return new ServerRequest('GET', '/');
}

function requestWithIp(string $ipAddress): ServerRequestInterface
{
    return dummyRequest()->withAttribute(IP_ADDRESS_REQUEST_ATTRIBUTE, $ipAddress);
}
