<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('emp_uid');
            $table->string('job_title')->collation('utf8mb4_general_ci')->nullable();
            $table->text('role_overview')->nullable();

            $table->double('salary_range_start', 15, 2)->default('0.00');
            $table->unsignedBigInteger('salary_range_start_symbol')->nullable();

            $table->double('salary_range_end', 15, 2)->default('0.00');
            $table->unsignedBigInteger('salary_range_end_symbol')->nullable();
            
            $table->text('candidate_requirements')->nullable();
            $table->text('additional_benefits')->nullable();
            $table->boolean('status')->default(1)->comment('0=Inactive, 1=Active');

            $table->string('attach_file')->collation('utf8mb4_general_ci')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();

            $table->foreign('emp_uid')->references('uuid')->on('employers');
            
            $table->foreign('salary_range_start_symbol')->references('id')->on('mst_currencies');
            $table->foreign('salary_range_end_symbol')->references('id')->on('mst_currencies');
            
            
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
        Schema::dropIfExists('jobs');
        Schema::enableForeignKeyConstraints();
        
    }
}
