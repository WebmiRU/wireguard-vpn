<?php

namespace App\Services;

use App\Interfaces\WireguardServiceInterface;
use App\Models\Client;

class WireguardService implements WireguardServiceInterface
{
    public function up(string $interface = 'wg0'): bool {
        $success = true;
        $ip = long2ip(ip2long(env('VPN_IP4_NETWORK', '192.168.93.0')) + 1);
        $ip4Netmask = env('VPN_IP4_MASK');
        $commands = [
            "sudo ip link add {$interface} type wireguard",
            "sudo wg setconf {$interface} /app/{$interface}.conf",
            "sudo ip -4 address add {$ip}/{$ip4Netmask} dev {$interface}",
            "sudo ip link set mtu 1420 up dev {$interface}",
        ];

        foreach ($commands as $command) {
            $stdout = null;
            $code = null;

            exec($command, $stdout, $code);

            if($code != 0) {
                $success = false;
            }
        }

        $commands1 = [ // Without gateway
            // UP
            'ip link add wg0 type wireguard',
            'wg setconf wg0 /dev/fd/63',
            'ip link set mtu 1420 up dev wg0',
            'ip -4 route add 192.168.93.8/32 dev wg0',
            'ip -4 route add 192.168.93.7/32 dev wg0',
            'ip -4 route add 192.168.93.6/32 dev wg0',
        ];

        return $success;
    }

    public function down(string $interface = 'wg0'): bool {
        $stdout = null;
        $code = null;

        exec("ip link delete dev {$interface}", $stdout, $code);

        if($code == 0) {
            return true;
        }

        return false;
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
