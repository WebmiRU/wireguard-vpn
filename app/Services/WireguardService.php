<?php

namespace App\Services;

use App\Interfaces\WireguardServiceInterface;

class WireguardService implements WireguardServiceInterface
{
    public function up(string $interface) {

        $commands1 = [ // Without gateway
            // UP
            'ip link add wg0 type wireguard',
            'wg setconf wg0 /dev/fd/63',
            'ip link set mtu 1420 up dev wg0',
            'ip -4 route add 192.168.93.8/32 dev wg0',
            'ip -4 route add 192.168.93.7/32 dev wg0',
            'ip -4 route add 192.168.93.6/32 dev wg0',
            // DOWN
            'ip link delete dev wg0',
        ];

        $commands2 = [ // With gateway
            // UP
            'ip link add wg0 type wireguard',
            'wg setconf wg0 /dev/fd/63',
            'ip -4 address add 192.168.93.1/24 dev wg0',
            'ip link set mtu 1420 up dev wg0',
            // DOWN
            'ip link delete dev wg0',
        ];
    }

    public function setConfig(string $interface = 'wg0', string $path = null): bool {
        $path = $path ?? base_path('wg0.conf');
        $stdout = null;
        $code = null;

        exec("sudo wg setconf {$interface} {$path}", $stdout, $code);

        if ($code == 0) {
            return true;
        }

        return false;
    }

    public function syncConfig(string $interface = 'wg0', string $path = null): bool {
        $path = $path ?? base_path('wg0.conf');
        $stdout = null;
        $code = null;

        exec("sudo wg syncconf {$interface} {$path}", $stdout, $code);

        if ($code == 0) {
            return true;
        }

        return false;
    }
}
