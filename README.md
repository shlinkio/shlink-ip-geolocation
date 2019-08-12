# Shlink IP Address Geolocation module

Shlink module with tools to geolocate an IP address using different strategies.

Most of the elements it provides require a [PSR-11](https://www.php-fig.org/psr/psr-11/) container, and it's easy to integrate on [expressive](https://github.com/zendframework/zend-expressive) applications thanks to the `ConfigProvider` it includes.

[![Build Status](https://img.shields.io/travis/shlinkio/shlink-ip-geolocation.svg?style=flat-square)](https://travis-ci.org/shlinkio/shlink-ip-geolocation)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/shlinkio/shlink-ip-geolocation.svg?style=flat-square)](https://scrutinizer-ci.com/g/shlinkio/shlink-ip-geolocation/?branch=master)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/shlinkio/shlink-ip-geolocation.svg?style=flat-square)](https://scrutinizer-ci.com/g/shlinkio/shlink-ip-geolocation/?branch=master)
[![Latest Stable Version](https://img.shields.io/github/release/shlinkio/shlink-ip-geolocation.svg?style=flat-square)](https://packagist.org/packages/shlinkio/shlink-ip-geolocation)
[![License](https://img.shields.io/github/license/shlinkio/shlink-ip-geolocation.svg?style=flat-square)](https://github.com/shlinkio/shlink-ip-geolocation/blob/master/LICENSE)
[![Paypal donate](https://img.shields.io/badge/Donate-paypal-blue.svg?style=flat-square&logo=paypal&colorA=aaaaaa)](https://acel.me/donate)

## Install

Install this library using composer:

    composer require shlinkio/shlink-ip-geolocation

> This library is also an expressive module which provides its own `ConfigProvider`. Add it to your configuration to get everything automatically set up.

## *TODO*

```php
<?php
declare(strict_types=1);

return [

    'geolite2' => [
        'db_location' => __DIR__ . '/../../data/GeoLite2-City.mmdb',
        'temp_dir' => sys_get_temp_dir(),
        // 'download_from' => 'http://geolite.maxmind.com/download/geoip/database/GeoLite2-City.tar.gz',
    ],

];
```
