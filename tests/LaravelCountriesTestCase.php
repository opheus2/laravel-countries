<?php

namespace Orpheus\LaravelCountries\Tests;

abstract class LaravelCountriesTestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * @var Orpheus\LaravelCountries\CountriesRepository
     */
    protected $countries;

    public function setUp(): void
    {
        parent::setUp();

        $this->countries = $this->app->make('countries');
    }

    /**
     * Get package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \Orpheus\LaravelCountries\ServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Countries' => \Orpheus\LaravelCountries\Facades\Countries::class,
        ];
    }
}
