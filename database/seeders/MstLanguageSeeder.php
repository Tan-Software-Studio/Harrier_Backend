<?php

namespace Database\Seeders;

use App\Models\Master\MstLanguage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class MstLanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        MstLanguage::truncate();
        Schema::enableForeignKeyConstraints();
        $statuses = config('constants.types.languages.titles');
        if($statuses != [])
        {
            foreach($statuses as $name) {
                MstLanguage::updateOrCreate( ['title' =>  $name]);
            }
        }
    }
}
