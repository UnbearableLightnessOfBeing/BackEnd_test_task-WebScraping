<?php

declare(strict_types = 1);

use App\App;
use App\Config;
use App\Controllers\HomeController;
use App\Controllers\NewsController;
use App\Controllers\TransactionController;
use App\Router;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();


$router = new Router();

$router
    ->get('/posts', [NewsController::class, 'index'])
    ->get('/posts/{id}', [NewsController::class, 'show'])
    ->put('/posts/{id}', [NewsController::class, 'edit'])
    ->get('/refresh', [NewsController::class, 'refresh']);


(new App(
    $router,
    ['uri' => getRequestUri(), 'method' => $_SERVER['REQUEST_METHOD']],
    new Config($_ENV)
))->run();



function getRequestUri() {

    $request = $_SERVER['REQUEST_URI'];

    $params = App::getArrayOfParams($request);

    if(array_key_exists(1, $params)) {
        $request = str_replace($params[1], '{id}', $request);
    }

    if($request[count(str_split($request)) - 1] === '/') {

        $strArray = str_split($request);
        array_pop($strArray);
        return $request = implode($strArray);

    } else {
        return $request;
    }
}


