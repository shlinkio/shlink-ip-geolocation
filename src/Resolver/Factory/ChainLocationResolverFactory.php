<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Geolocation\Resolver\Factory;

use Psr\Container\ContainerInterface;
use Shlinkio\Shlink\Geolocation\Exception\RuntimeException;
use Shlinkio\Shlink\Geolocation\Resolver\ChainLocationResolver;
use Shlinkio\Shlink\Geolocation\Resolver\EmptyLocationResolver;
use Shlinkio\Shlink\Geolocation\Resolver\GeolocationStrategy;

use function array_map;
use function array_unique;
use function is_array;

use const SORT_REGULAR;

class ChainLocationResolverFactory
{
    public function __invoke(ContainerInterface $container): ChainLocationResolver
    {
        /** @var array $config */
        $config = $container->get('config');
        $strategies = $config['geolocation_resolvers'] ?? [];
        if (!is_array($strategies)) {
            throw new RuntimeException(
                'geolocation_resolvers config is invalid. Expected an array of GeolocationStrategy values',
            );
        }

        $resolvers = array_map(
            static fn (GeolocationStrategy $strategy) => $container->get($strategy->toResolverClass()),
            array_unique($strategies, SORT_REGULAR),
        );
        $resolvers[] = $container->get(EmptyLocationResolver::class);

        // @mago-expect analysis:mixed-argument
        return new ChainLocationResolver(...$resolvers);
    }
}
