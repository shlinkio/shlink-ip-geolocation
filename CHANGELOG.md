# CHANGELOG

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com), and this project adheres to [Semantic Versioning](https://semver.org).

## [3.4.0] - 2023-12-17
### Added
* Add support for `geoip2/geoip2` 3.0

### Changed
* Remove dependency on functional-php.

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* *Nothing*


## [3.3.0] - 2023-11-25
### Added
* Add support for PHP 8.3

### Changed
* Update to PHPUnit 10 and migrate to PHPUnit 10.1 config format.
* Define stricter callable types in `DbUpdater`.

### Deprecated
* *Nothing*

### Removed
* Drop support for PHP 8.1

### Fixed
* *Nothing*


## [3.2.0] - 2022-12-16
### Added
* *Nothing*

### Changed
* Migrated GeoLite2Options to ValinorConfigFactory.
* Migrated infection config to json5
* Migrated from phpspec/prophecy to PHPUnit mocks

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* *Nothing*


## [3.1.1] - 2022-09-18
### Added
* *Nothing*

### Changed
* *Nothing*

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* Extracted specific exception for missing license key for easier handling of that specific use case.


## [3.1.0] - 2022-09-18
### Added
* Ensured an exception is thrown when trying to download visits and a license key is not available, to let consumers handle this as they want.

### Changed
* *Nothing*

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* *Nothing*


## [3.0.0] - 2022-08-07
### Added
* *Nothing*

### Changed
* Updated to infection 0.26, enabling HTML reports.
* Added explicitly enabled composer plugins to composer.json.
* Applied some PHP 8.1 syntax.

### Deprecated
* *Nothing*

### Removed
* Dropped support for PHP 8.0

### Fixed
* *Nothing*


## [2.2.0] - 2021-12-02
### Added
* Added support for PHP 8.1
* Added support for Symfony 6.0

### Changed
* *Nothing*

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* *Nothing*


## [2.1.0] - 2021-11-01
### Added
* *Nothing*

### Changed
* Updated to infection-php 0.25
* Updated to phpstan 1.0
* Updated required phpstan level to 9.
* Added experimental builds under PHP 8.1.
* Moved ci workflow to external repo and reused

### Deprecated
* *Nothing*

### Removed
* Dropped support for PHP 7.4

### Fixed
* *Nothing*


## [2.0.0] - 2021-05-30
### Added
* [#37](https://github.com/shlinkio/shlink-ip-geolocation/issues/37) Made license key optional, skipping its update if the key is not set.

### Changed
* [#32](https://github.com/shlinkio/shlink-ip-geolocation/issues/32) Ensured downloaded GeoLite2 db file is set with `0666` mode.
* Migrated build to Github Actions.
* Updated to latest infection-php.

### Deprecated
* *Nothing*

### Removed
* [#38](https://github.com/shlinkio/shlink-ip-geolocation/issues/38) Removed option to override GeoLite2 db file download source.

### Fixed
* *Nothing*


## [1.6.0] - 2020-11-01
### Added
* Added explicit support for PHP 8

### Changed
* Added PHP 8 to the build matrix, allowing failures on it.
* Updated to infection 0.20

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* *Nothing*


## [1.5.0] - 2020-06-28
### Added
* Added support for Guzzle 7

### Changed
* *Nothing*

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* *Nothing*


## [1.4.1] - 2020-05-20
### Added
* *Nothing*

### Changed
* [#27](https://github.com/shlinkio/shlink-ip-geolocation/issues/27) Updated `phpunit` to v9 and `infection` to v0.16.

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* [#29](https://github.com/shlinkio/shlink-ip-geolocation/issues/29) Fixed timeout connections when downloading GeoLite databases leaving connection stuck forever.


## [1.4.0] - 2020-03-13
### Added
* [#22](https://github.com/shlinkio/shlink-ip-geolocation/issues/22) Added `DbUpdateException` which is thrown by the `DbUpdater` when something fails. It extends from the previous exception, making it backwards compatible.

### Changed
* [#24](https://github.com/shlinkio/shlink-ip-geolocation/issues/24) Migrated from `shlinkio/shlink-common` to` shlinkio/shlink-config`.

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* *Nothing*


## [1.3.1] - 2020-01-27
### Added
* *Nothing*

### Changed
* *Nothing*

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* [#17](https://github.com/shlinkio/shlink-ip-geolocation/issues/17) Allowed the library to receive the geolite license key via config.


## [1.3.0] - 2020-01-03
### Added
* *Nothing*

### Changed
* [#15](https://github.com/shlinkio/shlink-ip-geolocation/issues/15) Updated coding-standard (v2.1) and phpstan (0.12) dependencies.
* [#16](https://github.com/shlinkio/shlink-ip-geolocation/issues/16) Migrated from Zend Framework components to [Laminas](https://getlaminas.org/).

### Deprecated
* [#14](https://github.com/shlinkio/shlink-ip-geolocation/issues/14) Dropped support for PHP 7.2 and 7.3

### Removed
* *Nothing*

### Fixed
* *Nothing*


## [1.2.0] - 2019-11-30
### Added
* *Nothing*

### Changed
* Updated dependencies, including the requirement of Symfony 5 components.

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* *Nothing*


## [1.1.0] - 2019-10-25
### Added
* *Nothing*

### Changed
* [#7](https://github.com/shlinkio/shlink-ip-geolocation/issues/7) Updated dependencies, including shlink-common, coding-standard and infection.
* [#1](https://github.com/shlinkio/shlink-ip-geolocation/issues/1) Increased minimum required mutation score to 70%.

### Deprecated
* *Nothing*

### Removed
* [#9](https://github.com/shlinkio/shlink-ip-geolocation/issues/9) Deleted `IpApiLocationResolver`.

### Fixed
* *Nothing*


## [1.0.1] - 2019-09-11
### Added
* *Nothing*

### Changed
* *Nothing*

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* [#5](https://github.com/shlinkio/shlink-ip-geolocation/issues/5) Added support for [shlink-common](https://github.com/shlinkio/shlink-common) v2.0.0.


## [1.0.0] - 2019-08-12
### Added
* First stable release

### Changed
* *Nothing*

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* *Nothing*
