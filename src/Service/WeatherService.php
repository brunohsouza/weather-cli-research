<?php

namespace Weather\Service;

use GuzzleHttp\Client;
use JsonMachine\JsonMachine;
use Weather\Service\TemperatureService;

/**
 * Class WeatherService
 * @package Weather\Service
 */
class WeatherService
{

    /**
     * Key necessary to consume the OpenWeatherAPI
     */
    private const API_KEY = '6723df6a02149f85e517ad8d4836c748';

    /**
     * Service to get and treat the temperature as Celsius
     * @var TemperatureService
     */
    private $tempService;

    /**
     * Guzzle Client that request data to Api
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * WeatherService constructor.
     */
    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'http://api.openweathermap.org',
            'timeout' => 5.0
        ]);
        $this->tempService = new TemperatureService();
    }

    /**
     * Method to manage and return weather data
     * @param $params
     * @return \stdClass
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function getWeatherByCity($params) : \stdClass
    {
        $weather =  new \stdClass();
        if (isset($params->query) && !empty($params->query)) {
            $arrLocalCity = $this->getCityData($params->query);
            $weatherData = $this->getWeatherFromApi(current($arrLocalCity));
            $weather->temp = $this->tempService->getTemperature($weatherData);
            $weather->overall = $this->getOverAll($weatherData);
            return $weather;
        }
        throw new \Exception('Please, type a city name to get the weather information');
    }

    /**
     * To make the request to OpenWeatherApi is recommended to use the city id instead of city name.
     * Because of this, the application data has a big json file with the dataset with the necessary information
     * This method makes a parse from this file and return am array within given city information
     * @param $cityName
     * @return array|mixed
     * @throws \Exception
     */
    public function getCityData($cityName) :array
    {
        if (is_string($cityName) && !empty($cityName)) {
            $cityName = $this->treatCityName($cityName);
            $arrLocalCity = [];
            $jsonFile = 'data/city.list.json';
            if (file_exists($jsonFile)) {
                $jsonStream = JsonMachine::fromFile($jsonFile);
                foreach ($jsonStream as $city) {
                    if (strtolower($city['name']) === strtolower($cityName)) {
                        $arrLocalCity = $city;
                    }
                }
            }
            return $arrLocalCity;
        }
        throw new \Exception('No city passed to get data');
    }

    /**
     * Method to make the request to OpenWeatherApi
     * @param $cityId
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getWeatherFromApi($cityId)
    {
        /*$filemock = 'data/mock.weather.json';
        if (file_exists($filemock)) {
            return $this->getMockCity($filemock);
        }*/
        $arrQuery = [
            'query' => [
                'id' => $cityId,
                'appid' => self::API_KEY
            ]
        ];
        $response = $this->client->request('GET', '/data/2.5/weather', $arrQuery);
        return json_decode($response->getBody());
    }

    /**
     * Treats the name of the city taking off the url encode and putting the string in lowercase
     * @param string $cityName
     * @return string
     */
    public function treatCityName(string $cityName)
    {
        return urldecode(strtolower($cityName));
    }

    /**
     * If there is an information about the city requested in a file, this function returns this file data
     * @param $filemock
     * @return mixed
     */
    public function getMockCity($filemock) :object
    {
        return json_decode(file_get_contents($filemock));
    }

    /**
     * Method to get the overall information like weather description and humidity
     * @param $weatherData
     * @return \stdClass
     */
    public function getOverAll($weatherData)
    {
        $weather = new \stdClass();
        foreach ($weatherData->weather as $key => $desc) {
            $weather->desc[$key] = $desc->main;
        }
        $weather->desc = implode(', ', $weather->desc);
        $weather->humidity = $weatherData->main->humidity;
        return $weather;
    }
}