<?php

namespace LTBackup\Extension\Console\Commands;

use Illuminate\Console\Command;
use LTBackup\Extension\Databases\Seeders\LTUpdateDatabaseSeeder;

class InstallCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'ltbackup:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the ltbackup package';

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
        $this->initDatabase();

    }

    /**
     * Create tables and seed it.
     *
     * @return void
     */
    public function initDatabase()
    {
        !is_dir(storage_path('logs/backup')) && mkdir(storage_path('logs/backup'),0777,true);
        $this->call('migrate');
        $this->call('db:seed', ['--class' => LTUpdateDatabaseSeeder::class]);
    }


}
