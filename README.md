# Laravel Countries

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
![Build Status][ico-github-action]
[![Total Downloads][ico-downloads]][link-downloads]

A Laravel package that provides comprehensive data for every country — codes, names, currencies, languages, capitals, regions, demonyms, translations, and more. Laravel 11+ supported.

## What's New in v3.0.0

- **Updated data source** — Country data is now compiled from the [REST Countries v3.1](https://gitlab.com/restcountries/restcountries) dataset.
- **New search methods** — Search by name, language, capital, demonym, translation, and independence status.
- **Unified code lookup** — New `getByCode()` method auto-detects cca2, cca3, ccn3, or cioc codes.
- **Deprecations** — `getByAlpha2Code()`, `getByAlpha3Code()`, and `getByNumericCode()` are deprecated in favor of `getByCode()`. They still work but will trigger `E_USER_DEPRECATED` notices.
- **PHPUnit 12 compatibility** — Test suite updated.

## Install

``` bash
composer require opheus2/laravel-countries
```

## Upgrading from v2.x

Replace deprecated method calls:

```php
use Orpheus\LaravelCountries\Facades\Countries;

// Before (deprecated)
$country = Countries::getByAlpha2Code('CA');
$country = Countries::getByAlpha3Code('CAN');
$country = Countries::getByNumericCode(124);

// After
$country = Countries::getByCode('CA');
$country = Countries::getByCode('CAN');
$country = Countries::getByCode(124);
```

The deprecated methods still function but will emit `E_USER_DEPRECATED` warnings and will be removed in a future major release.

## Usage

### Lookup by Code

The `getByCode()` method auto-detects the code type:

```php
use Orpheus\LaravelCountries\Facades\Countries;

$country = Countries::getByCode('CA');    // 2-letter (cca2)
$country = Countries::getByCode('CAN');   // 3-letter (cca3) or CIOC
$country = Countries::getByCode(124);     // Numeric (ccn3)
$country = Countries::getByCode('124');   // Numeric as string
```

### Search by Name

```php
use Orpheus\LaravelCountries\Facades\Countries;

// Partial match (searches common, official, and native names)
$countries = Countries::getByName('united');

// Exact match on common or official name
$countries = Countries::getByFullName('Canada');
$countries = Countries::getByFullName('United States of America');
```

### Search by Language

```php
use Orpheus\LaravelCountries\Facades\Countries;

// By language code
$countries = Countries::getByLanguage('fra');

// By language name (partial match)
$countries = Countries::getByLanguage('French');
$countries = Countries::getByLanguage('Spanish');
```

### Search by Capital

```php
use Orpheus\LaravelCountries\Facades\Countries;

$countries = Countries::getByCapital('Ottawa');
$countries = Countries::getByCapital('Wash');   // Partial match
```

### Search by Demonym

```php
use Orpheus\LaravelCountries\Facades\Countries;

$countries = Countries::getByDemonym('Canadian');
$countries = Countries::getByDemonym('peruvian');  // Case-insensitive
```

### Search by Translation

```php
use Orpheus\LaravelCountries\Facades\Countries;

$countries = Countries::getByTranslation('Alemania');   // Germany in Spanish
$countries = Countries::getByTranslation('Saksamaa');    // Germany in Estonian
```

### Search by Currency

```php
use Orpheus\LaravelCountries\Facades\Countries;

$countries = Countries::getByCurrency('CAD');              // By currency code
$countries = Countries::getByCurrency('Canadian dollar');  // By currency name
```

### Filter by Region / Subregion

```php
use Orpheus\LaravelCountries\Facades\Countries;

$countries = Countries::getByRegion(Countries::$REGION_AFRICA);
$countries = Countries::getByRegion(Countries::$REGION_AMERICAS);
$countries = Countries::getByRegion(Countries::$REGION_ANTARCTIC);
$countries = Countries::getByRegion(Countries::$REGION_ASIA);
$countries = Countries::getByRegion(Countries::$REGION_EUROPE);
$countries = Countries::getByRegion(Countries::$REGION_OCEANIA);

$countries = Countries::getBySubregion('Northern Europe');
```

### Filter by Independence Status

```php
use Orpheus\LaravelCountries\Facades\Countries;

$independent    = Countries::getIndependent(true);
$nonIndependent = Countries::getIndependent(false);
```

### Get All Countries

```php
use Orpheus\LaravelCountries\Facades\Countries;

$all = Countries::getAll();

// As a Laravel Collection
$collection = Countries::getAll([], true);

// Filtered by codes (mixed types supported)
$filtered = Countries::getAll(['CA', 'USA', '276']);
```

### Dropdown List Helper

```php
use Orpheus\LaravelCountries\Facades\Countries;

// Default: keyed by cca3, common name
$list = Countries::getListForDropdown();

// Custom key, official name, translated
$list = Countries::getListForDropdown('cca2', true, 'fra');

// Returns: ['CA' => 'Canada', 'US' => "Les états-unis d'Amérique", ...]
```

### Country Object

```php
use Orpheus\LaravelCountries\Facades\Countries;

$country = Countries::getByCode('CAN');

$country->getAlpha2Code();      // 'CA'
$country->getAlpha3Code();      // 'CAN'
$country->getNumericCode();     // '124'
$country->getOfficialName();    // 'Canada'
$country->getCommonName();      // 'Canada'
$country->getCurrency();        // Currency object (first currency)
$country->getCurrencies();      // Collection of Currency objects
$country->getAttributes();      // Raw attributes array

// Dynamic property access to any attribute
$country->capital;              // ['Ottawa']
$country->region;               // 'Americas'
$country->languages;            // ['eng' => 'English', 'fra' => 'French']
$country->independent;          // true
$country->demonyms;             // ['eng' => ['f' => 'Canadian', 'm' => 'Canadian'], ...]
```

### Currency Object

```php
$currency = $country->getCurrency();

$currency->getCode();           // 'CAD'
$currency->getName();           // 'Canadian dollar'
$currency->getSymbol();         // '$'
```

### Eloquent Cast

Store country references in your database and cast them automatically:

```php
use Orpheus\LaravelCountries\Casts\CountryCast;

class Order extends Model
{
    protected $casts = [
        'country' => CountryCast::class,           // Default: alpha2
        'country' => CountryCast::class . ':alpha3',
        'country' => CountryCast::class . ':numeric',
    ];
}
```

### Macros

The `Country` class implements Laravel's `Macroable` trait:

```php
use Orpheus\LaravelCountries\Country;
use Orpheus\LaravelCountries\Facades\Countries;

Country::macro('getFlag', fn () => sprintf(
    'https://flagcdn.com/w320/%s.png',
    strtolower($this->getAlpha2Code())
));

$country = Countries::getByCode('CAN');
$country->getFlag(); // 'https://flagcdn.com/w320/ca.png'
```

### Mixins

```php
use Orpheus\LaravelCountries\Country;
use Orpheus\LaravelCountries\Facades\Countries;

class CustomCountry
{
    public function getFlag(): \Closure
    {
        return fn () => sprintf(
            'https://flagcdn.com/w320/%s.png',
            strtolower($this->getAlpha2Code())
        );
    }
}

Country::mixin(new CustomCountry);

$country = Countries::getByCode('CAN');
$country->getFlag(); // 'https://flagcdn.com/w320/ca.png'
```

### Raw Data Access

```php
use Orpheus\LaravelCountries\Facades\Countries;

$rawData = Countries::getRawData(); // Full array of all country data
```

## Configuration

Publish the config file to customize the data source:

```bash
php artisan vendor:publish --provider="Orpheus\LaravelCountries\ServiceProvider"
```

This creates `config/laravel-countries.php` where you can point to a custom JSON file:

```php
return [
    'countries_json_path' => null, // null = use built-in data
];
```

## Data Source

Country data is compiled from the [REST Countries v3.1 JSON](https://gitlab.com/restcountries/restcountries) dataset using the included compile script (`scripts/compile-data-source.php`).

### Differences from upstream REST Countries v3.1

- **Field mapping** — `nativeName` is stored as `native` under the `name` key.
- **Computed field** — `callingCodes` is computed from `idd.root` + `idd.suffixes` for convenience.
- **Translation subset** — Only 26 translation languages are included (ara, bre, ces, cym, deu, est, fin, fra, hrv, hun, ind, ita, jpn, kor, nld, per, pol, por, rus, slk, spa, srp, swe, tur, urd, zho).
- **Currency overrides** — The following corrections are applied to upstream data:
  - **Cuba (CU)** — CUC removed (convertible peso eliminated Jan 1, 2021), CUP only.
  - **Bouvet Island (BV)** — NOK added (uninhabited Norwegian territory).
  - **Heard Island (HM)** — AUD added (uninhabited Australian territory).
  - **Micronesia (FM)** — USD added (sometimes missing upstream).
  - **Antarctica (AQ)** — USD, GBP, EUR added (used at research stations).

### Recompiling Data

To regenerate the data source from the latest upstream JSON:

```bash
composer compile-data-source
```

## API Reference

| Method | Returns | Description |
|--------|---------|-------------|
| `getByCode($code)` | `Country\|null` | Lookup by cca2, cca3, ccn3, or cioc |
| `getByName($name)` | `array` | Partial match on common/official/native names |
| `getByFullName($name)` | `array` | Exact match on common or official name |
| `getByLanguage($language)` | `array` | Search by language code or name |
| `getByCapital($capital)` | `array` | Search by capital city (partial) |
| `getByDemonym($demonym)` | `array` | Search by demonym (exact, case-insensitive) |
| `getByTranslation($translation)` | `array` | Search any translation name (partial) |
| `getByCurrency($currency)` | `array` | Search by currency code or name |
| `getByRegion($region)` | `array` | Filter by region |
| `getBySubregion($subregion)` | `array` | Filter by subregion |
| `getIndependent($status)` | `array` | Filter by independence status |
| `getAll($codes, $asCollection)` | `array\|Collection` | Get all or filtered countries |
| `getListForDropdown($key, $official, $locale)` | `array` | Key-value pairs for dropdowns |
| `getRawData()` | `array` | Raw country data array |

### Deprecated Methods (v3.0.0)

| Deprecated Method | Replacement |
|-------------------|-------------|
| `getByAlpha2Code($code)` | `getByCode($code)` |
| `getByAlpha3Code($code)` | `getByCode($code)` |
| `getByNumericCode($code)` | `getByCode($code)` |

## Credits

- [Orpheus][link-author] — Maintainer and fork author.
- [Patrick Samson](https://github.com/patricksamson) — Original [laravel-countries](https://github.com/patricksamson/laravel-countries) package.
- [REST Countries](https://gitlab.com/restcountries/restcountries) — v3.1 country data source (compiled and adapted with overrides).
- [Mohammed Le Doze](https://github.com/mledoze) — Previous data source via [mledoze/countries](https://github.com/mledoze/countries).
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
