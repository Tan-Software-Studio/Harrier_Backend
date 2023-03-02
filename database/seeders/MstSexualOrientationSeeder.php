<?php

namespace Database\Seeders;

use App\Models\Master\MstSexualOrientation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class MstSexualOrientationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        MstSexualOrientation::truncate();
        Schema::enableForeignKeyConstraints();
        
        $statuses = config('constants.types.sexual_orientation.titles');
        if($statuses != [])
        {
            foreach($statuses as $name) {
                MstSexualOrientation::updateOrCreate( ['title' =>  $name]);
            }
        }
    }
}
