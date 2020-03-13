<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\IpGeolocation\Exception;

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

    /** @test */
    public function forFailedDownloadReturnsExpectedException(): void
    {
        $e = DbUpdateException::forFailedDownload($this->prev);

        $this->assertEquals(
            'An error occurred while trying to download a fresh copy of the GeoLite2 database',
            $e->getMessage(),
        );
        $this->assertEquals($this->prev, $e->getPrevious());
        $this->assertEquals(0, $e->getCode());
    }

    /** @test */
    public function forFailedExtractionReturnsExpectedException(): void
    {
        $e = DbUpdateException::forFailedExtraction($this->theFile, $this->prev);

        $this->assertEquals(
            sprintf('An error occurred while trying to extract the GeoLite2 database from %s', $this->theFile),
            $e->getMessage(),
        );
        $this->assertEquals($this->prev, $e->getPrevious());
        $this->assertEquals(0, $e->getCode());
    }

    /** @test */
    public function forFailedCopyToDestinationReturnsExpectedException(): void
    {
        $e = DbUpdateException::forFailedCopyToDestination($this->theFile, $this->prev);

        $this->assertEquals(
            sprintf('An error occurred while trying to copy GeoLite2 db file to %s folder', $this->theFile),
            $e->getMessage(),
        );
        $this->assertEquals($this->prev, $e->getPrevious());
        $this->assertEquals(0, $e->getCode());
    }
}
