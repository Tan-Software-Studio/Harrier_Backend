<?php

namespace App\Http\Traits;

use App\Models\Emp\EmpOfficeLocation;
use App\Models\Emp\EmpWorkingSchedule;
use App\Models\Emp\JobOfficeLocation;
use App\Models\Emp\JobWorkingSchedule;
use App\Models\Employer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Job;
use App\Models\JobCondidate;
use App\Notifications\JobEmployeRequestCV;
use App\Notifications\JobInterestCandidate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

trait JobTrait {
    
    /* job store */
    public function jobPost($request, $emp_uid = null)
    {
        
        DB::beginTransaction();
        try {
            $req = $request;
            // $data = Validator::make($request->all(), [
            //     'attach_file'=> 'nullable|mimes:pdf,doc,docx|max:10000',
            // ]);
    
            // if ($data->fails()) {
            //     return sendError($data->errors()->first(), [], errorValid());
            // }

            if(respValid($request)) { return respValid($request); }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/

            //Convert your value to float
            $min_salary = floatval(str_replace(',' ,'', $request['salary_range_start']));
            $max_salary = floatval(str_replace(',' ,'', $request['salary_range_end']));

            //Get your range
            $min = $min_salary  + 0.01;
            $max = $max_salary - 0.01;
            
            $data = Validator::make($request, [
                'job_title' => 'required',
            ]);

            if ($data->fails()) {
                return sendError($data->errors()->first(), [], errorValid());
            }

            if ( Job::where('job_title', $request['job_title'])->where('emp_uid', $emp_uid)->first() ) {
                return sendError("The job title has already been taken.", [], errorValid());
            }


            $data = Validator::make($request, [
                'job_title' => 'required',
                'office_location' => 'required',
                'role_overview' => 'required',
                'candidate_requirements' => 'required',
                'working_schedule' => 'required',
                'salary_range_start_symbol' => 'required',
                'salary_range_start' =>  ['required', 'not_in:0'],
                'salary_range_end' =>  [
                    'required' ,'not_in:0'  ,
                    function($attribute, $value, $fail) use($max_salary, $min) {
                            if ($max_salary < $min) {
                                return $fail('Maximum salary must be greater than minimum salary.');
                            }
                        }],
                'salary_range_end_symbol' => 'nullable',
                'additional_benefits' => 'required',
            ],[
                'job_title.required' => 'Enter job title',
                'office_location.required' => 'Select office locations',
                'role_overview.required' => 'Enter What are the duties required in this role?',
                'candidate_requirements.required' => 'Enter candidate requirements',
                'working_schedule.required' => 'Select working schedules',
                'salary_range_start_symbol.required' => 'Select salary range currency',
                'salary_range_start.required' => 'Enter minimum salary ',
                'salary_range_start.not_in' => 'Enter minimum salary, 0 value not accepted',
                'salary_range_end.required' => 'Enter maximum salary',
                'salary_range_end.not_in' => 'Enter maximum salary',
                'additional_benefits.required' => 'Enter additional benefits',
            ]);

            if ($data->fails()) {
                return sendError($data->errors()->first(), [], errorValid());
            }else{
                if(!$emp_uid)
                {
                    $data = Validator::make($request, [
                        'emp_uid' => 'required|exists:employers,uuid',
                    ]);
        
                    if ($data->fails()) {
                        return sendError($data->errors()->first(), [], errorValid());
                    }
                    
                    $emp_uid = $request['emp_uid'];
                }
                $request = (object) $request;
                if ( Job::where('job_title', $request->job_title)->where('emp_uid', $emp_uid)->first() ) {
                    return sendError("The job title has already been taken.", [], errorValid());
                }

                $data = Validator::make($req->all(), [
                    'attach_file'=> 'required|mimes:pdf,doc,docx|max:10000',
                ],[
                    'attach_file.required' => 'Please upload a file'
                ]); 
        
                if ($data->fails()) {
                    return sendError($data->errors()->first(), [], errorValid());
                }

                $in = new Job;
                $in->emp_uid = $emp_uid;
                $in->job_title = @$request->job_title ?? null;
                // if(@$request->office_location)
                // {
                //     $in->office_location = @$request->office_location;
                // }
                $in->role_overview = @$request->role_overview ?? null;
                $in->salary_range_start = @$request->salary_range_start ?? null;
                $in->salary_range_start_symbol = @$request->salary_range_start_symbol ?? 1;
                $in->salary_range_end = @$request->salary_range_end ?? null;
                $in->salary_range_end_symbol = @$request->salary_range_end_symbol ?? 1;
                $in->candidate_requirements = @$request->candidate_requirements ?? null;
                $in->additional_benefits = @$request->additional_benefits ?? null;

                if ($req->hasFile('attach_file'))    
                {   
                    $in->attach_file  = uploadFile($req['attach_file'], 'uploads/attach_file') ?? null;
                }
                
                // return $in;
                $in->save();
                $vv = Employer::where('uuid',$in->emp_uid)->first();
                $datas = [
                    'name' => Auth::user()->name,
                    'job_title' =>  @$request->job_title,   
                ];  
                // dd($inn);
                $vv->notify(new JobEmployeRequestCV($datas));
                if($in && @$request->office_location)
                {
                    $this->updateAndCreateWorkingSchedule($in->id, @$request->working_schedule);
                }

                if($in && @$request->office_location)
                {
                    $this->updateAndCreateOfficeLocation($in->id, @$request->office_location);
                }
                DB::commit();
                
                return sendDataHelper(trans('msg.details_saved'), [], ok());
            }
        } catch (\Throwable $th) {
            throw $th;
            // throw $th;
            DB::rollBack();
            $bug = $th->getMessage();
            return sendErrorHelper(trans('msg.error'), $bug, error());
        }
    }

