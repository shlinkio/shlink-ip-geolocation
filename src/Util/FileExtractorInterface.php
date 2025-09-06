<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\IpGeolocation\Util;

use Shlinkio\Shlink\IpGeolocation\Exception\ExtractException;

interface FileExtractorInterface
{
    /**
     * Extract a file from compressed file, and return its path.
     *
     * @param string|null $destinationDir Directory where the uncompressed file should be located.
     *                                    Defaults to the system temp directory
     * @throws ExtractException
     */
    public function extractFile(
        string $compressedFilePath,
        string $fileToExtract,
        string|null $destinationDir = null,
    ): string;
}
