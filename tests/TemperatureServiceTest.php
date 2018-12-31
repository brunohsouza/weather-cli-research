<?php

namespace Test;

use PHPUnit\Framework\TestCase;
use Weather\Service\TemperatureService;
use Weather\Service\WeatherService;

/**
 * Class TemperatureServiceTest
 * @group temp
 * @package Test
 */
class TemperatureServiceTest extends TestCase
{
    /**
     * WeatherService object
     * @var WeatherService
     */
    private $weatherService;

    /**
     * TemperatureService object
     * @var TemperatureService
     */
    private $tempService;

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->weatherService = new WeatherService();
        $this->tempService = new TemperatureService();
    }

    /**
     * Tests the function getTemperature passing a city's name
     * @throws \GuzzleHttp\Exception\GuzzleException | \Exception
     */
    public function testGetTemperature() :void
    {
        $arrCityData = $this->weatherService->getCityData('Brasilia');
        $weatherData = $this->weatherService->getWeatherFromApi($arrCityData['id']);
        $tempData = $this->tempService->getTemperature($weatherData);
        $this->assertInstanceOf(\stdClass::class, $tempData);
        $this->assertIsFloat($tempData->realtime);
        $this->assertIsFloat($tempData->min);
        $this->assertIsFloat($tempData->max);
    }

    /**
     * Tests the function getTemperature passing a city's name
     * @throws \GuzzleHttp\Exception\GuzzleException | \Exception
     * @group temp-no-params
     * @expectedException \Exception
     */
    public function testGetTemperatureWithoutParams() :void
    {
        $arrCityData = $this->weatherService->getCityData('Brasilia');
        $this->weatherService->getWeatherFromApi($arrCityData['id']);
        $this->tempService->getTemperature(new \stdClass());
    }

    /**
     * Tests the convertion from Farhenreit to Celsius
     * @group fah
     */
    public function testFahrenheitToCelsius() :void
    {
        $fahDegrees = 10;
        $result = $this->tempService->fahrenheitToCelsius($fahDegrees);
        $celsiusDegrees = (float) number_format(($fahDegrees- 32) / 1.8000, 2);
        $this->assertIsFloat($result);
        $this->assertEquals($result, $celsiusDegrees);
    }

    /**
     * Tests the convertion from Kelvin to Celsius
     * @group kelvin
     */
    public function testKelvinToCelsius() :void
    {
        $kelvinDegrees = 10;
        $result = $this->tempService->kelvinToCelsius($kelvinDegrees);
        $celsiusDegrees = (float) number_format($kelvinDegrees - 273.15, 2);
        $this->assertIsFloat($result);
        $this->assertEquals($result, $celsiusDegrees);
    }
}
