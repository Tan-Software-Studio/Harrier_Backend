<?php

namespace App\Http\Controllers\API\Unique;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobCondidate;
use Illuminate\Http\Request;

class CVController extends Controller
{
    public function cvReqListAdminGet(Request $request)
    {
        return self::cvReqList($request, auth()->user()->role);
    }

    public function cvReqListEmpGet(Request $request)
    {
        return self::cvReqList($request, auth()->user()->role);
    }

    /* Employers request a candidates CV list */
    public function cvReqList($request, $role)
    {   
        try {
            $query = JobCondidate::query();
            $query->orderBy('created_at', 'Desc');
            $query->with('job_list.employer_list');
            $query->where('is_cv', is_requested());
            switch ($role) {
                case canGUEST():
                    $query->select('id', 'job_id', 'c_uid', 'c_job_status', 'is_cv', 'cv');
                    break;
                case canEMP():
                    $auth = employer(auth()->user()->email);
                    $query->select('id', 'job_id', 'c_uid', 'c_job_status', 'is_cv', 'cv');
                    // return $query = $query->get();
                    break;
                case canADMIN():
                    $query->with('candidate_list');
                    $query->with('job_list.employer_list');
                    break;               
                default:
                    $query->select('id', 'job_id', 'c_uid', 'c_job_status', 'is_cv', 'cv');
                    break;
            }
            
            if($s = $request->input('search'))
            {
                $query->whereRaw("emp_uid LIKE '%". $s. "%'")
                ->orWhereRaw("c_uid LIKE '%". $s . "%'");
            }

            if($status = $request->input('c_job_status'))
            {
                $query->where('c_job_status', $status);
            }
            // return $i = $query->get();
            $list = $query->paginate(
                $perPage = 10, $columns = ['*'], $pageName = 'page'
            );        
            $response = [
                'list' => $list
            ];
            return sendDataHelper("List", $response, ok());
            
        } catch (\Throwable $th) {
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }
    }
}
