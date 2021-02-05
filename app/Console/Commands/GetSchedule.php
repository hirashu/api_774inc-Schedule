<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Console\Commands\GetScheduleModule;

class GetSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getschedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        echo 'getscheduleを実行します';
        $module= new GetScheduleModule();
        $module->saveVideoInfo();

        echo 'getscheduleが完了しました';
    }
}
