<?php

namespace App\Services;

use App\Interfaces\KeyServiceInterface;

class KeyService implements KeyServiceInterface
{
    public function private(): ?string {
        $stdout = null;
        $code = null;

        exec('wg genkey', $stdout, $code);

        if ($code == 0 && mb_strlen($stdout[0])) { // Private key generation OK
            return $stdout[0];
        }

        return null;
    }

    public function public(string $key_private): ?string {
        $stdout = null;
        $code = null;

        exec("echo '{$key_private}' | wg pubkey", $stdout, $code);

        if ($code == 0 && mb_strlen($stdout[0])) { // Private key generation OK
            return $stdout[0];
        }

        return null;
    }

    public function couple(): ?array {
        $private = $this->private();
        $public = $this->public($private);

        if($private && $public) {
            return [
                'private' => $private,
                'public' => $public,
            ];
        }

        return null;
    }
}
