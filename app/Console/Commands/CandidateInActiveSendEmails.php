<?php

namespace App\Console\Commands;

use App\Http\Controllers\CronJobController;
use Illuminate\Console\Command;

class CandidateInActiveSendEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:inactive_candidate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Candidate Inactive last 3 months';

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
        CronJobController::inactiveCandidatesList();
        \Log::info("Cron command- Candidate inactive!");
        return 0;
    }
}
