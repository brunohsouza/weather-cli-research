<?php
/**
 * Created by PhpStorm.
 * User: brunosouza
 * Date: 26/12/18
 * Time: 01:00
 */

namespace Weather\Service;

use GuzzleHttp\Client;
use JsonMachine\JsonMachine;
use Weather\Service\TemperatureService;

/**
 * Class OpenWeatherService
 * @package Weather\Service
 */
class OpenWeatherService
{

    /**
     * Key necessary to consume the OpenWeatherAPI
     */
    const API_KEY = '6723df6a02149f85e517ad8d4836c748';

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
     * OpenWeatherService constructor.
     */
    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'http://samples.openweathermap.org',
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
        if (isset($params->query)) {
            $arrLocalCity = $this->getCityData($params->query);
            $weatherData = $this->getWeatherFromApi(current($arrLocalCity));
            $weather->temp = $this->tempService->getTempFahrenheit($weatherData);
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
     */
    public function getCityData($cityName) :array
    {
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

    /**
     * Method to make the request to OpenWeatherApi
     * @param $cityId
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getWeatherFromApi($cityId)
    {
        $filemock = 'data/mock.weather.json';
        if (file_exists($filemock)) {
            return $this->getMockCity($filemock);
        }
        die('234');
        $arrQuery = [
            'query' => [
                'id' => $cityId,
                'appid' => self::API_KEY
            ]
        ];
        $response = $this->client->request('GET', '/data/2.5/forecast', $arrQuery);
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
        $weather->desc = $weatherData->list[0]->weather[0]->description;
        $weather->humidity = $weatherData->list[0]->main->humidity;
        return $weather;
    }
}