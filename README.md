# Laravel Countries

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
![Build Status][ico-github-action]
[![Total Downloads][ico-downloads]][link-downloads]

This package gives you access effortlessly to data from every country.

## Install

Via Composer

``` bash
composer require Orpheus/laravel-countries
```

## Usage

You can search for a specific country using one of the following unique country codes :
``` php
$country = \Countries::getByAlpha2Code('CA'); // 2-letters country code from ISO3166
$country = \Countries::getByAlpha3Code('CAN'); // 3-letters country code from ISO3166
$country = \Countries::getByNumericCode(124); // 3-digits country code from ISO3166
```

You can retrieve an array of countries using their region of one of their currencies :
``` php
$countries = \Countries::getByCurrency('CAD'); /// A 3-letters currency code

// Search by region
$countries = \Countries::getByRegion(\Countries::$REGION_AFRICA);
$countries = \Countries::getByRegion(\Countries::$REGION_AMERICAS);
$countries = \Countries::getByRegion(\Countries::$REGION_ANTARCTICA);
$countries = \Countries::getByRegion(\Countries::$REGION_ASIA);
$countries = \Countries::getByRegion(\Countries::$REGION_EUROPE);
$countries = \Countries::getByRegion(\Countries::$REGION_OCEANIA);
```

Results will be returned as Country objects. These objects have the following helper methods :
``` php
$country = \Countries::getByAlpha3Code('CAN');

$country->getAlpha2Code();      // 'CA'
$country->getAlpha3Code();      // 'CAN'
$country->getNumericCode();     // 124
$country->getOfficialName();    // 'Canada'

$country->getAttributes();      // An array of all the raw attributes.
```

It is also possible to generate simple PHP array that can be used to generate an HTML select input.
``` php
// All parameters are optionnal. These are the defaults.
// $key             The country attribute to use as key. (default: 'cca3', 3-letters country code from ISO3166)
// $official        True for the offical country name, False for the common name. (default: false)
// $localization    A 3-letter locale code to try to translate. Will default to English if it`s missing. (default: null)
$countries = \Countries::getListForDropdown('cca3', false, 'fra');

//This will return the following
[
    'CAN' => 'Canada',
    'USA' => 'États-Unis',
    ...
]
```

## Credits

- [Patrick Samson][link-author]
- [Mohammed Le Doze](https://github.com/mledoze) for compiling all this data in [this repository](https://github.com/mledoze/countries).
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/Orpheus/laravel-countries.svg
[ico-license]: https://img.shields.io/packagist/l/Orpheus/laravel-countries.svg
[ico-github-action]: https://github.com/patricksamson/laravel-countries/workflows/Run%20Tests%20-%20Current/badge.svg?branch=master
[ico-downloads]: https://img.shields.io/packagist/dt/Orpheus/laravel-countries.svg

[link-packagist]: https://packagist.org/packages/Orpheus/laravel-countries
[link-downloads]: https://packagist.org/packages/Orpheus/laravel-countries
[link-author]: https://github.com/Orpheus
[link-contributors]: ../../contributors
