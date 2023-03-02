<?php

namespace App\Jobs;

use App\Http\Controllers\CronJobController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class TokenExpireJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::table('sessions')->updateOrInsert(
            ['key' => 'expire_datetime'],
            ['value' => date('Y-m-d H:i:s')]
        );
        CronJobController::oneHourExpire();
        \Log::info("Cron Job is working fine Guest Token Expired!");
    }
}
