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
     * Get a country by any code: cca2, cca3, ccn3 or cioc.
     * Auto-detects the code type based on input.
     *
     * @param  string|int $code The country code (2-letter, 3-letter, numeric, or CIOC).
     * @return Country|null The matching country.
     */
    public function getByCode($code)
    {
        if (is_int($code) || is_numeric($code)) {
            return $this->searchItem('ccn3', (string) $code);
        }

        $code = mb_strtoupper($code);

        if (mb_strlen($code) === 2) {
            return $this->searchItem('cca2', $code);
        }

        if (mb_strlen($code) === 3) {
            // Try cca3 first, then cioc
            $result = $this->searchItem('cca3', $code);
            if ($result) {
                return $result;
            }

            return $this->searchItem('cioc', $code);
        }

        return null;
    }

    /**
     * Get the country from it's 2-letters country code from ISO3166.
     *
     * @deprecated 3.0.0 Use getByCode() instead.
     * @param  string $code The country's code.
     * @return Country|null The matching country.
     */
    public function getByAlpha2Code($code)
    {
        @trigger_error('getByAlpha2Code() is deprecated since v3.0.0, use getByCode() instead.', E_USER_DEPRECATED);

        return $this->searchItem('cca2', mb_strtoupper($code));
    }

    /**
     * Get the country from it's 3-letters country code from ISO3166.
     *
     * @deprecated 3.0.0 Use getByCode() instead.
     * @param  string $code The country's code.
     * @return Country|null The matching country.
     */
    public function getByAlpha3Code($code)
    {
        @trigger_error('getByAlpha3Code() is deprecated since v3.0.0, use getByCode() instead.', E_USER_DEPRECATED);

        return $this->searchItem('cca3', mb_strtoupper($code));
    }

    /**
     * Get the country from it's 3-digits country code from ISO3166.
     *
     * @deprecated 3.0.0 Use getByCode() instead.
     * @param  int $code The country's code.
     * @return Country|null The matching country.
     */
    public function getByNumericCode($code)
    {
        @trigger_error('getByNumericCode() is deprecated since v3.0.0, use getByCode() instead.', E_USER_DEPRECATED);

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
     * @param  string $currency The currency code or name
     * @return array   An array of the matching countries.
     */
    public function getByCurrency($currency)
    {
        $currency = mb_strtoupper($currency);

        $results = array_filter($this->data, function ($value) use ($currency) {
            if (empty($value['currencies'])) {
                return false;
            }

            // Match by currency code
            if (array_key_exists($currency, $value['currencies'])) {
                return true;
            }

            // Match by currency name (case-insensitive)
            foreach ($value['currencies'] as $curr) {
                if (isset($curr['name']) && mb_stripos($curr['name'], $currency) !== false) {
                    return true;
                }
            }

            return false;
        });

        return $this->castArrayToCountries($results);
    }

    /**
     * Search countries by common or official name (partial, case-insensitive).
     *
     * @param  string $name The name to search for.
     * @return array An array of the matching countries.
     */
    public function getByName($name)
    {
        $results = array_filter($this->data, function ($value) use ($name) {
            if (mb_stripos($value['name']['common'], $name) !== false) {
                return true;
            }
            if (mb_stripos($value['name']['official'], $name) !== false) {
                return true;
            }
            // Search native names
            if (!empty($value['name']['native'])) {
                foreach ($value['name']['native'] as $native) {
                    if (mb_stripos($native['common'] ?? '', $name) !== false) {
                        return true;
                    }
                    if (mb_stripos($native['official'] ?? '', $name) !== false) {
                        return true;
                    }
                }
            }
            return false;
        });

        return $this->castArrayToCountries($results);
    }

    /**
     * Search countries by exact common or official name (case-insensitive).
     *
     * @param  string $name The full name to match.
     * @return array An array of the matching countries.
     */
    public function getByFullName($name)
    {
        $results = array_filter($this->data, function ($value) use ($name) {
            if (mb_strtolower($value['name']['common']) === mb_strtolower($name)) {
                return true;
            }
            if (mb_strtolower($value['name']['official']) === mb_strtolower($name)) {
                return true;
            }
            return false;
        });

        return $this->castArrayToCountries($results);
    }

    /**
     * Search countries by language code or name (case-insensitive).
     *
     * @param  string $language The language code (e.g. 'fra') or name (e.g. 'French').
     * @return array An array of the matching countries.
     */
    public function getByLanguage($language)
    {
        $results = array_filter($this->data, function ($value) use ($language) {
            if (empty($value['languages'])) {
                return false;
            }

            // Match by language code (case-insensitive)
            foreach (array_keys($value['languages']) as $code) {
                if (mb_strtolower($code) === mb_strtolower($language)) {
                    return true;
                }
            }

            // Match by language name (partial, case-insensitive)
            foreach ($value['languages'] as $langName) {
                if (mb_stripos($langName, $language) !== false) {
                    return true;
                }
            }

            return false;
        });

        return $this->castArrayToCountries($results);
    }

    /**
     * Search countries by capital city (partial, case-insensitive).
     *
     * @param  string $capital The capital city name.
     * @return array An array of the matching countries.
     */
    public function getByCapital($capital)
    {
        $results = array_filter($this->data, function ($value) use ($capital) {
            if (empty($value['capital'])) {
                return false;
            }

            foreach ($value['capital'] as $cap) {
                if (mb_stripos($cap, $capital) !== false) {
                    return true;
                }
            }

            return false;
        });

        return $this->castArrayToCountries($results);
    }

    /**
     * Search countries by demonym (case-insensitive).
     *
     * @param  string $demonym The demonym to search for (e.g. 'Canadian').
     * @return array An array of the matching countries.
     */
    public function getByDemonym($demonym)
    {
        $results = array_filter($this->data, function ($value) use ($demonym) {
            if (empty($value['demonyms'])) {
                return false;
            }

            foreach ($value['demonyms'] as $lang => $genders) {
                if (mb_strtolower($genders['f'] ?? '') === mb_strtolower($demonym)) {
                    return true;
                }
                if (mb_strtolower($genders['m'] ?? '') === mb_strtolower($demonym)) {
                    return true;
                }
            }

            return false;
        });

        return $this->castArrayToCountries($results);
    }

    /**
     * Search countries by any translation name (partial, case-insensitive).
     *
     * @param  string $translation The translation text to search for.
     * @return array An array of the matching countries.
     */
    public function getByTranslation($translation)
    {
        $results = array_filter($this->data, function ($value) use ($translation) {
            if (empty($value['translations'])) {
                return false;
            }

            foreach ($value['translations'] as $lang => $names) {
                if (mb_stripos($names['official'] ?? '', $translation) !== false) {
                    return true;
                }
                if (mb_stripos($names['common'] ?? '', $translation) !== false) {
                    return true;
                }
            }

            return false;
        });

        return $this->castArrayToCountries($results);
    }

    /**
     * Get all independent or non-independent countries.
     *
     * @param  bool $status True for independent countries, false for non-independent.
     * @return array An array of the matching countries.
     */
    public function getIndependent($status = true)
    {
        $results = array_filter($this->data, function ($value) use ($status) {
            return ($value['independent'] ?? false) === $status;
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
