<?php

use App\Http\Controllers\API\CandidateController;
use App\Http\Controllers\API\Emp\JobConttroller;
use App\Http\Controllers\API\PassportAuthController;
use App\Http\Controllers\API\Unique\ListController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

@include('admin.php');
@include('guest.php');
@include('emp.php');
@include('authAPI.php');


// Route::post('v1/countries', [ListController::class, 'countriesGet']); 

Route::group(['middleware' => 'auth:api', 'prefix' => 'v1'], function(){ 
    
    Route::post('logout', [PassportAuthController::class, 'logout']);

    Route::post('files/path', [ListController::class, 'filesPath']);
    
    Route::post('jobs/list', [JobConttroller::class, 'jobsList']);
    
    Route::post('countries/typelist', [ListController::class, 'countriesGet']);
    Route::post('states/typelist', [ListController::class, 'statesGet']);
    Route::post('cities/typelist', [ListController::class, 'citiesGet']);
    
    Route::get('filter/quick/search', [CandidateController::class, 'candidateQuickSearchOptions']);
    Route::get('more/filter/quick/search', [CandidateController::class, 'moreCandidateQuickSearchOptions']);
    
    
});


Route::post('testlist', [Controller::class, 'testData']);
Route::post('encrypt', [Controller::class, 'encryptData']);
Route::post('decrypt', [Controller::class, 'decryptData']);
Route::post('check-encrypt-decrypt', [Controller::class, 'checkEncryptDecrypt']);

Route::post('get-encrypted-body-param', function (Request $request) {
    try {
        $data = $request->all();
        return sendDataHelper('Body encrypted.', $data, $code = 200);
    } catch (\Throwable $th) {
        $bug = $th->getMessage();
        return sendErrorHelper('Error', $bug, 400);
    }
});

