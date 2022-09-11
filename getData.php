<?php
require_once('./cors.inc.php');

$jsonData = file_get_contents(__DIR__."/jsonData.txt");
$posts = json_decode($jsonData, true);

// echo '<pre>';
// var_dump($posts);
// echo '</pre>';

print_r($jsonData);