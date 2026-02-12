<?php

namespace Orpheus\LaravelCountries\Tests;

use Orpheus\LaravelCountries\Facades\Countries;

class CountriesRepositoryTest extends LaravelCountriesTestCase
{
    public function test_it_gets_country_from_alpha2_code()
    {
        $country = $this->countries->getByCode('CA');
        $this->assertInstanceOf(\Orpheus\LaravelCountries\Country::class, $country);
        $this->assertEquals('Canada', $country->getOfficialName());

        $country = $this->countries->getByCode('ca');
        $this->assertInstanceOf(\Orpheus\LaravelCountries\Country::class, $country);
        $this->assertEquals('Canada', $country->getOfficialName());
    }

    public function test_it_gets_country_from_alpha3_code()
    {
        $country = $this->countries->getByCode('CAN');
        $this->assertInstanceOf(\Orpheus\LaravelCountries\Country::class, $country);
        $this->assertEquals('Canada', $country->getOfficialName());

        $country = $this->countries->getByCode('can');
        $this->assertInstanceOf(\Orpheus\LaravelCountries\Country::class, $country);
        $this->assertEquals('Canada', $country->getOfficialName());
    }

    public function test_it_gets_country_from_numeric_code()
    {
        $country = $this->countries->getByCode(124);

        $this->assertInstanceOf(\Orpheus\LaravelCountries\Country::class, $country);
        $this->assertEquals('Canada', $country->getOfficialName());
    }

