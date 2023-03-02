<?php

namespace Database\Seeders;

use App\Models\Master\MstLegalTechTool;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class MstLegalTechToolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        MstLegalTechTool::truncate();
        Schema::enableForeignKeyConstraints();
        $statuses = config('constants.types.legal_tech_tools.titles');
        if($statuses != [])
        {
            foreach($statuses as $name) {
                MstLegalTechTool::updateOrCreate( ['title' =>  $name]);
            }
        }
    }
}
