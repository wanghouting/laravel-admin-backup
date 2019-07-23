<?php

namespace LTBackup\Extension\Console\Commands;

use Illuminate\Console\Command;
use LTBackup\Extension\Databases\Seeders\LTBackupDatabaseSeeder;

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
        $this->call('migrate');
        $this->call('db:seed', ['--class' => LTBackupDatabaseSeeder::class]);
        $this->call('vendor:publish', ['--provider'=> "LTBackup\Extension\LaravelServiceProvider"]);
    }


}
