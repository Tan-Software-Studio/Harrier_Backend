<?php

use App\Http\Controllers\API\CandidateController;
use App\Http\Controllers\API\Emp\EmpCandidateController;
use App\Http\Controllers\API\Emp\EmployerController;
use App\Http\Controllers\API\Emp\EmpNotificationController;
use App\Http\Controllers\API\Emp\JobConttroller;
use App\Http\Controllers\API\Emp\OfficeLocationController;
use App\Http\Controllers\API\Unique\CVController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;


Route::group(['middleware' => ['auth:api', 'emp'] , 'prefix' => 'v1/emp', 'as'=>'emp.'], function(){ 
    Route::post('/user', function (Request $request) {
        $response = [
            'details' => employer($request->user()->email)
        ];
        return sendDataHelper('Details.', $response, 200);
    });

    Route::post('/details', function (Request $request) {
        $response = employer($request->user()->email);
        return sendDataHelper('Details.', $response, 200);
    });
    Route::post('profile/update', [EmployerController::class, 'empProfileUpdate']);
    Route::post('change/password/update', [EmployerController::class, 'empChangePasswordUpdate']);

    Route::post('job/post', [JobConttroller::class, 'empjobPost']);
    Route::post('job/post/update', [JobConttroller::class, 'empJobPostUpdate']);
    Route::post('single/job/show', [JobConttroller::class, 'empSingleJobShow']);
    Route::post('live/job/names', [JobConttroller::class, 'liveJobNames']);
    Route::post('live/jobs/details', [JobConttroller::class, 'empLiveJobDetails']);
    Route::post('jobs/active/inactive', [JobConttroller::class, 'empJobActiveInactive']);

    Route::post('all/jobs/details', [JobConttroller::class, 'empAllJobsDetails']);    

    Route::post('candidates/list/filter', [CandidateController::class, 'empCandidatesListFilter']);
    Route::post('candidates/list', [CandidateController::class, 'empCandidatesList']);
    Route::post('single/candidates/list', [CandidateController::class, 'empSingleCandidatesList']);
    Route::post('candidates/interested/live/jobs/list', [JobConttroller::class, 'empCandidateInterestedLiveJobslist']); /* all interested live jobs list*/

    //Empployee add & remove for candidate short list  
    Route::post('shortlist/addremove/candidate', [EmpCandidateController::class, 'shortListAddREmove']);
    Route::post('candidates/short/list', [CandidateController::class, 'empCandidatesShortList']);

    Route::post('cv/request/post', [EmployerController::class, 'cvReqPost']);  /*Candidate CV reuqested */
    Route::post('requested/cv/list', [CVController::class, 'cvReqListEmpGet']); /*Candidate CV requested list */ 
    

    //User All Notification
    Route::post('unread/notifications', [EmpNotificationController::class, 'unreadNotifications']);
    Route::post('delete/notification/{notification_id}', [EmpNotificationController::class, 'deleteNotification']);
  

    Route::group(['prefix' =>'{prefix}'],function(){
        $tblname = 'emp_office_locations|emp_working_schedules';
        Route::get('index',[OfficeLocationController::class, 'index'])->where('prefix', $tblname);
        Route::post('store',[OfficeLocationController::class, 'store'])->where('prefix', $tblname);
        Route::post('update',[OfficeLocationController::class, 'update'])->where('prefix', $tblname);
        Route::get('show/{id}',[OfficeLocationController::class, 'show'])->where('prefix', $tblname);
        Route::post('delete',[OfficeLocationController::class, 'destroy'])->where('prefix', $tblname);
        Route::post('status/active/inactive',[OfficeLocationController::class, 'statusActiveInactive'])->where('prefix', $tblname);
    });

    

});