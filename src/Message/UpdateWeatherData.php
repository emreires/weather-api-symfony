<?php

namespace App\Message;

class UpdateWeatherData
{
    private $city;
    private $countryCode;

    public function __construct(string $city, string $countryCode)
    {
        $this->city = $city;
        $this->countryCode = $countryCode;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }
} 