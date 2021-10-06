<?php

namespace App\Console;

use App\Console\Commands\DeleteOldUsers;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('eb_refund_data_sync')
            ->timezone(env('TIME_ZONE_A'))
            ->dailyAt('05:00');

//        $schedule->command('order_data_sync')
//            ->timezone(env('TIME_ZONE_A'))
//            ->dailyAt('05:00');

        $schedule->command('calculate_commission')
            ->timezone(env('TIME_ZONE_A'))
            ->monthlyOn(15, '00:00');
        //        minute hour day_of_month month day_of_week command_to_run
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
