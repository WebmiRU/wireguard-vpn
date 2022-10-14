<?php

//if (file_exists(DB_FILE)) {
//    return 0;
//}

require_once __DIR__ . '/lib/DB.php';
require_once __DIR__ . '/lib/Config.php';
require_once __DIR__ . '/lib/Key.php';

$db = new DB();
$config = new Config();
$key = new Key();


$IPv4_mask = 24;
$IPv4_network = '192.168.93.0';
$server_port = 52000;

//$db->fill_client_pool($IPv4_network, $IPv4_mask);
$config->init($server_port);

$url = rtrim($_SERVER['PATH_INFO'], '/');
$method = $_SERVER['REQUEST_METHOD'];

$request = null;
$request = json_decode(file_get_contents('php://input'), false);
//var_dump($request);


switch ($url) {
    case '/api/user':
        switch ($method) {
            case 'GET':

                break;
            case 'POST':
                echo api_user_post();
                http_response_code(201);
                break;
            case 'PUT':
                api_user_put();
                break;
        }
        break;

    default:
        http_response_code(404);
}

function api_user_post(): int
{
    global $db;
    global $key;
    global $config;

    $keys = $key->couple();

    $r = $db->peer_append($keys['private'], $keys['public']);
    $config->peer_append($keys['public']);

    if ($r) {
        return 201;
    }

    return 403;
}

function api_user_put()
{
    echo 'API USER PUT';
}

//echo '<pre>';
//print_r($url);