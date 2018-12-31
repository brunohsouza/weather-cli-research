<?php

namespace Weather\Core;

use Weather\Core\IRequest;

/**
 * Class Request
 * @package Weather\Core
 */
class Request implements IRequest
{
    /**
     * HTTP method from the request
     * @var string
     */
    private $method;

    /**
     * Path from the request
     * @var string
     */
    private $path;

    /**
     * Determined Id from the path data passed from request
     * @var integer
     */
    private $id;

    /**
     * Parameters passed inside the request
     * @var array
     */
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
     * Gets the content inside the request
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
     * Gets the id inside request and stores in id var
     * @return mixed
     * @throws \Exception
     */
    public function setId($request)
    {
        $this->id = $request[3] ?? null;
    }

    /**
     * Gets the method http passed in request
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Gets the path data and stores in path local var
     * @param $path
     */
    public function setPath($path)
    {
        $this->path = $path[1] ?? 'weather';
    }

    /**
     * Returns the path passed inside request
     * @return string
     */
    public function getPath() :string
    {
        return $this->path;
    }

    /**
     * Gets the parameters passed inside the request and stores in params var
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
     * Return the parameters passed inside request
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
    }
}