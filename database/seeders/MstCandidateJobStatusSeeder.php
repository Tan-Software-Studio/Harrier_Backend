<?php

namespace Database\Seeders;

use App\Models\Master\MstCandidateJobStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class MstCandidateJobStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        MstCandidateJobStatus::truncate();
        Schema::enableForeignKeyConstraints();
        $statuses = config('constants.types.candidate_job_status.titles');
        if($statuses != [])
        {
            foreach($statuses as $name) {
                MstCandidateJobStatus::updateOrCreate( ['title' =>  $name]);
            }
        }
    }
}
