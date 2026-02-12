<?php

namespace Orpheus\LaravelCountries\Tests;

class CountryTest extends LaravelCountriesTestCase
{
    /** @var \Orpheus\LaravelCountries\Country $country */
    protected $country;

    public function setUp(): void
    {
        parent::setUp();

        $this->country = $this->countries->getByCode('CA');
    }

    public function test_it_gets_alpha2_code()
    {
        $this->assertEquals('CA', $this->country->getAlpha2Code());
    }

    public function test_it_gets_alpha3_code()
    {
        $this->assertEquals('CAN', $this->country->getAlpha3Code());
    }

    public function test_it_gets_numeric_code()
    {
        $this->assertEquals(124, $this->country->getNumericCode());
    }

    public function test_it_gets_official_name()
    {
        $this->assertEquals('Canada', $this->country->getOfficialName());
    }

    public function test_it_gets_common_name()
    {
        $this->assertEquals('Canada', $this->country->getCommonName());
    }

    public function test_it_gets_currency()
    {
        $currency = $this->country->getCurrency();

        $this->assertInstanceOf(\Orpheus\LaravelCountries\Currency::class, $currency);
        $this->assertEquals('CAD', $currency->getCode());
        $this->assertEquals('Canadian dollar', $currency->getName());
        $this->assertEquals('$', $currency->getSymbol());
    }

    public function test_it_gets_all_currencies()
    {
        $country = $this->countries->getByCode('SH');
        $currencies = $country->getCurrencies();

        $this->assertCount(2, $currencies);
        foreach ($currencies as $currency) {
            $this->assertInstanceOf(\Orpheus\LaravelCountries\Currency::class, $currency);
        }
    }
}
