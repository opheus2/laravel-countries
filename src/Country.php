<?php

namespace Orpheus\LaravelCountries;

use JsonSerializable;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;

class Country implements Arrayable, Jsonable, JsonSerializable
{
    use Macroable {
        __callStatic as protected macroCallStatic;
    }

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

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'name' => $this->getCommonName(),
            'alpha2Code' => $this->getAlpha2Code(),
            'alpha3Code' => $this->getAlpha3Code(),
            'numericCode' => $this->getNumericCode(),
            'officialName' => $this->getOfficialName(),
            'commonName' => $this->getCommonName(),
            'currency' => $this->getCurrency()->toArray(),
        ];
    }

    /**
     * Get the instance as JSON.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Dynamically retrieve attributes on the country.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->attributes[$key];
    }

    /**
     * Dynamically set attributes on the country.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Determine if an attribute exists on the country.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->attributes[$key]);
    }


    /**
     * Handle dynamic method calls into the country.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->macroCallStatic($method, $parameters);
    }

    /**
     * Dynamically handle calls to the class.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public static function __callStatic($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return static::macroCallStatic($method, $parameters);
        }

        throw new \BadMethodCallException("Method {$method} does not exist.");
    }

    /**
     * Serialize the object to its JSON representation.
     *
     * @return array
     */
    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
