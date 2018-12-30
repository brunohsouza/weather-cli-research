<?php
/**
 * Created by PhpStorm.
 * User: brunosouza
 * Date: 25/12/18
 * Time: 17:54
 */

require __DIR__ . '/vendor/autoload.php';

use Weather\Core\Request;
use Weather\Core\Router;

try {
    $request = new Request();
    $router = new Router($request);
    print(json_encode($router->{$request->getMethod()}($request->getPath(), $request)));
} catch (\Exception $exception) {
    return print(json_encode([$exception->getMessage(), $exception->getCode()]));
}


