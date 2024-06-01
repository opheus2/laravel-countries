# Laravel Countries

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
![Build Status][ico-github-action]
[![Total Downloads][ico-downloads]][link-downloads]

This package gives you access effortlessly to data from every country.
Laravel 7 above is supported.

Original [package](https://github.com/patricksamson/laravel-countries) by [Patrick Samson](https://github.com/patricksamson) is not actively maintained anymore, so I decided to fork it.

## Features
- Get a country by its 2-letters, 3-letters or 3-digits ISO3166 code.
- Get a list of countries by their region or sub region.
- Get a list of countries by their currency.
- Get a list of countries as a simple PHP array for dropdowns.
- Country
  - Get the 2-letters ISO3166 code.
  - Get the 3-letters ISO3166 code.
  - Get the 3-digits ISO3166 code.
  - Get the official name.
  - Get the common name.
  - Get a Currency object.
  - Get a collection of Currency objects.
- Currency
  - Get the currency code.
  - Get the currency name.
  - Get the currency symbol.
- CountryCast
  - A custom Eloquent cast to store a Country object in the database.


- Coming Soon
  - Get a list of countries by their language.
  - Get a list of countries by their calling code.
  - Get a list of countries by their time zone.
  - Get a list of countries by their top level domain.
  - Get a list of countries by their continent.
  - Get a country flag by its 2-letters ISO3166 code. (size, type, style, etc.)
  - blade components for country dropdowns
  - blade components for country flags
  - Get Google Maps URL for a country


## Install

Via Composer

``` bash
composer require opheus2/laravel-countries
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
$country->getCommonName();      // 'Canada' 
$country->getCurrency();        // The first Currency object
$country->getCurrencies();      // A collection of Currency objects

$currency = $country->getCurrency();
$currency->getCode();           // 'CAD'
$currency->getName();           // 'Canadian dollar'
$currency->getSymbol();         // '$'

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

### Macros

This package implements the Laravel `Macroable` trait, allowing macros and mixins on both `Country`.

Example use case:

```php
use Orpheus\LaravelCountries\Country;

Country::macro(
    'getFlag',
    fn () => sprintf('https://www.countryflags.io/%s/flat/64.png', $this->getAlpha2Code())
);

$country = \Countries::getByAlpha3Code('CAN');
$flag = $country->getFlag();

// Output: https://www.countryflags.io/CA/flat/64.png
```

### Mixins

Along with Macros, Mixins are also supported. This allows merging another classes methods into the Country.

Define the mixin class:

```php

class CustomCountry
{
    public function getFlag(): string
    {
        return sprintf('https://www.countryflags.io/%s/flat/64.png', $this->getAlpha2Code());
    }
}
```

Register the mixin, by passing an instance of the class:

```php
Country::mixin(new CustomCountry);
```

The methods from the custom class will be available:

```php
$country = \Countries::getByAlpha3Code('CAN');
$flag = $country->getFlag();
```

## Credits

- [Orpheus][link-author] for the fork.
- [Patrick Samson](https://github.com/patricksamson) for the initial package.
- [Mohammed Le Doze](https://github.com/mledoze) for compiling all this data in [this repository](https://github.com/mledoze/countries).
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/Orpheus/laravel-countries.svg
[ico-license]: https://img.shields.io/packagist/l/Orpheus/laravel-countries.svg
[ico-github-action]: https://github.com/opheus2/laravel-countries/workflows/Run%20Tests%20-%20Current/badge.svg?branch=master
[ico-downloads]: https://img.shields.io/packagist/dt/Orpheus/laravel-countries.svg

[link-packagist]: https://packagist.org/packages/opheus2/laravel-countries
[link-downloads]: https://packagist.org/packages/opheus2/laravel-countries
[link-author]: https://github.com/opheus2
[link-contributors]: ../../contributors
