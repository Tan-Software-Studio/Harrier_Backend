<?php

namespace App\Http\Traits;

use App\Models\Candidate;
use App\Models\Candidate\CandDesiredCountry;
use App\Models\Candidate\CandDesiredEmployerTypes;
use App\Models\Candidate\CandLegalTechTools;
use App\Models\Candidate\CandMstCulturalBackground;
use App\Models\Candidate\CandMstCustomerTypes;
use App\Models\Candidate\CandQualifications;
use App\Models\Candidate\CandTechTools;
use App\Models\Candidate\CandWorkingArrangements;
use App\Models\Emp\EmpCandidate;
use App\Models\Job;
use App\Models\Master\MstCandidateStatus;
use App\Models\Master\MstChannel;
use App\Models\Master\MstCulturalBackground;
use App\Models\Master\MstCurrency;
use App\Models\Master\MstCustomerType;
use App\Models\Master\MstEmployerType;
use App\Models\Master\MstRegion;
use App\Models\Master\MstWorkingArrangements;
use App\Models\Unique\Country;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

trait CandidateFilterTrait {
     /* Get Candidates list */
    public function candidatesList($request, $role)
    {   
         $req = $request;
         try {
            if(respValid($request)) { return respValid($request); }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/
            $request = (object) $request;   // count show to comment in filter count
            
            $query = Candidate::query();
            
            $query->with('desired_employer_types.desired_employer_types_view');
            
            if($role == roleGuest() || $role ==  roleEmp())
            {
                $query->where('harrier_candidate', yes());
            }
            if($role ==  roleEmp())
            {
                $query->with('emp_short_list', function($q){
                    $emp_uuid = employer(auth()->user()->email)->value('uuid');
                    $q->where('emp_uuid', $emp_uuid);
                });
            }
                            
            // if($mst_genders = @$request->mst_genders) {   $query->where('gender', $mst_genders);   }
            // if($mst_school_types = @$request->mst_school_types) {   $query->where('school_type', $mst_school_types);   }
            // if($mst_sexes = @$request->mst_sexes) {   $query->where('sex', $mst_sexes);   }
            // if($mst_sexual_orientations = @$request->mst_sexual_orientations) {   $query->where('sexual_orientation', $mst_sexual_orientations);   }
            
            $d = null;
            if(@$request)
            {
                if(@$request && @$request->main_filter && count(@$request->main_filter) > 0)
                {
                    foreach($request->main_filter as $loop)
                    {
                        @$loop = (object) $loop;
                        if(@$loop->is_show == true)
                        {   
                            $search = null;
                            $search_two = null;
                            if(@$loop->second_option){  $search = @$loop->second_option;    }
                            if(@$loop->third_option){  $search_two = @$loop->third_option;    }
                            
                            if($role == roleAdmin())
                            {
                                // is_more_than; is_less_than; is_between
                                if(@$loop->first_option == "current_salary" && $loop->currency)
                                {   
                                    if($search)
                                    {
                                        if($loop->filter_type == 'is_more_than')
                                        {
                                            $query->where('current_salary', '>', $search)->where('current_salary_symbol', $loop->currency);
                                        }
                                        if($loop->filter_type == 'is_less_than')
                                        {
                                            $query->where('current_salary', '<', $search)->where('current_salary_symbol', $loop->currency);
                                        }
                                        if($loop->filter_type == 'is_between')
                                        {
                                            $query->where('current_salary', '>=', $search)->where('current_salary_symbol', $loop->currency);
                                            $query->where('current_salary', '<=', $search_two)->where('current_salary_symbol', $loop->currency);
                                        }
                                    }
                                }

                                if(@$loop->first_option == "current_bonus_or_commission" && $loop->currency)
                                {   
                                    if($search)
                                    {
                                        if($loop->filter_type == 'is_more_than')
                                        {
                                            $query->where('current_bonus_or_commission', '>', $search)->where('current_bonus_or_commission_symbol', $loop->currency);
                                        }
                                        if($loop->filter_type == 'is_less_than')
                                        {
                                            $query->where('current_bonus_or_commission', '<', $search)->where('current_bonus_or_commission_symbol', $loop->currency);
                                        }
                                        if($loop->filter_type == 'is_between')
                                        {
                                            $query->where('current_bonus_or_commission', '>=', $search)->where('current_bonus_or_commission_symbol', $loop->currency);
                                            $query->where('current_bonus_or_commission', '<=', $search_two)->where('current_bonus_or_commission_symbol', $loop->currency);
                                        }
                                    }
                                }
                            }

                            if(@$loop->first_option == "job_title")
                            {   
                                if($search)
                                {
                                    if($loop->filter_type == 'is')
                                    {
                                        // return 'is';
                                        $query->where('job_title', $search);
                                    }
                                    if($loop->filter_type == 'is_not')
                                    {
                                        // return 'is_not';
                                        $query->whereNotIn('job_title', [$search]);
                                    }
                                    if($loop->filter_type == 'contains')
                                    {
                                        // return 'contains';
                                        $query->whereRaw("job_title LIKE '%". $search. "%'");
                                    }
                                }
                            }

                            
                            if(@$loop->first_option == "desired_salary" && $loop->currency)
                            {   
                                if($search)
                                {
                                    if($loop->filter_type == 'is_more_than')
                                    {
                                        $query->where('desired_salary', '>', $search)->where('desired_salary_symbol', $loop->currency);
                                    }
                                    if($loop->filter_type == 'is_less_than')
                                    {
                                        $query->where('desired_salary', '<', $search)->where('desired_salary_symbol', $loop->currency);
                                    }
                                    if($loop->filter_type == 'is_between')
                                    {
                                        $query->where('desired_salary', '>=', $search)->where('desired_salary_symbol', $loop->currency);
                                        $query->where('desired_salary', '<=', $search_two)->where('desired_salary_symbol', $loop->currency);
                                    }
                                }
                            }

                            if(@$loop->first_option == "desired_bonus_or_commission" && $loop->currency)
                            {   
                                if($search)
                                {
                                    if($loop->filter_type == 'is_more_than')
                                    {
                                        $query->where('desired_bonus_or_commission', '>', $search)->where('desired_bonus_or_commission_symbol', $loop->currency);
                                    }
                                    if($loop->filter_type == 'is_less_than')
                                    {
                                        $query->where('desired_bonus_or_commission', '<', $search)->where('desired_bonus_or_commission_symbol', $loop->currency);
                                    }
                                    if($loop->filter_type == 'is_between')
                                    {
                                        $query->where('desired_bonus_or_commission', '>=', $search)->where('desired_bonus_or_commission_symbol', $loop->currency);
                                        $query->where('desired_bonus_or_commission', '<=', $search_two)->where('desired_bonus_or_commission_symbol', $loop->currency);
                                    }
                                }
                            }

                            if(@$loop->first_option == "sales_quota" && $loop->currency)
                            {   
                                if($search)
                                {
                                    if($loop->filter_type == 'is_more_than')
                                    {
                                        $query->where('sales_quota', '>', $search)->where('sales_quota_symbol', $loop->currency);
                                    }
                                    if($loop->filter_type == 'is_less_than')
                                    {
                                        $query->where('sales_quota', '<', $search)->where('sales_quota_symbol', $loop->currency);
                                    }
                                    if($loop->filter_type == 'is_between')
                                    {
                                        $query->where('sales_quota', '>=', $search)->where('sales_quota_symbol', $loop->currency);
                                        $query->where('sales_quota', '<=', $search_two)->where('sales_quota_symbol', $loop->currency);
                                    }
                                }
                            }

                            if(@$loop->first_option == "deal_size" && $loop->currency)
                            {   
                                if($search)
                                {
                                    if($loop->filter_type == 'is_more_than')
                                    {
                                        $query->where('deal_size', '>', $search)->where('deal_size_symbol', $loop->currency);
                                    }
                                    if($loop->filter_type == 'is_less_than')
                                    {
                                        $query->where('deal_size', '<', $search)->where('deal_size_symbol', $loop->currency);
                                    }
                                    if($loop->filter_type == 'is_between')
                                    {
                                        $query->where('deal_size', '>=', $search)->where('deal_size_symbol', $loop->currency);
                                        $query->where('deal_size', '<=', $search_two)->where('deal_size_symbol', $loop->currency);
                                    }
                                }
                            }

                            if(@$loop->first_option == "freelance_daily_rate" && $loop->currency)
                            {   
                                if($search)
                                {
                                    if($loop->filter_type == 'is_more_than')
                                    {
                                        $query->where('freelance_daily_rate', '>', $search)->where('freelance_daily_rate_symbol', $loop->currency);
                                    }
                                    if($loop->filter_type == 'is_less_than')
                                    {
                                        $query->where('freelance_daily_rate', '<', $search)->where('freelance_daily_rate_symbol', $loop->currency);
                                    }
                                    if($loop->filter_type == 'is_between')
                                    {
                                        $query->where('freelance_daily_rate', '>=', $search)->where('freelance_daily_rate_symbol', $loop->currency);
                                        $query->where('freelance_daily_rate', '<=', $search_two)->where('freelance_daily_rate_symbol', $loop->currency);
                                    }
                                }
                            }
                        
                            if(@$loop->first_option == "mst_employer_types")
                            {   
                                if($search)
                                {
                                    if($loop->filter_type == 'is')
                                    {
                                        $d_emp_t = MstEmployerType::where('title', @$search)->first();
                                        if($d_emp_t)
                                        {
                                            $query->where('employer_type', $d_emp_t->id);
                                        }
                                    }
                                    if($loop->filter_type == 'is_not')
                                    {
                                        $d_emp_t = MstEmployerType::where('title', @$search)->first();
                                        if($d_emp_t)
                                        {
                                            $query->whereNotIn('employer_type', [$d_emp_t]);
                                        }
                                    }
                                    if($loop->filter_type == 'contains')
                                    {
                                        $d_emp_t = MstEmployerType::whereRaw("title LIKE '%". $search. "%'")->first();
                                        if($d_emp_t)
                                        {
                                            $query->where('employer_type', $d_emp_t->id);
                                        }
                                    }
                                }
                            }
                            
                            if(@$loop->first_option == "desired_employer_type")
                            {   
                                if($search)
                                {
                                    if($loop->filter_type == 'is')
                                    {
                                        $d_emp_t_ids = MstEmployerType::where('title', $search)->pluck('id');
                                        $dataIds =  CandDesiredEmployerTypes::whereIn('mst_id', $d_emp_t_ids)
                                        ->pluck('c_uuid');
                                        if(count($dataIds) > 0)
                                        {
                                            $query->whereIn('uuid', $dataIds);
                                        }else{
                                            $query->whereIn('uuid', []);
                                        }
                                    }
                                    if($loop->filter_type == 'is_not')
                                    {
                                        $d_emp_t_ids = MstEmployerType::where('title', $search)->pluck('id');
                                        $dataIds =  CandDesiredEmployerTypes::whereIn('mst_id', $d_emp_t_ids)
                                        ->pluck('c_uuid');
                                        if(count($dataIds) > 0)
                                        {
                                            $query->whereNotIn('uuid', $dataIds);
                                        }
                                    }
                                    if($loop->filter_type == 'contains')
                                    {
                                        $d_emp_t_ids = MstEmployerType::whereRaw("title LIKE '%". $search. "%'")->pluck('id');
                                        $dataIds =  CandDesiredEmployerTypes::whereIn('mst_id', $d_emp_t_ids)
                                        ->pluck('c_uuid');
                                        if(count($dataIds) > 0)
                                        {
                                            $query->whereIn('uuid', $dataIds);

                                        }else{
                                            $query->whereIn('uuid', []);
                                        }
                                    }
                                }
                                
                            }

                            if(@$loop->first_option == "mst_legal_tech_tools")
                            {   
                                if($search)
                                {
                                    if($loop->filter_type == 'is')
                                    {
                                        $dataIds =  CandLegalTechTools::where('title', $search)->pluck('c_uuid');
                                        if(count($dataIds) > 0)
                                        {
                                            $query->whereIn('uuid', $dataIds);
                                        }else{
                                            $query->whereIn('uuid', []);
                                        }
                                    }
                                    if($loop->filter_type == 'is_not')
                                    {
                                        $dataIds =  CandLegalTechTools::where('title', $search)->pluck('c_uuid');
                                        if(count($dataIds) > 0)
                                        {
                                            $query->whereNotIn('uuid', $dataIds);
                                        }
                                    }
                                    if($loop->filter_type == 'contains')
                                    {
                                        $dataIds =  CandLegalTechTools::whereRaw("title LIKE '%". $search. "%'")->pluck('c_uuid');
                                        if(count($dataIds) > 0)
                                        {
                                            $query->whereIn('uuid', $dataIds);
                                        }else{
                                            $query->whereIn('uuid', []);
                                        }
                                    }
                                }
                            }

                            if(@$loop->first_option == "mst_tech_tools")
                            {   
                                if($search)
                                {
                                    if($loop->filter_type == 'is')
                                    {
                                        $dataIds =  CandTechTools::where('title', $search)->pluck('c_uuid');
                                        if(count($dataIds) > 0)
                                        {
                                            $query->whereIn('uuid', $dataIds);
                                        }else{
                                            $query->whereIn('uuid', []);
                                        }
                                    }
                                    if($loop->filter_type == 'is_not')
                                    {
                                        $dataIds =  CandTechTools::where('title', $search)->pluck('c_uuid');
                                        if(count($dataIds) > 0)
                                        {
                                            $query->whereNotIn('uuid', $dataIds);
                                        }
                                    }
                                    if($loop->filter_type == 'contains')
                                    {
                                        $dataIds =  CandTechTools::whereRaw("title LIKE '%". $search. "%'")->pluck('c_uuid');
                                        if(count($dataIds) > 0)
                                        {
                                            $query->whereIn('uuid', $dataIds);
                                        }else{
                                            $query->whereIn('uuid', []);
                                        }
                                    }
                                }
                            }

                            if(@$loop->first_option == "mst_qualifications")
                            {   
                                if($search)
                                {
                                    if($loop->filter_type == 'is')
                                    {
                                        $dataIds =  CandQualifications::where('title', $search)->pluck('c_uuid');
                                        if(count($dataIds) > 0)
                                        {
                                            $query->whereIn('uuid', $dataIds);
                                        }else{
                                            $query->whereIn('uuid', []);
                                        }
                                    }
                                    if($loop->filter_type == 'is_not')
                                    {
                                        $dataIds =  CandQualifications::where('title', $search)->pluck('c_uuid');
                                        if(count($dataIds) > 0)
                                        {
                                            $query->whereNotIn('uuid', $dataIds);
                                        }
                                    }
                                    if($loop->filter_type == 'contains')
                                    {
                                        $dataIds =  CandQualifications::whereRaw("title LIKE '%". $search. "%'")->pluck('c_uuid');
                                        if(count($dataIds) > 0)
                                        {
                                            $query->whereIn('uuid', $dataIds);
                                        }else{
                                            $query->whereIn('uuid', []);
                                        }
                                    }
                                }
                            }

                            if(@$loop->first_option == "mst_working_arrangements")
                            {   
                                if($search)
                                {
                                    if($loop->filter_type == 'is')
                                    {
                                        $d4 = MstWorkingArrangements::where('title', @$search)->first();
                                        if($d4)
                                        {
                                            $query->where('working_arrangements', $d4->id);
                                        }
                                    }
                                    if($loop->filter_type == 'is_not')
                                    {
                                        $d4 = MstWorkingArrangements::where('title', @$search)->first();
                                        if($d4)
                                        {
                                            $query->whereNotIn('working_arrangements', [$d4->id]);
                                        }
                                    }
                                    if($loop->filter_type == 'contains')
                                    {
                                        $d4 = MstWorkingArrangements::whereRaw("title LIKE '%". $search. "%'")->first();
                                        if($d4)
                                        {
                                            $query->where('working_arrangements', $d4->id);
                                        }
                                    }
                                }
                            }
                            
                            if(@$loop->first_option == "desired_working_arrangements")
                            {   
                                if($search)
                                {
                                    if($loop->filter_type == 'is')
                                    {
                                        $dataIds =  CandWorkingArrangements::where('title', $search)->pluck('c_uuid');
                                        if(count($dataIds) > 0)
                                        {
                                            $query->whereIn('uuid', $dataIds);
                                        }else{
                                            $query->whereIn('uuid', []);
                                        }
                                    }
                                    if($loop->filter_type == 'is_not')
                                    {
                                        $dataIds =  CandWorkingArrangements::where('title', $search)->pluck('c_uuid');
                                        if(count($dataIds) > 0)
                                        {
                                            $query->whereNotIn('uuid', $dataIds);
                                        }
                                    }
                                    if($loop->filter_type == 'contains')
                                    {
                                        $dataIds =  CandWorkingArrangements::whereRaw("title LIKE '%". $search. "%'")->pluck('c_uuid');
                                        if(count($dataIds) > 0)
                                        {
                                            $query->whereIn('uuid', $dataIds);
                                        }else{
                                            $query->whereIn('uuid', []);
                                        }
                                    }
                                }
                            }

                            if(@$loop->first_option == "mst_channels")
                            {   
                                if($search)
                                {
                                    if($loop->filter_type == 'is')
                                    {
                                        $d3 = MstChannel::where('title', @$search)->first();
                                        if($d3)
                                        {
                                            $query->where('channel', $d3->id);
                                        }
                                    }
                                    if($loop->filter_type == 'is_not')
                                    {
                                        $d3 = MstChannel::where('title', @$search)->first();
                                        if($d3)
                                        {
                                            $query->whereNotIn('channel', [$d3]);
                                        }
                                    }
                                    if($loop->filter_type == 'contains')
                                    {
                                        $d3 = MstChannel::whereRaw("title LIKE '%". $search. "%'")->first();
                                        if($d3)
                                        {
                                            $query->where('channel', $d3->id);
                                        }
                                    }
                                }
                            }

                            if(@$loop->first_option == "mst_cultural_backgrounds")
                            {   
                                if($search)
                                {
                                    if($loop->filter_type == 'is')
                                    {
                                        $mst_ids = MstCulturalBackground::where('title', $search)->pluck('id');
                                        $dataIds =  CandMstCulturalBackground::whereIn('mst_id', $mst_ids)->pluck('c_uuid');
                                        if(count($dataIds) > 0)
                                        {
                                            $query->whereIn('uuid', $dataIds);
                                        }else{
                                            $query->whereIn('uuid', []);
                                        }
                                    }
                                    if($loop->filter_type == 'is_not')
                                    {
                                        $mst_ids = MstCulturalBackground::where('title', $search)->pluck('id');
                                        $dataIds =  CandMstCulturalBackground::whereIn('mst_id', $mst_ids)->pluck('c_uuid');
                                        if(count($dataIds) > 0)
                                        {
                                            $query->whereNotIn('uuid', $dataIds);
                                        }
                                    }
                                    if($loop->filter_type == 'contains')
                                    {
                                        $mst_ids = MstCulturalBackground::where('title', $search)->pluck('id');
                                        $dataIds =  CandMstCulturalBackground::whereIn('mst_id', $mst_ids)->pluck('c_uuid');
                                        if(count($dataIds) > 0)
                                        {
                                            $query->whereIn('uuid', $dataIds);
                                        }else{
                                            $query->whereIn('uuid', []);
                                        }
                                    }
                                }
                            }

                            if(@$loop->first_option == "mst_countries")
                            {   
                                if($search)
                                {
                                    if($loop->filter_type == 'is')
                                    {
                                        $d4 = Country::where('country_name', @$search)->first();
                                        if($d4)
                                        {
                                            $query->where('current_country', $d4->id);
                                        }
                                    }
                                    if($loop->filter_type == 'is_not')
                                    {
                                        $d4 = Country::where('country_name', @$search)->first();
                                        if($d4)
                                        {
                                            $query->whereNotIn('current_country', [$d4->id]);
                                        }
                                    }
                                    if($loop->filter_type == 'contains')
                                    {
                                        $d4 = Country::whereRaw("country_name LIKE '%". $search. "%'")->first();
                                        if($d4)
                                        {
                                            $query->where('current_country', $d4->id);
                                        }
                                    }
                                }
                            }

                            if(@$loop->first_option == "desired_country")
                            {   
                                if($search)
                                {
                                    if($loop->filter_type == 'is')
                                    {
                                        $mst_ids = Country::where('country_name', $search)->pluck('id');
                                        $dataIds =  CandDesiredCountry::whereIn('mst_id', $mst_ids)->pluck('c_uuid');
                                        if(count($dataIds) > 0)
                                        {
                                            $query->whereIn('uuid', $dataIds);
                                        }else{
                                            $query->whereIn('uuid', []);
                                        }
                                    }
                                    if($loop->filter_type == 'is_not')
                                    {
                                        $mst_ids = Country::where('country_name', $search)->pluck('id');
                                        $dataIds =  CandDesiredCountry::whereIn('mst_id', $mst_ids)->pluck('c_uuid');
                                        if(count($dataIds) > 0)
                                        {
                                            $query->whereNotIn('uuid', $dataIds);
                                        }
                                    }
                                    if($loop->filter_type == 'contains')
                                    {
                                        $mst_ids = Country::where('country_name', $search)->pluck('id');
                                        $dataIds =  CandDesiredCountry::whereIn('mst_id', $mst_ids)->pluck('c_uuid');
                                        if(count($dataIds) > 0)
                                        {
                                            $query->whereIn('uuid', $dataIds);
                                        }else{
                                            $query->whereIn('uuid', []);
                                        }
                                    }
                                }
                            }

                            if(@$loop->first_option == "line_management")
                            {   
                                if($search && ctype_digit($search) != 1)
                                {   
                                    switch ($search) {
                                        case '0 People':   
                                            $start_p = 0;  $end_p = 0;
                                            break;
                                        case '1-4 People':
                                            $start_p = 1;  $end_p = 4;
                                            break;
                                        case '5-9 People':
                                            $start_p = 5;  $end_p = 9;
                                            break;
                                        case '10-19 People':
                                            $start_p = 10;  $end_p = 19;
                                            break;
                                        case '20-49 People':
                                            $start_p = 20;  $end_p = 49;
                                            break;
                                        default:
                                            $start_p = 50;  $end_p = 10000000000000;
                                            break;
                                    }
                                    
                                    if($loop->filter_type == 'is')
                                    {   
                                        $query->where('line_management', '>=', $start_p);
                                        $query->where('line_management', '<=', $end_p);
                                    }
                                    if($loop->filter_type == 'is_not')
                                    {   
                                        $query->where('line_management', '<', $start_p);
                                        $query->orWhere('line_management', '>', $end_p);
                                    }
                                }
                            }

                            if(@$loop->first_option == "mst_regions")
                            {   
                                if($search)
                                {
                                    if($loop->filter_type == 'is')
                                    {
                                        $d6 = MstRegion::where('state_name', @$search)->first();
                                        if($d6)
                                        {
                                            $query->where('current_region', $d6->id);
                                        }
                                    }
                                    if($loop->filter_type == 'is_not')
                                    {
                                        $d6 = MstRegion::where('state_name', @$search)->first();
                                        if($d6)
                                        {
                                            $query->whereNotIn('current_region', [$d6->id]);
                                        }
                                    }
                                    if($loop->filter_type == 'contains')
                                    {
                                        $d6 = MstRegion::whereRaw("state_name LIKE '%". $search. "%'")->first();
                                        if($d6)
                                        {
                                            $query->where('current_region', $d6->id);
                                        }
                                    }
                                }
                            }
                            
                            if(@$loop->first_option == "desired_region")
                            {   
                                if($search)
                                {
                                    if($loop->filter_type == 'is')
                                    {
                                        $query->where('desired_region',$search);
                                    }
                                    if($loop->filter_type == 'is_not')
                                    {
                                        $query->whereNotIn('desired_region', [$search]);
                                    }
                                    if($loop->filter_type == 'contains')
                                    {
                                        $query->whereRaw("desired_region LIKE '%". $search. "%'");
                                    }
                                }
                            }

                            if(@$loop->first_option == "notice_period")
                            {   
                                if($search && ctype_digit($search) != 1)
                                {   
                                    switch ($search) {
                                        case '0 Weeks':   
                                            $start_w = 0;  $end_w = 0;
                                            break;
                                        case '1-4 Weeks':
                                            $start_w = 1;  $end_w = 4;
                                            break;
                                        case '5-8 Weeks':
                                            $start_w = 5;  $end_w = 8;
                                            break;
                                        case '9-12 Weeks':
                                            $start_w = 9;  $end_w = 12;
                                            break;
                                        default:
                                            $start_w = 13;  $end_w = 10000000000000;
                                            break;
                                    }
                                    
                                    if($loop->filter_type == 'is')
                                    {   
                                        $query->where('notice_period', '>=', $start_w);
                                        $query->where('notice_period', '<=', $end_w);
                                    }
                                    if($loop->filter_type == 'is_not')
                                    {   
                                        $query->where('notice_period', '<', $start_w);
                                        $query->orWhere('notice_period', '>', $end_w);
                                    }
                                }
                            }

                            if(@$loop->first_option == "mst_customer_types")
                            {   
                                if($search)
                                {
                                    if($loop->filter_type == 'is')
                                    {
                                        $mst_ids = MstCustomerType::where('title', $search)->pluck('id');
                                        $dataIds =  CandMstCustomerTypes::whereIn('mst_id', $mst_ids)->pluck('c_uuid');
                                        if(count($dataIds) > 0)
                                        {
                                            $query->whereIn('uuid', $dataIds);
                                        }else{
                                            $query->whereIn('uuid', []);
                                        }
                                    }
                                    if($loop->filter_type == 'is_not')
                                    {
                                        $mst_ids = MstCustomerType::where('title', $search)->pluck('id');
                                        $dataIds =  CandMstCustomerTypes::whereIn('mst_id', $mst_ids)->pluck('c_uuid');
                                        if(count($dataIds) > 0)
                                        {
                                            $query->whereNotIn('uuid', $dataIds);
                                        }
                                    }
                                    if($loop->filter_type == 'contains')
                                    {
                                        $mst_ids = MstCustomerType::where('title', $search)->pluck('id');
                                        $dataIds =  CandMstCustomerTypes::whereIn('mst_id', $mst_ids)->pluck('c_uuid');
                                        if(count($dataIds) > 0)
                                        {
                                            $query->whereIn('uuid', $dataIds);
                                        }else{
                                            $query->whereIn('uuid', []);
                                        }
                                    }
                                }
                            }
                            
                            if(@$loop->first_option == "status")
                            {   
                                if($search)
                                {
                                    if($loop->filter_type == 'is')
                                    {
                                        $d7 = MstCandidateStatus::where('title', @$search)->first();
                                        if($d7)
                                        {
                                            $query->where('status', $d7->id);
                                        }
                                    }
                                    if($loop->filter_type == 'is_not')
                                    {
                                        $d7 = MstCandidateStatus::where('title', @$search)->first();
                                        if($d7)
                                        {
                                            $query->whereNotIn('status', [$d7->id]);
                                        }
                                    }
                                }
                            }

                            if(@$loop->first_option == "freelance_current")
                            {   
                                if($search)
                                {
                                    if($loop->filter_type == 'is')
                                    {
                                        $query->where('freelance_current',  yesNoFetch($search));
                                    }
                                    if($loop->filter_type == 'is_not')
                                    {
                                        $query->whereNotIn('freelance_current', [yesNoFetch($search)]);
                                    }
                                }
                            }

                            if(@$loop->first_option == "freelance_future")
                            {   
                                if($search)
                                {
                                    if($loop->filter_type == 'is')
                                    {
                                        $query->where('freelance_future',  yesNoFetch($search));
                                    }
                                    if($loop->filter_type == 'is_not')
                                    {
                                        $query->whereNotIn('freelance_future', [yesNoFetch($search)]);
                                    }
                                }
                            }

                            if(@$loop->first_option == "law_degree")
                            {   
                                if($search)
                                {
                                    if($loop->filter_type == 'is')
                                    {
                                        $query->where('law_degree',  yesNoFetch($search));
                                    }
                                    if($loop->filter_type == 'is_not')
                                    {
                                        $query->whereNotIn('law_degree', [yesNoFetch($search)]);
                                    }
                                }
                            }

                            if(@$loop->first_option == "qualified_lawyer")
                            {   
                                if($search)
                                {
                                    if($loop->filter_type == 'is')
                                    {
                                        $query->where('qualified_lawyer',  yesNoFetch($search));
                                    }
                                    if($loop->filter_type == 'is_not')
                                    {
                                        $query->whereNotIn('qualified_lawyer', [yesNoFetch($search)]);
                                    }
                                }
                            }

                            if(@$loop->first_option == "legaltech_vendor_or_consultancy")
                            {   
                                if($search)
                                {
                                    if($loop->filter_type == 'is')
                                    {
                                        $query->where('legaltech_vendor_or_consultancy',  yesNoFetch($search));
                                    }
                                    if($loop->filter_type == 'is_not')
                                    {
                                        $query->whereNotIn('legaltech_vendor_or_consultancy', [yesNoFetch($search)]);
                                    }
                                }
                            }

                            if(@$loop->first_option == "jurisdiction")
                            {   
                                if($search)
                                {
                                    if($loop->filter_type == 'is')
                                    {
                                        $query->where('jurisdiction', $search);
                                    }
                                    if($loop->filter_type == 'is_not')
                                    {
                                        $query->whereNotIn('jurisdiction', [$search]);
                                    }
                                    if($loop->filter_type == 'contains')
                                    {
                                        $query->whereRaw("jurisdiction LIKE '%". $search. "%'");
                                    }
                                }
                            }

                            if(@$loop->first_option == "pqe")
                            {   
                                if($search)
                                {
                                    if($loop->filter_type == 'is_more_than')
                                    {
                                        $query->where('if(@$loop->first_option == "pqe")', '>', $search);
                                    }
                                    if($loop->filter_type == 'is_less_than')
                                    {
                                        $query->where('if(@$loop->first_option == "pqe")', '<', $search);
                                    }
                                    if($loop->filter_type == 'is_between')
                                    {
                                        $query->where('if(@$loop->first_option == "pqe")', '>=', $search);
                                        $query->where('if(@$loop->first_option == "pqe")', '<=', $search_two);
                                    }
                                }
                            }

                            if(@$loop->first_option == "legal_experience")
                            {   
                                if($search)
                                {
                                    if($loop->filter_type == 'is')
                                    {
                                        $query->where('legal_experience', $search);
                                    }
                                    if($loop->filter_type == 'is_not')
                                    {
                                        $query->whereNotIn('legal_experience', [$search]);
                                    }
                                    if($loop->filter_type == 'contains')
                                    {
                                        $query->whereRaw("legal_experience LIKE '%". $search. "%'");
                                    }
                                }
                            }

                            if(@$loop->first_option == "area_of_law")
                            {   
                                if($search)
                                {
                                    if($loop->filter_type == 'is')
                                    {
                                        $query->where('area_of_law', $search);
                                    }
                                    if($loop->filter_type == 'is_not')
                                    {
                                        $query->whereNotIn('area_of_law', [$search]);
                                    }
                                    if($loop->filter_type == 'contains')
                                    {
                                        $query->whereRaw("area_of_law LIKE '%". $search. "%'");
                                    }
                                }
                            }

                            if(@$loop->first_option == "legal_experience")
                            {   
                                if($search)
                                {
                                    if($loop->filter_type == 'legal_experience')
                                    {
                                        $query->where('legal_experience', $search);
                                    }
                                    if($loop->filter_type == 'is_not')
                                    {
                                        $query->whereNotIn('legal_experience', [$search]);
                                    }
                                    if($loop->filter_type == 'contains')
                                    {
                                        $query->whereRaw("legal_experience LIKE '%". $search. "%'");
                                    }
                                }
                            }

                            if(@$loop->first_option == "time_in_industry")
                            {     
                                if($search && ctype_digit($search) != 1)
                                {   
                                    switch ($search) {
                                        case 'Less than a year':   
                                            $s_n_role = 0;  $s_n_role2 = 1;
                                            break;
                                        case '1-3 Years':
                                            $s_n_role = 1;  $s_n_role2 = 3;
                                            break;
                                        case '3-6 Years':
                                            $s_n_role = 3;  $s_n_role2 = 6;
                                            break;
                                        case '6-10 Years':
                                            $s_n_role = 6;  $s_n_role2 = 10;
                                            break;
                                        case '10-15 Years':
                                            $s_n_role = 10;  $s_n_role2 = 15;
                                            break;
                                        case '15-20 Years':
                                            $s_n_role = 15;  $s_n_role2 = 20;
                                            break;
                                        default:
                                            $s_n_role = 20;  $s_n_role2 = 100000;
                                            break;
                                    }
                                    $pastDtRole = date('Y-m-d', strtotime('-'.$s_n_role.' year'));
                                    $curDtRole = date('Y-m-d', strtotime('-'.$s_n_role2.' year'));
                                    
                                    if($loop->filter_type == 'is')
                                    {   
                                        $query->whereDate('time_in_industry', '<=', $pastDtRole);
                                        $query->whereDate('time_in_industry', '>', $curDtRole);
                                    }
                                    if($loop->filter_type == 'is_not')
                                    {
                                        $query->whereDate('time_in_industry', '>', $pastDtRole);
                                        $query->orWhereDate('time_in_industry', '<', $curDtRole);
                                    }
                                }
                            }

                            if(@$loop->first_option == "time_in_current_role")
                            {     
                                if($search && ctype_digit($search) != 1)
                                {   
                                    switch ($search) {
                                        case 'Less than a year':   
                                            $s_n_role = 0;  $s_n_role2 = 1;
                                            break;
                                        case '1-2 Years':
                                            $s_n_role = 1;  $s_n_role2 = 2;
                                            break;
                                        case '2-4 Years':
                                            $s_n_role = 2;  $s_n_role2 = 4;
                                            break;
                                        case '4-7 Years':
                                            $s_n_role = 4;  $s_n_role2 = 7;
                                            break;
                                        case '7-10 Years':
                                            $s_n_role = 7;  $s_n_role2 = 10;
                                            break;
                                        default:
                                            $s_n_role = 10;  $s_n_role2 = 100000;
                                            break;
                                    }
                                    $pastDtRole = date('Y-m-d', strtotime('-'.$s_n_role.' year'));
                                    $curDtRole = date('Y-m-d', strtotime('-'.$s_n_role2.' year'));
                                    
                                    if($loop->filter_type == 'is')
                                    {   
                                        $query->whereDate('time_in_current_role', '<=', $pastDtRole);
                                        $query->whereDate('time_in_current_role', '>', $curDtRole);
                                    }
                                    if($loop->filter_type == 'is_not')
                                    {
                                        $query->whereDate('time_in_current_role', '>', $pastDtRole);
                                        $query->orWhereDate('time_in_current_role', '<', $curDtRole);
                                    }
                                }
                            }
                        }
                    }
                }
            }
             
            $query->with(['employer_type_list', 'current_country_list', 'current_salary_symbol_list', 'current_bonus_or_commission_symbol_list', 
                'desired_salary_symbol_list', 'desired_bonus_or_commission_symbol_list', 'deal_size_symbol_list', 
                'sales_quota_symbol_list', 'current_working_arrangements_list','freelance_daily_rate_symbol_list', 'current_regions_list'
            ]);
            $query->orderBy('created_at', 'Desc');

            
            switch ($role) {
                case roleAdmin():
                    break;
                case roleGuest():
                    $query->select('id','uuid', 
                    'job_title', 'employer_type', 
                    'time_in_current_role', 'time_in_industry',
                    'desired_employer_type',
                    'line_management', 'notice_period', 
                    'desired_salary', 'desired_bonus_or_commission', 'desired_salary_symbol', 'desired_bonus_or_commission_symbol', 
                    'current_salary', 'current_salary_symbol', 'current_bonus_or_commission', 'current_bonus_or_commission_symbol', 
                    'current_country', 'desired_country', 'current_region',
                    'freelance_current', 'freelance_future', 'freelance_daily_rate as day_rate', 'freelance_daily_rate_symbol',
                    'working_arrangements', 'desired_working_arrangements', 
                    'law_degree', 'qualified_lawyer', 'jurisdiction', 'pqe', 'area_of_law',
                    'customer_type', 
                    'legal_tech_tools', 'tech_tools', 'qualification',
                    'legal_experience as legal_specialism',
                    );
                    break;
                case roleEmp():
                    $query->select('id','uuid',
                    'job_title', 'employer_type', 
                    'time_in_current_role', 'time_in_industry',
                    'status',  'desired_employer_type',
                    'line_management', 'notice_period', 
                    'desired_salary', 'desired_bonus_or_commission', 'desired_salary_symbol', 'desired_bonus_or_commission_symbol', 
                    'current_salary', 'current_salary_symbol', 'current_bonus_or_commission', 'current_bonus_or_commission_symbol', 
                    'current_country', 'desired_country', 'current_region',
                    'freelance_current', 'freelance_future', 'freelance_daily_rate as day_rate', 'freelance_daily_rate_symbol',
                    'working_arrangements', 'desired_working_arrangements', 
                    'law_degree', 'qualified_lawyer', 'jurisdiction', 'pqe', 'area_of_law',
                    'legaltech_vendor_or_consultancy', 'customer_type', 'deal_size', 'deal_size_symbol', 'sales_quota', 'sales_quota_symbol', 
                    'legal_tech_tools', 'tech_tools', 'qualification', 'languages',
                    'legal_experience as legal_specialism', 'profile_about',
                    );
                    $query->with(['is_cv_list_same_emp'  => function ($query) {
                            return $query->select('id','job_id', 'c_uid', 'is_cv');
                        }
                    ]);
                    
                    $query->orderBy('created_at', 'Desc');
                    $query->where(function ($query) {
                        $query->where('current_company_url', '!=' , employer(auth()->user()->email)->url)
                        ->orWhere('current_company_url', null);
                    });
                    
                    break;
                default:
                    $query->select('job_title');
                    break;
            }
            $response = $query->paginate(
                $perPage = 10, $columns = ['*'], $pageName = 'page'
            );
 
            return sendDataHelper("List", $response, ok());
             
        } catch (\Throwable $th) {
            throw $th;
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }    
         
    }
    
