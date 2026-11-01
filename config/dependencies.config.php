<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Geolocation;

use GuzzleHttp\Client as GuzzleClient;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Psr\Container\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;

return [
    'dependencies' => [
        'factories' => [
            Filesystem::class => InvokableFactory::class,
            File\MemoryEfficientFileExtractor::class => InvokableFactory::class,

            Resolver\GeoLite2LocationResolver::class => ConfigAbstractFactory::class,
            Resolver\EmptyLocationResolver::class => InvokableFactory::class,
            Resolver\ChainLocationResolver::class => ConfigAbstractFactory::class,

            GeoLite2\GeoLite2Options::class => static function (ContainerInterface $c): GeoLite2\GeoLite2Options {
                /** @var array{license_key?: string, db_location?: string, temp_dir?: string} $config */
                $config = $c->get('config.geolite2');
                return new GeoLite2\GeoLite2Options(
                    licenseKey: $config['license_key'] ?? null,
                    dbLocation: $config['db_location'] ?? '',
                    tempDir: $config['temp_dir'] ?? '',
                );
            },
            GeoLite2\DbUpdater::class => ConfigAbstractFactory::class,
            GeoLite2\GeoLite2ReaderFactory::class => ConfigAbstractFactory::class,
        ],
        'aliases' => [
            Resolver\LocationResolverInterface::class => Resolver\ChainLocationResolver::class,
        ],
    ],

    ConfigAbstractFactory::class => [
        Resolver\GeoLite2LocationResolver::class => [GeoLite2\GeoLite2ReaderFactory::class],
        Resolver\ChainLocationResolver::class => [
            Resolver\GeoLite2LocationResolver::class,
            Resolver\EmptyLocationResolver::class,
        ],

        GeoLite2\DbUpdater::class => [
            GuzzleClient::class,
            Filesystem::class,
            File\MemoryEfficientFileExtractor::class,
            GeoLite2\GeoLite2Options::class,
        ],
        GeoLite2\GeoLite2ReaderFactory::class => [GeoLite2\GeoLite2Options::class],
    ],
];
