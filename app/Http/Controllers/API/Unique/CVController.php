<?php

namespace App\Http\Controllers\API\Unique;

use App\Http\Controllers\Controller;
use App\Models\JobCondidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Notifications\InterviewRequested;
use App\Models\User;
use App\Models\Candidate;

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

            if ($s = $request->input('search')) {
                $query->whereRaw("emp_uid LIKE '%" . $s . "%'")
                    ->orWhereRaw("c_uid LIKE '%" . $s . "%'");
            }

            if ($status = $request->input('c_job_status')) {
                $query->where('c_job_status', $status);
            }
            // return $i = $query->get();
            $list = $query->paginate(
                $perPage = 10,
                $columns = ['*'],
                $pageName = 'page'
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


    public function interviewReq(Request $request)
    {
        try {
            if(respValid($request)) { return respValid($request); }  /* response required validation */
            $request = decryptData($request['response']);
            $data = Validator::make($request, [
                'id' => 'required'
            ],[
                'id.required' => 'Candidate ID required.',
             ]);

            if ($data->fails()) {
                return sendError($data->errors()->first(), [], errorValid());
            } else {
                $request = (object) $request;
                $in = JobCondidate::where("c_uid", $request->id)->latest()->first();

                $in->c_uid = $request->id;
                $in->interview_request = is_Interview_requested();
                $in->interview_request_date = date('Y-m-d');
                $in->save();
                
                DB::commit();
                
                if ($admin = adminTable()) {
                    $emp_uuid = $request->empId;
                    $user = User::where('role', roleAdmin())->first();
                    $cand = Candidate::where('uuid', $in->c_uid)->first();
                    $emp = employer_uuid($emp_uuid);
                    if ($emp && $cand) {
                        $data = [
                            'type' => config('constants.notification_type.interview_req.key'),
                            'email' => $emp->email,
                            'message' => config('constants.notification_type.interview_req.message') . ' for Candidate Id #' . $cand->id,
                            'cand_id' => $cand->id,
                            'cand_email' => $cand->email,
                            'cand_name' => $cand->first_name . ' ' . $cand->last_name,
                            'emp_email' => $emp->email,
                            'user_name' => $user->name,
                            'job_name' => $cand->job_title,
                        ];
                        $data = (object) $data;
                        $admin['email'] = env('INTER_REQUEST_RECIEVE_MAIL');
                        $admin->notify(new InterviewRequested($data));
                        // $emp->notify(new CVRequestSendYou($data));

                    }
                }
                return sendDataHelper('Interview request sent.', [], ok());
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            // throw $th;
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }
    }
}
