<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpCandidatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emp_candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('c_uuid');
            $table->foreignUuid('emp_uuid');
            $table->timestamps();
            $table->foreign('c_uuid')->references('uuid')->on('candidates');
            $table->foreign('emp_uuid')->references('uuid')->on('employers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('emp_candidates');
    }
}
