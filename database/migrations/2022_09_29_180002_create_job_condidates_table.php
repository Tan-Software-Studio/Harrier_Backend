<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateJobCondidatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
    */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('job_candidates');
        
        Schema::create('job_candidates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_id');
            $table->foreignUuid('c_uid');
            $table->boolean('is_cv')->default(0)->comment('1=requested, 2=accepted, 3=rejected');
            $table->date('request_date', $precision = 0)->nullable();
            $table->date('accepted_date', $precision = 0)->nullable();
            $table->date('rejected_date', $precision = 0)->nullable();
            $table->boolean('interview_request')->nullable()->comment('0=No, 1=Yes');
            $table->date('interview_request_date', $precision = 0)->nullable();
            $table->date('offer_accepted_date', $precision = 0)->nullable();
            
            $table->double('offer_salary', 15, 2)->default('0.00');
            $table->unsignedBigInteger('offer_salary_symbol')->nullable();

            $table->double('offer_bonus_commission', 15, 2)->default('0.00');
            $table->unsignedBigInteger('offer_bonus_commission_symbol')->nullable();

            $table->string('cv')->collation('utf8mb4_general_ci')->nullable();
            $table->unsignedBigInteger('c_job_status')->nullable()->comment('Admin update candidate job status');
            $table->date('start_date', $precision = 0)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
            $table->foreign('job_id')->references('id')->on('jobs')->onDelete('cascade');
            $table->foreign('c_uid')->references('uuid')->on('candidates');
            $table->foreign('c_job_status')->references('id')->on('mst_candidate_job_statuses');

            $table->foreign('offer_salary_symbol')->references('id')->on('mst_currencies');
            $table->foreign('offer_bonus_commission_symbol')->references('id')->on('mst_currencies');
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
        Schema::dropIfExists('job_candidates');
    }
}
