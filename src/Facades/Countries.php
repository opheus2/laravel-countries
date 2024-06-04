<?php

namespace Orpheus\LaravelCountries\Facades;

use Illuminate\Support\Facades\Facade;
use Orpheus\LaravelCountries\RegionsTrait;

/**
 * @codeCoverageIgnore
 * 
 * @property static $REGION_AFRICA
 * @property static $REGION_AMERICAS
 * @property static $REGION_ANTARCTIC
 * @property static $REGION_ASIA
 * @property static $REGION_EUROPE
 * @property static $REGION_OCEANIA
 * @method static \Orpheus\LaravelCountries\Country getByAlpha2Code(string $alpha2Code)
 * @method static \Orpheus\LaravelCountries\Country getByAlpha3Code(string $alpha3Code)
 * @method static \Orpheus\LaravelCountries\Country getByNumericCode(int $numericCode)
 * @method static Array<\Orpheus\LaravelCountries\Country> getByRegion(string $region)
 * @method static Array<\Orpheus\LaravelCountries\Country> getBySubregion(string $subregion)
 * @method static Array<\Orpheus\LaravelCountries\Country> getByCurrency(string $currency)
 * @method static Array<\Orpheus\LaravelCountries\Country> getAll(array $countries = [], bool $asCollection = false)
 * @method static \Orpheus\LaravelCountries\CountriesRepository getListForDropdown(string $key = 'alpha3Code', bool $official = false, string $localization = 'en')
 * @method static \Orpheus\LaravelCountries\CountriesRepository getRawData()
 */
class Countries extends Facade
{
    use RegionsTrait;

    public static function getFacadeAccessor()
    {
        return 'countries';
    }
}
