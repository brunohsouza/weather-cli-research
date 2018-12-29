<?php
/**
 * Created by PhpStorm.
 * User: brunosouza
 * Date: 25/12/18
 * Time: 18:56
 */

namespace Weather\Core;

include_once 'IRequest.php';

use Weather\Core\IRequest;

class Request implements \Weather\Core\IRequest
{
    private $method;

    private $path;

    private $id;

    private $params;

    /**
     * Request constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        // get the HTTP method passed inside the request
        $this->method = $_SERVER['REQUEST_METHOD'];

        // get the path passed inside the request
        $requestUri = explode('/', $_SERVER['REQUEST_URI']);
        $this->setPath($requestUri);

        //set the parameters from request
        $this->setId($requestUri);
        $this->setParams($requestUri);
    }

    /**
     * @return array
     */
    public function getBody()
    {
        $result = [];
        foreach ($_POST as $key => $value) {
            $result[$key] = filter_input_array(INPUT_POST, $key, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }
        return $result;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function setId($request)
    {
        $this->id = $request[3] ?? null;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    public function setPath($path)
    {
        $this->path = $path[1] ?? 'weather';
    }

    /**
     * @return string
     */
    public function getPath() :string
    {
        return $this->path;
    }

    /**
     * @param $request
     */
    public function setParams($request)
    {
        $this->params = new \stdClass();
        if ($this->method === 'POST' || $this->method === 'PUT') {
            $content = $this->getBody();
        }
        if (is_string($request[2])) {
            $this->params->query = $request[2];
        }
        $this->params->id = $this->id;
        $this->params->content = $content ?? null;
    }

    /**
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
    }
}