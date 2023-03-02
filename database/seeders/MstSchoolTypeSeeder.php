<?php

namespace Database\Seeders;

use App\Models\Master\MstSchoolType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class MstSchoolTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        MstSchoolType::truncate();
        Schema::enableForeignKeyConstraints();
        
        $statuses = config('constants.types.mst_school_types.titles');
        if($statuses != [])
        {
            foreach($statuses as $name) {
                MstSchoolType::updateOrCreate( ['title' =>  $name]);
            }
        }
    }
}
