<?php

namespace App\Interfaces;

interface ConfigServiceInterface
{
    public function init();
    public function peerAppend(string $ip4, string $keyPublic);
}
