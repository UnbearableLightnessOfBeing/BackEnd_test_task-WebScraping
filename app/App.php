<?php

declare(strict_types = 1);

namespace App;

use App\Exceptions\RouteNotFoundException;

class App
{
    private static DB $db;

    public function __construct(protected Router $router, protected array $request, protected Config $config)
    {
        static::$db = new DB($config->db ?? []);
    }

    public static function db(): DB
    {
        return static::$db;
    }

    public function run()
    {
        try {
            header('Content-Type: application/json');
            $args = array();

            echo $this->router->resolve($this->request['uri'], strtolower($this->request['method']), $args);
        } catch (RouteNotFoundException) {
            http_response_code(404);
            echo json_encode([
               'status' => '404',
               'message' => 'Resource was not found'
           ]);
        }
    } 


    public static function getArrayOfParams($uri): array {
        $request = str_split($uri);
        array_shift($request);
    
       return explode('/', implode($request));
    
    }
}
