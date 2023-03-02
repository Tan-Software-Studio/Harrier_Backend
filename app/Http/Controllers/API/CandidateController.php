<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Traits\CandidateFilterTrait;
use App\Http\Traits\CandidateTrait;
use App\Http\Traits\FilterTrait;
use App\Models\Candidate;
use App\Models\Candidate\CandDesiredEmployerTypes;
use App\Models\Master\MstEmployerType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CandidateController extends Controller
{
    use CandidateTrait, FilterTrait, CandidateFilterTrait;

    /* Guest get a candidates list */
    public function guestCandidatesList(Request $request)
    {
        return self::candidatesList($request, auth()->user()->role);
    }

    /* Admin get a candidates list */
    public function adminCandidatesList(Request $request)
    {
        return self::candidatesListOnlyAdmin($request, auth()->user()->role);
    }

    /* Emp get a candidates list */
    public function empCandidatesList(Request $request)
    {
        return self::candidatesList($request, auth()->user()->role);
    }

    
    /* Emp get a candidates short list */
    public function empCandidatesShortList(Request $request)
    {
        return self::candidatesShortList($request, auth()->user()->role);
    }
   
    /* Admin get a All candidates list at that time*/
    public function adminAllCandidatesList(Request $request)
    {
        return self::allCandidatesList($request, auth()->user()->role);
    }

    /* Get Candidates list */
    public function allCandidatesList($request, $role)
    {   
        $req = $request;
        try {
            if(respValid($request)) { return respValid($request); }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/
            $request = (object) $request;
            $query = Candidate::query();
            $query->with('desired_employer_types.desired_employer_types_view'); 
            $query->with(['employer_type_list', 'current_country_list', 'current_salary_symbol_list', 'current_bonus_or_commission_symbol_list', 
                'desired_salary_symbol_list', 'desired_bonus_or_commission_symbol_list', 'deal_size_symbol_list', 
                'sales_quota_symbol_list', 'is_cv_list' => function ($query) {
                    return $query->select('id','job_id', 'c_uid', 'is_cv');
                }
            ]);
            $response = $query->get;
            return sendDataHelper("List", $response, ok());
            
        } catch (\Throwable $th) {
            throw $th;
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }    
        
    }

    

    /* Inactive candidates list call*/
    public function candidatesInactivelist(Request $request)
    {
        try {
            $query = Candidate::query();
            $query->select('id', 'updated_at');
            $query->whereDate('updated_at', '<', now()->subMonth(3) );
            $query->select('uuid','name', 'job_title', 'employer_type', 'current_salary', 'desired_salary', 'current_bonus_or_commission', 'desired_bonus_or_commission', 'updated_at');
            if($s = $request->input('search'))
            {
                $query->whereRaw("name LIKE '%". $s. "%'")
                ->orWhereRaw("email LIKE '%". $s . "%'");
            }

            if($sort = $request->input('sort'))
            {
                $query->orderBy('created_at', $sort);
            }
            
            $response = $query->paginate(
                $perPage = 10, $columns = ['*'], $pageName = 'page'
            );        
            return sendDataHelper("List", $response, ok());
        } catch (\Throwable $th) {
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }
    }


    /* */
    public function detailsUpdate(Request $request)
    {
        try {
            $data = Validator::make($request->all(), [
                'cv'=> 'required|mimetypes:application/pdf|max:10000',
                'profile_image' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            ],[
                'cv.required' => 'Please upload CV.'
            ]);

            if ($data->fails()) {
                return sendError($data->errors()->first(), [], errorValid());
            }
            
            $in = Candidate::where('email', auth()->user()->email)->first();
            if(!$in)
            {
                $in = new Candidate();
                $in->uuid = Str::uuid()->toString();
                $in->email = auth()->user()->email;
                $in->password = null;
            }
            if ($request->hasFile('cv'))    
            {   
                if(!empty($in->cv)){
                    if (File::exists(cv_public_path().$in->cv)) 
                    {
                        unlink(cv_public_path().$in->cv);
                    }
                }
                $in->cv = uploadFile($request['cv'], 'uploads/cv') ?? null;
            }
            if ($request->hasFile('profile_image'))    
            {   
                if(!empty($in->profile_image))
                {
                    if (File::exists(profile_public_path().$in->profile_image)) 
                    {
                        unlink(profile_public_path().$in->profile_image);
                    }
                }
                $in->profile_image = uploadFile($request['profile_image'], 'uploads/profile') ?? null;
            }
            $in->save(); 

            $response = [
                'details' => $in
            ];
            $in->makeHidden('uuid', 'id')->toArray();
            if ($response) {
                return sendDataHelper('Details updated.', $response, ok());
            } else {
                return sendError('Something went wrong', [], unAuth());
            }
            
            
        } catch (\Throwable $th) {
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }
    }

    /*
        Single  candidates list
    */

    /* Guest get a single candidates list */
    public function guestSingleCandidatesList(Request $request)
    {
        return self::singleCandidatesList($request, auth()->user()->role);
    }

    /* Admin get a single candidates list */
    public function adminSingleCandidatesList(Request $request)
    {
        return self::singleCandidatesList($request, auth()->user()->role);
    }

    /* Emp get a single candidates list */
    public function empSingleCandidatesList(Request $request)
    {
        return self::singleCandidatesList($request, auth()->user()->role);
    }

    /* Get Single Candidates list */
    public function singleCandidatesList($request, $role)
    {   
        try {
            if(respValid($request)) { return respValid($request); }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/

            $data = Validator::make($request, [
                'uuid'=> 'required|exists:candidates,uuid',
            ]);

            if ($data->fails()) {
                return sendError($data->errors()->first(), [], errorValid());
            }
            $request = (object) $request;

            $query = Candidate::query();
            
            if($role == roleGuest() || $role ==  roleEmp())
            {
                $query->where('harrier_candidate', yes());
            }
            if($role ==  roleEmp())
            {
                $query->with(['is_cv_list_same_emp'  => function ($query) {
                        return $query->select('id','job_id', 'c_uid', 'is_cv');
                    }
                ]);
                $query->with('emp_short_list', function($q){
                    $emp_uuid = employer(auth()->user()->email)->value('uuid');
                    $q->where('emp_uuid', $emp_uuid);
                });
            }

            if($uuid = $request->uuid)
            {
                $query->where('uuid', $uuid);
            }
            switch ($role) {
                case roleAdmin():
                    break;
                case roleGuest(): 
                    break;
                case roleEmp():
                    // employer_type
                    break;

                default:
                    $query->select('job_title');
                    break;  
            }   
            $response = $query->first();
            if($role == roleEmp() || $role == roleGuest())
            {   
                if(mst_employer_types($response->employer_type))
                {
                    $response->employer_type = mst_employer_types($response->employer_type)->title;
                } 

                if(count(mst_employer_types($response->desired_employer_type)) > 0)
                {
                    $response->desired_employer_type = mst_employer_types($response->desired_employer_type)->pluck('title');
                }

                if(mst_regions($response->current_region))
                {
                    $response->current_region = mst_regions($response->current_region)->title;
                }

                if(mst_countries($response->current_country))
                {
                    $response->current_country = mst_countries($response->current_country)->country_name;
                }

                if(count(mst_countries($response->desired_country)) > 0)
                {
                    $response->desired_country = mst_countries($response->desired_country)->pluck('country_name');
                }

                if(mst_working_arrangements($response->working_arrangements))
                {
                    $response->working_arrangements = mst_working_arrangements($response->working_arrangements)->title;
                }

                if(count(mst_working_arrangements($response->desired_working_arrangements)) > 0)
                {
                    $response->desired_working_arrangements = mst_working_arrangements($response->desired_working_arrangements)->pluck('title');
                }

                if(count(mst_customer_types($response->customer_type)) > 0)
                {                      
                    $response->customer_type = mst_customer_types($response->customer_type)->pluck('title');
                }

                if(count(mst_legal_tech_tools($response->legal_tech_tools)) > 0)
                {
                    $response->legal_tech_tools = mst_legal_tech_tools($response->legal_tech_tools)->pluck('title');
                }

                if(count(mst_tech_tools($response->tech_tools)) > 0)
                {
                    $response->tech_tools = mst_tech_tools($response->tech_tools)->pluck('title');
                }
                if(count(mst_qualifications($response->qualification)) > 0)
                {
                    $response->qualification = mst_qualifications($response->qualification)->pluck('title');
                }
                if(count(mst_languages($response->languages)) > 0)
                {
                    $response->languages = mst_languages($response->languages)->pluck('title');
                }
                
                if(mst_currencies($response->current_salary_symbol))
                {
                    $response->current_salary_symbol = mst_currencies($response->current_salary_symbol)->currency_code;
                }
                if(mst_currencies($response->desired_salary_symbol))
                {
                    $response->desired_salary_symbol = mst_currencies($response->desired_salary_symbol)->currency_code;
                }
                if(mst_currencies($response->current_bonus_or_commission_symbol))
                {
                    $response->current_bonus_or_commission_symbol = mst_currencies($response->current_bonus_or_commission_symbol)->currency_code;
                }
                if(mst_currencies($response->desired_bonus_or_commission_symbol))
                {
                    $response->desired_bonus_or_commission_symbol = mst_currencies($response->desired_bonus_or_commission_symbol)->currency_code;
                }
                if(mst_currencies($response->deal_size_symbol))
                {
                    $response->deal_size_symbol = mst_currencies($response->deal_size_symbol)->currency_code;
                }
                if(mst_currencies($response->sales_quota_symbol))
                {
                    $response->sales_quota_symbol = mst_currencies($response->sales_quota_symbol)->currency_code;
                }
                if(mst_currencies($response->freelance_daily_rate_symbol))
                {
                    $response->freelance_daily_rate_symbol = mst_currencies($response->freelance_daily_rate_symbol)->currency_code;
                }
            }
            if($response)
            {
                $response->toArray();
            }else{
                $response = [];
            }
            return sendDataHelper("List", $response, ok());
            
        } catch (\Throwable $th) {
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }
    }

    /* */
    public function candidatesStatusChange(Request $request)
    {
        try {
            if(respValid($request)) { return respValid($request); }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/

            $data = Validator::make($request, [
                'uuid' => 'required|exists:candidates,uuid',
                'status'=> 'required|numeric'
            ]);
            if ($data->fails()) {
                return sendError($data->errors()->first(), [], errorValid());
            }

            $request = (object) $request;
            $in = Candidate::where('uuid', @$request->uuid)->first();
            $in->status = $request->status;
            $in->save(); 
            
            self::notfication($in->uuid, 'Status updated');

            $response = [
                'details' => $in
            ];
            if ($response) {
                return sendDataHelper('Status updated.', $response, ok());
            } else {
                return sendError('Something went wrong', [], unAuth());
            }
            
        } catch (\Throwable $th) {
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }
    }


    
    /* Get Guest to Candidates list */
    public function guestCandidatesListFilter(Request $request)
    {   
        $role = roleGuest();
        $req = $request;
        try {
            if(respValid($request)) { return respValid($request); }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/
            $request = (object) $request;
            $query = Candidate::query();
            $query->where('harrier_candidate', yes());
                            
            if($job_title = @$request->job_title) { $query->where('job_title', $job_title); }
            
            if($mst_employer_types = @$request->mst_employer_types) {   $query->where('employer_type', $mst_employer_types);    }
            if($desired_employer_type = @$request->desired_employer_type) {   $query->whereIn('desired_employer_type', [$desired_employer_type]);    }
            
            if($mst_legal_tech_tools = @$request->mst_legal_tech_tools) {   $query->whereIn('legal_tech_tools', [$mst_legal_tech_tools]);   }
            if($mst_tech_tools = @$request->mst_tech_tools) {   $query->whereIn('tech_tools', [$mst_tech_tools]);   }
            if($mst_channels = @$request->mst_channels) {   $query->where('channel', $mst_channels);   }
            if($mst_cultural_backgrounds = @$request->mst_cultural_backgrounds) {   $query->whereIn('cultural_background', [$mst_cultural_backgrounds]);   }
            if($mst_customer_types = @$request->mst_customer_types) {   $query->whereIn('customer_type', [$mst_customer_types]);   }
            if($mst_faiths = @$request->mst_faiths) {   $query->where('faith', $mst_faiths);   }
            if($mst_genders = @$request->mst_genders) {   $query->where('gender', $mst_genders);   }
            if($mst_qualifications = @$request->mst_qualifications) {   $query->whereIn('qualification', [$mst_qualifications]);   }
            
            if($mst_regions = @$request->mst_regions) {   $query->where('current_region', $mst_regions);   }
            if($desired_region = @$request->desired_region) {   $query->whereIn('desired_region', [$desired_region]);   }            
            
            if($mst_school_types = @$request->mst_school_types) {   $query->where('school_type', $mst_school_types);   }
            if($mst_sexes = @$request->mst_sexes) {   $query->where('sex', $mst_sexes);   }
            if($mst_sexual_orientations = @$request->mst_sexual_orientations) {   $query->where('sexual_orientation', $mst_sexual_orientations);   }
            if($mst_tech_tools = @$request->mst_tech_tools) {   $query->whereIn('tech_tools', [$mst_tech_tools]);   }
            
            if($mst_working_arrangements = @$request->mst_working_arrangements) {   $query->where('working_arrangements', $mst_working_arrangements);   }
            if($desired_working_arrangements = @$request->desired_working_arrangements) {   $query->whereIn('desired_working_arrangements', [$desired_working_arrangements]);   }

            if($mst_countries = @$request->mst_countries) {   $query->where('current_country', $mst_countries);   }
            if($desired_country = @$request->desired_country) {   $query->whereIn('current_country', [$desired_country]);   }

            // if($notice_period = @$request->notice_period) {   $query->whereBetween('notice_period', [100, 200]);  }
            
            // if($time_in_industry = @$request->time_in_industry) {   $query->where('time_in_industry', $time_in_industry);   }
            // if($time_in_industry = @$request->time_in_industry) {   $query->where('time_in_industry', $time_in_industry);   }
            
            switch ($role) {
                case roleAdmin():
                    break;
                case roleGuest():
                    $query->select('id','uuid', 'job_title', 'employer_type', 'time_in_current_role', 'customer_type',
                    'time_in_industry', 'current_salary', 'current_bonus_or_commission', 'desired_salary', 
                    'desired_bonus_or_commission', 'notice_period', 'pqe', 'legal_tech_tools', 'current_company_url', 'current_salary_symbol', 
                    'current_bonus_or_commission_symbol', 'desired_salary_symbol', 'desired_bonus_or_commission_symbol', 'deal_size', 'deal_size_symbol', 
                    'sales_quota', 'sales_quota_symbol', 'law_degree', 'qualified_lawyer', 'jurisdiction', 'pqe', 'area_of_law', 'qualification');
                    break;
                case roleEmp():
                    $query->select('id','status','uuid', 'job_title', 'employer_type', 'time_in_current_role', 
                    'time_in_industry', 'current_salary', 'current_bonus_or_commission', 'desired_salary', 
                    'desired_bonus_or_commission', 'notice_period', 'pqe', 'legal_tech_tools', 'current_company_url', 'current_salary_symbol', 'current_bonus_or_commission_symbol', 'desired_salary_symbol', 'desired_bonus_or_commission_symbol', 'deal_size_symbol', 'sales_quota_symbol');
                    $query->where(function ($query) {
                        $query->where('current_company_url', '!=' , employer(auth()->user()->email)->url)
                        ->orWhere('current_company_url', null);
                    });
                break;
                default:
                    $query->select('job_title');
                    break;
            }
            $query->with(['employer_type_list', 'current_country_list', 'current_salary_symbol_list', 'current_bonus_or_commission_symbol_list', 
                'desired_salary_symbol_list', 'desired_bonus_or_commission_symbol_list', 'deal_size_symbol_list', 
                'sales_quota_symbol_list', 'is_cv_list' => function ($query) {
                    return $query->select('id','job_id', 'c_uid', 'is_cv');
                }
            ]);

            $list = $query->paginate(
                $perPage = 10, $columns = ['*'], $pageName = 'page'
            );

            $response = [
                $this->fields('#', 'Candidate UUID', 'uuid',  $list),
                $this->fields(1, 'Job Title', 'job_title',  $list),
                $this->fields(2, 'Employer Type', 'employer_type', $list)   
            ];
            // return $response;
            return sendDataHelper("List", $response, ok());

            /*
                [
                    { id: 1, title: "Job Title" },
                    { id: 2, title: "Employer Type" },
                    { id: 3, title: "Time in Current Role" },
                    { id: 4, title: "Time in Industry" },
                    { id: 5, title: "Line Management" },
                    { id: 6, title: "Desired Employer Type" },
                    { id: 7, title: "Current Country" },
                    { id: 8, title: "Current Region" },
                    { id: 9, title: "Current Salary" },
                    { id: 10, title: "Current Bonus / Commission" },
                    { id: 11, title: "Desired Salary" },
                    { id: 12, title: "Desired Bonus / Commission" },
                    { id: 13, title: "Notice Period" },
                    { id: 14, title: "Working Arrangements" },
                    { id: 15, title: "Desired Working Arrangements" },
                    { id: 16, title: "Law Degree" },
                    { id: 17, title: "Qualified Lawyer" },
                    { id: 18, title: "Jurisdiction" },
                    { id: 19, title: "PQE" },
                    { id: 20, title: "Area of Law" },
                    { id: 21, title: "Legal Experience" },
                    { id: 22, title: "Customer Type" },
                    { id: 23, title: "Deal Size" },
                    { id: 24, title: "Sales quota" },
                    { id: 25, title: "LegalTech Tools" },
                    { id: 26, title: "Tech Tools" },
                    { id: 27, title: "Qualifications" },
                ];
            */
        } catch (\Throwable $th) {
            // throw $th;
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }    
        
    }

    /* Get Emp to Candidates list */
    public function empCandidatesListFilter(Request $request)
    {   
        $role = roleGuest();
        $req = $request;
        try {
            if(respValid($request)) { return respValid($request); }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/
            $request = (object) $request;
            $query = Candidate::query();
            $query->where('harrier_candidate', yes());
            
            if($job_title = @$request->job_title) { $query->where('job_title', $job_title); }
            
            if($mst_employer_types = @$request->mst_employer_types) {   $query->where('employer_type', $mst_employer_types);    }
            if($desired_employer_type = @$request->desired_employer_type) {   $query->whereIn('desired_employer_type', [$desired_employer_type]);    }
            
            if($mst_legal_tech_tools = @$request->mst_legal_tech_tools) {   $query->whereIn('legal_tech_tools', [$mst_legal_tech_tools]);   }
            if($mst_tech_tools = @$request->mst_tech_tools) {   $query->whereIn('tech_tools', [$mst_tech_tools]);   }
            if($mst_channels = @$request->mst_channels) {   $query->where('channel', $mst_channels);   }
            if($mst_cultural_backgrounds = @$request->mst_cultural_backgrounds) {   $query->whereIn('cultural_background', [$mst_cultural_backgrounds]);   }
            if($mst_customer_types = @$request->mst_customer_types) {   $query->whereIn('customer_type', [$mst_customer_types]);   }
            if($mst_faiths = @$request->mst_faiths) {   $query->where('faith', $mst_faiths);   }
            if($mst_genders = @$request->mst_genders) {   $query->where('gender', $mst_genders);   }
            if($mst_qualifications = @$request->mst_qualifications) {   $query->whereIn('qualification', [$mst_qualifications]);   }
            
            if($mst_regions = @$request->mst_regions) {   $query->where('current_region', $mst_regions);   }
            if($desired_region = @$request->desired_region) {   $query->whereIn('desired_region', [$desired_region]);   }            
            
            if($mst_school_types = @$request->mst_school_types) {   $query->where('school_type', $mst_school_types);   }
            if($mst_sexes = @$request->mst_sexes) {   $query->where('sex', $mst_sexes);   }
            if($mst_sexual_orientations = @$request->mst_sexual_orientations) {   $query->where('sexual_orientation', $mst_sexual_orientations);   }
            if($mst_tech_tools = @$request->mst_tech_tools) {   $query->whereIn('tech_tools', [$mst_tech_tools]);   }
            
            if($mst_working_arrangements = @$request->mst_working_arrangements) {   $query->where('working_arrangements', $mst_working_arrangements);   }
            if($desired_working_arrangements = @$request->desired_working_arrangements) {   $query->whereIn('desired_working_arrangements', [$desired_working_arrangements]);   }

            if($mst_countries = @$request->mst_countries) {   $query->where('current_country', $mst_countries);   }
            if($desired_country = @$request->desired_country) {   $query->whereIn('current_country', [$desired_country]);   }

            // if($notice_period = @$request->notice_period) {   $query->whereBetween('notice_period', [100, 200]);  }
            
            // if($time_in_industry = @$request->time_in_industry) {   $query->where('time_in_industry', $time_in_industry);   }
            // if($time_in_industry = @$request->time_in_industry) {   $query->where('time_in_industry', $time_in_industry);   }
            
            switch ($role) {
                case roleAdmin():
                    break;
                case roleGuest():
                    $query->select('id','uuid', 'job_title', 'employer_type', 'time_in_current_role', 'customer_type',
                    'time_in_industry', 'current_salary', 'current_bonus_or_commission', 'desired_salary', 
                    'desired_bonus_or_commission', 'notice_period', 'pqe', 'legal_tech_tools', 'current_company_url', 'current_salary_symbol', 
                    'current_bonus_or_commission_symbol', 'desired_salary_symbol', 'desired_bonus_or_commission_symbol', 'deal_size', 'deal_size_symbol', 
                    'sales_quota', 'sales_quota_symbol', 'law_degree', 'qualified_lawyer', 'jurisdiction', 'pqe', 'area_of_law', 'qualification');
                    break;
                case roleEmp():
                    $query->select('id','status','uuid', 'job_title', 'employer_type', 'time_in_current_role', 
                    'time_in_industry', 'current_salary', 'current_bonus_or_commission', 'desired_salary', 
                    'desired_bonus_or_commission', 'notice_period', 'pqe', 'legal_tech_tools', 'current_company_url', 'current_salary_symbol', 'current_bonus_or_commission_symbol', 'desired_salary_symbol', 'desired_bonus_or_commission_symbol', 'deal_size_symbol', 'sales_quota_symbol');
                    $query->where(function ($query) {
                        $query->where('current_company_url', '!=' , employer(auth()->user()->email)->url)
                        ->orWhere('current_company_url', null);
                    });
                break;
                default:
                    $query->select('job_title');
                    break;
            }
            $query->with(['employer_type_list', 'current_country_list', 'current_salary_symbol_list', 'current_bonus_or_commission_symbol_list', 
                'desired_salary_symbol_list', 'desired_bonus_or_commission_symbol_list', 'deal_size_symbol_list', 
                'sales_quota_symbol_list', 'is_cv_list' => function ($query) {
                    return $query->select('id','job_id', 'c_uid', 'is_cv');
                }
            ]);

            $list = $query->paginate(
                $perPage = 10, $columns = ['*'], $pageName = 'page'
            );

            $response = [
                $this->fields('#', 'Candidate UUID', 'uuid',  $list),
                $this->fields(1, 'Job Title', 'job_title',  $list),
                $this->fields(2, 'Employer Type', 'employer_type', $list)   
            ];
            // return $response;
            return sendDataHelper("List", $response, ok());

            /*
                [
                    { id: 1, title: "Job Title" },
                    { id: 2, title: "Employer Type" },
                    { id: 3, title: "Time in Current Role" },
                    { id: 4, title: "Time in Industry" },
                    { id: 5, title: "Line Management" },
                    { id: 6, title: "Desired Employer Type" },
                    { id: 7, title: "Current Country" },
                    { id: 8, title: "Current Region" },
                    { id: 9, title: "Current Salary" },
                    { id: 10, title: "Current Bonus / Commission" },
                    { id: 11, title: "Desired Salary" },
                    { id: 12, title: "Desired Bonus / Commission" },
                    { id: 13, title: "Notice Period" },
                    { id: 14, title: "Working Arrangements" },
                    { id: 15, title: "Desired Working Arrangements" },
                    { id: 16, title: "Law Degree" },
                    { id: 17, title: "Qualified Lawyer" },
                    { id: 18, title: "Jurisdiction" },
                    { id: 19, title: "PQE" },
                    { id: 20, title: "Area of Law" },
                    { id: 21, title: "Legal Experience" },
                    { id: 22, title: "Customer Type" },
                    { id: 23, title: "Deal Size" },
                    { id: 24, title: "Sales quota" },
                    { id: 25, title: "LegalTech Tools" },
                    { id: 26, title: "Tech Tools" },
                    { id: 27, title: "Qualifications" },
                ];
            */
        } catch (\Throwable $th) {
            // throw $th;
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }    
        
    }
}