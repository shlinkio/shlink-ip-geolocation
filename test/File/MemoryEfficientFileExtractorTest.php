<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\IpGeolocation\File;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\IpGeolocation\Exception\ExtractException;
use Shlinkio\Shlink\IpGeolocation\File\MemoryEfficientFileExtractor;

use function sprintf;

class MemoryEfficientFileExtractorTest extends TestCase
{
    private const string COMPRESSED_FILE = __DIR__ . '/../../test-resources/compressed.tar.gz';
    private const string FILE_TO_EXTRACT = 'internal-file.txt';

    private MemoryEfficientFileExtractor $extractor;

    protected function setUp(): void
    {
        $this->extractor = new MemoryEfficientFileExtractor();
    }

    #[Test]
    public function exceptionIsThrownIfCompressedFileCannotBeOpened(): void
    {
        $this->expectException(ExtractException::class);
        $this->expectExceptionMessage('It was not possible to open file "__invalid__"');

        $this->extractor->extractFile('__invalid__', '');
    }

    #[Test]
    public function exceptionIsThrownIfDestinationDirDoesNotExist(): void
    {
        $this->expectException(ExtractException::class);
        $this->expectExceptionMessage('It was not possible to open destination dir "__invalid__"');

        $this->extractor->extractFile(self::COMPRESSED_FILE, self::FILE_TO_EXTRACT, '__invalid__');
    }

    #[Test]
    public function exceptionIsThrownIfFileToExtractIsNotFoundInsideCompressedFile(): void
    {
        $this->expectException(ExtractException::class);
        $this->expectExceptionMessage(
            sprintf('File "__invalid__" not found inside compressed file "%s"', self::COMPRESSED_FILE),
        );

        $this->extractor->extractFile(self::COMPRESSED_FILE, '__invalid__');
    }

    #[Test]
    public function pathToFileIsReturnedIfDecompressionSucceeds(): void
    {
        $destDir = __DIR__ . '/../../test-resources';

        $result = $this->extractor->extractFile(self::COMPRESSED_FILE, self::FILE_TO_EXTRACT, $destDir);

        self::assertEquals($destDir . '/' . self::FILE_TO_EXTRACT, $result);
    }
}
