<?php
/**
 * Created by PhpStorm.
 * User: brunosouza
 * Date: 25/12/18
 * Time: 18:57
 */

namespace Weather\Core;

interface IRequest
{
    public function getBody();

    public function getQuery();
}