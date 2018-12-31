<?php

namespace Weather\Controller;

use Weather\Service\WeatherService;

/**
 * Class WeatherController
 * This class is used on request made by applications
 * @package Weather\Controller
 */
class WeatherController
{
    /**
     * Receives the weatherService
     * @var WeatherService
     */
    private $weatherService;

    /**
     * WeatherController constructor.
     */
    public function __construct()
    {
        $this->weatherService = new WeatherService();
    }

    /**
     * Fetch the weather of a given city
     * @param $params
     * @return \stdClass|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetch($params)
    {
        try {
            return $this->weatherService->getWeatherByCity($params);
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }
}