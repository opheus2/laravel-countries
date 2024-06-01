<?php

namespace Orpheus\LaravelCountries\Tests;

class CountryTest extends LaravelCountriesTestCase
{
    /** @var \Orpheus\LaravelCountries\Country $country */
    protected $country;

    public function setUp(): void
    {
        parent::setUp();

        $this->country = $this->countries->getByAlpha2Code('CA');
    }

    /** @test */
    public function it_gets_alpha2_code()
    {
        $this->assertEquals('CA', $this->country->getAlpha2Code());
    }

    /** @test */
    public function it_gets_alpha3_code()
    {
        $this->assertEquals('CAN', $this->country->getAlpha3Code());
    }

    /** @test */
    public function it_gets_numeric_code()
    {
        $this->assertEquals(124, $this->country->getNumericCode());
    }

    /** @test */
    public function it_gets_official_name()
    {
        $this->assertEquals('Canada', $this->country->getOfficialName());
    }

    /** @test */
    public function it_gets_common_name()
    {
        $this->assertEquals('Canada', $this->country->getCommonName());
    }

    /** @test */
    public function it_gets_currency()
    {
        $currency = $this->country->getCurrency();

        $this->assertInstanceOf(\Orpheus\LaravelCountries\Currency::class, $currency);
        $this->assertEquals('CAD', $currency->getCode());
        $this->assertEquals('Canadian dollar', $currency->getName());
        $this->assertEquals('$', $currency->getSymbol());
    }

    /** @test */
    public function it_gets_all_currencies()
    {
        $country = $this->countries->getByAlpha2Code('SH');
        $currencies = $country->getCurrencies();

        $this->assertCount(2, $currencies);
        foreach ($currencies as $currency) {
            $this->assertInstanceOf(\Orpheus\LaravelCountries\Currency::class, $currency);
        }
    }
}
