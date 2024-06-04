<?php

namespace Orpheus\LaravelCountries;

use Exception;
use Illuminate\Support\Collection;

class CountriesRepository
{
    use RegionsTrait;

    protected $data = [];

    public function __construct()
    {
        $countriesJsonPath = config('laravel-countries.countries_json_path', null);
        if (is_null($countriesJsonPath)) {
            $this->data = CountriesDataSource::COUNTRIES_DATA;
        } else {
            if (file_exists($countriesJsonPath)) {
                $this->data = json_decode(file_get_contents($countriesJsonPath), true);
            } else {
                throw new Exception(sprintf('Cannot find the file "%s".', $countriesJsonPath));
            }
        }
    }

    /**
     * Get the country from it's 2-letters country code from ISO3166.
     *
     * @param  string $code The country's code.
     * @return Country   The matching country.
     */
    public function getByAlpha2Code($code)
    {
        return $this->searchItem('cca2', mb_strtoupper($code));
    }

    /**
     * Get the country from it's 3-letters country code from ISO3166.
     *
     * @param  string $code The country's code.
     * @return Country   The matching country.
     */
    public function getByAlpha3Code($code)
    {
        return $this->searchItem('cca3', mb_strtoupper($code));
    }

    /**
     * Get the country from it's 3-digits country code from ISO3166.
     *
     * @param  int $code The country's code.
     * @return Country   The matching country.
     */
    public function getByNumericCode($code)
    {
        return $this->searchItem('ccn3', $code);
    }

    /**
     * Get all the countries in a region.
     *
     * @param  string $region The region name
     * @return array   An array of the matching countries.
     */
    public function getByRegion($region)
    {
        return $this->searchArray('region', $region);
    }

    /**
     * Get all the countries in a subregion.
     *
     * @param  string $region The subregion name
     * @return array   An array of the matching countries.
     */
    public function getBySubregion($subregion)
    {
        return $this->searchArray('subregion', $subregion);
    }

    /**
     * Get all the countries using this currency.
     *
     * @param  string $currency The currency code
     * @return array   An array of the matching countries.
     */
    public function getByCurrency($currency)
    {
        $results = array_filter($this->data, function ($value) use ($currency) {
            return array_key_exists($currency, $value['currencies']);
        });

        return $this->castArrayToCountries($results);
    }

    /**
     * Get all the countries.
     *
     * @param  array $countries An array of country codes to filter the results.
     * @return array|Collection   An array of the matching countries.
     */
    public function getAll(array $countries = [], bool $asCollection = false)
    {
        $foundCountries = [];
        foreach ($countries as $country) {
            if (is_int($country) || is_numeric($country)) {
                /** @var Country|null $foundCountry */
                $foundCountry = $this->searchItem('ccn3', $country);
                if ($foundCountry) {
                    $foundCountries[$foundCountry->getNumericCode()] = $foundCountry;
                }

                continue;
            }

            if (mb_strlen($country) === 2) {
                /** @var Country|null $foundCountry */
                $foundCountry = $this->searchItem('cca2', $country);
                if ($foundCountry) {
                    $foundCountries[$foundCountry->getAlpha2Code()] = $foundCountry;
                }

                continue;
            }

            if (mb_strlen($country) === 3) {
                /** @var Country|null $foundCountry */
                $foundCountry = $this->searchItem('cca3', $country);
                if ($foundCountry) {
                    $foundCountries[$foundCountry->getAlpha3Code()] = $foundCountry;
                }

                continue;
            }
        }

        if (empty($foundCountries)) {
            $foundCountries = $this->castArrayToCountries($this->data);
        }
        return $asCollection ? collect($foundCountries) : $foundCountries;
    }

    /**
     * Get a dropdown-ready list of countries.
     *
     * @param  string  $key          The country attribute to use as key.
     * @param  bool $official     True for the offical country name, False for the common name.
     * @param  string  $localization A 3-letter locale code to try to translate. Will default to English if it`s missing.
     * @return array                 An array composed of the selected Keys, and the Countries names as values.
     */
    public function getListForDropdown($key = 'cca3', $official = false, $localization = null)
    {
        $list = [];

        $size = count($this->data);
        for ($i = 0; $i < $size; $i++) {
            // Try to get the translated names, if they are present
            $names = ($localization === null || !isset($this->data[$i]['translations'][$localization]))
                ? $this->data[$i]['name']
                : $this->data[$i]['translations'][$localization];

            // Set this country in the list to either it's Official or common name
            $list[$this->data[$i][$key]] = $official ? $names['official'] : $names['common'];
        }

        return $list;
    }

    /**
     * Get a single Country by filtering this column.
     *
     * @param  string $columnKey The column to filter
     * @param  mixed $input     The value to filter for
     * @return Orpheus\LaravelCountries\Country|null  The matching country or null
     */
    protected function searchItem($columnKey, $input)
    {
        // Only the first matching key will be returned, or null.
        $key = array_search($input, array_column($this->data, $columnKey));

        if ($key === false) {
            return null;
        }

        return new Country($this->data[$key]);
    }

    /**
     * Get an array of Countries by filtering this column.
     *
     * @param  string $columnKey The column to filter
     * @param  mixed $input     The value to filter for
     * @return Orpheus\LaravelCountries\Country|null  The matching country or null
     */
    protected function searchArray($columnKey, $input)
    {
        // Apply filter on the dataset.
        $keys = array_keys(array_column($this->data, $columnKey), $input);

        // Flip the keys and values to get the original keys from the dataset.
        $keys = array_flip($keys);

        // Extract the matching keys and associated data from the dataset.
        $keys = array_intersect_key($this->data, $keys);

        return $this->castArrayToCountries($keys);
    }

    public function getRawData()
    {
        return $this->data;
    }

    protected function castArrayToCountries(array $inputArray)
    {
        $countries = [];
        foreach ($inputArray as $value) {
            $country = new Country($value);
            $countries[$country->getAlpha2Code()] = $country;
        }

        return $countries;
    }
}
