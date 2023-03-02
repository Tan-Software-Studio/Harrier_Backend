<?php

namespace Database\Seeders;

use App\Models\Master\MstQualification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class MstQualificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        MstQualification::truncate();
        Schema::enableForeignKeyConstraints();
        
        $statuses = config('constants.types.qualifications.titles');
        if($statuses != [])
        {
            foreach($statuses as $name) {
                MstQualification::updateOrCreate( ['title' =>  $name]);
            }
        }
    }
}
