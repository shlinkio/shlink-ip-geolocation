<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\IpGeolocation\Exception;

use Throwable;

use function sprintf;

class LocationResolutionException extends RuntimeException
{
    public static function anonymous(Throwable|null $prev = null): self
    {
        return new self('Could not resolve request location', previous: $prev);
    }

    public static function fromIpAddress(string $ipAddress, Throwable|null $prev = null): self
    {
        return new self(sprintf('Could not resolve location for IP address %s', $ipAddress), previous: $prev);
    }
}
