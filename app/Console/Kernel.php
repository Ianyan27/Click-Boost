<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Run every hour
        $schedule->command('clickup:sync-users')->hourly();
        
        // OR run daily at 2am
        // $schedule->command('clickup:sync-users')->dailyAt('02:00');
        
        // OR run every 6 hours
        // $schedule->command('clickup:sync-users')->everySixHours();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}