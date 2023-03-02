<?php

namespace App\Http\Controllers\API\Emp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employer;
use App\Models\Job;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use App\Http\Traits\JobTrait;
use App\Models\JobCondidate;
use App\Models\User;
use App\Notifications\JobInterestCandidate;

class JobConttroller extends Controller
{
    use JobTrait;
    /**
     * Jobs Store
    */
    public function adminjobPost(Request $request)
    {
        return self::jobPost($request);
    }

    /* Emp get a candidates list */
    public function empjobPost(Request $request)
    {
        $emp = employer(auth()->user()->email);
        return self::jobPost($request, $emp->uuid);
    }

    /**
     * Jobs post update
    */
    public function adminJobPostUpdate(Request $request)
    {
        return self::jobPostUpdate($request);
    }

    /* Emp get a candidates list */
    public function empJobPostUpdate(Request $request)
    {
        return self::jobPostUpdate($request, auth()->user());
    }

    /* Jobs list */
    public function jobsList(Request $request)
    {
        $req = $request;
        try {
            if(respValid($request)) { return respValid($request); }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/
            $request = (object) $request;

            $query = Job::query();
            $query->select('job_title', 'office_location', 'role_overview', 'salary_range_start', 'salary_range_end', 'candidate_requirements', 'additional_benefits');
          

            if($s = $req->input('search'))
            {
                $query->whereRaw("job_title LIKE '%". $s. "%'")
                ->orWhereRaw("role_overview LIKE '%". $s . "%'");
            }

            if($role_overview = $req->input('role_overview'))
            {
                $query->where('role_overview', $role_overview);
            }

            if($job_title = $req->input('job_title'))
            {
                $query->where('job_title', $job_title);
            }
            if($status = $req->input('status'))
            {
                $query->where('status', $status);
            }
            if($salary_range_start = $req->input('salary_range_start'))
            {
                $query->where('salary_range_start', '>=', $salary_range_start);
            }
            if($salary_range_end = $req->input('salary_range_end'))
            {
                $query->where('salary_range_end', '<=', $salary_range_end);
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

    /* Jobs Names */
    public function liveJobNames(Request $request)
    {
        try {
            if(respValid($request)) { return respValid($request); }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/
            $request = (object) $request;
            $emp = employer(auth()->user()->email);
            $query = Job::query();
            $query->where('status', active());
            $query->where('emp_uid', $emp->uuid);
            $query->select('job_title', 'id', 'emp_uid');
            $response = $query->get();
            
            return sendDataHelper(trans('msg.list'), $response, ok());
        } catch (\Throwable $th) {
            $bug = $th->getMessage();
            return sendErrorHelper(trans('msg.error'), $bug, error());
        }
    }

    /* Guest get a candidates list */
    public function guestLiveJobDetails(Request $request) 
    {
        return self::liveJobDetails($request, auth()->user()->role);
    }

    /* Admin get a candidates list */
    public function adminLiveJobDetails(Request $request)
    {
        return self::liveJobDetails($request, auth()->user()->role);
    }

    /* Emp get a candidates list */
    public function empLiveJobDetails(Request $request)
    {
        return self::liveJobDetails($request, auth()->user()->role);
    }

    /* Select live jobs interested. */
    public function adminSelectLiveJobsInterested(Request $request)
    {
        DB::beginTransaction();
        try {
            if(respValid($request)) { return respValid($request); }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/
           
            $data = Validator::make($request, [
                'job_id' => 'required|exists:jobs,id',
                'candidate_uuid' => 'required|exists:candidates,uuid',
                'job_status' => 'required',
            ]);

            if ($data->fails()) {
                return sendError($data->errors()->first(), [], errorValid());
            }else{
                $request = (object) $request;
                self::jobIntNotify($request);
                DB::commit();
                return sendDataHelper(trans('msg.details_saved'), [], ok());
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            // throw $th;
            $bug = $th->getMessage();
            return sendErrorHelper(trans('msg.error'), $bug, error());
        }
    }

    /* Guest get a candidates list */
    public function adminCandidateInterestedLiveJobslist(Request $request) 
    {
        return self::candidateInterestedLiveJobslist($request, auth()->user()->role);
    }

    /* Admin get a candidates list */
    public function empCandidateInterestedLiveJobslist(Request $request)
    {
        return self::candidateInterestedLiveJobslist($request, auth()->user()->role);
    }

    /* all interested live jobs list*/
    public function candidateInterestedLiveJobslist($request, $role)
    {
        // $req = $request;
        try {
            if(respValid($request)) { return respValid($request); }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/           
            $request = (object) $request;
            
            $query = Job::query();
            
            if($role == roleEmp() )
            {
                $query->where('emp_uid', employer(auth()->user()->email)->uuid);
            }
            
            $query->with(['salary_range_start_symbol_list', 'salary_range_end_symbol_list', 'working_schedule.emp_working_schedule', 'office_location.emp_office_locations']);
            $query->whereHas('job_candidate_list.candidate_list')->with(['job_candidate_list.candidate_list' => function($job_cand) use($role){
                $job_cand->where('harrier_candidate', yes());
            }]);
            
            $responseArrays = $query->get();
            $response = array();
            if(count($responseArrays))
            {
                $job_data = [];
                foreach($responseArrays as $res)
                {
                    if($res)
                    {
                        $job_data['job_id'] = $res->id;
                        $job_data['job_title'] = $res->job_title;
                        $job_data['salary_range_start_symbol'] = @$res->salary_range_start_symbol_list->currency_code;
                        $job_data['salary_range_start'] = number_format($res->salary_range_start);
                        $job_data['salary_range_end_symbol'] = @$res->salary_range_end_symbol_list->currency_code;
                        $job_data['salary_range_end'] = number_format($res->salary_range_end);

                        $working_schedule = [];
                        if(count($res->working_schedule) > 0)
                        {
                            // $working_schedule = $this->working_schedule_names($res->working_schedule);
                            $working_schedule = implode(', ', $this->working_schedule_names($res->working_schedule));
                        }

                        $office_location = [];
                        if(count($res->office_location) > 0)
                        {
                            $office_location = $this->office_location_names($res->office_location);
                            // $office_location = implode(', ', $this->office_location_names($res->office_location));
                        }

                        $job_data['working_schedule'] = $working_schedule; 
                        $job_data['office_location'] = $office_location;

                        $job_candidateArray = [];
                        $cv_count = 0;
                        $int_req_count = 0;
                        if(count($res->job_candidate_list) > 0)
                        {
                            
                            foreach($res->job_candidate_list as $job_cand)
                            {
                                if($job_cand->is_cv == is_requested()) { $cv_count++;    }
                                if($job_cand->interview_request == 1) { $int_req_count++; }
                                
                                if($job_cand->candidate_list)
                                {
                                    $cands = [
                                        'c_id' => $job_cand->candidate_list->id,
                                        'c_uuid' => $job_cand->candidate_list->uuid,
                                        'created_at' => date('d-m-Y', strtotime($job_cand->created_at)),
                                        'c_job_status' => ($job_cand->candidate_job_status_list ? $job_cand->candidate_job_status_list->title : null ) ,
                                    ];
                                    array_push($job_candidateArray, $cands);
                                    // $job_candArray['candidate_list'] = $job_cand;
                                }
                            }
                        }
                        $job_data['job_candidate_list'] = $job_candidateArray;
                        $job_data['cv_requests_count'] =  $cv_count;
                        $job_data['interview_requests_count'] =  $int_req_count;

                    }
                    $responseArray = $job_data;
                    array_push($response, $responseArray);
                }
            }
            return sendDataHelper("List", $response, ok());
        } catch (\Throwable $th) {
            throw $th;
            $bug = $th->getMessage();
            return sendErrorHelper(trans('msg.error'), $bug, error());
        }
    }

    
    public function admEmpJobActiveInactive(Request $request)
    {
        return self::jobActiveInactive($request);
    }

    /* Emp get a candidates list */
    public function empJobActiveInactive(Request $request)
    {
        $emp = employer(auth()->user()->email);
        return self::jobActiveInactive($request, $emp);
    }


    /*Admin single job show */
    public function admEmpSingleJobShow(Request $request)
    {        
        return self::singleJobShow($request);        
    }

    /*Emp single job show */
    public function empSingleJobShow(Request $request)
    {
        $emp = employer(auth()->user()->email);
        return self::singleJobShow($request, $emp);
        
    }

    /* Admin get a all active & inactive Jobs list */
    public function adminAllJobsDetails(Request $request)
    {
        return self::allJobsDetails($request, auth()->user()->role);
    }

    /* Emp get a all active & inactive Jobs list */
    public function empAllJobsDetails(Request $request)
    {
        return self::allJobsDetails($request, auth()->user()->role);
    }
    
}