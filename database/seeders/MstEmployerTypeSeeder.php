<?php

namespace Database\Seeders;

use App\Models\Master\MstEmployerType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class MstEmployerTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        MstEmployerType::truncate();
        Schema::enableForeignKeyConstraints();
        $statuses = config('constants.types.employer_type.titles');
        if($statuses != [])
        {
            foreach($statuses as $name) {
                MstEmployerType::updateOrCreate( ['title' =>  $name]);
            }
        }
    }
}
