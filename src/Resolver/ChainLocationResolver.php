<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Geolocation\Resolver;

use Psr\Http\Message\ServerRequestInterface;
use Shlinkio\Shlink\Geolocation\Exception\LocationResolutionException;
use Shlinkio\Shlink\Geolocation\Model;

class ChainLocationResolver implements LocationResolverInterface
{
    /** @var LocationResolverInterface[] */
    private readonly array $resolvers;

    public function __construct(LocationResolverInterface ...$resolvers)
    {
        $this->resolvers = $resolvers;
    }

    /** @inheritDoc */
    public function resolveRequestLocation(ServerRequestInterface $request): Model\Location
    {
        $error = null;

        foreach ($this->resolvers as $resolver) {
            try {
                return $resolver->resolveRequestLocation($request);
            } catch (LocationResolutionException $e) {
                $error = $e;
            }
        }

        // If this instruction is reached, it means no resolver was capable of resolving the location
        throw LocationResolutionException::anonymous($error);
    }
}
