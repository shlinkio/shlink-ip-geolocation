<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\IpGeolocation;

use GuzzleHttp\Client as GuzzleClient;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Psr\Container\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;

return [

    'dependencies' => [
        'factories' => [
            Filesystem::class => InvokableFactory::class,

            Resolver\GeoLite2LocationResolver::class => ConfigAbstractFactory::class,
            Resolver\EmptyIpLocationResolver::class => InvokableFactory::class,
            Resolver\ChainIpLocationResolver::class => ConfigAbstractFactory::class,

            GeoLite2\GeoLite2Options::class => static function (ContainerInterface $c): GeoLite2\GeoLite2Options {
                /** @var array $config */
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
            Resolver\IpLocationResolverInterface::class => Resolver\ChainIpLocationResolver::class,
        ],
    ],

    ConfigAbstractFactory::class => [
        Resolver\GeoLite2LocationResolver::class => [GeoLite2\GeoLite2ReaderFactory::class],
        Resolver\ChainIpLocationResolver::class => [
            Resolver\GeoLite2LocationResolver::class,
            Resolver\EmptyIpLocationResolver::class,
        ],

        GeoLite2\DbUpdater::class => [
            GuzzleClient::class,
            Filesystem::class,
            GeoLite2\GeoLite2Options::class,
        ],
        GeoLite2\GeoLite2ReaderFactory::class => [GeoLite2\GeoLite2Options::class],
    ],

];
