<?php

namespace Database\Seeders;

use App\Models\Master\MstTechTools;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class MstTechToolsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        MstTechTools::truncate();
        Schema::enableForeignKeyConstraints();
        
        $statuses = config('constants.types.tech_tools.titles');
        if($statuses != [])
        {
            foreach($statuses as $name) {
                MstTechTools::updateOrCreate( ['title' =>  $name]);
            }
        }
    }
}
