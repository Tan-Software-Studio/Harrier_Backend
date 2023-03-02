<?php

namespace Database\Seeders;

use App\Models\Master\MstWorkingArrangements;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class MstWorkingArrangementsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        MstWorkingArrangements::truncate();
        Schema::enableForeignKeyConstraints();
        
        $statuses = config('constants.types.working_arrangements.titles');
        if($statuses != [])
        {
            foreach($statuses as $name) {
                MstWorkingArrangements::updateOrCreate( ['title' =>  $name]);
            }
        }
    }
}
