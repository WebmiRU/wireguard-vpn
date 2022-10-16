<?php

namespace App\Console\Commands;

use App\Interfaces\ConfigServiceInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class Init extends Command
{
    protected $signature = 'init';
    protected $description = 'Init';

    public function handle(ConfigServiceInterface $configService)
    {
        Artisan::call('migrate', [], $this->getOutput());
        $configService->init();

        $this->info('Init OK');
    }
}
