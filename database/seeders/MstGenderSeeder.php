<?php

namespace Database\Seeders;

use App\Models\Master\MstGender;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class MstGenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        MstGender::truncate();
        Schema::enableForeignKeyConstraints();
        
        $statuses = config('constants.types.gender.titles');
        if($statuses != [])
        {
            foreach($statuses as $name) {
                MstGender::updateOrCreate( ['title' =>  $name]);
            }
        }
    }
}
