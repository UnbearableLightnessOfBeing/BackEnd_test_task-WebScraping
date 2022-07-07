<?php


require_once('./Api.class.php');
require_once('../config/cors.inc.php');
require_once('../config/db.class.php');

header('Content-Type: application/json; charset=utf-8');


$database = new Database();
$db = $database->connect();
$api = new Api($db);

//method option
$method = $_SERVER['REQUEST_METHOD'];

//custom urls
$type = $_GET['q'];
$params = explode('/' ,$type);

$type = $params[0];
@ $id = $params[1];

//calling api methods
if($method === 'GET'){
    if($type === 'posts'){
        if($id){
            $api->getSinglePost($id);
            exit();
        }
        $api->getPosts();
        exit();
    }
}
elseif($method === 'POST'){
    //post methods implementation
}
elseif($method === 'DELETE'){
    //delete methods implementation
}
elseif($method === 'PUT'){
    if($type === 'update-rating'){
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);
        $api->updateRating($data['id'], $data['rating']);
        exit();
    }
}




?>

