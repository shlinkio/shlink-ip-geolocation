<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\IpGeolocation\Exception;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Shlinkio\Shlink\IpGeolocation\Exception\DbUpdateException;
use Throwable;

use function sprintf;

class DbUpdateExceptionTest extends TestCase
{
    private Throwable $prev;
    private string $theFile = '/the/file';

    protected function setUp(): void
    {
        $this->prev = new RuntimeException('Error');
    }

    #[Test]
    public function forFailedDownloadReturnsExpectedException(): void
    {
        $e = DbUpdateException::forFailedDownload($this->prev);

        self::assertEquals(
            'An error occurred while trying to download a fresh copy of the GeoLite2 database',
            $e->getMessage(),
        );
        self::assertEquals($this->prev, $e->getPrevious());
        self::assertEquals(0, $e->getCode());
    }

    #[Test]
    public function forFailedExtractionReturnsExpectedException(): void
    {
        $e = DbUpdateException::forFailedExtraction($this->theFile, $this->prev);

        self::assertEquals(
            sprintf('An error occurred while trying to extract the GeoLite2 database from %s', $this->theFile),
            $e->getMessage(),
        );
        self::assertEquals($this->prev, $e->getPrevious());
        self::assertEquals(0, $e->getCode());
    }

    #[Test]
    public function forFailedCopyToDestinationReturnsExpectedException(): void
    {
        $e = DbUpdateException::forFailedCopyToDestination($this->theFile, $this->prev);

        self::assertEquals(
            sprintf('An error occurred while trying to copy GeoLite2 db file to %s folder', $this->theFile),
            $e->getMessage(),
        );
        self::assertEquals($this->prev, $e->getPrevious());
        self::assertEquals(0, $e->getCode());
    }
}
