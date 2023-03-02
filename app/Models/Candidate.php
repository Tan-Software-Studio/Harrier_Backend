<?php

namespace App\Models;

use App\Models\Candidate\CandDesiredEmployerTypes;
use App\Models\Emp\EmpCandidate;
use App\Models\Master\MstChannel;
use App\Models\Master\MstCurrency;
use App\Models\Master\MstCustomerType;
use App\Models\Master\MstEmployerType;
use App\Models\Master\MstLanguage;
use App\Models\Master\MstLegalTechTool;
use App\Models\Master\MstQualification;
use App\Models\Master\MstRegion;
use App\Models\Master\MstTechTools;
use App\Models\Master\MstWorkingArrangements;
use App\Models\Unique\Country;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\File;

class Candidate extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table="candidates";
    protected $fillable = [
        'uuid',
        'name',
        'first_name',
        'last_name',
        'phone',
        'email',
        'password',
        'status',
        'job_title',
        'employer',
        
        'employer_type',
        'desired_employer_type',
        
        'time_in_current_role',
        'time_in_industry',
        'line_management',
        
        'current_region',
        'desired_region',

        'current_country',
        'desired_country',

        'current_salary',
        'current_salary_symbol',

        'current_bonus_or_commission',
        'current_bonus_or_commission_symbol',

        'desired_salary',
        'desired_salary_symbol',

        'desired_bonus_or_commission',
        'desired_bonus_or_commission_symbol',

        'notice_period',

        'working_arrangements',
        'desired_working_arrangements',

        'law_degree',
        'qualified_lawyer',
        'jurisdiction',
        'pqe',
        'area_of_law',
        'legal_experience',
        'legaltech_vendor_or_consultancy',
        'customer_type',
        
        'deal_size',
        'deal_size_symbol',

        'sales_quota',
        'sales_quota_symbol',
        
        'legal_tech_tools',
        'tech_tools',
        'qualification',
        'languages',
        'profile_about',
        'cultural_background',
        'first_gen_he',
        'gender',
        'disability',
        'disability_specific',
        'free_school_meals',
        'parents_he',
        'school_type',
        'faith',
        'sex',
        'gender_identity',
        'sexual_orientation',
        'visa',
        'privacy_policy',
        'harrier_search',
        'harrier_candidate',
        'channel',
        'channel_other',
        'referral',
        'is_job_search',
        'freelance_current',
        'freelance_future',
        
        'freelance_daily_rate',
        'freelance_daily_rate_symbol',

        'current_company_url',
        'cv',
        'profile_image',
    ];

    protected $appends = ['profile_path', 'cv_path', 'legal_tech_tools_list', 'tech_tools_list', 'time_in_current_role_diff',
    'time_in_industry_diff','pqe_diff', 'qualification_list', 'customer_type_list',
    'desired_working_arrangements_list', 'desired_country_list', 'languages_list', 'current_freelance', 'open_to_freelance',
    'legaltech_vendor_or_consultancy_list',
    //  'current_country_list', 'employer_type_list', 
    //  'desired_region_list', 'working_arrangements_list', 
    //     'current_salary_amount', 
    //  'desired_salary_amount', 'desired_bonus_or_commission_amount', 'freelance_future_list', 'law_degree_list', 
    //  'qualified_lawyer_list', 'legal_experience_list', 'legal_experience_list',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'customer_type' => 'array',
        'desired_employer_type' => 'array',
        'desired_region' => 'array',
        'desired_country' => 'array',
        'desired_working_arrangements' => 'array',
        
        'cultural_background' => 'array',

        'legal_tech_tools' => 'array',
        'tech_tools' => 'array',
        'qualification' => 'array',
        'languages' => 'array',
        
        'email_verified_at' => 'datetime:Y-m-d H:i:s',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function emp_short_list()  {   return $this->belongsTo(EmpCandidate::class, 'uuid', 'c_uuid');   }

    public function is_cv_list()  {   return $this->belongsTo(JobCondidate::class, 'uuid', 'c_uid');   }
    public function is_cv_list_same_emp()  {   
        $emp = employer(auth()->user()->email);
        $job_ids = Job::where('emp_uid', $emp->uuid)->pluck('id');
        return $this->belongsTo(JobCondidate::class, 'uuid', 'c_uid')->whereIn('job_id', $job_ids);   
    }

    public function country_list()  {   return $this->belongsTo(Country::class, 'current_country', 'id');   }
    public function current_country_list()  {   return $this->belongsTo(Country::class, 'current_country', 'id');   }

    public function employer_type_list()  {   return $this->belongsTo(MstEmployerType::class, 'employer_type', 'id');   }    
    public function desired_employer_types()
    {
        return $this->hasMany(CandDesiredEmployerTypes::class, 'c_uuid', 'uuid');
    }
    /* start currency list*/
    public function current_salary_symbol_list()    {   return $this->belongsTo(MstCurrency::class, 'current_salary_symbol', 'id'); }
    public function current_bonus_or_commission_symbol_list()   {   return $this->belongsTo(MstCurrency::class, 'current_bonus_or_commission_symbol', 'id');    }
    public function desired_salary_symbol_list()    {   return $this->belongsTo(MstCurrency::class, 'desired_salary_symbol', 'id'); }
    public function desired_bonus_or_commission_symbol_list()   {   return $this->belongsTo(MstCurrency::class, 'desired_bonus_or_commission_symbol', 'id');    }
    public function deal_size_symbol_list()    {    return $this->belongsTo(MstCurrency::class, 'deal_size_symbol', 'id');  }
    public function sales_quota_symbol_list()    {    return $this->belongsTo(MstCurrency::class, 'sales_quota_symbol', 'id');  }

    public function channel_list()    {    return $this->belongsTo(MstChannel::class, 'channel', 'id');  }
    /* end currency list*/

    public function current_working_arrangements_list()    {    return $this->belongsTo(MstWorkingArrangements::class, 'working_arrangements', 'id')->select('id', 'title');  }
    public function current_regions_list()    {    return $this->belongsTo(MstRegion::class, 'current_region', 'id')->select('id', 'state_name as title');  }
    public function freelance_daily_rate_symbol_list()    {    return $this->belongsTo(MstCurrency::class, 'freelance_daily_rate_symbol', 'id')->select('id', 'currency_code');  }

    public function getCurrentFreelanceAttribute()
    {
        if(!empty($this->freelance_current)){
            return trueFalse($this->freelance_current);            
        }
    }

    public function getProfilePathAttribute()
    {
        if(!empty($this->logo)){
            if (File::exists(profile_public_path().$this->logo)) 
            {
                return profile_file_show($this->logo);
            }
        }
    }

    public function getOpenToFreelanceAttribute()
    {
        if(!empty($this->freelance_future)){
            return trueFalse($this->freelance_future);    
        }
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

    // /* desired employer types */
    public function getDesiredEmployerTypeListAttribute()
    {
        if(!empty($this->desired_employer_type)){
            return MstEmployerType::whereIn('id', $this->desired_employer_type)->select('title', 'id')->get() ?? [];
        }else{
            return [];
        }
    }
    
    // /* desired country list */
    public function getDesiredCountryListAttribute()
    {
        if(!empty($this->desired_country)){
            return Country::whereIn('id', $this->desired_country)->select('country_name', 'id')->get() ?? [];
        }else{
            return [];
        }
    }



    // /* desired region list */
    // public function getDesiredRegionListAttribute()
    // {
    //     if(!empty($this->desired_region)){
    //         return MstRegion::whereIn('id', json_decode($this->desired_region))->select('title', 'id')->get() ?? [];
    //     }else{
    //         return [];
    //     }
    // }

    // /* working arrangements list list */
    // public function getWorkingArrangementsListAttribute()
    // {
    //     if(!empty($this->working_arrangements)){
    //         return MstWorkingArrangements::whereIn('id', json_decode($this->working_arrangements))->select('title', 'id')->get() ?? [];
    //     }else{
    //         return [];
    //     }
    // }

    // /* desired working arrangements list */
    public function getDesiredWorkingArrangementsListAttribute()
    {
        if(!empty($this->desired_working_arrangements)){
            return MstWorkingArrangements::whereIn('id', $this->desired_working_arrangements)->select('title', 'id')->get() ?? [];
        }else{
            return [];
        }
    }

    // /* customer type list */
    public function getCustomerTypeListAttribute()
    {
        if(!empty($this->customer_type)){
            return MstCustomerType::whereIn('id', $this->customer_type)->select('title', 'id')->get() ?? [];
        }else{
            return [];
        }
    }

    /* Legal tech tools list */
    public function getLegalTechToolsListAttribute()
    {
        if(!empty($this->legal_tech_tools)){
            return MstLegalTechTool::whereIn('id', $this->legal_tech_tools)->select('title', 'id')->get() ?? [];
        }else{
            return [];
        }
    }
    
    // /* Tech tools list */
    public function getTechToolsListAttribute()
    {
        if(!empty($this->tech_tools)){
            return MstTechTools::whereIn('id', $this->tech_tools)->select('title', 'id')->get() ?? [];
        }else{
            return [];
        }
    }

    // /* languages list */
    public function getLanguagesListAttribute()
    {
        if(!empty($this->languages)){
            return MstLanguage::whereIn('id', $this->languages)->select('title', 'id')->get()  ?? [];
        }else{
            return [];
        }
    }

    // /* time in industry diff */
    public function getTimeInIndustryDiffAttribute()
    {
        if(!empty($this->time_in_industry)){
            return diff_days($this->time_in_industry);
        }
    }

    // /*pqe diif */
    public function getPqeDiffAttribute()
    {
        if(!empty($this->pqe)){
            return $this->pqe;
            return diff_days($this->pqe);
        }
    }

    // /*time in current role diif */
    public function getTimeInCurrentRoleDiffAttribute()
    {
        if(!empty($this->time_in_current_role)){
            return diff_days($this->time_in_current_role);
        }
    }

    // /*	current salary amount*/
    // public function getCurrentSalaryAmountAttribute()
    // {
    //     if(!empty($this->current_salary)){
    //         return moneyFormatIndia($this->current_salary);
    //     }
    // }

    // /*	desired salary amount*/
    // public function getDesiredSalaryAmountAttribute()
    // {
    //     if(!empty($this->desired_salary)){
    //         return moneyFormatIndia($this->desired_salary);
    //     }
    // }

    // /* desired bonus or commission amount */
    // public function getDesiredBonusOrCommissionAmountAttribute()
    // {
    //     if(!empty($this->desired_bonus_or_commission)){
    //         return moneyFormatIndia($this->desired_bonus_or_commission);
    //     }
    // }

    // /* freelance_future_list */
    // public function getFreelanceFutureListAttribute()
    // {
    //     return trueFalse($this->freelance_future);
    // }

    // /* law_degree_list */
    // public function getLawDegreeListAttribute()
    // {
    //     return trueFalse($this->law_degree);
    // }

    // /* qualified_lawyer_list */
    // public function getQualifiedLawyerListAttribute()
    // {
    //     return trueFalse($this->qualified_lawyer);
    // }
    // /* legal_experience_list */
    // public function getLegalExperienceListAttribute()
    // {
    //     return trueFalse($this->legal_experience);
    // }

    // /* legaltech_vendor_or_consultancy_list */
    public function getLegaltechVendorOrConsultancyListAttribute()
    {
        return trueFalse($this->legaltech_vendor_or_consultancy);
    }

    
    // /* qualification_list */
    public function getQualificationListAttribute()
    {
        if(!empty($this->qualification)){
            return MstQualification::where('id', $this->qualification)->select('title', 'id')->get() ?? [];
        }else{
            return [];
        }
    }

    
}


/* Yes/No


law_degree   
qualified_lawyer

*/

/*  Manual Entry

jurisdiction
area_of_law
legal_experience

*/