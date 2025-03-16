<?php

namespace App\MessageHandler;

use App\Message\UpdateWeatherData;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class UpdateWeatherDataHandler implements MessageHandlerInterface
{
    private $cache;
    private $apiKey;

    public function __construct()
    {
        $this->cache = new FilesystemAdapter();
        $this->apiKey = $_ENV['OPENWEATHERMAP_API_KEY'];
    }

    public function __invoke(UpdateWeatherData $message)
    {
        $city = $message->getCity();
        $countryCode = $message->getCountryCode();

        // Update current weather
        $currentCacheKey = "weather_current_{$city}_{$countryCode}";
        $currentCachedData = $this->cache->getItem($currentCacheKey);

        $client = HttpClient::create();
        $response = $client->request('GET', 'https://api.openweathermap.org/data/2.5/weather', [
            'query' => [
                'q' => "{$city},{$countryCode}",
                'appid' => $this->apiKey,
                'units' => 'metric'
            ]
        ]);

        if ($response->getStatusCode() === 200) {
            $data = $response->toArray();
            $weatherData = [
                'temperature' => $data['main']['temp'],
                'feelsLike' => $data['main']['feels_like'],
                'humidity' => $data['main']['humidity'],
                'windSpeed' => $data['wind']['speed'],
                'description' => $data['weather'][0]['description'],
                'icon' => $data['weather'][0]['icon'],
                'timestamp' => time()
            ];

            $currentCachedData->set($weatherData);
            $currentCachedData->expiresAfter(3600); // Cache for 1 hour
            $this->cache->save($currentCachedData);
        }

        // Update forecast
        $forecastCacheKey = "weather_forecast_{$city}_{$countryCode}";
        $forecastCachedData = $this->cache->getItem($forecastCacheKey);

        $response = $client->request('GET', 'https://api.openweathermap.org/data/2.5/forecast', [
            'query' => [
                'q' => "{$city},{$countryCode}",
                'appid' => $this->apiKey,
                'units' => 'metric'
            ]
        ]);

        if ($response->getStatusCode() === 200) {
            $data = $response->toArray();
            $forecastData = array_map(function ($item) {
                return [
                    'temperature' => $item['main']['temp'],
                    'feelsLike' => $item['main']['feels_like'],
                    'humidity' => $item['main']['humidity'],
                    'windSpeed' => $item['wind']['speed'],
                    'description' => $item['weather'][0]['description'],
                    'icon' => $item['weather'][0]['icon'],
                    'timestamp' => strtotime($item['dt_txt'])
                ];
            }, array_slice($data['list'], 0, 5)); // Get next 5 forecasts

            $forecastCachedData->set($forecastData);
            $forecastCachedData->expiresAfter(3600); // Cache for 1 hour
            $this->cache->save($forecastCachedData);
        }
    }
} 