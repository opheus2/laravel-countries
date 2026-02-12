<?php

namespace Orpheus\LaravelCountries\Tests;

class CountryCastTest extends LaravelCountriesTestCase
{
    /** @var \Orpheus\LaravelCountries\Country $country */
    protected $country;

    public function setUp(): void
    {
        parent::setUp();

        $this->country = $this->countries->getByAlpha2Code('CA');
    }


    public function test_it_casts_country_to_alpha2_code()
    {
        $cast = new \Orpheus\LaravelCountries\Casts\CountryCast('alpha2');

        $this->assertEquals('CA', $cast->set(null, 'key', $this->country, []));
        $this->assertEquals($this->country, $cast->get(null, 'key', 'CA', []));
    }

    public function test_it_casts_country_to_alpha3_code()
    {
        $cast = new \Orpheus\LaravelCountries\Casts\CountryCast('alpha3');

        $this->assertEquals('CAN', $cast->set(null, 'key', $this->country, []));
        $this->assertEquals($this->country, $cast->get(null, 'key', 'CAN', []));
    }

    public function test_it_casts_country_to_numeric_code()
    {
        $cast = new \Orpheus\LaravelCountries\Casts\CountryCast('numeric');

        $this->assertEquals(124, $cast->set(null, 'key', $this->country, []));
        $this->assertEquals($this->country, $cast->get(null, 'key', 124, []));
    }
}
