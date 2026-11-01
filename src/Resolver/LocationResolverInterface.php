<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Geolocation\Resolver;

use Psr\Http\Message\ServerRequestInterface;
use Shlinkio\Shlink\Geolocation\Exception\LocationResolutionException;
use Shlinkio\Shlink\Geolocation\Model;

interface LocationResolverInterface
{
    /**
     * @throws LocationResolutionException
     */
    public function resolveRequestLocation(ServerRequestInterface $request): Model\Location;
}
