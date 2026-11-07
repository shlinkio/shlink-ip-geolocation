<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Geolocation;

use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ServerRequestInterface;

use function Shlinkio\Shlink\Geolocation\requestWithIpAddress;

function dummyRequest(): ServerRequestInterface
{
    return new ServerRequest('GET', '/');
}

function dummyRequestWithIp(string $ipAddress): ServerRequestInterface
{
    return requestWithIpAddress(dummyRequest(), $ipAddress);
}
