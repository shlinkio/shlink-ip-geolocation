<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\IpGeolocation\Resolver;

use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use GeoIp2\Model\City;
use GeoIp2\Record\Subdivision;
use MaxMind\Db\Reader\InvalidDatabaseException;
use Shlinkio\Shlink\IpGeolocation\Exception\WrongIpException;
use Shlinkio\Shlink\IpGeolocation\Model;

use function Functional\first;

class GeoLite2LocationResolver implements IpLocationResolverInterface
{
    public function __construct(private readonly Reader $geoLiteDbReader)
    {
    }

    /**
     * @throws WrongIpException
     */
    public function resolveIpLocation(string $ipAddress): Model\Location
    {
        try {
            $city = $this->geoLiteDbReader->city($ipAddress);
            return $this->mapFields($city);
        } catch (AddressNotFoundException $e) {
            throw WrongIpException::fromIpAddress($ipAddress, $e);
        } catch (InvalidDatabaseException $e) {
            throw new WrongIpException('Provided GeoLite2 db file is invalid', 0, $e);
        }
    }

    private function mapFields(City $city): Model\Location
    {
        /** @var Subdivision $region */
        $region = first($city->subdivisions);

        return new Model\Location(
            $city->country->isoCode ?? '',
            $city->country->name ?? '',
            $region->name ?? '',
            $city->city->name ?? '',
            (float) ($city->location->latitude ?? 0.0),
            (float) ($city->location->longitude ?? 0.0),
            $city->location->timeZone ?? '',
        );
    }
}
