<?php

namespace App\Console;

use App\Jobs\CandidateInActive;
use App\Jobs\TokenExpireJob;
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
        // 'App\Console\Commands\DatabaseBackUp',
        Commands\TokenExpire::class,
        Commands\CandidateInActiveSendEmails::class,
    ];
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule) 
    {
        // $schedule->command('cron:token_expire')->everyMinute();
        // $schedule->command('cron:inactive_candidate')->everyMinute();
        // $schedule->command('inspire')->hourly();
        $schedule->job(new TokenExpireJob)->everyMinute();
        $schedule->job(new CandidateInActive)->everyMinute();
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
