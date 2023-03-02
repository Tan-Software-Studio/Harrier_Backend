<?php

namespace App\Models;

use App\Models\Master\MstCandidateJobStatus;
use App\Models\Master\MstCurrency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\File;


class JobCondidate extends Model
{
    use HasFactory, SoftDeletes;
    protected $table="job_candidates";

    protected $fillable = [
        'job_id',
        'c_uid',
        'c_job_status',
        'is_cv',
        'request_date',
        'accepted_date',
        'rejected_date',
        'interview_request',
        'interview_request_date',
        'offer_accepted_date',
        'offer_salary',
        'offer_salary_symbol',
        'offer_bonus_commission',
        'offer_bonus_commission_symbol',
        'start_date',
        'cv',
    ];
    protected $appends = [
        'cv_path', 
        'cv_request',
        'job_title',
        'emp_uid',
        'emp_name',
        'c_list'
    ];
   
    protected $hidden = [
        
    ];

    protected $casts = [
        'request_date' => 'date:Y-m-d',
        'accepted_date' => 'date:Y-m-d',
        'interview_requested_date' => 'date:Y-m-d',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function job_list()
    {
        return $this->belongsTo(Job::class, 'job_id', 'id');
    }

    public function candidate_list()
    {
        return $this->belongsTo(Candidate::class, 'c_uid', 'uuid');
    }

    public function candidate_job_status_list()
    {
        return $this->belongsTo(MstCandidateJobStatus::class, 'c_job_status', 'id');
    }

    public function offer_salary_symbol_list()  {   return $this->belongsTo(MstCurrency::class, 'offer_salary_symbol', 'id');   }
    public function offer_bonus_commission_symbol_list()  {   return $this->belongsTo(MstCurrency::class, 'offer_bonus_commission_symbol', 'id');   }

    public function ats_history()
    {
        return $this->hasMany(AtsHistory::class, 'id', 'job_candidate_id');
    }

    /* CV path */
    public function getCvPathAttribute()
    {
        if(!empty($this->cv)){
            if (File::exists(cv_public_path().$this->cv)) 
            {
                return cv_file_show($this->cv);
            }
        }
    }
    public function getCvRequestAttribute()
    {
        if(!empty($this->is_cv_request)){
            return requestHelper($this->is_cv_request);  /* Requested, Accepted, Rejected */
        }
    }
    
    public function getJobTitleAttribute()
    {
        if(!empty($this->job_id)){
            return Job::where('id', $this->job_id)->value('job_title');
        }
    }

    public function getEmpUidAttribute()
    {
        if(!empty($this->job_id)){
            return Job::where('id', $this->job_id)->value('emp_uid');
        }
    }

    public function getEmpNameAttribute()
    {
        if(!empty($this->emp_uid) && $emp = employer_uuid($this->emp_uid)){
            return $emp->name;
        }
    }

    public function getCListAttribute()
    {
        if(!empty($this->c_uid)){
            return c_uuid_list($this->c_uid)->only('name', 'email', 'harrier_candidate'); /*Candidate list */
        }
    }   
}