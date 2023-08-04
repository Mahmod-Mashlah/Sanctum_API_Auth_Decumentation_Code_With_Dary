<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command();
        // $schedule->command('inspire')->hourly(); // just a comment example y default

         $schedule->command('sanctum:prune-expired --hours=24')->dailyAt('08:00'); //8:00 am
        //                                              |             |
        //                                              |             |
        //                                              |             |
        //          |------------------------------------             |
        //          |                                                 👇🏻
        //          👇🏻                                            schedular will restart function
        //          tokens ends in the database                   after ...

        }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
