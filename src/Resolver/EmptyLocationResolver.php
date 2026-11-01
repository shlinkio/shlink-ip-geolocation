<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\IpGeolocation\Resolver;

use Psr\Http\Message\ServerRequestInterface;
use Shlinkio\Shlink\IpGeolocation\Model;

class EmptyLocationResolver implements LocationResolverInterface
{
    /** @inheritDoc */
    public function resolveRequestLocation(ServerRequestInterface $request): Model\Location
    {
        return Model\Location::empty();
    }
}
