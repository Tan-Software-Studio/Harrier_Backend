<?php

namespace Database\Seeders;

use App\Models\Master\MstCustomerType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class MstCustomerTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        MstCustomerType::truncate();
        Schema::enableForeignKeyConstraints();
        $statuses = config('constants.types.customer_type.titles');
        if($statuses != [])
        {
            foreach($statuses as $name) {
                MstCustomerType::updateOrCreate( ['title' =>  $name]);
            }
        }
    }
}
