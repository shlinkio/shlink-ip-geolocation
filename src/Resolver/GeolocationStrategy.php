<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Geolocation\Resolver;

enum GeolocationStrategy: string
{
    case GEOLITE2 = 'geolite2';
    case CLOUDFLARE = 'cloudflare';

    /**
     * @return class-string<LocationResolverInterface>
     */
    public function toResolverClass(): string
    {
        return match ($this) {
            self::GEOLITE2 => GeoLite2LocationResolver::class,
            self::CLOUDFLARE => CloudflareLocationResolver::class,
        };
    }
}
