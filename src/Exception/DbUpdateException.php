<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\IpGeolocation\Exception;

use Throwable;

use function sprintf;

class DbUpdateException extends RuntimeException
{
    public static function forFailedDownload(Throwable $prev): self
    {
        return new self(
            'An error occurred while trying to download a fresh copy of the GeoLite2 database',
            previous: $prev,
        );
    }

    public static function forFailedExtraction(string $compressedFile, Throwable $prev): self
    {
        return new self(
            sprintf('An error occurred while trying to extract the GeoLite2 database from %s', $compressedFile),
            previous: $prev,
        );
    }

    public static function forFailedCopyToDestination(string $destination, Throwable $prev): self
    {
        return new self(
            sprintf('An error occurred while trying to copy GeoLite2 db file to %s folder', $destination),
            previous: $prev,
        );
    }
}
