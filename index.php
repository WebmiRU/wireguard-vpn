<?php

//if (file_exists(DB_FILE)) {
//    return 0;
//}

require_once __DIR__ . '/lib/DB.php';

$db = new DB();
//$key = new Key();

$IPv4_mask = 24;
$IPv4_network = '192.168.93.0';
$server_port = 52000;

$db->fill_client_pool($IPv4_network, $IPv4_mask);

//$db1->commit();

//function config_file_write(array $peers, string $path = 'wg0.conf')
//{
//    global $server_port;
//    $keys = generate_keys();
//
//    $f = fopen($path, 'w+');
//    fwrite($f, "[Interface]\n");
//    fwrite($f, "PrivateKey = {$keys['private']}\n");
//    fwrite($f, "ListenPort = {$server_port}\n\n");
//
//    foreach ($peers as $v) {
//        fwrite($f, "[Peer]\n");
//        fwrite($f, "PublicKey = {$v['key_public']}\n");
//        fwrite($f, "AllowedIPs = {$v['IPv4']}/32\n\n");
//    }
//
//    fwrite($f, "\n");
//    fclose($f);
//}
//
//config_file_write($peers);

//SQLITE3_INTEGER

$url = rtrim($_SERVER['PATH_INFO'], '/');
$method = $_SERVER['REQUEST_METHOD'];

switch ($url) {
    case '/api/user':
        switch ($method) {
            case 'GET':

                break;
            case 'POST':

                break;
            case 'PUT':

                break;
        }
        break;

    default:
        http_response_code(404);
}



//echo '<pre>';
//print_r($url);