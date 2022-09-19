<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\IpGeolocation;

use GeoIp2\Database\Reader;
use GuzzleHttp\Client as GuzzleClient;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Shlinkio\Shlink\Config\Factory\ValinorConfigFactory;
use Symfony\Component\Filesystem\Filesystem;

return [

    'dependencies' => [
        'factories' => [
            Resolver\GeoLite2LocationResolver::class => ConfigAbstractFactory::class,
            Resolver\EmptyIpLocationResolver::class => InvokableFactory::class,
            Resolver\ChainIpLocationResolver::class => ConfigAbstractFactory::class,

            GeoLite2\GeoLite2Options::class => [ValinorConfigFactory::class, 'config.geolite2'],
            GeoLite2\DbUpdater::class => ConfigAbstractFactory::class,
        ],
        'aliases' => [
            Resolver\IpLocationResolverInterface::class => Resolver\ChainIpLocationResolver::class,
        ],
    ],

    ConfigAbstractFactory::class => [
        Resolver\GeoLite2LocationResolver::class => [Reader::class],
        Resolver\ChainIpLocationResolver::class => [
            Resolver\GeoLite2LocationResolver::class,
            Resolver\EmptyIpLocationResolver::class,
        ],

        GeoLite2\DbUpdater::class => [
            GuzzleClient::class,
            Filesystem::class,
            GeoLite2\GeoLite2Options::class,
        ],
    ],

];
