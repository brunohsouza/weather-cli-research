<?php
/**
 * Created by PhpStorm.
 * User: brunosouza
 * Date: 26/12/18
 * Time: 01:00
 */

namespace Weather\Service;


use GuzzleHttp\Client;

class OpenWeatherService
{

    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://openweathermap.org/api',
            'timeout' => 2.0,
            'key' => '6723df6a02149f85e517ad8d4836c748'
        ]);
    }

    public function getWeatherByCity($cityName)
    {
        $this->getCityId($cityName);
        $params = 'q=' . strtolower($cityName);
//        var_dump($this->client->request('GET', $params));die;
    }

    public function getCityId($cityName)
    {
        if (file_exists('data/city.list.json')) {
            $jsonCity = json_decode(file_get_contents('data/city.list.json'));
            var_dump($jsonCity);die;
        }
    }
}