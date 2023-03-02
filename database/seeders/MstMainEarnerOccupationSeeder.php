<?php

namespace Database\Seeders;

use App\Models\Master\MstMainEarnerOccupation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class MstMainEarnerOccupationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        MstMainEarnerOccupation::truncate();
        Schema::enableForeignKeyConstraints();
        
        $statuses = config('constants.types.main_earner_occupations.titles');
        if($statuses != [])
        {
            foreach($statuses as $name) {
                MstMainEarnerOccupation::updateOrCreate( ['title' =>  $name]);
            }
        }
    }
}
