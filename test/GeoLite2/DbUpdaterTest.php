<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\IpGeolocation\GeoLite2;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Shlinkio\Shlink\IpGeolocation\Exception\MissingLicenseException;
use Shlinkio\Shlink\IpGeolocation\Exception\RuntimeException;
use Shlinkio\Shlink\IpGeolocation\GeoLite2\DbUpdater;
use Shlinkio\Shlink\IpGeolocation\GeoLite2\GeoLite2Options;
use Symfony\Component\Filesystem\Exception as FilesystemException;
use Symfony\Component\Filesystem\Filesystem;
use Throwable;

class DbUpdaterTest extends TestCase
{
    private MockObject $httpClient;
    private MockObject $filesystem;
    private ResponseInterface $response;

    public function setUp(): void
    {
        $this->httpClient = $this->createMock(ClientInterface::class);
        $this->filesystem = $this->createMock(Filesystem::class);
        $this->response = new Response();
    }

    /** @test */
    public function anExceptionIsThrownIfFreshDbCannotBeDownloaded(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->withAnyParameters()
            ->willThrowException(new ClientException('', new Request('GET', ''), $this->response));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage(
            'An error occurred while trying to download a fresh copy of the GeoLite2 database',
        );

        $this->dbUpdater()->downloadFreshCopy();
    }

    /** @test */
    public function anExceptionIsThrownIfFreshDbCannotBeExtracted(): void
    {
        $this->setUpHttpClient();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage(
            'An error occurred while trying to extract the GeoLite2 database from __invalid__/GeoLite2-City.tar.gz',
        );

        $this->dbUpdater('__invalid__')->downloadFreshCopy();
    }

    /**
     * @test
     * @dataProvider provideFilesystemExceptions
     */
    public function anExceptionIsThrownIfFreshDbCannotBeCopiedToDestination(callable $prepareFs): void
    {
        $this->setUpHttpClient();
        $prepareFs($this->filesystem);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('An error occurred while trying to copy GeoLite2 db file to db_location folder');

        $this->dbUpdater()->downloadFreshCopy();
    }

    public function provideFilesystemExceptions(): iterable
    {
        $onCopy = function (MockObject $fs, Throwable $e): void {
            $fs->expects($this->once())->method('copy')->withAnyParameters()->willThrowException($e);
            $fs->expects($this->never())->method('chmod');
        };
        $onChmod = function (MockObject $fs, Throwable $e): void {
            $fs->expects($this->once())->method('copy')->withAnyParameters();
            $fs->expects($this->once())->method('chmod')->withAnyParameters()->willThrowException($e);
        };

        yield 'file not found on copy' => [fn ($fs) => $onCopy($fs, new FilesystemException\FileNotFoundException())];
        yield 'IO error on copy' => [fn ($fs) => $onCopy($fs, new FilesystemException\IOException(''))];
        yield 'file not found on chmod' => [
            fn ($fs) => $onChmod($fs, new FilesystemException\FileNotFoundException()),
        ];
        yield 'IO error on chmod' => [fn ($fs) => $onChmod($fs, new FilesystemException\IOException(''))];
    }

    /** @test */
    public function noExceptionsAreThrownIfEverythingWorksFine(): void
    {
        $this->setUpHttpClient();
        $this->filesystem->expects($this->once())->method('copy')->withAnyParameters();
        $this->filesystem->expects($this->once())->method('chmod')->withAnyParameters();
        $this->filesystem->expects($this->once())->method('remove')->withAnyParameters();

        $this->dbUpdater()->downloadFreshCopy();
    }

    /**
     * @test
     * @dataProvider provideExists
     */
    public function databaseFileExistsChecksIfTheFilesExistsInTheFilesystem(bool $expected): void
    {
        $this->filesystem
            ->expects($this->once())
            ->method('exists')
            ->with($this->equalTo('db_location'))
            ->willReturn($expected);

        $result = $this->dbUpdater()->databaseFileExists();

        self::assertEquals($expected, $result);
    }

    public function provideExists(): iterable
    {
        return [[true], [false]];
    }

    /**
     * @test
     * @dataProvider provideInvalidLicenses
     */
    public function anExceptionIsThrownIfNoLicenseKeyIsProvided(?string $license): void
    {
        $this->expectException(MissingLicenseException::class);
        $this->expectExceptionMessage('Impossible to download GeoLite2 db file. A license key was not provided.');

        $this->dbUpdater(null, $license)->downloadFreshCopy();
    }

    public function provideInvalidLicenses(): iterable
    {
        yield 'null license' => [null];
        yield 'empty license' => [''];
    }

    private function dbUpdater(?string $tempDir = null, ?string $licenseKey = 'foobar'): DbUpdater
    {
        $options = new GeoLite2Options(
            licenseKey: $licenseKey,
            dbLocation: 'db_location',
            tempDir: $tempDir ?? __DIR__ . '/../../test-resources',
        );
        return new DbUpdater($this->httpClient, $this->filesystem, $options);
    }

    private function setUpHttpClient(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->withAnyParameters()
            ->willReturn($this->response);
    }
}
