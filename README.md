# Shlink IP Address Geolocation module

Shlink module with tools to geolocate an IP address using different strategies.

Most of the elements it provides require a [PSR-11](https://www.php-fig.org/psr/psr-11/) container, and it's easy to integrate on [mezzio](https://github.com/mezzio/mezzio) applications thanks to the `ConfigProvider` it includes.

[![Build Status](https://img.shields.io/travis/com/shlinkio/shlink-ip-geolocation.svg?style=flat-square)](https://travis-ci.com/shlinkio/shlink-ip-geolocation)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/shlinkio/shlink-ip-geolocation.svg?style=flat-square)](https://scrutinizer-ci.com/g/shlinkio/shlink-ip-geolocation/)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/shlinkio/shlink-ip-geolocation.svg?style=flat-square)](https://scrutinizer-ci.com/g/shlinkio/shlink-ip-geolocation/)
[![Latest Stable Version](https://img.shields.io/github/release/shlinkio/shlink-ip-geolocation.svg?style=flat-square)](https://packagist.org/packages/shlinkio/shlink-ip-geolocation)
[![License](https://img.shields.io/github/license/shlinkio/shlink-ip-geolocation.svg?style=flat-square)](https://github.com/shlinkio/shlink-ip-geolocation/blob/main/LICENSE)
[![Paypal donate](https://img.shields.io/badge/Donate-paypal-blue.svg?style=flat-square&logo=paypal&colorA=aaaaaa)](https://slnk.to/donate)

## Install

Install this library using composer:

    composer require shlinkio/shlink-ip-geolocation

> This library is also an expressive module which provides its own `ConfigProvider`. Add it to your configuration to get everything automatically set up.

## Resolving IP locations

This module provides a few different ways to resolve the location of an IP address, all of them implementing `Shlinkio\Shlink\IpGeolocation\Resolver\IpLocationResolverInterface`.

This interface basically exposes this method:

```php
/**
 * @throws WrongIpException
 */
public function resolveIpLocation(string $ipAddress): Model\Location;
```

These are the strategies provided implementing this interface:

* `GeoLite2LocationResolver`: It makes use of a [GeoLite2](https://dev.maxmind.com/geoip/geoip2/geolite2/) database to resolve IP address locations.
* `EmptyIpLocationResolver`: This one is a dummy resolver which always returns an empty location as if it was not capable of resolving the address location.
* `CainIpLocationResolver`: It wraps a list of IP resolvers and calls them sequentially until one of them is capable of resolving the address location.

The first one is the most reliable, but requires an up-to-date GeoLite2 database (which handling is explained in next section).

However, the chain resolver encapsulates the other two in the order they are listed here, and is the one recommended to use.

It is aliased to the service with name `Shlinkio\Shlink\IpGeolocation\Resolver\IpLocationResolverInterface`.

## Handling GeoLite2 DB file

In order to resolve IP address locations with the `GeoLite2LocationResolver`, an up-to-date local GeoLite2 database file.

To ease the management of this file, a `Shlinkio\Shlink\IpGeolocation\GeoLite2\DbUpdater` service is provided.

It exposes two public methods:

```php
public function databaseFileExists(): bool;

/**
 * @throws RuntimeException
 */
public function downloadFreshCopy(?callable $handleProgress = null): void;
```

* `databaseFileExists`: Just tells if the database file exists already (either in an outdated or up to date form).
* `downloadFreshCopy`: Forces a new copy of the GeoLite2 database to be downloaded from MaxMind repos. It allows to optionally handle the progress of the download.

## GeoLite2 config

To get both the resolver and the database updater to work, this configuration has to be defined:

```php
<?php
declare(strict_types=1);

return [

    'geolite2' => [
        // Mandatory options
        'db_location' => __DIR__ . '/../../data/GeoLite2-City.mmdb',
        'temp_dir' => sys_get_temp_dir(),

        // Optional options
        'license_key' => 'kjhk45hkj34fdwe5',
        'download_from' => 'https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-City&license_key={license_key}&suffix=tar.gz',
    ],

];
```

* `db_location`: Tells where in the local filesystem the database file is located (or should be located once the `DbUpdater` downloads it).
* `temp_dir`: A temporary location where new versions of the database are located while downloading. Once a download succeeds, the new DB will be moved to the location defined in previous config option.
* `license_key`: The GeoLite license key used to download the database. It has a default value, but it is recommended to [generate your own](https://support.maxmind.com/account-faq/account-related/how-do-i-generate-a-license-key/).
* `download_from`: The repository from which new GeoLite2 db files are downloaded. This option has a default value which is usually ok. It can contain a `{license_key}` placeholder which will be replaced with the value provided in previous config option, but it can also be a hardcoded URL which will make the license key option to be ignored.

> This project includes GeoLite2 data created by MaxMind, available from [https://www.maxmind.com](https://www.maxmind.com)
