<?php

namespace App\Services;

use App\Interfaces\ConfigServiceInterface;
use App\Interfaces\KeyServiceInterface;
use App\Models\Client;
use App\Models\Config;
use Illuminate\Support\Facades\DB;

class ConfigService implements ConfigServiceInterface
{
    protected string $path;

    public function __construct(string $path = null)
    {
        $this->path = $path ?? base_path('wg0.conf');
    }

    public function init(): void
    {
        $port = env('VPN_PORT', 52000);
        $keyService = app(KeyServiceInterface::class);

        $f = fopen($this->path, 'w+');
        flock($f, LOCK_EX);
        fwrite($f, "[Interface]\n");
        fwrite($f, "PrivateKey = {$keyService->private()}\n");
        fwrite($f, "ListenPort = {$port}\n\n");
        fwrite($f, "\n");
        fclose($f);

        $this->fillClientPool();
    }

    public function peerAppend(string $ip4, string $keyPublic): void
    {
        $f = fopen($this->path, 'a');
        flock($f, LOCK_EX);
        fwrite($f, "[Peer]\n");
        fwrite($f, "PublicKey = {$keyPublic}\n");
        fwrite($f, "AllowedIPs = {$ip4}/32\n\n");
        fclose($f);
    }

    public function fillClientPool(bool $fillKeyPrivate = false, bool $fillKeyPublic = false)
    {
        $keyService = app(KeyServiceInterface::class);
        DB::beginTransaction();

        $ip4Network = env('VPN_IP4_NETWORK', '192.168.93.0');
        $ip4Mask = env('VPN_IP4_MASK', 24);

        $ip4Counter = pow(2, 32 - $ip4Mask);
        $ip4Start = ip2long($ip4Network);

        for ($i = 0; $i < $ip4Counter; $i++) {
            if ($i == 0) {
                Config::updateOrCreate(['key' => 'network_ip4'], ['value' => long2ip($ip4Start + $i)]);
                Config::updateOrCreate(['key' => 'network_mask_ip4'], ['value' => $ip4Mask]);
            } elseif ($i == 1) {
                Config::updateOrCreate(['key' => 'server_ip4'], ['value' => long2ip($ip4Start + $i)]);
            } elseif (($i + 1) < $ip4Counter) {
                $keyPrivate = null;
                $keyPublic = null;

                if ($fillKeyPublic) {
                    $keyPrivate = $keyService->private();
                    $keyPublic = $keyService->public($keyPrivate);
                } elseif ($fillKeyPrivate) {
                    $keyPrivate = $keyService->private();
                }

                $ip4_client = long2ip($ip4Start + $i);

                Client::updateOrCreate([
                    'ip4' => $ip4_client,
                ], [
                    'key_private' => $keyPrivate,
                    'key_public' => $keyPublic,
                ]);
            } elseif ($i + 1 == $ip4Counter) {
                Config::updateOrCreate(['key' => 'broadcast_ip4'], ['value' => long2ip($ip4Start + $i)]);
            }
        }

        DB::commit();
    }
}
