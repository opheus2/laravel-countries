<?php

namespace Orpheus\LaravelCountries\Casts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Orpheus\LaravelCountries\Facades\Countries;

class CountryCast implements CastsAttributes
{
    public $isoCodeVersion;

    /**
     * Create a new cast instance.
     *
     * @param  string  $isoCodeVersion The ISO code version to use (alpha2, alpha3, numeric)
     */
    public function __construct(
        $isoCodeVersion = 'alpha2' // alpha2, alpha3, numeric
    ) {
        $this->isoCodeVersion = $isoCodeVersion;
    }

    public function get($model, string $key, $value, array $attributes)
    {
        if (is_null($value)) {
            return null;
        }

        if ($this->isoCodeVersion === 'alpha3') {
            return Countries::getByAlpha3Code($value);
        }

        if ($this->isoCodeVersion === 'numeric') {
            return Countries::getByNumericCode($value);
        }

        return Countries::getByAlpha2Code($value);
    }

    public function set($model, string $key, $value, array $attributes)
    {
        if ($value instanceof \Orpheus\LaravelCountries\Country) {
            if ($this->isoCodeVersion === 'alpha3') {
                return $value->getAlpha3Code();
            }

            if ($this->isoCodeVersion === 'numeric') {
                return $value->getNumericCode();
            }

            return $value->getAlpha2Code();
        }

        return $value;
    }
}
