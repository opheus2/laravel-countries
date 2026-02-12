<?php

/**
 * PHP var_export() with short array syntax (square brackets) indented 2 spaces.
 *
 * NOTE: The only issue is when a string value has `=>\n[`, it will get converted to `=> [`
 * @link https://www.php.net/manual/en/function.var-export.php
 */
function short_var_export($expression, $return = false)
{
    $export = var_export($expression, true);
    $patterns = [
        "/array \(/" => '[',
        "/^([ ]*)\)(,?)$/m" => '$1]$2',
        "/=>[ ]?\n[ ]+\[/" => '=> [',
        "/([ ]*)(\'[^\']+\') => ([\[\'])/" => '$1$2 => $3',
    ];
    $export = preg_replace(array_keys($patterns), array_values($patterns), $export);
    if ((bool)$return) {
        return $export;
    }
    echo $export;

}

/**
 * Compute callingCodes from idd root + suffixes.
 */
function computeCallingCodes(array $idd): array
{
    $codes = [];
    $root = $idd['root'] ?? '';
    $suffixes = $idd['suffixes'] ?? [];

    if ($root !== '') {
        if (empty($suffixes)) {
            $codes[] = $root;
        } else {
            foreach ($suffixes as $suffix) {
                $codes[] = $root . $suffix;
            }
        }
    }

    return $codes;
}

/**
 * Currency overrides for known upstream issues.
 * These correct data that is missing or obsolete in the restcountries v3.1 source.
 */
function getCurrencyOverrides(): array
{
    return [
        // Cuba: CUC (convertible peso) was eliminated on Jan 1, 2021
        'CU' => [
            'CUP' => ['name' => 'Cuban peso', 'symbol' => '$'],
        ],
        // Bouvet Island: uninhabited Norwegian territory
        'BV' => [
            'NOK' => ['name' => 'Norwegian krone', 'symbol' => 'kr'],
        ],
        // Heard Island and McDonald Islands: uninhabited Australian territory
        'HM' => [
            'AUD' => ['name' => 'Australian dollar', 'symbol' => '$'],
        ],
        // Micronesia: uses USD (sometimes missing upstream)
        'FM' => [
            'USD' => ['name' => 'United States dollar', 'symbol' => '$'],
        ],
        // Antarctica: no official currency, but USD/GBP/EUR used at research stations
        'AQ' => [
            'USD' => ['name' => 'United States dollar', 'symbol' => '$'],
            'GBP' => ['name' => 'Pound sterling', 'symbol' => '£'],
            'EUR' => ['name' => 'Euro', 'symbol' => '€'],
        ],
    ];
}

/**
 * Transform a single country entry from v3.1 format to our format.
 */
function transformCountry(array $country, array $currencyOverrides): array
{
    $cca2 = $country['cca2'];

    // Determine currencies: use override if available, otherwise upstream data
    if (isset($currencyOverrides[$cca2])) {
        $currencies = $currencyOverrides[$cca2];
    } else {
        $currencies = $country['currencies'] ?? [];
    }

    // Select only the translation languages we support
    $supportedLangs = [
        'ara', 'bre', 'ces', 'cym', 'deu', 'est', 'fin', 'fra', 'hrv', 'hun',
        'ind', 'ita', 'jpn', 'kor', 'nld', 'per', 'pol', 'por', 'rus', 'slk',
        'spa', 'srp', 'swe', 'tur', 'urd', 'zho',
    ];
    $translations = [];
    foreach ($supportedLangs as $lang) {
        if (isset($country['translations'][$lang])) {
            $translations[$lang] = $country['translations'][$lang];
        }
    }

    $idd = $country['idd'] ?? ['root' => '', 'suffixes' => []];

    return [
        'name' => [
            'common' => $country['name']['common'],
            'official' => $country['name']['official'],
            'native' => $country['name']['nativeName'] ?? [],
        ],
        'tld' => $country['tld'] ?? [],
        'cca2' => $cca2,
        'ccn3' => $country['ccn3'] ?? '',
        'cca3' => $country['cca3'] ?? '',
        'cioc' => $country['cioc'] ?? '',
        'independent' => $country['independent'] ?? false,
        'status' => $country['status'] ?? '',
        'unMember' => $country['unMember'] ?? false,
        'currencies' => $currencies,
        'idd' => $idd,
        'capital' => $country['capital'] ?? [],
        'altSpellings' => $country['altSpellings'] ?? [],
        'region' => $country['region'] ?? '',
        'subregion' => $country['subregion'] ?? '',
        'languages' => $country['languages'] ?? [],
        'translations' => $translations,
        'latlng' => $country['latlng'] ?? [],
        'landlocked' => $country['landlocked'] ?? false,
        'borders' => $country['borders'] ?? [],
        'area' => $country['area'] ?? 0,
        'flag' => $country['flag'] ?? '',
        'demonyms' => $country['demonyms'] ?? [],
        'callingCodes' => computeCallingCodes($idd),
    ];
}

// --- Main ---

// The data source: restcountries v3.1 JSON
$countriesJsonPath = 'https://gitlab.com/restcountries/restcountries/-/raw/master/src/main/resources/countriesV3.1.json?ref_type=heads';

echo "Fetching countries data from: $countriesJsonPath\n";

$raw = [];
if (filter_var($countriesJsonPath, FILTER_VALIDATE_URL)) {
    $raw = json_decode(file_get_contents($countriesJsonPath), true);
} elseif (file_exists($countriesJsonPath)) {
    $raw = json_decode(file_get_contents($countriesJsonPath), true);
} else {
    throw new Exception(sprintf('Cannot find the file "%s".', $countriesJsonPath));
}

if (empty($raw)) {
    throw new Exception('Failed to parse countries JSON data.');
}

echo sprintf("Loaded %d countries from source.\n", count($raw));

$currencyOverrides = getCurrencyOverrides();
$data = [];
$emptyCurrencies = [];

foreach ($raw as $i => $country) {
    $data[$i] = transformCountry($country, $currencyOverrides);

    if (empty($data[$i]['currencies'])) {
        $emptyCurrencies[] = $data[$i]['cca2'] . ' (' . $data[$i]['name']['common'] . ')';
    }
}

if (!empty($emptyCurrencies)) {
    echo "\nWarning: Countries with empty currencies:\n";
    foreach ($emptyCurrencies as $entry) {
        echo "  - $entry\n";
    }
}

echo sprintf("\nCompiling %d countries into CountriesDataSource.php...\n", count($data));

$stub = file_get_contents(__DIR__.'/CountriesDataSource.stub');
$stub = str_replace('[/*COUNTRIES_DATA*/]', short_var_export($data, true), $stub);

file_put_contents(__DIR__.'/../src/CountriesDataSource.php', $stub);

echo "Done! CountriesDataSource.php has been generated.\n";
