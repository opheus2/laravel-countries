<?php

namespace Orpheus\LaravelCountries;

use Illuminate\Support\Collection;

class Country
{
    protected $attributes = [];

    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Get this country's 2-letters country code from ISO3166.
     *
     * @return string   The 2-letters country code
     */
    public function getAlpha2Code()
    {
        return $this->attributes['cca2'];
    }

    /**
     * Get this country's 3-letters country code from ISO3166.
     *
     * @return string   The 3-letters country code
     */
    public function getAlpha3Code()
    {
        return $this->attributes['cca3'];
    }

    /**
     * Get this country's 3-digits country code from ISO3166.
     *
     * @return int   The 3-digits country code
     */
    public function getNumericCode()
    {
        return $this->attributes['ccn3'];
    }

    /**
     * Get this country's official name.
     *
     * @return string   The country's official name
     */
    public function getOfficialName()
    {
        return $this->attributes['name']['official'];
    }

    /**
     * Get this country's common name.
     *
     * @return string   The country's common name
     */
    public function getCommonName()
    {
        return $this->attributes['name']['common'];
    }

    /**
     * Get this country's attributes.
     * @return array All of this country's attributes.
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Get this country's currency.
     *
     * @return Currency  The Country's currency class.
     */
    public function getCurrency()
    {
        $code = array_keys($this->attributes['currencies'])[0];

        return Currency::make([
            'code' => $code,
            'name' => $this->attributes['currencies'][$code]['name'],
            'symbol' => $this->attributes['currencies'][$code]['symbol']
        ]);
    }

    /**
     * Get this country's currencies.
     *
     * @return Collection<Currency>  The Country's currencies collection.
     */
    public function getCurrencies()
    {
        return collect(array_map(function ($code, $currency) {
            return Currency::make([
                'code' => $code,
                'name' => $currency['name'],
                'symbol' => $currency['symbol']
            ]);
        }, array_keys($this->attributes['currencies']), $this->attributes['currencies']));
    }
}
