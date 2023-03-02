<?php

namespace App\Console\Commands;

use App\Http\Controllers\CronJobController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TokenExpire extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:token_expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily 24 hours after guest token expire';

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
        DB::table('sessions')->updateOrInsert(
            ['key' => 'expire_datetime'],
            ['value' => date('Y-m-d H:i:s')]
        );
        CronJobController::oneHourExpire();
        \Log::info("Cron command is working fine Guest Token Expired!");
        return 0;
    }
}
