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
 *
 * @method static \Orpheus\LaravelCountries\Country|null getByCode(string|int $code)
 * @method static array getByName(string $name)
 * @method static array getByFullName(string $name)
 * @method static array getByLanguage(string $language)
 * @method static array getByCapital(string $capital)
 * @method static array getByDemonym(string $demonym)
 * @method static array getByTranslation(string $translation)
 * @method static array getIndependent(bool $status = true)
 * @method static array getByRegion(string $region)
 * @method static array getBySubregion(string $subregion)
 * @method static array getByCurrency(string $currency)
 * @method static array|\Illuminate\Support\Collection getAll(array $countries = [], bool $asCollection = false)
 * @method static array getListForDropdown(string $key = 'cca3', bool $official = false, string $localization = null)
 * @method static array getRawData()
 *
 * @deprecated 3.0.0 Use getByCode() instead of getByAlpha2Code(), getByAlpha3Code(), getByNumericCode().
 * @method static \Orpheus\LaravelCountries\Country|null getByAlpha2Code(string $alpha2Code)
 * @method static \Orpheus\LaravelCountries\Country|null getByAlpha3Code(string $alpha3Code)
 * @method static \Orpheus\LaravelCountries\Country|null getByNumericCode(int $numericCode)
 */
class Countries extends Facade
{
    use RegionsTrait;

    public static function getFacadeAccessor()
    {
        return 'countries';
    }
}
