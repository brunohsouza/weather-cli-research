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

    private $request;

    /**
     * Request constructor.
     * @throws \Exception
     */
    function __construct()
    {
        // get the HTTP method passed inside the request
        $this->method = $_SERVER['REQUEST_METHOD'];

        // get the path passed inside the request
        $this->request = explode('/', $_REQUEST['path']);
        $this->path = $this->request[0];
        $this->handlerRequest();
    }

    public function getBody()
    {
        $result = [];
        foreach ($_POST as $key => $value) {
            $result[$key] = filter_input_array(INPUT_POST, $key, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }
        return $result;
    }

    public function getQuery()
    {
        $result = [];
        foreach ($_GET as $key => $value) {
            $result[$key] = filter_input_array(INPUT_GET, $key, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }
        return $result;
    }

    /**
     * @throws \Exception
     */
    public function handlerRequest()
    {
        if ($this->method === 'POST' || $this->method === 'PUT') {
            $this->params = $this->getBody();
        }

        if ($this->method === 'GET' || $this->method === 'DELETE')
        {
            $this->params = $this->getQuery();
        }
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getId()
    {
        if (is_int($this->request[1])) {
            return $this->id = $this->request[1];
        }
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getParams()
    {
        $this->request['id'] = $this->getId();
        return $this->request;
    }


}