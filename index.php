<?php
/**
 * Created by PhpStorm.
 * User: brunosouza
 * Date: 25/12/18
 * Time: 17:54
 */

include_once 'src/Core/Request.php';
include_once 'src/Core/Router.php';
require __DIR__ . '/vendor/autoload.php';

$request = new \Weather\Core\Request();
$router = new \Weather\Core\Router($request);

$router->{$request->getMethod()}($request->getPath(), $request);


