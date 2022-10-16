<?php

namespace App\Interfaces;

interface WireguardServiceInterface
{
    public function setConfig(string $interface, string $path);
    public function syncConfig(string $interface, string $path);
    public function up(string $interface): bool;
    public function down(string $interface): bool;
}
