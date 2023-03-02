<?php

namespace Database\Seeders;

use App\Models\Master\MstFaith;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class MstFaithSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        MstFaith::truncate();
        Schema::enableForeignKeyConstraints();
        
        $statuses = config('constants.types.faith.titles');
        if($statuses != [])
        {
            foreach($statuses as $name) {
                MstFaith::updateOrCreate( ['title' =>  $name]);
            }
        }
    }
}