    public function candidatesShortList($request, $role)
    {   
         $req = $request;
         try {
            if(respValid($request)) { return respValid($request); }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/
            $request = (object) $request;   // count show to comment in filter count
            $response = [];
            if($role ==  roleEmp())
            {
                $emp_uuid = employer(auth()->user()->email)->value('uuid');
                $candidates_ids = EmpCandidate::where('emp_uuid', $emp_uuid)->pluck('c_uuid');
                $query = Candidate::query();
                $query->whereIn('uuid', $candidates_ids);
                $query->with('desired_employer_types.desired_employer_types_view');
                
                $query->where('harrier_candidate', yes());
                         
                $query->with(['employer_type_list', 'desired_salary_symbol_list', 'desired_bonus_or_commission_symbol_list','is_cv_list' => function ($query) {
                        return $query->select('id','job_id', 'c_uid', 'is_cv');
                    }
                ]);
                $query->whereHas('is_cv_list');
                $query->orderBy('created_at', 'Desc');

                
                switch ($role) {
                    case roleAdmin():
                        break;
                    case roleGuest():
                        break;
                    case roleEmp():
                        $query->select('id','uuid',
                            'job_title', 'employer_type', 
                            'time_in_current_role', 'time_in_industry',
                            'status',  'desired_employer_type',
                            'desired_salary', 'desired_bonus_or_commission', 'status',
                        );
                        $query->with(['is_cv_list_same_emp'  => function ($query) {
                                return $query->select('id','job_id', 'c_uid', 'is_cv');
                            }
                        ]);
                        
                        $query->orderBy('created_at', 'Desc');
                        $query->where(function ($query) {
                            $query->where('current_company_url', '!=' , employer(auth()->user()->email)->url)
                            ->orWhere('current_company_url', null);
                        });
                        break;
                    default:
                        $query->select('job_title');
                        break;
                };
                $response = $query->paginate(
                    $perPage = 10, $columns = ['*'], $pageName = 'page'
                );
                $response->makeHidden(['profile_path', 'cv_path', 'legal_tech_tools_list', 'tech_tools_list', 'pqe_diff', 
                'qualification_list', 'customer_type_list', 'desired_working_arrangements_list', 'desired_country_list',
                'languages_list','current_freelance', 'open_to_freelance', 'legaltech_vendor_or_consultancy_list', 'desired_employer_types', 'is_cv_list_same_emp']);
            }
            return sendDataHelper("List", $response, ok());
             
        } catch (\Throwable $th) {
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }    
    }

