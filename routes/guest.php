<?php

use App\Http\Controllers\API\CandidateController;
use App\Http\Controllers\API\Emp\JobConttroller;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::group(['middleware' => ['auth:api', 'guest'] , 'prefix' => 'v1/guest', 'as'=>'guest.'], function(){ 
    Route::post('/user', function (Request $request) {
        // return 'guest';
        // return auth()->user()->isGuest();
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
        
    // /*Resume upload */
    Route::post('details/update', [CandidateController::class, 'detailsUpdate']);
    Route::post('candidate/detail/update', [CandidateController::class, 'GuestcandDetailsUpdate']);
    // all candidates job applied list 

    Route::post('candidates/list/filter', [CandidateController::class, 'guestCandidatesListFilter']);

    Route::post('candidates/list', [CandidateController::class, 'guestCandidatesList']);
    Route::post('single/candidates/list', [CandidateController::class, 'guestSingleCandidatesList']);
   

    Route::post('live/jobs/details', [JobConttroller::class, 'guestLiveJobDetails']);

    Route::post('candidates/check', [CandidateController::class, 'guestCandidatescheck']);
    // Route::post('form/store', [CandidateController::class, 'formStore']);
});
