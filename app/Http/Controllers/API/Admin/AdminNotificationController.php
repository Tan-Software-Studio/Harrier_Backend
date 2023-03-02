<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\EmployerTrait;
use App\Http\Traits\JobTrait;
use App\Models\JobCondidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\User;
use App\Notifications\CVAccepted;
use App\Notifications\CVRequested;
use Illuminate\Validation\Rule;

class AdminNotificationController extends Controller
{
    use  EmployerTrait;
    use JobTrait;

    /*User all notifications*/
    public function allNotifications()
    {
        $notifications = auth()->user()->notifications;
        $notifications->makeHidden(['created_at', 'updated_at']);
        $response = [
            'count' =>  $notifications->count(),
            'notifications' => $notifications
        ]; 
        return sendDataHelper('Notifications', $response, ok());
    }

    /*User Unread notifications*/
    public function unreadNotifications()
    {
        $notifications = auth()->user()->unreadNotifications;
        $notifications->makeHidden(['created_at', 'updated_at']);
        $response = [
            'count' =>  $notifications->count(),
            'notifications' => $notifications
        ]; 
        return sendDataHelper('Notifications', $response, ok());
    }

    /*Mark as read notifications */
    public function masAsReadNotifications(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $user->unreadNotifications->markAsRead();
            DB::commit();
            return sendDataHelper('All unread notifications reading done.', [], ok());
        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::info($th);
            return sendErrorHelper('Error', $th->getMessage(), error());
        }
    }

    /*Mark as read notification selected */
    public function masAsReadNotificationSelected(Request $request, $notificationid)
    {
        DB::beginTransaction();
        try {
            $notification = auth()->user()->notifications()->find($notificationid);
            if($notification) {
                $notification->markAsRead();
                DB::commit();
                return sendDataHelper('Notification reading done.', [], ok());
            }
            return sendErrorHelper('Data not found', [], error());
        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::info($th);
            return sendErrorHelper('Error', $th->getMessage(), error());
        }
    }
    /*Delete notifications */
    public function deleteNotification(Request $request, $notification_id)
    {
        $request['notification_id'] = $notification_id;
        DB::beginTransaction();
        try {
            $request->validate([
                'notification_id' => 'required|exists:notifications,id'
            ]);
            $user = auth()->user();
            $notify = $user->notifications->find($request->notification_id);
            if(isset($notify))
            {   
                $notify->delete();
                DB::commit();
                return sendDataHelper('Notification removed success.', [], ok());
            }
            DB::commit();
            return sendErrorHelper('Data not found', [], error());
        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::info($th);
            return sendErrorHelper('Error', $th->getMessage(), error());
        }
    }

    

    /** * employer login credential recieve via email*/
    public function empStatusUpdate(Request $request)
    {
        DB::beginTransaction();
        $role = roleEmp();
        // return $this->acceptAndCredentialSent($request, roleEmp());
        try {
            if(respValid($request)) { return respValid($request); }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/
           
            $data = Validator::make($request, [
                'email' => 'required|exists:users,email|'.Rule::exists('users')->where(function ($query, $role) {
                    return $query->where('role', $role);
                }),
                'status' => 'required|in:1,0'
            ]);

            if ($data->fails()) {
                return sendError($data->errors()->first(), [], errorValid());
            }else{

                $request = (object) $request;
                $passwordGenerate  = Str::random(6);
                if($in = User::where('role', $role)->where('email', $request->email)->first())
                {
                    $in->is_request = false;
                    $in->is_login = false;
                    
                    if($request->status == 1)
                    {
                        $in->email_verified_at = now();
                        $n = 'aceepted';
                        $in->password = bcrypt($passwordGenerate);
                    }else{
                        $n = 'rejected';
                    }
                    $in->status = $request->status;
                    $in->save();  
                    
                    DB::table('employers')->where('email', $in->email)->update(['status' => $request->status]);
                    DB::commit();
                    
                    $title = (canADMIN() ? 'Status' : 'Login');
                    if($in->status == 1)
                    {
                        $list = $in;
                        $list['password'] = $passwordGenerate;
                        $list['role'] = 'emp';
                        $this->mailsendLoginAccept($list);
                    }
                    
                    
                    return sendDataHelper($title.' '.$n.' successfully.', [], ok());
                }else{
                    return sendError('List not found.', [], error());
                }
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return sendErrorHelper('Error', $th->getMessage(), error());
        }
    }

    /**
     * Admin accept and credential sent
    */
    public function acceptAndCredentialSent($request, $role)
    {
        try {
            if(respValid($request)) { return respValid($request); }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/
           
            $data = Validator::make($request, [
                'email' => 'required|exists:users,email|'.Rule::exists('users')->where(function ($query, $role) {
                    return $query->where('role', $role);
                }),
                'status' => 'required|in:1,0'
            ]);

            if ($data->fails()) {
                return sendError($data->errors()->first(), [], errorValid());
            }else{
                $passwordGenerate  = Str::random(6);
                if($in = User::where('role', $role)->where('email', @$request['email'])->first())
                {
                    $in->is_request = false;
                    $in->is_login = false;
                    
                    if(@$request['status'] == 1)
                    {
                        $n = 'aceepted';
                        // $in->status = @$request['status'];
                    }else{
                        $n = 'rejected';
                        // $in->status = @$request['status'];
                    }

                    $in->status = false;
                    
                    $in->password = bcrypt($passwordGenerate);
                    $in->email_verified_at = now();
                    $in->save();  
                      // $in->notify(new RequestAccept($in));
                    
                    return sendDataHelper('Login '.$n.' successfully.', [], ok());
                }else{
                    return sendError('List not found.', [], error());
                }
            }
        } catch (\Throwable $th) {
            return sendErrorHelper('Error', $th->getMessage(), error());
        }
    }

    /**
     * Admin accept and credential sent
    */
    public function acceptCVShow(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $job_candidate = JobCondidate::find($id);
            if(!$job_candidate)
            {
                return sendError('Record not found', [], errorValid());
            }
            if(respValid($request)) { return respValid($request); }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/
           
            $data = Validator::make($request, [
                'is_cv' => 'required|in:1,2,3'
            ]);

            if ($data->fails()) {
                return sendError($data->errors()->first(), [], errorValid());
            }else{
                $request = (object) $request;
                $candidate = c_uuid_list($job_candidate->c_uid);

                $job_candidate->is_cv = $candidate->cv;
                $job_candidate->is_cv = $request->is_cv;
                $job_candidate->request_date = date('Y-m-d');
                $job_candidate->save();

                $candidate->cv;
                
                

                switch ($request->is_cv) {
                    case is_requested():
                        $n = config('constants.is_cv.requested.name');
                        break;
                    case is_accepted():
                        $n = config('constants.is_cv.accepted.name');
                        // $job_candidate = JobCondidate::find($id);
                        // $query =  DB::table('jobs');
                        // $query->leftJoin('job_candidates', 'jobs.id', '=','job_candidates.job_id');
                        // $query->where('job_candidates.id', $id);
                        // $query->leftJoin('employers as emp', 'jobs.emp_uid', '=','emp.uuid');
                        // $query->select('jobs.*','emp.email','emp.name');
                        // $job_emp = $query->first();
                        // if($job_emp)
                        // {
                        //     $emp = employer($job_emp->email);
                        //     if($emp)
                        //     {
                                
                        //         $in = [
                        //             'email' => $job_emp->email,
                        //             'name' => $job_emp->name, 
                        //             'job_id' => $job_emp->id,
                        //             'job_title' => $job_emp->job_title,
                        //         ];
                        //         $emp->notify(new CVAccepted($in));
                        //     }
                        // }
                        break;
                    case is_rejected():
                        $n = config('constants.is_cv.rejected.name');
                        break;
                    default:
                        $n = 'updated';
                        break;
                }
                DB::commit();
                return sendDataHelper('CV status '.$n.' successfully.', [], ok());
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return sendErrorHelper('Error', $th->getMessage(), error());
        }
    }
}
