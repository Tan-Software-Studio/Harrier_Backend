<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('sortname')->collation('utf8mb4_general_ci')->nullable();
            $table->string('country_name')->collation('utf8mb4_general_ci')->nullable();
            $table->string('currency_name')->collation('utf8mb4_general_ci')->nullable();
            $table->string('currency_code')->collation('utf8mb4_general_ci')->nullable();
            $table->string('symbol')->collation('utf8mb4_general_ci')->nullable();
            $table->string('phonecode')->collation('utf8mb4_general_ci')->nullable();
            $table->boolean('status')->default(1)->comment('0=Inactive, 1=Active');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('countries');
        Schema::enableForeignKeyConstraints();
    }
}
