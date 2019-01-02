<?php

namespace Test;

use PHPUnit\Framework\TestCase;
use Weather\Service\WeatherService;

class WeatherServiceTest extends TestCase
{
    /**
     * @var WeatherService
     */
    private $weatherService;

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->weatherService = new WeatherService();
    }

    /**
     * Tests the getWeatherByCity function with city = London
     * @group weather-city1
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testGetWeatherByCity() :void
    {
        $params = new \stdClass();
        $params->query = 'London';
        $result = $this->weatherService->getWeatherByCity($params);
        $this->assertNotEmpty($result);
        $this->assertIsFloat($result->temp->realtime);
        $this->assertIsFloat($result->temp->min);
        $this->assertIsFloat($result->temp->max);
        $this->assertInstanceOf(\stdClass::class, $result);
        $this->assertTrue(isset($result->overall));
        $this->assertIsString($result->overall->desc);
        $this->assertIsInt($result->overall->humidity);
    }

    /**
     * Tests the getWeatherByCity function with cityName empty
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @expectedException \Exception
     */
    public function testGetWeatherByCityWithoutParams() :void
    {
        $params = new \stdClass();
        $params->query = '';
        $this->weatherService->getWeatherByCity($params);
    }


    /**
     * Tests the function weatherCityData
     * @group weather-city
     * @throws \Exception
     */
    public function testWeatherCityData() :void
    {
        $result = $this->weatherService->getCityData('Berlin');
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('coord', $result);
    }

    /**
     * Tests the function weatherCityData without params
     * @group weather-city
     * @expectedException \Exception
     */
    public function testWeatherCityDataWithoutParams() :void
    {
        $this->weatherService->getCityData('');
    }

    /**
     * TemperatureServiceTest the request data from api
     * @group weather-api
     * @throws \GuzzleHttp\Exception\GuzzleException|\Exception
     */
    public function testGetWeatherFromApi() :void
    {
        $arrCityData = $this->weatherService->getCityData('Brasilia');
        $result = $this->weatherService->getWeatherFromApi($arrCityData['id']);
        $this->assertInstanceOf(\stdClass::class, $result);
        $this->assertTrue(isset($result->main));
        $this->assertIsFloat($result->main->temp);
        $this->assertIsFloat($result->main->temp_min);
        $this->assertIsFloat($result->main->temp_max);
    }

    /**
     * TemperatureServiceTest the city's name passed by uri
     * @group city-name
     */
    public function testTreatCityName() :void
    {
        $cityName = urlencode('New York');
        $newName = $this->weatherService->treatCityName($cityName);
        $this->assertFalse(strstr($newName, '%'));
        $this->assertRegExp('/[A-Z]*/', $newName);
    }

    /**
     * TemperatureServiceTest the response inside the api request to check the overall parameters
     * @group overall
     * @throws \GuzzleHttp\Exception\GuzzleException|\Exception
     */
    public function testGetOverall() :void
    {
        $arrCityData = $this->weatherService->getCityData('Brasilia');
        $weatherData = $this->weatherService->getWeatherFromApi($arrCityData['id']);
        $result = $this->weatherService->getOverAll($weatherData);
        $this->assertInstanceOf(\stdClass::class, $result);
        $this->assertTrue(isset($result->desc));
        $this->assertTrue(isset($result->humidity));
        $this->assertIsString($result->desc);
        $this->assertIsInt($result->humidity);
    }
}
