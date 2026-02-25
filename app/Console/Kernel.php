<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\LocalityNotice;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $now = now();

            LocalityNotice::where('is_active', false)
                ->where('start_date', '<=', $now)
                ->where('end_date', '>=', $now)
                ->update(['is_active' => true]);

            LocalityNotice::where('is_active', true)
                ->where('end_date', '<', $now)
                ->update(['is_active' => false]);
        })->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
