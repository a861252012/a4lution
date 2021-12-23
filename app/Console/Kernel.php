<?php

namespace App\Console;

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
            ->timezone(config('services.timezone.taipei'))
            ->dailyAt('05:00');

//        $schedule->command('order_data_sync')
//            ->timezone(config('services.timezone.taipei'))
//            ->dailyAt('09:00');

//        $schedule->command('order_data_sync')
//            ->everyMinute()
//            ->withoutOverlapping();

//        $schedule->command('order_data_sync 2021-12-01 2021-12-06')
//            ->timezone(config('services.timezone.taipei'))
//            ->dailyAt('10:54')
//            ->withoutOverlapping();


        $schedule->command('calculate_commission')
            ->timezone(config('services.timezone.taipei'))
            ->monthlyOn(15, '00:00');
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
