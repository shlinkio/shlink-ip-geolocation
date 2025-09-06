<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\IpGeolocation\Exception;

use function sprintf;

class ExtractException extends RuntimeException
{
    public static function forInvalidCompressedFile(string $compressedFilePath): self
    {
        return new self(sprintf('It was not possible to open file "%s"', $compressedFilePath));
    }

    public static function forInvalidDestinationDir(string $destinationDir): self
    {
        return new self(sprintf('It was not possible to open destination dir "%s"', $destinationDir));
    }

    public static function forFileToExtractNotFound(string $fileToExtract, string $compressedFilePath): self
    {
        return new self(
            sprintf('File "%s" not found inside compressed file "%s"', $fileToExtract, $compressedFilePath),
        );
    }
}
