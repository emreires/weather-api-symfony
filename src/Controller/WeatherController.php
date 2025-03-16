<?php

namespace App\Controller;

use App\Entity\FavoriteCity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/weather", name="api_weather_")
 */
class WeatherController extends AbstractController
{
    private $cache;
    private $limiter;
    private $apiKey;

    public function __construct(RateLimiterFactory $limiter)
    {
        $this->cache = new FilesystemAdapter();
        $this->limiter = $limiter;
        $this->apiKey = $_ENV['OPENWEATHERMAP_API_KEY'];
    }

    /**
     * @Route("/current/{city}/{countryCode}", name="current", methods={"GET"})
     */
    public function current(string $city, string $countryCode): JsonResponse
    {
        $limiter = $this->limiter->create($this->getUser()->getEmail());
        if (false === $limiter->consume(1)->isAccepted()) {
            return new JsonResponse(['message' => 'Too many requests'], JsonResponse::HTTP_TOO_MANY_REQUESTS);
        }

        $cacheKey = "weather_current_{$city}_{$countryCode}";
        $cachedData = $this->cache->getItem($cacheKey);

        if ($cachedData->isHit()) {
            return new JsonResponse($cachedData->get());
        }

        $client = HttpClient::create();
        $response = $client->request('GET', 'https://api.openweathermap.org/data/2.5/weather', [
            'query' => [
                'q' => "{$city},{$countryCode}",
                'appid' => $this->apiKey,
                'units' => 'metric'
            ]
        ]);

        if ($response->getStatusCode() !== 200) {
            return new JsonResponse(['message' => 'Weather data not found'], JsonResponse::HTTP_NOT_FOUND);
        }

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

        $cachedData->set($weatherData);
        $cachedData->expiresAfter(3600); // Cache for 1 hour
        $this->cache->save($cachedData);

        return new JsonResponse($weatherData);
    }

    /**
     * @Route("/forecast/{city}/{countryCode}", name="forecast", methods={"GET"})
     */
    public function forecast(string $city, string $countryCode): JsonResponse
    {
        $limiter = $this->limiter->create($this->getUser()->getEmail());
        if (false === $limiter->consume(1)->isAccepted()) {
            return new JsonResponse(['message' => 'Too many requests'], JsonResponse::HTTP_TOO_MANY_REQUESTS);
        }

        $cacheKey = "weather_forecast_{$city}_{$countryCode}";
        $cachedData = $this->cache->getItem($cacheKey);

        if ($cachedData->isHit()) {
            return new JsonResponse($cachedData->get());
        }

        $client = HttpClient::create();
        $response = $client->request('GET', 'https://api.openweathermap.org/data/2.5/forecast', [
            'query' => [
                'q' => "{$city},{$countryCode}",
                'appid' => $this->apiKey,
                'units' => 'metric'
            ]
        ]);

        if ($response->getStatusCode() !== 200) {
            return new JsonResponse(['message' => 'Weather forecast not found'], JsonResponse::HTTP_NOT_FOUND);
        }

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

        $cachedData->set($forecastData);
        $cachedData->expiresAfter(3600); // Cache for 1 hour
        $this->cache->save($cachedData);

        return new JsonResponse($forecastData);
    }

    /**
     * @Route("/favorites", name="favorites", methods={"GET"})
     */
    public function favorites(): JsonResponse
    {
        $user = $this->getUser();
        $favoriteCities = $user->getFavoriteCities();
        $weatherData = [];

        foreach ($favoriteCities as $city) {
            $weatherData[$city->getName()] = $this->current($city->getName(), $city->getCountryCode())->getContent();
        }

        return new JsonResponse($weatherData);
    }
} 