    public function jobEmployer($job_id)
    {
        $query =  DB::table('jobs');
        $query->where('jobs.id', $job_id);
        $query->leftJoin('employers as emp', 'jobs.emp_uid', '=','emp.uuid');
        $query->select('jobs.*','emp.email','emp.name');
        return $job = $query->first();
    }
    
    /* Live jobs Details */
    public function liveJobDetails($request, $role)
    {
        try {
            if(respValid($request)) { return respValid($request); }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/
            $request = (object) $request;
            $query = Job::query();  
            switch ($role) {
                case roleAdmin():
                    break;
                case roleGuest():   
                    break;
                case roleEmp():
                    $emp = employer(auth()->user()->email);
                    $query->where('emp_uid', $emp->uuid);
                    break;
                default:
                    $query->select('job_title');
                    break;
            }   
            $query->with(['salary_range_start_symbol_list', 'salary_range_end_symbol_list', 'working_schedule', 'office_location', 'employer_list']);
            $query->where('status', active());
            if(!is_array(@$request->live_job_ids) && @$request->live_job_ids && $live_job_ids = $request->live_job_ids)
            {
                $query->whereIn('id', [$live_job_ids]);
            }
            $query->select('job_title', 'id', 'emp_uid', 'salary_range_start', 'salary_range_start_symbol','salary_range_end', 'salary_range_end_symbol','candidate_requirements', 'additional_benefits', 'role_overview', 'attach_file');
            $response = $query->get();
            
            return sendDataHelper(trans('msg.list'), $response, ok());
        } catch (\Throwable $th) {
            $bug = $th->getMessage();
            return sendErrorHelper(trans('msg.error'), $bug, error());
        }
    }

