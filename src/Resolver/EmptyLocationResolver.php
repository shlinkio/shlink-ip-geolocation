<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Geolocation\Resolver;

use Override;
use Psr\Http\Message\ServerRequestInterface;
use Shlinkio\Shlink\Geolocation\Model;

class EmptyLocationResolver implements LocationResolverInterface
{
    /** @inheritDoc */
    #[Override]
    public function resolveRequestLocation(ServerRequestInterface $request): Model\Location
    {
        return Model\Location::empty();
    }
}
