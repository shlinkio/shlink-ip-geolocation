<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\IpGeolocation\Resolver;

use Psr\Http\Message\ServerRequestInterface;
use Shlinkio\Shlink\IpGeolocation\Exception\LocationResolutionException;
use Shlinkio\Shlink\IpGeolocation\Model;

interface LocationResolverInterface
{
    /**
     * @throws LocationResolutionException
     */
    public function resolveRequestLocation(ServerRequestInterface $request): Model\Location;
}
