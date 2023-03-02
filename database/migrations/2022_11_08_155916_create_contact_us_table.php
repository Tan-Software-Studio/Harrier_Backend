<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactUsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_us', function (Blueprint $table) {
            $table->id();
            $table->string('name')->collation('utf8mb4_general_ci')->nullable();
            $table->string('email')->collation('utf8mb4_general_ci')->nullable();
            $table->string('phone')->collation('utf8mb4_general_ci')->nullable();
            $table->string('subject')->collation('utf8mb4_general_ci')->nullable();
            $table->text('message')->collation('utf8mb4_general_ci')->nullable();
            $table->enum('is_read', [0, 1])->default(0)->comment('0=no, 1=yes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contact_us');
    }
}