     /* Get Candidates list */
     public function candidatesListOnlyAdmin($request, $role)
     {   
          $req = $request;
          try {
            if(respValid($request)) { return respValid($request); }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/

            $data = Validator::make($request, [
                'uuid'=> 'nullable|exists:candidates,uuid',
            ]);

            if ($data->fails()) {
                return sendError($data->errors()->first(), [], errorValid());
            }
            $request = (object) $request;   // count show to comment in filter count
             
            $query = Candidate::query();
            $query->with('desired_employer_types.desired_employer_types_view');              
             $query->with(['employer_type_list', 'current_country_list', 'current_salary_symbol_list', 'current_bonus_or_commission_symbol_list', 
                 'desired_salary_symbol_list', 'desired_bonus_or_commission_symbol_list', 'deal_size_symbol_list', 
                 'sales_quota_symbol_list', 'current_working_arrangements_list','freelance_daily_rate_symbol_list', 'current_regions_list'
             ]);
             $query->orderBy('created_at', 'Desc');
             $query = $query->get();
            
             foreach ($query as $key => $response) {
             
                if($uuid = @$request->uuid)
                {
                    $query[$key]['uuid'] = $response->where('uuid', $uuid);
                }
               
                if(mst_employer_types($response->employer_type))
                {
                    $query[$key]['employer_type'] = mst_employer_types($response->employer_type)->title;
                } 

                if(count(mst_employer_types($response->desired_employer_type)) > 0)
                {
                    $query[$key]['desired_employer_type'] = mst_employer_types($response->desired_employer_type)->pluck('title');
                }

                if(mst_regions($response->current_region))
                {
                    $query[$key]['current_region'] = mst_regions($response->current_region)->title;
                }

                if(mst_countries($response->current_country))
                {
                    $query[$key]['current_country'] = mst_countries($response->current_country)->country_name;
                }

                if(count(mst_countries($response->desired_country)) > 0)
                {
                    $query[$key]['desired_country'] = mst_countries($response->desired_country)->pluck('country_name');
                }

                if(mst_working_arrangements($response->working_arrangements))
                {
                    $query[$key]['working_arrangements'] = mst_working_arrangements($response->working_arrangements)->title;
                }

                if(count(mst_working_arrangements($response->desired_working_arrangements)) > 0)
                {
                    $query[$key]['desired_working_arrangements'] = mst_working_arrangements($response->desired_working_arrangements)->pluck('title');
                }

                if(count(mst_customer_types($response->customer_type)) > 0)
                {                      
                    $query[$key]['customer_type'] = mst_customer_types($response->customer_type)->pluck('title');
                }
              
                // if(count(mst_legal_tech_tools($response->legal_tech_tools)) > 0)
                // {
                //     $query[$key]['legal_tech_tools'] = mst_legal_tech_tools($response->legal_tech_tools)->pluck('title');
                // }

                // if(count(mst_tech_tools($response->tech_tools)) > 0)
                // {
                //     $query[$key]['tech_tools'] = mst_tech_tools($response->tech_tools)->pluck('title');
                // }
                if(count(mst_qualifications($response->qualification)) > 0)
                {
                    $query[$key]['qualification'] = mst_qualifications($response->qualification)->pluck('title');
                }
                if(count(mst_languages($response->languages)) > 0)
                {
                    $query[$key]['languages'] = mst_languages($response->languages)->pluck('title');
                }
                
                if(mst_currencies($response->current_salary_symbol))
                {
                    $query[$key]['current_salary_symbol'] = mst_currencies($response->current_salary_symbol)->currency_code;
                }
                if(mst_currencies($response->desired_salary_symbol))
                {
                    $query[$key]['desired_salary_symbol'] = mst_currencies($response->desired_salary_symbol)->currency_code;
                }
                if(mst_currencies($response->current_bonus_or_commission_symbol))
                {
                    $query[$key]['current_bonus_or_commission_symbol'] = mst_currencies($response->current_bonus_or_commission_symbol)->currency_code;
                }
                if(mst_currencies($response->desired_bonus_or_commission_symbol))
                {
                    $query[$key]['desired_bonus_or_commission_symbol'] = mst_currencies($response->desired_bonus_or_commission_symbol)->currency_code;
                }
                if(mst_currencies($response->deal_size_symbol))
                {
                    $query[$key]['deal_size_symbol'] = mst_currencies($response->deal_size_symbol)->currency_code;
                }
                if(mst_currencies($response->sales_quota_symbol))
                {
                    $query[$key]['sales_quota_symbol'] = mst_currencies($response->sales_quota_symbol)->currency_code;
                }
                if(mst_currencies($response->freelance_daily_rate_symbol))
                {
                    $query[$key]['freelance_daily_rate_symbol'] = mst_currencies($response->freelance_daily_rate_symbol)->currency_code;
                }
            
            }
             
             $data = [
                'data' => $query
             ];
  
             return sendDataHelper("List", $data, ok());
              
         } catch (\Throwable $th) {
             throw $th;
             $bug = $th->getMessage();
             return sendErrorHelper('Error', $bug, error());
         }    
          
     }
}


