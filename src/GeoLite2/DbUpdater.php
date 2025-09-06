<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\IpGeolocation\GeoLite2;

use Fig\Http\Message\RequestMethodInterface as RequestMethod;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\RequestOptions;
use Shlinkio\Shlink\IpGeolocation\Exception\DbUpdateException;
use Shlinkio\Shlink\IpGeolocation\Exception\MissingLicenseException;
use Shlinkio\Shlink\IpGeolocation\Util\FileExtractorInterface;
use Symfony\Component\Filesystem\Exception as FilesystemException;
use Symfony\Component\Filesystem\Filesystem;
use Throwable;

use function sprintf;

readonly class DbUpdater implements DbUpdaterInterface
{
    private const DB_COMPRESSED_FILE = 'GeoLite2-City.tar.gz';
    private const DB_DECOMPRESSED_FILE = 'GeoLite2-City.mmdb';

    public function __construct(
        private ClientInterface $httpClient,
        private Filesystem $filesystem,
        private FileExtractorInterface $fileExtractor,
        private GeoLite2Options $options,
    ) {
    }

    /**
     * @param (callable(int $total, int $downloaded): void) | null $handleProgress
     * @throws DbUpdateException
     * @throws MissingLicenseException
     */
    public function downloadFreshCopy(callable|null $handleProgress = null): void
    {
        if (! $this->options->hasLicenseKey()) {
            throw MissingLicenseException::forMissingLicense();
        }

        $tempDir = $this->options->tempDir;
        $compressedFile = sprintf('%s/%s', $tempDir, self::DB_COMPRESSED_FILE);

        $this->downloadDbFile($compressedFile, $handleProgress);
        $tempFullPath = $this->extractDbFile($compressedFile, $tempDir);
        $this->copyNewDbFile($tempFullPath);
        $this->deleteTempFiles([$compressedFile, $tempFullPath]);
    }

    /**
     * @param (callable(int $total, int $downloaded): void) | null $handleProgress
     */
    private function downloadDbFile(string $dest, callable|null $handleProgress = null): void
    {
        try {
            $this->httpClient->request(RequestMethod::METHOD_GET, $this->options->downloadFrom, [
                RequestOptions::SINK => $dest,
                RequestOptions::PROGRESS => $handleProgress,
                RequestOptions::CONNECT_TIMEOUT => $this->options->connectionTimeout,
            ]);
        } catch (Throwable $e) {
            throw DbUpdateException::forFailedDownload($e);
        }
    }

    /**
     * Decompress provided GZ file into a temp location, and return the path to that location
     */
    private function extractDbFile(string $compressedFile, string $tempDir): string
    {
        try {
            return $this->fileExtractor->extractFile($compressedFile, self::DB_DECOMPRESSED_FILE, $tempDir);
        } catch (Throwable $e) {
            throw DbUpdateException::forFailedExtraction($compressedFile, $e);
        }
    }

    private function copyNewDbFile(string $from): void
    {
        $destination = $this->options->dbLocation;

        try {
            $this->filesystem->copy($from, $destination, overwriteNewerFiles: true);
            $this->filesystem->chmod([$destination], 0666);
        } catch (FilesystemException\FileNotFoundException | FilesystemException\IOException $e) {
            throw DbUpdateException::forFailedCopyToDestination($destination, $e);
        }
    }

    private function deleteTempFiles(array $files): void
    {
        try {
            $this->filesystem->remove($files);
        } catch (FilesystemException\IOException) {
            // Ignore any error produced when trying to delete temp files
        }
    }

    public function databaseFileExists(): bool
    {
        return $this->filesystem->exists($this->options->dbLocation);
    }
}
