<?php

namespace Database\Seeders;

use App\Models\Master\MstCandidateStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class MstCandidateStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        MstCandidateStatus::truncate();
        Schema::enableForeignKeyConstraints();
        $statuses = config('constants.types.candidate_status.titles');
        if($statuses != [])
        {
            foreach($statuses as $name) {
                MstCandidateStatus::updateOrCreate( ['title' =>  $name]);
            }
        }
    }
}
