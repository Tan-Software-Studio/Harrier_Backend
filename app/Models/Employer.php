<?php

namespace App\Models;

use App\Models\Emp\EmpOfficeLocation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\File;

class Employer extends Model
{
    use HasFactory, Notifiable, SoftDeletes;
    
    protected $table="employers";

    protected $fillable = [
        'uuid',
        'name',/* Required*/
        'email',
        'email_verified_at',
        'password',
        'is_request',
        'is_login',
        'status',
        'uk_address',/* Required*/
        'hq_address',
        'billing_address',/* Required*/
        'contact_details', /* Required*/
        'logo',
        'url',
        'is_terms_and_conditions', /* Required*/
        'is_marketing_sign_up',
        'currency_code'
    ];

    protected $appends = ['logo_path'];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime:Y-m-d H:i:s',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
    
    public function emp_office_locations()
    {
        return $this->belongsTo(EmpOfficeLocation::class, 'uuid', 'emp_uuid');
    }

    public function emp_job_list()
    {
        return $this->belongsTo(Job::class, 'uuid', 'emp_uid');
    }

    public function getLogoPathAttribute()
    {
        if(!empty($this->logo)){
            if (File::exists(logo_public_path().$this->logo)) 
            {
                return logo_file_show($this->logo);
            }
        }
    }
}