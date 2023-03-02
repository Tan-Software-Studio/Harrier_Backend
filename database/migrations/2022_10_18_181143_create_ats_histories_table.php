<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateAtsHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ats_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_candidate_id');
            $table->unsignedBigInteger('c_job_status')->nullable()->comment('Admin update candidate job status');
            $table->longText('note')->nullable();
            $table->date('date', $precision = 0)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->foreign('job_candidate_id')->references('id')->on('job_candidates');
            $table->foreign('c_job_status')->references('id')->on('mst_candidate_job_statuses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ats_histories');
    }
}
