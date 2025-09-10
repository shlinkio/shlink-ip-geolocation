<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\IpGeolocation\Exception;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\IpGeolocation\Exception\ExtractException;

class ExtractExceptionTest extends TestCase
{
    #[Test]
    public function forInvalidCompressedFile(): void
    {
        $e = ExtractException::forInvalidCompressedFile('invalid_compressed_file');
        self::assertEquals('It was not possible to open file "invalid_compressed_file"', $e->getMessage());
    }

    #[Test]
    public function forInvalidDestinationDir(): void
    {
        $e = ExtractException::forInvalidDestinationDir('invalid_dest_dir');
        self::assertEquals('It was not possible to open destination dir "invalid_dest_dir"', $e->getMessage());
    }

    #[Test]
    public function forFileToExtractNotFound(): void
    {
        $e = ExtractException::forFileToExtractNotFound('file_to_extract', 'compressed_file_path');
        self::assertEquals(
            'File "file_to_extract" not found inside compressed file "compressed_file_path"',
            $e->getMessage(),
        );
    }
}
