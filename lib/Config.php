<?php

//require_once __DIR__ . '/Key.php';

class Config
{
    public function __construct(
        protected string $path = 'wg0.conf',
        protected Key $key = new Key(),
    ) {}

    public function init($port) {
        $f = fopen($this->path, 'w+');
        fwrite($f, "[Interface]\n");
        fwrite($f, "PrivateKey = {$this->key->private()}\n");
        fwrite($f, "ListenPort = {$port}\n\n");
        fwrite($f, "\n");
        fclose($f);
    }

    public function peer_append($IPv4, $key_public) {
        $f = fopen($this->path, 'a');
        fwrite($f, "[Peer]\n");
        fwrite($f, "PublicKey = {$key_public}\n");
        fwrite($f, "AllowedIPs = {$IPv4}/32\n\n");
        fclose($f);

        var_dump(101);
    }

//    public function peers_write(array $peers, $port = 5200) {
//        $keys = generate_keys();
//
//        $f = fopen($this->path, 'w+');
//        fwrite($f, "[Interface]\n");
//        fwrite($f, "PrivateKey = {$keys['private']}\n");
//        fwrite($f, "ListenPort = {$port}\n\n");
//
//        foreach ($peers as $v) {
//            fwrite($f, "[Peer]\n");
//            fwrite($f, "PublicKey = {$v['key_public']}\n");
//            fwrite($f, "AllowedIPs = {$v['IPv4']}/32\n\n");
//        }
//
//        fwrite($f, "\n");
//        fclose($f);
//    }
}