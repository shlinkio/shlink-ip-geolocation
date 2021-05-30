<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\IpGeolocation\GeoLite2;

use Fig\Http\Message\RequestMethodInterface as RequestMethod;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\RequestOptions;
use PharData;
use Shlinkio\Shlink\IpGeolocation\Exception\DbUpdateException;
use Symfony\Component\Filesystem\Exception as FilesystemException;
use Symfony\Component\Filesystem\Filesystem;
use Throwable;

use function sprintf;

class DbUpdater implements DbUpdaterInterface
{
    private const DB_COMPRESSED_FILE = 'GeoLite2-City.tar.gz';
    private const DB_DECOMPRESSED_FILE = 'GeoLite2-City.mmdb';

    private ClientInterface $httpClient;
    private Filesystem $filesystem;
    private GeoLite2Options $options;

    public function __construct(ClientInterface $httpClient, Filesystem $filesystem, GeoLite2Options $options)
    {
        $this->httpClient = $httpClient;
        $this->filesystem = $filesystem;
        $this->options = $options;
    }

    /**
     * @throws DbUpdateException
     */
    public function downloadFreshCopy(?callable $handleProgress = null): void
    {
        if (! $this->options->hasLicenseKey()) {
            return;
        }

        $tempDir = $this->options->getTempDir();
        $compressedFile = sprintf('%s/%s', $tempDir, self::DB_COMPRESSED_FILE);

        $this->downloadDbFile($compressedFile, $handleProgress);
        $tempFullPath = $this->extractDbFile($compressedFile, $tempDir);
        $this->copyNewDbFile($tempFullPath);
        $this->deleteTempFiles([$compressedFile, $tempFullPath]);
    }

    private function downloadDbFile(string $dest, ?callable $handleProgress = null): void
    {
        try {
            $this->httpClient->request(RequestMethod::METHOD_GET, $this->options->getDownloadFrom(), [
                RequestOptions::SINK => $dest,
                RequestOptions::PROGRESS => $handleProgress,
                RequestOptions::CONNECT_TIMEOUT => $this->options->getConnectionTimeout(),
            ]);
        } catch (Throwable $e) {
            throw DbUpdateException::forFailedDownload($e);
        }
    }

    private function extractDbFile(string $compressedFile, string $tempDir): string
    {
        try {
            $phar = new PharData($compressedFile);
            $internalPathToDb = sprintf('%s/%s', $phar->getBasename(), self::DB_DECOMPRESSED_FILE);
            $phar->extractTo($tempDir, $internalPathToDb, true);

            return sprintf('%s/%s', $tempDir, $internalPathToDb);
        } catch (Throwable $e) {
            throw DbUpdateException::forFailedExtraction($compressedFile, $e);
        }
    }

    private function copyNewDbFile(string $from): void
    {
        $destination = $this->options->getDbLocation();

        try {
            $this->filesystem->copy($from, $destination, true);
            $this->filesystem->chmod([$destination], 0666);
        } catch (FilesystemException\FileNotFoundException | FilesystemException\IOException $e) {
            throw DbUpdateException::forFailedCopyToDestination($destination, $e);
        }
    }

    private function deleteTempFiles(array $files): void
    {
        try {
            $this->filesystem->remove($files);
        } catch (FilesystemException\IOException $e) {
            // Ignore any error produced when trying to delete temp files
        }
    }

    public function databaseFileExists(): bool
    {
        return $this->filesystem->exists($this->options->getDbLocation());
    }
}
