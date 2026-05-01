<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class LogClear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'log:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menghapus semua file log di storage/logs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        exec('rm -f ' . storage_path('logs/*.log'));
        $this->info('Log berhasil dihapus!');
    }
}
