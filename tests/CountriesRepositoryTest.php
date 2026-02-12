<?php

namespace Orpheus\LaravelCountries\Tests;

class CountriesRepositoryTest extends LaravelCountriesTestCase
{
    public function test_it_gets_country_from_alpha2_code()
    {
        $country = $this->countries->getByAlpha2Code('CA');
        $this->assertInstanceOf(\Orpheus\LaravelCountries\Country::class, $country);
        $this->assertEquals('Canada', $country->getOfficialName());

        $country = $this->countries->getByAlpha2Code('ca');
        $this->assertInstanceOf(\Orpheus\LaravelCountries\Country::class, $country);
        $this->assertEquals('Canada', $country->getOfficialName());
    }

    public function test_it_gets_country_from_alpha3_code()
    {
        $country = $this->countries->getByAlpha3Code('CAN');
        $this->assertInstanceOf(\Orpheus\LaravelCountries\Country::class, $country);
        $this->assertEquals('Canada', $country->getOfficialName());

        $country = $this->countries->getByAlpha3Code('can');
        $this->assertInstanceOf(\Orpheus\LaravelCountries\Country::class, $country);
        $this->assertEquals('Canada', $country->getOfficialName());
    }

    public function test_it_gets_country_from_numeric_code()
    {
        $country = $this->countries->getByNumericCode(124);

        $this->assertInstanceOf(\Orpheus\LaravelCountries\Country::class, $country);
        $this->assertEquals('Canada', $country->getOfficialName());
    }

    public function test_it_gets_countries_by_region()
    {
        $results = $this->countries->getByRegion(\Countries::$REGION_AMERICAS);
        $codes = array_column($results, 'cca2');

        $this->assertContainsOnlyInstancesOf(\Orpheus\LaravelCountries\Country::class, $results);
        $this->assertArrayHasKey('CA', $results);
        $this->assertArrayNotHasKey('FR', $results);
    }

    public function test_it_gets_countries_by_subregion()
    {
        $results = $this->countries->getBySubregion('North America');
        $codes = array_column($results, 'cca2');

        $this->assertContainsOnlyInstancesOf(\Orpheus\LaravelCountries\Country::class, $results);
        $this->assertArrayHasKey('CA', $results);
        $this->assertArrayNotHasKey('FR', $results);
    }

    public function test_it_gets_countries_by_currency()
    {
        $results = $this->countries->getByCurrency('CAD');

        $this->assertContainsOnlyInstancesOf(\Orpheus\LaravelCountries\Country::class, $results);
        $this->assertArrayHasKey('CA', $results);
        $this->assertArrayNotHasKey('FR', $results);
    }

    public function test_it_gets_all_countries()
    {
        $results = $this->countries->getAll();

        $this->assertContainsOnlyInstancesOf(\Orpheus\LaravelCountries\Country::class, $results);
    }

    public function test_it_gets_all_countries_as_collections()
    {
        $results = $this->countries->getAll([], true);

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $results);
        $this->assertContainsOnlyInstancesOf(\Orpheus\LaravelCountries\Country::class, $results);
        $this->assertInstanceOf(\Orpheus\LaravelCountries\Country::class, $results->first());
    }

    public function test_it_gets_only_filtered_countries_by_cca2()
    {
        $results = $this->countries->getAll(['MT', 'CA'], true);

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $results);
        $this->assertContainsOnlyInstancesOf(\Orpheus\LaravelCountries\Country::class, $results);
        $this->assertEquals(2, $results->count());
        $this->assertTrue('Malta' === ($results->first())->getCommonName());
    }

    public function test_it_gets_only_filtered_countries_by_cca3()
    {
        $results = $this->countries->getAll(['MLT', 'CAN'], true);

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $results);
        $this->assertContainsOnlyInstancesOf(\Orpheus\LaravelCountries\Country::class, $results);
        $this->assertEquals(2, $results->count());
        $this->assertTrue('Malta' === ($results->first())->getCommonName());
    }
    
    public function test_it_gets_only_filtered_countries_by_ccn3()
    {
        $results = $this->countries->getAll(['470', '124'], true);

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $results);
        $this->assertContainsOnlyInstancesOf(\Orpheus\LaravelCountries\Country::class, $results);
        $this->assertEquals(2, $results->count());
        $this->assertTrue('Malta' === ($results->first())->getCommonName());
    }

    public function test_it_returns_list_form_dropdown()
    {
        // Test the key parameter
        $results = $this->countries->getListForDropdown('cca2');
        $this->assertArrayHasKey('US', $results);

        $results = $this->countries->getListForDropdown('cca3');
        $this->assertArrayHasKey('USA', $results);

        // Test the Official parameter
        $results = $this->countries->getListForDropdown('cca3', $official = false);
        $this->assertEquals('United States', $results['USA']);

        $results = $this->countries->getListForDropdown('cca3', $official = true);
        $this->assertEquals('United States of America', $results['USA']);

        // Test the Localization parameter
        $results = $this->countries->getListForDropdown('cca3', $official = false, null);
        $this->assertEquals('United States', $results['USA']);

        $results = $this->countries->getListForDropdown('cca3', $official = false, 'fra');
        $this->assertEquals('États-Unis', $results['USA']);

        $results = $this->countries->getListForDropdown('cca3', $official = true, 'fra');
        $this->assertEquals("Les états-unis d'Amérique", $results['USA']);
    }
}
