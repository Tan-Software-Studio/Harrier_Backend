<?php

namespace Database\Seeders;

use App\Models\Master\MstCulturalBackground;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class MstCulturalBackgroundSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        MstCulturalBackground::truncate();
        Schema::enableForeignKeyConstraints();
        $statuses = config('constants.types.cultural_background.titles');
        if($statuses != [])
        {
            foreach($statuses as $name) {
                MstCulturalBackground::updateOrCreate( ['title' =>  $name]);
            }
        }
    }
}
