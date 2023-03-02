<?php

namespace App\Models;

use App\Models\Emp\JobOfficeLocation;
use App\Models\Emp\JobWorkingSchedule;
use App\Models\Master\MstCurrency;
use App\Models\Unique\Country;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\File;

class Job extends Model
{
    use HasFactory, SoftDeletes;
    protected $table="jobs";

    protected $fillable = [
        'emp_uid',
        'job_title',
        'role_overview',
        'salary_range_start',
        'salary_range_start_symbol',
        'salary_range_end',
        'salary_range_end_symbol',
        'candidate_requirements',
        'additional_benefits',
        'status',
        'attach_file'
    ];

    protected $appends = [
        'job_attach_file_path'
        // 'emp_list'
    ];

    protected $hidden = [
        'office_location' => 'array',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    // public function emp_list()
    // {
    //     if(!empty($this->emp_uid)){
    //         return emp_uuid_list($this->emp_uid)->only('name', 'email'); /* Employer list*/
    //     }
    // }

    public function employer_list()
    {
        return $this->belongsTo(Employer::class, 'emp_uid', 'uuid');
    }

    public function working_schedule()
    {
        return $this->hasMany(JobWorkingSchedule::class, 'job_id', 'id');
    }

    public function office_location()
    {
        return $this->hasMany(JobOfficeLocation::class, 'job_id', 'id');
    }

    public function salary_range_start_symbol_list()  {   return $this->belongsTo(MstCurrency::class, 'salary_range_start_symbol', 'id');   }
    public function salary_range_end_symbol_list()  {   return $this->belongsTo(MstCurrency::class, 'salary_range_end_symbol', 'id');   }

    public function job_candidate_list()
    {
        return $this->hasMany(JobCondidate::class, 'job_id', 'id');
    }

    public function getJobAttachFilePathAttribute()
    {
        if(!empty($this->attach_file)){
            if (File::exists(attach_file_public_path().$this->attach_file)) 
            {
                return attach_file_show($this->attach_file);
            }
        }
    }
}
