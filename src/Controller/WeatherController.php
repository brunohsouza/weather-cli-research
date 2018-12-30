<?php
/**
 * Created by PhpStorm.
 * User: brunosouza
 * Date: 25/12/18
 * Time: 21:34
 */

namespace Weather\Controller;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Weather\Service\OpenWeatherService;
use Symfony\Component\Console\Command\Command;

class WeatherController extends Command
{

    private $openWeatherService;

    private $input;

    private $output;

    public function __construct()
    {
        $this->openWeatherService = new OpenWeatherService();
        parent::__construct();
    }

    public function configure()
    {
        $this->setName('weather')
             ->setDescription('Application to query the weather on a local city')
             ->setHelp('-h');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|\stdClass|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->input = $input;
            $this->output = $output;
            $helper = $this->getHelper('question');

            $cityNameQuestion = new Question('Type a name of a city that you want to know the weather: ');
            $cityNameAnswer = $helper->ask($input, $output, $cityNameQuestion);

            $weatherData = $this->openWeatherService->getWeatherByCity($this->prepareParams($cityNameAnswer));

            $this->showsOutput($weatherData);
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    public function showsOutput($weatherData)
    {
        $this->output->writeln(
            ucfirst($weatherData->overall->desc) . ', ' .
            ' Temperature in ' . $weatherData->temp->realtime . ' degrees Celsius'
        );

        $this->output->writeln('Minimum Temperature Forecast: ' . $weatherData->temp->min . ' degrees Celsius');
        $this->output->writeln('Maximum Temperature Forecast: ' . $weatherData->temp->max . ' degrees Celsius');
        $this->output->writeln('Relative humidity: ' . $weatherData->overall->humidity . '%');
    }

    public function prepareParams($cityName)
    {
        $params = new \stdClass();
        $params->query = $cityName;
        return $params;
    }
       

    /**
     * @param $params
     * @return \stdClass|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetch($params)
    {
        try {
            return $this->openWeatherService->getWeatherByCity($params);
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }
}