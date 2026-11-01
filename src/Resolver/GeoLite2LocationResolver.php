<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\IpGeolocation\Resolver;

use Closure;
use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use GeoIp2\Model\City;
use MaxMind\Db\Reader\InvalidDatabaseException;
use Psr\Http\Message\ServerRequestInterface;
use Shlinkio\Shlink\IpGeolocation\Exception\LocationResolutionException;
use Shlinkio\Shlink\IpGeolocation\Model;

use function Shlinkio\Shlink\IpGeolocation\ipAddressFromRequest;

class GeoLite2LocationResolver implements LocationResolverInterface
{
    /** @var Closure(): Reader */
    private readonly Closure $geoLiteDbReaderFactory;

    /**
     * @param callable(): Reader $geoLiteDbReaderFactory
     */
    public function __construct(callable $geoLiteDbReaderFactory)
    {
        $this->geoLiteDbReaderFactory = $geoLiteDbReaderFactory(...);
    }

    /** @inheritDoc */
    public function resolveRequestLocation(ServerRequestInterface $request): Model\Location
    {
        $ipAddress = ipAddressFromRequest($request);
        if ($ipAddress === null) {
            throw new LocationResolutionException('Provided request does not contain an IP address');
        }

        try {
            $city = ($this->geoLiteDbReaderFactory)()->city($ipAddress);
            return $this->mapFields($city);
        } catch (AddressNotFoundException $e) {
            throw LocationResolutionException::fromIpAddress($ipAddress, $e);
        } catch (InvalidDatabaseException $e) {
            throw new LocationResolutionException('Provided GeoLite2 db file is invalid', previous: $e);
        }
    }

    private function mapFields(City $city): Model\Location
    {
        return new Model\Location(
            $city->country->isoCode ?? '',
            $city->country->name ?? '',
            $city->subdivisions[0]->name ?? '',
            $city->city->name ?? '',
            $city->location->latitude ?? 0.0,
            $city->location->longitude ?? 0.0,
            $city->location->timeZone ?? '',
        );
    }
}
