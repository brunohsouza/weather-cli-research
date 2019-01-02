<?php

namespace Weather\Core;

/**
 * Class Router
 * This class is responsible to manage the routes passed inside requests
 * @package Weather\Core
 */
class Router
{
    /**
     * Request service
     * @var Request
     */
    private $request;

    /**
     * Array that stores the available method that can be used in this app
     * @var array
     */
    private const HTTP_METHODS = ['GET', 'POST', 'PUT', 'DELETE'];

    /**
     * Route passed inside the request
     * @var string
     */
    private $route;

    /**
     * Application name
     * @var string
     */
    private $appName;


    /**
     * Router constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Method responsible to get all callable functions request and manage this
     * @param $name
     * @param $arguments
     * @return mixed|string
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        try {
            $this->verityMethod($name);
            $route = $arguments[0];
            $this->route = $this->formatRoute($route);
            return $this->run();
        } catch (\Error $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Gets the route passed in the uri
     * @param $route
     * @return string
     */
    private function formatRoute($route) :string
    {
        $result = rtrim($route, '/');
        if ($result === '') {
            return '/';
        }
        return $result;
    }

    /**
     * Method responsible to get the controller class and function to carry on the request
     * @return mixed
     * @throws \Exception
     */
    public function run()
    {
        $routeConfig = $this->getRouteConfig();
        foreach ($routeConfig['routes'] as $key => $value) {
            if ($key === $this->route) {
                $this->getAppName();
                $class = $this->appName . "\\Controller\\" . ucfirst($value['controller']) . 'Controller';
                if ($value['method'] === $this->request->getMethod()) {
                    $action = $value['function'];
                }
            }
        }
        if (!isset($class, $action)) {
            throw new \RuntimeException('Not Found', 404);
        }
        $controller = new $class();
        return $controller->$action($this->request->getParams());
    }

    /**
     * Gets the config from route config file
     * @return mixed
     * @throws \Exception
     */
    public function getRouteConfig() :array
    {
        if (file_exists('src/Config/routes.json')) {
            return json_decode(file_get_contents('src/Config/routes.json'), true);
        }
        throw new \InvalidArgumentException('Could not find the route file');
    }

    /**
     * Verifies if the http method is valid
     * @param $method
     * @throws \Exception
     */
    public function verityMethod($method) :void
    {
        if (!in_array(strtoupper($method), self::HTTP_METHODS, true)) {
            throw new \RuntimeException('Method not available');
        }
    }

    /**
     * Gets the application's name inside the namespace
     */
    public function getAppName() :void
    {
        $namespace = explode('\\', __NAMESPACE__);
        $this->appName = $namespace[0];
    }
}
