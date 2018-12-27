<?php
/**
 * Created by PhpStorm.
 * User: brunosouza
 * Date: 25/12/18
 * Time: 21:34
 */

namespace Weather\Controller;

use Weather\Service\OpenWeatherService;

class WeatherController
{

    public $openWeatherService;

    public function __construct()
    {
        $this->openWeatherService = new OpenWeatherService();
    }

    /**
     * @param $params
     */
    public function fetch($params)
    {
        return $this->openWeatherService->getWeatherByCity($params);
    }
}