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

class OpenWeatherService
{

    const API_KEY = '6723df6a02149f85e517ad8d4836c748';

    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'http://samples.openweathermap.org',
            'timeout' => 5.0
        ]);
    }

    public function getWeatherByCity($params)
    {
        if (isset($params->query)) {
            $arrLocalCity = $this->getCityData($params->query);
        }
        $this->getWeatherFromApi(current($arrLocalCity));
    }

    public function getCityData($cityName)
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

    public function getWeatherFromApi($cityId)
    {
        $arrQuery = [
            'query' => [
                'id' => $cityId,
                'appid' => self::API_KEY
            ]
        ];
        $response = $this->client->request('GET', '/data/2.5/forecast', $arrQuery);
        return json_decode($response->getBody());
    }

    public function treatCityName(string $cityName)
    {
        return urldecode(strtolower($cityName));
    }
}