<?php

namespace LTBackup\Extension\Console\Commands;

use Illuminate\Console\Command;
use LTBackup\Extension\Facades\LTBackup;

class BackupCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'ltbackup:backup {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the ltbackup backup';

    /**
     * Install directory.
     *
     * @var string
     */
    protected $directory = '';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $all = $this->option('all');
        LTBackup::run($all);
    }



}
