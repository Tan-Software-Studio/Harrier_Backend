<?php


use App\Http\Controllers\API\Admin\AdminEmployerController;
use App\Http\Controllers\API\Admin\AdminNotificationController;
use App\Http\Controllers\API\Admin\ATSController;
use App\Http\Controllers\API\Admin\CandidateComment;
use App\Http\Controllers\API\Admin\ContactUsController;
use App\Http\Controllers\API\Admin\DashboardController;
use App\Http\Controllers\API\Admin\GuestController;
use App\Http\Controllers\API\CandidateController;
use App\Http\Controllers\API\Emp\EmployerController;
use App\Http\Controllers\API\Emp\JobConttroller;
use App\Http\Controllers\API\GuestRequestController;
use App\Http\Controllers\API\Master\MasterController;
use App\Http\Controllers\API\Unique\CVController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/* api/v1/admin */
Route::group(['middleware' => ['auth:api', 'admin' ] , 'prefix' => 'v1/adm', 'as'=>'admin.'], function(){ 

    Route::post('/user', function (Request $request) {
        // return 'admin';  
        // return auth()->user()->isAdmin();
        $response = [
            'details' => $request->user()
        ];
        return sendDataHelper('Details.', $response, 200); 
    });

    Route::post('/details', function (Request $request) {
        $response = [
            'details' => $request->user()
        ];
        return sendDataHelper('Details.', $response, 200);
    });
    Route::post('guest/create', [GuestController::class, 'guestCreate']);
    Route::post('guest/details', [GuestRequestController::class, 'guestList']); /* Guest list*/
    Route::post('guest/request/status', [GuestController::class, 'statusRequest']);
    Route::post('guest/profile/update', [GuestController::class, 'guestProfileUpdate']);
    Route::delete('guest/delete/{id}',[GuestController::class, 'destroy']);

    Route::post('all/candidates/list', [CandidateController::class, 'adminAllCandidatesList']); /* Candidates list*/
    Route::post('candidates/list', [CandidateController::class, 'adminCandidatesList']); /* Candidates list*/
    Route::post('single/candidates/list', [CandidateController::class, 'adminSingleCandidatesList']);   
    Route::post('candidate/status/change', [CandidateController::class, 'candidatesStatusChange']);
    Route::post('candidate/inactive/list', [CandidateController::class, 'candidatesInactivelist']);
    Route::post('candidate/details/update', [CandidateController::class, 'candDetailsUpdate']);

    Route::post('candidate/details/delete', [CandidateController::class, 'candDetailsDelete']);

    Route::post('all/employers/list', [AdminEmployerController::class, 'allEmployersList']); /* Employers list*/
    Route::post('employers/list', [AdminEmployerController::class, 'employersList']); /* Employers list*/
    Route::post('employer/register', [AdminEmployerController::class, 'empRegister']);
    Route::post('employer/profile/update', [EmployerController::class, 'admEmpployerProfileUpdate']);
    Route::post('emp/status/update', [AdminNotificationController::class, 'empStatusUpdate']);

    //User All Notification
    Route::post('all/notifications', [AdminNotificationController::class, 'allNotifications']);
    Route::post('unread/notifications', [AdminNotificationController::class, 'unreadNotifications']);
    Route::post('mark_as_read/notifications', [AdminNotificationController::class, 'masAsReadNotifications']);
    Route::post('mark_as_read/notification/{notification_id}', [AdminNotificationController::class, 'masAsReadNotificationSelected']);
    Route::post('delete/notification/{notification_id}', [AdminNotificationController::class, 'deleteNotification']);
        
    Route::post('job/post', [JobConttroller::class, 'adminjobPost']);
    Route::post('job/post/update', [JobConttroller::class, 'adminJobPostUpdate']);
    Route::post('single/job/show', [JobConttroller::class, 'empSingleJobShow']);
    Route::post('live/jobs/details', [JobConttroller::class, 'adminLiveJobDetails']);
    Route::post('jobs/active/inactive', [JobConttroller::class, 'admEmpJobActiveInactive']);
    Route::post('select/live/jobs/interested', [JobConttroller::class, 'adminSelectLiveJobsInterested']); /* select live jobs interested. */
    Route::post('candidates/interested/live/jobs/list', [JobConttroller::class, 'adminCandidateInterestedLiveJobslist']); /* all interested live jobs list*/
    Route::post('requested/cv/list', [CVController::class, 'cvReqListAdminGet']); /*Candidate CV requested list */ 
    Route::post('request/cv/accepted/{cv_request_id}', [AdminNotificationController::class, 'acceptCVShow']); /*Accept CV show */ 

    Route::post('all/ats', [ATSController::class, 'allIndex']);
    Route::post('ats', [ATSController::class, 'index']);
    Route::post('ats/single/candidate/{c_uuid}', [ATSController::class, 'show']);
    Route::post('ats/single/ats/{c_job_id}', [ATSController::class, 'showSingleATS']);
    Route::post('ats/update', [ATSController::class, 'update']);

    Route::get('dashboard', [DashboardController::class, 'index']);

    Route::post('note/create',[CandidateComment::class, 'store']);
    Route::get('notes/{c_uuid}',[CandidateComment::class, 'index']);

    Route::get('contactus/list', [ContactUsController::class, 'index']);
    Route::get('contactus/list/unread', [ContactUsController::class, 'unreadIndex']);
    Route::get('contactus/show/{id}',[ContactUsController::class, 'show']);
    Route::delete('contactus/delete/{id}',[ContactUsController::class, 'destroy']);
});

/* api/v1/admin */
Route::group(['middleware' => ['auth:api', 'admin' ] , 'prefix' => 'v1/master_country', 'as'=>'admin.'], function(){
    Route::get('/index',[CountryController::class, 'index']);
    Route::post('/update/{id}',[CountryController::class, 'update']);
    Route::get('/show/{id}',[CountryController::class, 'show']);
    Route::delete('/delete/{id}',[CountryController::class, 'destroy']);
    Route::post('/status/change/{id}',[CountryController::class, 'statusChange']);
});

/* api/v1/admin */
Route::group(['middleware' => ['auth:api', 'admin' ] , 'prefix' => 'v1/master', 'as'=>'admin.'], function(){ 
    Route::group(['prefix' =>'{prefix}'],function(){
        $tables = config('constants.master_table_names');
        // Route::apiResource('/',MasterController::class)->where('prefix', $tables);
        Route::get('/',[MasterController::class, 'index'])->where('prefix', $tables);
        Route::post('/',[MasterController::class, 'store'])->where('prefix', $tables);
        Route::post('/update/{id}',[MasterController::class, 'update'])->where('prefix', $tables);
        Route::get('/show/{id}',[MasterController::class, 'show'])->where('prefix', $tables);
        Route::delete('/delete/{id}',[MasterController::class, 'destroy'])->where('prefix', $tables);
        Route::post('/status/change/{id}',[MasterController::class, 'statusChange'])->where('prefix', $tables);
    });
});