<?php

namespace Weather\Core;

/**
 * Interface IRequest
 * @package Weather\Core
 */
interface IRequest
{
    /**
     * Method designed to get the content from request
     * @return mixed
     */
    public function getBody();

    /**
     * Method designed to get the parameters from request
     * @return mixed
     */
    public function getParams();

    /**
     * Method designed to get the http method from request
     * @return mixed
     */
    public function getMethod();
}
