<?php

namespace Database\Seeders;

use App\Models\Master\MstSex;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class MstSexSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        MstSex::truncate();
        Schema::enableForeignKeyConstraints();
        
        $statuses = config('constants.types.sex.titles');
        if($statuses != [])
        {
            foreach($statuses as $name) {
                MstSex::updateOrCreate( ['title' =>  $name]);
            }
        }
    }
}
