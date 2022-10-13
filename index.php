<?php

const DB_FILE = 'vpn.db';

//if (file_exists(DB_FILE)) {
//    return 0;
//}


$db = new SQLite3(DB_FILE, SQLITE3_OPEN_READWRITE);
$db->exec('BEGIN;');


//$ver = $db->querySingle('SELECT SQLITE_VERSION()');
$db->exec("DROP TABLE IF EXISTS client;");
$db->exec("DROP TABLE IF EXISTS config;");


$db->exec("CREATE TABLE IF NOT EXISTS client (id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, IPv4 TEXT NOT NULL, key_private TEXT NOT NULL, key_public TEXT, is_granted INTEGER DEFAULT 0, handshake_at TEXT, active_at TEXT);");
$db->exec("CREATE TABLE IF NOT EXISTS config (key TEXT NOT NULL PRIMARY KEY, value TEXT);");

$network_mask = 24;
$network = '192.168.93.0';
$server_port = 52000;
$server_key_private = null;

$IPv4_counter = pow(2, 32 - $network_mask);
$IPv4_start = ip2long($network);
$server_IPv4 = null;
$peers = [];

function generate_keys(): array {
    $stdout = null;
    $code = null;

    exec('wg genkey', $stdout, $code);

    if ($code == 0) { // Private key generation OK
        $key_private = $stdout[0];
        exec("echo '{$key_private}' | wg pubkey", $stdout, $code);
    } else {
        throw new Exception('Private key generation error');
    }

    if($code == 0) { // Public key generation OK
        $key_public = $stdout[0];
    } else {
        throw new Exception('Public key generation error');
    }

    return [
        'private' => $key_private,
        'public' => $key_public,
    ];
}

function config_insert(string $key, string $value): void {
    global $db;

    $query = $db->prepare('INSERT INTO config (key, value) VALUES (:key, :value)');
    $query->bindValue(':key', $key, SQLITE3_TEXT);
    $query->bindValue(':value', $value, SQLITE3_TEXT);
    $query->execute();
}

for ($i = 0; $i < $IPv4_counter; $i++) {
    if ($i == 0) {
        config_insert('network_IPv4', long2ip($IPv4_start + $i));
        config_insert('network_mask_IPv4', $network_mask);
    } elseif ($i == 1) {
        config_insert('server_IPv4', long2ip($IPv4_start + $i));
    } elseif (($i + 1) < $IPv4_counter) {
        $keys = generate_keys();

        $IPv4_client = long2ip($IPv4_start + $i);
        $query = $db->prepare('INSERT INTO client (IPv4, key_private, key_public) VALUES (:IPv4, :key_private, :key_public)');
        $query->bindValue(':IPv4', $IPv4_client, SQLITE3_TEXT);
        $query->bindValue(':key_private', $keys['private'], SQLITE3_TEXT);
        $query->bindValue(':key_public', $keys['public'], SQLITE3_TEXT);
        $query->execute();

        $peers[] = [
            'IPv4' => $IPv4_client,
            'key_private' => $keys['private'],
            'key_public' => $keys['public'],
        ];
    } elseif ($i + 1 == $IPv4_counter) {
        config_insert('broadcast_IPv4', long2ip($IPv4_start + $i));
    }
}

$db->exec('COMMIT;');

function config_file_write(array $peers, string $file = 'wg0.conf') {
    global $server_port;
    $keys = generate_keys();

    $f = fopen($file, 'w+');
    fwrite($f, "[Interface]\n");
    fwrite($f, "PrivateKey = {$keys['private']}\n");
    fwrite($f, "ListenPort = {$server_port}\n\n");

    foreach ($peers as $v) {
        fwrite($f, "[Peer]\n");
        fwrite($f, "PublicKey = {$v['key_public']}\n");
        fwrite($f, "AllowedIPs = {$v['IPv4']}/32\n\n");
    }

    fwrite($f, "\n");
    fclose($f);
}

config_file_write($peers);

//SQLITE3_INTEGER