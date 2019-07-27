<?php

require __DIR__ . '/vendor/autoload.php';

use Weather\Core\Request;
use Weather\Core\Router;
use Symfony\Component\Dotenv\Dotenv;

try {
    $request = new Request();
    $router = new Router($request);
    $dotenv = new Dotenv();
    $dotenv->loadEnv(__DIR__.'/.env');
    var_dump($_ENV);die;
    print(json_encode($router->{$request->getMethod()}($request->getPath(), $request)));
} catch (\Exception $exception) {
    return print(json_encode([$exception->getMessage(), $exception->getCode()]));
}
