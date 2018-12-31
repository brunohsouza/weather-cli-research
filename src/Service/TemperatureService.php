<?php

namespace Weather\Service;

/**
 * Class TemperatureService
 * @package Weather\Service
 */
class TemperatureService
{

    /**
     * Receives the city data object and pass to the function to become the fahrenheit degrees to celsius
     * @param $dataCity
     * @return \stdClass
     * @throws \Exception
     */
    public function getTemperature($dataCity) : \stdClass
    {
        if (isset($dataCity->main)) {
            $dataTemp = $dataCity->main;
            $temp = new \stdClass();
            $temp->realtime = $this->kelvinToCelsius($dataTemp->temp);
            $temp->min = $this->kelvinToCelsius($dataTemp->temp_min);
            $temp->max = $this->kelvinToCelsius($dataTemp->temp_max);
            return $temp;
        }
        throw new \Exception('No data passed to get the temperature');
    }

    /**
     * Transform temperatures from Fahrenheit to Celsius
     * @param float $temp
     * @return float
     */
    public function fahrenheitToCelsius(float $temp) :float
    {
        return (float) number_format(($temp - 32) / 1.8000, 2);
    }

    /**
     * Transform temperatures from Kelvin to Celsius
     * @param float $temp
     * @return float
     */
    public function kelvinToCelsius(float $temp) :float
    {
        return (float) number_format($temp - 273.15, 2);
    }
}