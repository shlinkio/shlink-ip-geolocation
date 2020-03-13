<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\IpGeolocation\Exception;

use Throwable;

use function sprintf;

class DbUpdateException extends RuntimeException
{
    public static function forFailedDownload(Throwable $prev): self
    {
        return self::build('An error occurred while trying to download a fresh copy of the GeoLite2 database', $prev);
    }

    public static function forFailedExtraction(string $compressedFile, Throwable $prev): self
    {
        return self::build(
            sprintf('An error occurred while trying to extract the GeoLite2 database from %s', $compressedFile),
            $prev,
        );
    }

    public static function forFailedCopyToDestination(string $destination, Throwable $prev): self
    {
        return self::build(
            sprintf('An error occurred while trying to copy GeoLite2 db file to %s folder', $destination),
            $prev,
        );
    }

    private static function build(string $message, Throwable $prev): self
    {
        return new self($message, 0, $prev);
    }
}
