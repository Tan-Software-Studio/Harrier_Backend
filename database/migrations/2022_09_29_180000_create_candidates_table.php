<?php

use App\Models\Candidate;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCandidatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('candidates');
       

        Schema::create('candidates', function (Blueprint $table) {
            $table->id();            
            $table->string('uuid')->unique()->nullable();
            /* Core Questions 
               -Step one 
            */ 
            $table->string('first_name')->collation('utf8mb4_general_ci')->nullable();
            $table->string('last_name')->collation('utf8mb4_general_ci')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->unique()->nullable();
            $table->string('job_title')->collation('utf8mb4_general_ci')->nullable();
            $table->string('employer')->collation('utf8mb4_general_ci')->nullable();
            $table->string('current_company_url')->nullable()->comment('website url');
            $table->unsignedBigInteger('employer_type')->nullable();
            $table->date('time_in_current_role', $precision = 0)->nullable();
            $table->date('time_in_industry', $precision = 0)->nullable();
            $table->integer('line_management')->default(0);
            $table->string('desired_employer_type')->collation('utf8mb4_general_ci')->nullable();
            $table->unsignedBigInteger('current_country')->nullable();
            $table->unsignedBigInteger('current_region')->nullable();
            $table->text('desired_country')->collation('utf8mb4_general_ci')->nullable();
            $table->text('desired_region')->collation('utf8mb4_general_ci')->nullable();

            $table->double('current_salary', 15, 2)->default('0.00');
            $table->unsignedBigInteger('current_salary_symbol')->nullable();
            
            $table->double('current_bonus_or_commission', 15, 2)->default('0.00');
            $table->unsignedBigInteger('current_bonus_or_commission_symbol')->nullable();

            $table->double('desired_salary', 15, 2)->default('0.00');
            $table->unsignedBigInteger('desired_salary_symbol')->nullable();

            $table->double('desired_bonus_or_commission', 15, 2)->default('0.00');
            $table->unsignedBigInteger('desired_bonus_or_commission_symbol')->nullable();
                        
            $table->integer('notice_period')->nullable();
            $table->boolean('status')->default(1)->comment('1=Active, 2=Passive, 3=Very Passive, 4=Closed');
            $table->unsignedBigInteger('working_arrangements')->nullable()->comment('1=Fulltime office, 2=Fulltime remote, 3=Hybrid');
            $table->text('desired_working_arrangements')->collation('utf8mb4_general_ci')->nullable();

            $table->boolean('freelance_current')->default(0)->comment('0=No, 1=Yes');
            $table->boolean('freelance_future')->default(0)->comment('0=No, 1=Yes');

            $table->string('freelance_daily_rate')->collation('utf8mb4_general_ci')->nullable();
            $table->unsignedBigInteger('freelance_daily_rate_symbol')->nullable();
            /* Role Specific  
               -Step two 
            */
            $table->boolean('law_degree')->default(0)->comment('0=No, 1=Yes');
            $table->boolean('qualified_lawyer')->default(0)->comment('0=No, 1=Yes');
            $table->string('jurisdiction')->collation('utf8mb4_general_ci')->nullable();
            $table->integer('pqe')->nullable()->comment('Post-Qualified Experience numeric');
            $table->string('area_of_law')->collation('utf8mb4_general_ci')->nullable();
            $table->string('legal_experience')->collation('utf8mb4_general_ci')->nullable();
            $table->string('customer_type')->collation('utf8mb4_general_ci')->nullable();
            
            $table->integer('deal_size')->nullable();
            $table->unsignedBigInteger('deal_size_symbol')->nullable();

            $table->integer('sales_quota')->nullable();
            $table->unsignedBigInteger('sales_quota_symbol')->nullable();

            $table->string('languages')->collation('utf8mb4_general_ci')->nullable();
            $table->string('legal_tech_tools')->collation('utf8mb4_general_ci')->nullable();
            $table->string('tech_tools')->collation('utf8mb4_general_ci')->nullable();
            $table->string('qualification')->collation('utf8mb4_general_ci')->nullable();
            $table->string('profile_about', 300)->collation('utf8mb4_general_ci')->nullable();
            $table->boolean('legaltech_vendor_or_consultancy')->default(0)->comment('0=No, 1=Yes');
            /* Diversity & Inclusion
               -Step three
            */
            $table->string('cultural_background')->collation('utf8mb4_general_ci')->nullable();
            $table->string('sex')->collation('utf8mb4_general_ci')->nullable();
            $table->string('gender')->collation('utf8mb4_general_ci')->nullable();
            $table->string('gender_identity')->collation('utf8mb4_general_ci')->nullable();
            $table->string('disability')->collation('utf8mb4_general_ci')->nullable();
            $table->string('first_gen_he')->collation('utf8mb4_general_ci')->nullable();
            $table->string('parents_he')->collation('utf8mb4_general_ci')->nullable();
            $table->string('free_school_meals')->collation('utf8mb4_general_ci')->nullable();
            $table->string('faith')->collation('utf8mb4_general_ci')->nullable();
            $table->string('visa')->collation('utf8mb4_general_ci')->nullable();
            $table->boolean('main_earner_occupation')->default(2)->comment('1=Yes, 2=No, Prefer not to say');
            $table->string('disability_specific')->collation('utf8mb4_general_ci')->nullable();
            $table->string('school_type')->collation('utf8mb4_general_ci')->nullable();
            $table->string('sexual_orientation')->collation('utf8mb4_general_ci')->nullable();
            /*  Data Privacy
                -Step Four
            */
            $table->string('privacy_policy')->collation('utf8mb4_general_ci')->nullable();
            $table->string('harrier_search')->collation('utf8mb4_general_ci')->nullable();
            $table->string('harrier_candidate')->collation('utf8mb4_general_ci')->nullable();
            $table->unsignedBigInteger('channel')->nullable();
            $table->text('channel_other')->nullable()->comment('channel other specify');
            $table->string('referral')->collation('utf8mb4_general_ci')->nullable();
            
            /* Other extra
            */
            $table->string('name')->collation('utf8mb4_general_ci')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->boolean('is_job_search')->default(1)->comment('0=No, 1=Yes');
            $table->string('cv')->collation('utf8mb4_general_ci')->nullable();
            $table->string('profile_image')->collation('utf8mb4_general_ci')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
            
            $table->foreign('employer_type')->references('id')->on('mst_employer_types');
            $table->foreign('current_country')->references('id')->on('countries');
            $table->foreign('current_region')->references('id')->on('mst_regions');
            $table->foreign('current_salary_symbol')->references('id')->on('mst_currencies');
            $table->foreign('current_bonus_or_commission_symbol')->references('id')->on('mst_currencies');
            $table->foreign('desired_salary_symbol')->references('id')->on('mst_currencies');
            $table->foreign('desired_bonus_or_commission_symbol')->references('id')->on('mst_currencies');
            $table->foreign('freelance_daily_rate_symbol')->references('id')->on('mst_currencies');
            $table->foreign('deal_size_symbol')->references('id')->on('mst_currencies');
            $table->foreign('sales_quota_symbol')->references('id')->on('mst_currencies');
            $table->foreign('working_arrangements')->references('id')->on('mst_working_arrangements');
            $table->foreign('channel')->references('id')->on('mst_channels')->onDelete('cascade');

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
        Schema::dropIfExists('candidates');
        Schema::enableForeignKeyConstraints();
    }
}
