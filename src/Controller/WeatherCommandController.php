<?php

namespace Weather\Controller;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Weather\Service\WeatherService;

/**
 * Class WeatherCommandController
 * This class is used on request made by CLI
 * @package Weather\Controller
 */
class WeatherCommandController extends Command
{
    /**
     * Receives the weatherService
     * @var WeatherService
     */
    private $weatherService;

    /**
     * Receives the text typed on console
     * @var Input
     */
    private $input;

    /**
     * Send responses to console
     * @var Output
     */
    private $output;

    /**
     * WeatherController constructor.
     */
    public function __construct()
    {
        $this->weatherService = new WeatherService();
        parent::__construct();
    }

    /**
     * Method to start and set the console configuration
     */
    public function configure() :void
    {
        $this->setName('weather')
            ->setDescription('Application to query the weather on a local city')
            ->setHelp('-h');
    }

    /**
     * Execute the actions used in console
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

            $weatherData = $this->weatherService->getWeatherByCity($this->prepareParams($cityNameAnswer));

            $this->showsOutput($weatherData);
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Method that formats the data to show in console
     * @param $weatherData
     */
    public function showsOutput($weatherData): void
    {
        $this->output->writeln(
            ucfirst($weatherData->overall->desc) . ', ' .
            ' Temperature in ' . $weatherData->temp->realtime . ' degrees Celsius'
        );

        $this->output->writeln('Minimum Temperature Forecast: ' . $weatherData->temp->min . ' degrees Celsius');
        $this->output->writeln('Maximum Temperature Forecast: ' . $weatherData->temp->max . ' degrees Celsius');
        $this->output->writeln('Relative Humidity: ' . $weatherData->overall->humidity . '%');
    }

    /**
     * Method to prepare the parameters to search the city weather
     * @param $cityName
     * @return \stdClass
     */
    public function prepareParams($cityName) :\stdClass
    {
        $params = new \stdClass();
        $params->query = $cityName;
        return $params;
    }
}