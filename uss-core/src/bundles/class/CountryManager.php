<?php

class CountryManager 
{
    protected string $json = UssImmutable::ASSETS_DIR . '/JSON/countries.min.json';
    protected array $countries;

    public function __construct(protected bool $throwOutOfBoundException = true)
    {
        $contents = file_get_contents($this->json);
        $this->countries = json_decode($contents);
    }

    /**
     * Get all available countries
     * 
     * @param bool $nameInclusive Whether to return only the ISO-2 code or the country name as well
     * @return array
     */
    public function getAllCountries(bool $nameInclusive = false): array
    {
        $countryList = [];
        foreach($this->countries as $index => $country) {
            $key = $nameInclusive ? $country->iso_2 : $index;
            $value = $nameInclusive ? $country->name : $country->iso_2;
            $countryList[$key] = $value;
        }
        return $countryList;
    }

    /**
     * Get full information about a particular country
     * 
     * @param string $iso Either an ISO-2 or ISO-3 code indication which country info to return
     * @return ?array full information about the country
     */
    public function getCountryInfo(string $iso): ?array
    {
        return ($result = $this->searchCountry($iso, __METHOD__)) ? (array)$result : null;
    }

    /**
     * Get the full name of a country based on it's ISO code
     * 
     * @param string $iso The ISO 2/3 code of the country
     * @return ?string The country full name
     */
    public function getCountryName(string $iso): ?string
    {
        return $this->searchCountry($iso, __METHOD__)?->name;
    }

    /**
     * Get the ISO-2 code of a country
     * 
     * @param string $iso The ISO 2/3 code of the country
     * @return string The country ISO-2
     */
    public function getCountryISO2(string $iso): string
    {
        return $this->searchCountry($iso, __METHOD__)?->iso_2;
    }

    /**
     * Get the ISO-3 code of a country
     * 
     * @param string $iso The ISO 2/3 code of the country
     * @return string The country ISO-3
     */
    public function getCountryISO3(string $iso): string
    {
        return $this->searchCountry($iso, __METHOD__)?->iso_3;
    }

    /**
     * Get the flag (image) url of a country from CDN
     * 
     * @param string $iso The ISO 2/3 of the country
     * @return string An image url of the country flag
     */
    public function getCountryFlag(string $iso): string
    {
        return $this->searchCountry($iso, __METHOD__)?->flag;
    }

    /**
     * Get the country code of a specified country
     * 
     * @param string $iso The ISO 2/3 of the country
     * @return int The country code (internation telephone)
     */
    public function getCountryCode(string $iso): int
    {
        return $this->searchCountry($iso, __METHOD__)?->code;
    }

    /**
     * Get the currency code of a country
     * 
     * @param string $iso The ISO 2/3 of the country
     * @return string The international currency code
     */
    public function getCountryCurrencyCode(string $iso): string
    {
        return $this->searchCountry($iso, __METHOD__)?->currency_code;
    }

    /**
     * Get the continent code of a country
     * 
     * @param string $iso The ISO 2/3 of the country
     * @return string The continent code
     */
    public function getCountryContinentCode(string $iso): string
    {
        return $this->searchCountry($iso, __METHOD__)?->continent_code;
    }

    /**
     * Get the continent of a country
     * 
     * @param string $iso The ISO 2/3 of the country
     * @return int The full name of the continent
     */
    public function getCountryContinentName(string $iso): string
    {
        return $this->searchCountry($iso, __METHOD__)?->continent;
    }

    /**
     * Search for a country internally
     * 
     * @throws Exception if iso less than 2 or greater than 3
     * @throws Exception if country not found
     * @return ?stdClass The data of the found country
     */
    protected function searchCountry(string $iso, string $method): ?stdClass
    {
        $length = strlen($iso);
        if(!in_array($length, [2, 3], true)) {
            throw new \LengthException(
                sprintf("Invalid Country Code passed to %s() method", $method)
            );
        };
        $iso = strtoupper($iso);
        foreach($this->countries as $country) {
            switch($iso) {
                case $country->iso_2:
                case $country->iso_3:
                    return $country;
            }
        };
        if($this->throwOutOfBoundException) {
            throw new \OutOfBoundsException(
                sprintf("Unknown ISO-%s Country Code \"%s\" passed to %s() method", $length, $iso, $method)
            );
        }
        return null;
    }
}