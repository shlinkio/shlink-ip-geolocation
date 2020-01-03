<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\IpGeolocation\Exception;

use Throwable;

use function sprintf;

class WrongIpException extends RuntimeException
{
    public static function fromIpAddress(string $ipAddress, ?Throwable $prev = null): self
    {
        return new self(sprintf('Provided IP "%s" is invalid', $ipAddress), 0, $prev);
    }
}