    public function jobIntNotify($request)
    {
        $jobCand = JobCondidate::firstOrNew(
            ['job_id' => $request->job_id, 'c_uid' => $request->candidate_uuid],
        );
        $jobCand->c_job_status = $request->job_status ?? 0;
        $jobCand->save();
        
        if($jobCand)
        {
            if($job = jobEmployer($jobCand->job_id))
            {
                if($emp = emp_uuid_list($job->emp_uid))
                {   
                    $data = [
                        'email' => $job->email,
                        'name' => $job->name,
                        'job_title' => $job->job_title,
                        'job_id' => $job->id
                    ];
                    $emp->notify(new JobInterestCandidate($data));
                }
            }   
        }
    }

    /* Job post update
     */
    public function jobPostUpdate($request, $auth = null)
    {
        $req = $request;

        // $data = Validator::make($req->all(), [
        //     'attach_file'=> 'nullable|mimes:pdf,doc,docx|max:10000',
        // ]);

        // if ($data->fails()) {
        //     return sendError($data->errors()->first(), [], errorValid());
        // }

        if(respValid($request)) { return respValid($request); }  /* response required validation */
        $request = decryptData($request['response']); /* Dectrypt  **/


        
        DB::beginTransaction();
        try {

            //Convert your value to float
            $min_salary = floatval(str_replace(',' ,'', $request['salary_range_start']));
            $max_salary = floatval(str_replace(',' ,'', $request['salary_range_end']));

            //Get your range
            $min = $min_salary  + 0.01;
            $max = $max_salary - 0.01;

            $data = Validator::make($request, [
                'job_id' => 'required|exists:jobs,id',
                'job_title' => 'required',
            ],[
                'job_title.required' => 'Enter job title',
                'job_id.exists' => 'Selected job not found'
            ]);

            if ($data->fails()) {
                return sendError($data->errors()->first(), [], errorValid());
            }

            $job = Job::find($request['job_id']);
            if($auth)
            {
                $emp = employer($auth->email);
                if ($job->emp_uid != $emp->uuid) {
                    return sendError("The selected job id is invalid.", [], errorValid());
                }

                if (Job::where('job_title', $request['job_title'])->where('emp_uid', $emp->uuid)->whereNotIn('id', [$job->id])->first()) {
                    return sendError("The job title has already been taken.", [], errorValid());
                }
            }else{
                if ( Job::where('job_title', $request['job_title'])->where('emp_uid', $job->emp_uid)->whereNotIn('id', [$job->id])->first() ) {
                    return sendError("The job title has already been taken.", [], errorValid());
                }
            }
            
            
            $data = Validator::make($request, [
                'office_location' => 'required',
                'role_overview' => 'required',
                'candidate_requirements' => 'required',
                'working_schedule' => 'required',
                'salary_range_start_symbol' => 'required',
                'salary_range_start' =>  ['required', 'not_in:0'],
                'salary_range_end' =>  [
                    'required' ,'not_in:0'  ,
                    function($attribute, $value, $fail) use($max_salary, $min) {
                            if ($max_salary < $min) {
                                return $fail('Maximum salary must be greater than minimum salary.');
                            }
                        }],
                'salary_range_end_symbol' => 'nullable',
                'additional_benefits' => 'required',
            ],[
                'office_location.required' => 'Select office locations',
                'role_overview.required' => 'Enter What are the duties required in this role?',
                'candidate_requirements.required' => 'Enter candidate requirements',
                'working_schedule.required' => 'Select working schedules',
                'salary_range_start_symbol.required' => 'Select salary range currency',
                'salary_range_start.required' => 'Enter minimum salary ',
                'salary_range_start.not_in' => 'Enter minimum salary, 0 value not accepted',
                'salary_range_end.required' => 'Enter maximum salary',
                'salary_range_end.not_in' => 'Enter maximum salary',
                'additional_benefits.required' => 'Enter additional benefits',
            ]);

            if ($data->fails()) {
                return sendError($data->errors()->first(), [], errorValid());
            }else{

                $request = (object) $request;
               
                if(!$job->attach_file)
                {
                    $data = Validator::make($req->all(), [
                        'attach_file'=> 'required|mimes:pdf,doc,docx|max:10000',
                    ],[
                        'attach_file.required' => 'Please upload a file'
                    ]); 
            
                    if ($data->fails()) {
                        return sendError($data->errors()->first(), [], errorValid());
                    }
                }
                
                
                $job->job_title = $request->job_title ?? $job->job_title;
                
                $job->role_overview = @$request->role_overview;
                $job->salary_range_start = @$request->salary_range_start;
                $job->salary_range_start_symbol = @$request->salary_range_start_symbol ?? 1;
                $job->salary_range_end = @$request->salary_range_end;
                $job->salary_range_end_symbol = @$request->salary_range_end_symbol ?? 1;
                $job->candidate_requirements = @$request->candidate_requirements;
                $job->additional_benefits = @$request->additional_benefits;

                if ($req->hasFile('attach_file'))    
                {   
                    if($job && !empty($job->attach_file)){
                        if (File::exists(attach_file_public_path().$job->attach_file)) 
                        {
                            unlink(attach_file_public_path().$job->attach_file);
                        }
                    }
                    $job->attach_file  = uploadFile($req['attach_file'], 'uploads/attach_file') ?? null;
                }

                $job->save();

                if($job && @$request->working_schedule)
                {
                    $this->updateAndCreateWorkingSchedule($job->id, @$request->working_schedule);
                }

                if($job && @$request->office_location)
                {
                    $this->updateAndCreateOfficeLocation($job->id, @$request->office_location);
                }
                
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

    /* Job active or inactive
     */
    public function jobActiveInactive($request, $emp = null)
    {
        $req = $request;

        if(respValid($request)) { return respValid($request); }  /* response required validation */
        $request = decryptData($request['response']); /* Dectrypt  **/

        DB::beginTransaction();
        try {
            $data = Validator::make($request, [
                'job_id' => 'required|exists:jobs,id',
            ]);

            if ($data->fails()) {
                return sendError($data->errors()->first(), [], errorValid());
            }
            
            $request = (object) $request;

            if(!$emp)
            {
                $data = Validator::make($request, [
                    'emp_uuid' => 'required|exists:employers,uuid',
                ]);
    
                if ($data->fails()) {
                    return sendError($data->errors()->first(), [], errorValid());
                }
                $emp_uid = $request->emp_uuid;
            }else{
                $emp_uid = $emp->uuid;
            }
            $request = (object) $request;
            if($job = Job::where('emp_uid', $emp_uid)->find($request->job_id))
            {       
                $job->status = (($job->status == 1) ? inactive() : active());
                $job->save();
                DB::commit();
                return sendDataHelper('Status updated succcessfully.', [], ok());
            }
            return sendError('Something went wrong.', [], errorValid());
        } catch (\Throwable $th) {
            DB::rollBack();
            // throw $th;
            $bug = $th->getMessage();
            return sendErrorHelper(trans('msg.error'), $bug, error());
        }
    }

    /* single job show */
    public function singleJobShow($request, $emp = null)
    {
        try {
            if(respValid($request)) { return respValid($request); }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/
            $data = Validator::make($request, [
                'job_id' => 'required|exists:jobs,id',
            ]);    
            if ($data->fails()) {
                return sendError($data->errors()->first(), [], errorValid());
            }else{
                $request = (object) $request;

                $query = Job::query();
                $query->with(['salary_range_start_symbol_list', 'salary_range_end_symbol_list', 'working_schedule', 'office_location', 'employer_list']);
                $query->withTrashed();

                if(!$emp)
                {
                    // $query->with(['working_schedule', 'employer_list']);
                }else{
                    // $query->with(['working_schedule']);
                }
                $response = $query->find($request->job_id);
                return sendDataHelper(trans('msg.list'), $response, ok());
            }
        } catch (\Throwable $th) {
            $bug = $th->getMessage();
            return sendErrorHelper(trans('msg.error'), $bug, error());
        }
    }

    /* Live jobs Details */
    public function allJobsDetails($request, $role)
    {
        try {
            if(respValid($request)) { return respValid($request); }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/
            $request = (object) $request;
            $query = Job::query();  
            switch ($role) {
                case roleAdmin():
                    break;
                case roleGuest():   
                    break;
                case roleEmp():
                    $emp = employer(auth()->user()->email);
                    $query->where('emp_uid', $emp->uuid);
                    break;
                default:
                    $query->select('job_title');
                    break;
            }   
            $query->with(['salary_range_start_symbol_list', 'salary_range_end_symbol_list', 'working_schedule.emp_working_schedule', 'office_location.emp_office_locations', 'employer_list']);
            if(!is_array(@$request->live_job_ids) && @$request->live_job_ids && $live_job_ids = $request->live_job_ids)
            {
                $query->whereIn('id', [$live_job_ids]);
            }
            $query->orderBy('status', 'Desc');
            $response = $query->get();
            
            return sendDataHelper(trans('msg.list'), $response, ok());
        } catch (\Throwable $th) {
            $bug = $th->getMessage();
            return sendErrorHelper(trans('msg.error'), $bug, error());
        }
    }

    public function updateAndCreateWorkingSchedule($job_id, $working_schedule_ids)
    {
        if(count($working_schedule_ids))
        {
            $schedules_ids =  EmpWorkingSchedule::whereIn('id', $working_schedule_ids)->pluck('id');
            if(count($schedules_ids))
            {
                $technologies =  JobWorkingSchedule::where('job_id', $job_id)
                ->whereIn('working_schedule_id', $schedules_ids)
                ->pluck('working_schedule_id');
    
                $delete = JobWorkingSchedule::where('job_id', $job_id)
                    ->whereNotIn('working_schedule_id', $schedules_ids)
                    ->delete();
            
                foreach($schedules_ids as $sched_id)
                {
                    if(!JobWorkingSchedule::where(['working_schedule_id' =>  $sched_id, 'job_id' => $job_id])->first())
                    {
                        $input['working_schedule_id'] = $sched_id;
                        $input['job_id'] = $job_id;
                        $response = DB::table('job_working_schedules')->insert($input);
                    }
                }
            }
        }
    }

    public function updateAndCreateOfficeLocation($job_id, $office_location_id)
    {
        if(count($office_location_id))
        {
            $location_ids =  EmpOfficeLocation::whereIn('id', $office_location_id)->pluck('id');
            if(count($location_ids))
            {
                $technologies =  JobOfficeLocation::where('job_id', $job_id)
                ->whereIn('office_location_id', $location_ids)
                ->pluck('office_location_id');
    
                $delete = JobOfficeLocation::where('job_id', $job_id)
                    ->whereNotIn('office_location_id', $location_ids)
                    ->delete();
            
                foreach($location_ids as $location_id)
                {
                    if(!JobOfficeLocation::where(['office_location_id' =>  $location_id, 'job_id' => $job_id])->first())
                    {
                        $input['office_location_id'] = $location_id;
                        $input['job_id'] = $job_id;
                        $response = DB::table('job_office_locations')->insert($input);
                    }
                }
            }
        }
    }

    public function working_schedule_names($working_schedule_rel)
    {
        $names = [];
        foreach($working_schedule_rel as $ks =>  $scheule)
        {
            if($scheule && $scheule->emp_working_schedule)
            {
                $names[$ks] = $scheule->emp_working_schedule->schedule;
            }
        }
        return $names;
    } 

    public function office_location_names($office_location_rel)
    {
        $names = [];
        foreach($office_location_rel as $ks =>  $loc)
        {
            if($loc && $loc->emp_office_locations)
            {
                $names[$ks] = $loc->emp_office_locations->location;
            }
        }
        return $names;
    } 
}