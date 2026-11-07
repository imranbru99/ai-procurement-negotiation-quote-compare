<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DemoResetCommand extends Command
{
    protected $signature = 'demo:reset {--fresh : Run migrate:fresh --seed (default)}';

    protected $description = 'Reset the database to demo seed data (migrate:fresh --seed)';

    public function handle(): int
    {
        if (! app()->environment(['local', 'staging', 'testing']) && ! env('DEMO_RESET_ENABLED', false)) {
            $this->error('demo:reset is disabled outside local/staging unless DEMO_RESET_ENABLED=true.');

            return self::FAILURE;
        }

        $this->warn('Running migrate:fresh --seed …');

        return $this->call('migrate:fresh', ['--seed' => true, '--force' => true]);
    }
}