    public function test_it_gets_countries_by_region()
    {
        $results = $this->countries->getByRegion(Countries::$REGION_AMERICAS);
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

    // --- getByCode() ---

    public function test_it_gets_country_by_cca2_code()
    {
        $country = $this->countries->getByCode('CA');
        $this->assertInstanceOf(\Orpheus\LaravelCountries\Country::class, $country);
        $this->assertEquals('Canada', $country->getCommonName());
    }

    public function test_it_gets_country_by_cca2_code_case_insensitive()
    {
        $country = $this->countries->getByCode('ca');
        $this->assertInstanceOf(\Orpheus\LaravelCountries\Country::class, $country);
        $this->assertEquals('Canada', $country->getCommonName());
    }

    public function test_it_gets_country_by_cca3_code()
    {
        $country = $this->countries->getByCode('CAN');
        $this->assertInstanceOf(\Orpheus\LaravelCountries\Country::class, $country);
        $this->assertEquals('Canada', $country->getCommonName());
    }

    public function test_it_gets_country_by_numeric_code()
    {
        $country = $this->countries->getByCode(124);
        $this->assertInstanceOf(\Orpheus\LaravelCountries\Country::class, $country);
        $this->assertEquals('Canada', $country->getCommonName());
    }

    public function test_it_gets_country_by_numeric_string_code()
    {
        $country = $this->countries->getByCode('124');
        $this->assertInstanceOf(\Orpheus\LaravelCountries\Country::class, $country);
        $this->assertEquals('Canada', $country->getCommonName());
    }

    public function test_it_returns_null_for_invalid_code()
    {
        $this->assertNull($this->countries->getByCode('XXXX'));
        $this->assertNull($this->countries->getByCode('ZZ'));
        $this->assertNull($this->countries->getByCode(999));
    }

    // --- getByName() ---

    public function test_it_searches_countries_by_partial_name()
    {
        $results = $this->countries->getByName('canad');
        $this->assertNotEmpty($results);
        $this->assertArrayHasKey('CA', $results);
    }

    public function test_it_searches_countries_by_name_case_insensitive()
    {
        $results = $this->countries->getByName('CANADA');
        $this->assertArrayHasKey('CA', $results);
    }

    public function test_it_returns_empty_for_no_name_match()
    {
        $results = $this->countries->getByName('zzzznonexistent');
        $this->assertEmpty($results);
    }

    // --- getByFullName() ---

    public function test_it_searches_by_exact_full_name()
    {
        $results = $this->countries->getByFullName('Canada');
        $this->assertNotEmpty($results);
        $this->assertArrayHasKey('CA', $results);
    }

    public function test_it_does_not_match_partial_on_full_name()
    {
        $results = $this->countries->getByFullName('Canad');
        $this->assertEmpty($results);
    }

    public function test_it_matches_official_full_name()
    {
        $results = $this->countries->getByFullName('United States of America');
        $this->assertNotEmpty($results);
        $this->assertArrayHasKey('US', $results);
    }

    // --- getByLanguage() ---

    public function test_it_searches_by_language_code()
    {
        $results = $this->countries->getByLanguage('fra');
        $this->assertNotEmpty($results);
        $this->assertArrayHasKey('FR', $results);
    }

    public function test_it_searches_by_language_name()
    {
        $results = $this->countries->getByLanguage('French');
        $this->assertNotEmpty($results);
        $this->assertArrayHasKey('FR', $results);
    }

    public function test_it_returns_empty_for_unknown_language()
    {
        $results = $this->countries->getByLanguage('Klingon');
        $this->assertEmpty($results);
    }

    // --- getByCapital() ---

    public function test_it_searches_by_capital()
    {
        $results = $this->countries->getByCapital('Ottawa');
        $this->assertNotEmpty($results);
        $this->assertArrayHasKey('CA', $results);
    }

    public function test_it_searches_by_partial_capital()
    {
        $results = $this->countries->getByCapital('Ott');
        $this->assertNotEmpty($results);
        $this->assertArrayHasKey('CA', $results);
    }

    public function test_it_returns_empty_for_unknown_capital()
    {
        $results = $this->countries->getByCapital('Nonexistentville');
        $this->assertEmpty($results);
    }

    // --- getByDemonym() ---

    public function test_it_searches_by_demonym()
    {
        $results = $this->countries->getByDemonym('Canadian');
        $this->assertNotEmpty($results);
        $this->assertArrayHasKey('CA', $results);
    }

    public function test_it_searches_by_demonym_case_insensitive()
    {
        $results = $this->countries->getByDemonym('canadian');
        $this->assertNotEmpty($results);
        $this->assertArrayHasKey('CA', $results);
    }

    public function test_it_returns_empty_for_unknown_demonym()
    {
        $results = $this->countries->getByDemonym('Martian');
        $this->assertEmpty($results);
    }

    // --- getByTranslation() ---

    public function test_it_searches_by_translation()
    {
        $results = $this->countries->getByTranslation('Alemania');
        $this->assertNotEmpty($results);
        $this->assertArrayHasKey('DE', $results);
    }

    public function test_it_searches_by_translation_partial()
    {
        $results = $this->countries->getByTranslation('Saksamaa');
        $this->assertNotEmpty($results);
        $this->assertArrayHasKey('DE', $results);
    }

    // --- getIndependent() ---

    public function test_it_gets_independent_countries()
    {
        $results = $this->countries->getIndependent(true);
        $this->assertNotEmpty($results);

        foreach ($results as $country) {
            $this->assertTrue($country->independent);
        }
    }

    public function test_it_gets_non_independent_countries()
    {
        $results = $this->countries->getIndependent(false);
        $this->assertNotEmpty($results);

        foreach ($results as $country) {
            $this->assertFalse($country->independent);
        }
    }

    public function test_independent_and_non_independent_equal_total()
    {
        $independent = $this->countries->getIndependent(true);
        $nonIndependent = $this->countries->getIndependent(false);
        $all = $this->countries->getRawData();

        $this->assertEquals(count($all), count($independent) + count($nonIndependent));
    }

    // --- Deprecated methods still work ---

    public function test_deprecated_getByAlpha2Code_still_works()
    {
        $country = @$this->countries->getByAlpha2Code('CA');
        $this->assertInstanceOf(\Orpheus\LaravelCountries\Country::class, $country);
        $this->assertEquals('Canada', $country->getCommonName());
    }

    public function test_deprecated_getByAlpha3Code_still_works()
    {
        $country = @$this->countries->getByAlpha3Code('CAN');
        $this->assertInstanceOf(\Orpheus\LaravelCountries\Country::class, $country);
        $this->assertEquals('Canada', $country->getCommonName());
    }

    public function test_deprecated_getByNumericCode_still_works()
    {
        $country = @$this->countries->getByNumericCode(124);
        $this->assertInstanceOf(\Orpheus\LaravelCountries\Country::class, $country);
        $this->assertEquals('Canada', $country->getCommonName());
    }
}
