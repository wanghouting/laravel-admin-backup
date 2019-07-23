<?php

namespace LTBackup\Extension\Console\Commands;

use Illuminate\Console\Command;
use LTBackup\Extension\Facades\LTBackup;

class ClearCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'ltbackup:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear the ltbackup backup expired files';

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

        LTBackup::clear();
    }



}
