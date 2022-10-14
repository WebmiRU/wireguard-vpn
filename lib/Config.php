<?php

class Config
{
    public function __construct(
        protected Key $key,
    ) {}

    public function init($port, $path = 'wg0.conf') {
        $f = fopen($path, 'w+');
        fwrite($f, "[Interface]\n");
        fwrite($f, "PrivateKey = {$this->key->private()}\n");
        fwrite($f, "ListenPort = {$port}\n\n");
        fwrite($f, "\n");
        fclose($f);
    }

    public function peer_append() {

    }

    public function peers_write(array $peers, $port = 5200, string $path = 'wg0.conf') {
        $keys = generate_keys();

        $f = fopen($path, 'w+');
        fwrite($f, "[Interface]\n");
        fwrite($f, "PrivateKey = {$keys['private']}\n");
        fwrite($f, "ListenPort = {$port}\n\n");

        foreach ($peers as $v) {
            fwrite($f, "[Peer]\n");
            fwrite($f, "PublicKey = {$v['key_public']}\n");
            fwrite($f, "AllowedIPs = {$v['IPv4']}/32\n\n");
        }

        fwrite($f, "\n");
        fclose($f);
    }
}