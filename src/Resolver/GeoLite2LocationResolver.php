<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Geolocation\Resolver;

use Closure;
use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use GeoIp2\Model\City;
use MaxMind\Db\Reader\InvalidDatabaseException;
use Psr\Http\Message\ServerRequestInterface;
use Shlinkio\Shlink\Geolocation\Exception\LocationResolutionException;
use Shlinkio\Shlink\Geolocation\Model;

use function Shlinkio\Shlink\Geolocation\ipAddressFromRequest;

class GeoLite2LocationResolver implements LocationResolverInterface
{
    private const string LOCALHOST = '127.0.0.1';

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

        if ($ipAddress === self::LOCALHOST) {
            throw LocationResolutionException::fromIpAddress(self::LOCALHOST);
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
