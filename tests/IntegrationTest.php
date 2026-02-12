<?php

namespace Orpheus\LaravelCountries\Tests;

class IntegrationTest extends LaravelCountriesTestCase
{
    public function test_it_resolves_from_container()
    {
        $countries = $this->app['countries'];
        $country = $countries->getByAlpha2Code('CA');
        $this->assertEquals('Canada', $country->getOfficialName());
    }

    public function test_it_resolves_from_facade()
    {
        $country = \Countries::getByAlpha2Code('CA');
        $this->assertEquals('Canada', $country->getOfficialName());
    }
}
