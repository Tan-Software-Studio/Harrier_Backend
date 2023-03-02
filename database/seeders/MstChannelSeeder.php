<?php

namespace Database\Seeders;

use App\Models\Master\MstChannel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class MstChannelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        MstChannel::truncate();
        Schema::enableForeignKeyConstraints();
        $statuses = config('constants.types.channel.titles');
        if($statuses != [])
        {
            foreach($statuses as $name) {
                MstChannel::updateOrCreate( ['title' =>  $name]);
            }
        }
    }
}
