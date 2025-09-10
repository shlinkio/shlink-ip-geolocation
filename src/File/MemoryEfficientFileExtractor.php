<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\IpGeolocation\File;

use Shlinkio\Shlink\IpGeolocation\Exception\ExtractException;

use function fclose;
use function feof;
use function fopen;
use function fread;
use function fseek;
use function fwrite;
use function gzclose;
use function gzopen;
use function gzread;
use function min;
use function octdec;
use function str_ends_with;
use function strlen;
use function substr;
use function sys_get_temp_dir;
use function trim;
use function unlink;

use const SEEK_CUR;

/**
 * A File extractor which takes memory efficiency into consideration, by processing files in batches.
 */
final readonly class MemoryEfficientFileExtractor implements FileExtractorInterface
{
    /**
     * @inheritDoc
     */
    public function extractFile(
        string $compressedFilePath,
        string $fileToExtract,
        string|null $destinationDir = null,
    ): string {
        $destinationDir ??= sys_get_temp_dir();
        $outputPath = $destinationDir . '/' . $fileToExtract;

        // Decompress temporary tar package
        $tempTarPath = $outputPath . '.tmp';
        $gz = @gzopen($compressedFilePath, 'rb');
        if (! $gz) {
            throw ExtractException::forInvalidCompressedFile($compressedFilePath);
        }

        $tar = @fopen($tempTarPath, 'wb');
        if (! $tar) {
            gzclose($gz);
            throw ExtractException::forInvalidDestinationDir($destinationDir);
        }
        while ($chunk = gzread($gz, length: 4096)) {
            fwrite($tar, $chunk);
        }
        gzclose($gz);
        fclose($tar);

        // Process tar file sequentially, looking for the db file
        /** @var resource $tar - We have already opened this file a few lines above, so it's safe to cast type here */
        $tar = fopen($tempTarPath, 'rb');
        while (! feof($tar)) {
            $header = fread($tar, 512);
            if (! $header || strlen($header) < 512) {
                break;
            }

            $filename = trim(substr($header, offset: 0, length: 100));
            if ($filename === '') {
                break;
            }

            $size = octdec(trim(substr($header, offset: 124, length: 12)));

            // Once we find the file, read it sequentially and return
            if (str_ends_with($filename, $fileToExtract) && $out = fopen($outputPath, 'wb')) {
                $remaining = $size;
                while ($remaining > 0) {
                    /** @var positive-int $readLen */
                    $readLen = min(4096, $remaining);
                    $data = fread($tar, $readLen);
                    if (! $data) {
                        break;
                    }
                    fwrite($out, $data);
                    $remaining -= strlen($data);
                }
                fclose($out);
                fclose($tar);
                unlink($tempTarPath);

                return $outputPath;
            }

            // Skip this file
            $skip = $size + (512 - ($size % 512)) % 512;
            fseek($tar, offset: (int) $skip, whence: SEEK_CUR);
        }

        fclose($tar);
        unlink($tempTarPath);

        throw ExtractException::forFileToExtractNotFound($fileToExtract, $compressedFilePath);
    }
}
