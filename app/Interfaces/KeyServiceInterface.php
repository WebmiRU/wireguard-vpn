<?php

namespace App\Interfaces;

interface KeyServiceInterface
{
    public function private(): ?string;
    public function public(string $key): ?string;
    public function couple(): ?array;
}
