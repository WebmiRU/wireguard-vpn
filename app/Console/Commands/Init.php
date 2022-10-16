<?php

namespace App\Console\Commands;

use App\Interfaces\ConfigServiceInterface;
use App\Interfaces\WireguardServiceInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class Init extends Command
{
    protected $signature = 'init';
    protected $description = 'Init';

    public function handle(ConfigServiceInterface $configService, WireguardServiceInterface $wireguardService)
    {
        Artisan::call('migrate', [], $this->getOutput());
        $configService->init();
        $wireguardService->up();

        $this->info('Init OK');
    }
}
