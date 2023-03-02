<?php

use App\Models\ATSView;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateAtsView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!DB::statement("DROP VIEW IF EXISTS view_ats_data;"))
        {
            DB::statement("
            ALTER VIEW view_ats_data AS
            SELECT job_candidates.id as c_job_id, employers.name as employers_name, jobs.job_title, jobs.created_at, jobs.salary_range_start,jobs.salary_range_start_symbol, jobs.salary_range_end, jobs.salary_range_end_symbol, job_candidates.is_cv, job_candidates.request_date, job_candidates.accepted_date, job_candidates.rejected_date, job_candidates.interview_request, job_candidates.interview_request_date, job_candidates.offer_accepted_date, candidates.id as c_id, candidates.uuid as c_uuid, job_candidates.offer_salary, job_candidates.offer_salary_symbol, job_candidates.offer_bonus_commission, job_candidates.offer_bonus_commission_symbol, job_candidates.c_job_status, job_candidates.start_date FROM employers JOIN jobs ON jobs.emp_uid = employers.uuid JOIN job_candidates ON job_candidates.job_id = jobs.id JOIN candidates ON candidates.uuid = job_candidates.c_uid ORDER BY job_candidates.created_at DESC;
            "); 
                
        }else{
            DB::statement("
            CREATE VIEW view_ats_data AS
            SELECT job_candidates.id as c_job_id, employers.name as employers_name, jobs.job_title, jobs.created_at, jobs.salary_range_start,jobs.salary_range_start_symbol, jobs.salary_range_end, jobs.salary_range_end_symbol, job_candidates.is_cv, job_candidates.request_date, job_candidates.accepted_date, job_candidates.rejected_date, job_candidates.interview_request, job_candidates.interview_request_date, job_candidates.offer_accepted_date, candidates.id as c_id, candidates.uuid as c_uuid, job_candidates.offer_salary, job_candidates.offer_salary_symbol, job_candidates.offer_bonus_commission, job_candidates.offer_bonus_commission_symbol, job_candidates.c_job_status, job_candidates.start_date FROM employers JOIN jobs ON jobs.emp_uid = employers.uuid JOIN job_candidates ON job_candidates.job_id = jobs.id JOIN candidates ON candidates.uuid = job_candidates.c_uid ORDER BY job_candidates.created_at DESC;
            ");
        }
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS view_user_data");
    }
}
