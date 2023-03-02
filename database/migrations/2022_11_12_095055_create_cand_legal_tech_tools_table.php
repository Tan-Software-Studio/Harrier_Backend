<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCandLegalTechToolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cand_legal_tech_tools', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('c_uuid');
            $table->string('title')->nullable()->collation('utf8mb4_general_ci');
            $table->unsignedBigInteger('mst_id')->nullable();
            $table->foreign('c_uuid')->references('uuid')->on('candidates');
            $table->foreign('mst_id')->references('id')->on('mst_legal_tech_tools');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cand_legal_tech_tools');
    }
}
