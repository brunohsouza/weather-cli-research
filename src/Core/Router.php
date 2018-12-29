<?php
/**
 * Created by PhpStorm.
 * User: brunosouza
 * Date: 25/12/18
 * Time: 18:57
 */

namespace Weather\Core;

class Router
{
    private $request;

    private $httpMethods = ['GET', 'POST', 'PUT', 'DELETE'];

    private $route;


    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param $method
     * @param $arguments
     * @return \Exception
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        try {
            $this->verityMethod($name);
            $route = $arguments[0];
            $this->route = $this->formatRoute($route);
            $this->run();
        } catch (\Error $exception) {
            return new \Exception($exception->getMessage());
        }
    }

    private function formatRoute($route)
    {
        $result = rtrim($route, '/');
        if ($result === '') {
            return '/';
        }
        return $result;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function run()
    {
        $routeConfig = $this->getRouteConfig();
        foreach ($routeConfig['routes'] as $key => $value) {
            if ($key === $this->route) {
                $class = "Weather\\Controller\\" . ucfirst($value['controller']) . "Controller";
                if ($value['method'] === $this->request->getMethod()) {
                    $action = $value['function'];
                }
            }
        }
        if (!isset($class) || !isset($action)) {
            throw new \Exception('Not Found', 404);
        }
        $controller = new $class();
        return $controller->$action($this->request->getParams());
    }

    public function getRouteConfig()
    {
        if (file_exists('src/Config/routes.json')) {
            return json_decode(file_get_contents('src/Config/routes.json'), true);
        }
    }

    /**
     * @param $method
     * @throws \Exception
     */
    public function verityMethod($method) :void
    {
        if (!in_array(strtoupper($method), $this->httpMethods)) {
            throw new \Exception('Method not available');
        }
    }

}