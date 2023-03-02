
<?php

use App\Http\Controllers\API\Admin\ContactUsController;
use App\Http\Controllers\API\CandidateController;
use App\Http\Controllers\API\ForgotPasswordController;
use App\Http\Controllers\API\GuestRequestController;
use App\Http\Controllers\API\Master\MasterController;
use App\Http\Controllers\API\PassportAuthController;
use App\Http\Controllers\API\Unique\ListController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['notoken', 'cors' ] , 'prefix' => 'v1'], function(){ 
    Route::post('admin/login', [PassportAuthController::class, 'login']);
    Route::prefix('guest')->group(function(){
        Route::post('login', [PassportAuthController::class, 'guestLogin']);
        
        Route::post('/login/request/post',[GuestRequestController::class,'requestLoginPost']);
    });
    Route::prefix('candidate')->group(function(){
        Route::post('/form/step/one/validate',[CandidateController::class,'formOneValidate']);
        Route::post('/form/step/two/validate',[CandidateController::class,'formTwoValidate']);
        Route::post('/form/step/three/validate',[CandidateController::class,'formThreeValidate']);
        Route::post('/form/create',[CandidateController::class,'formStore']);
    });
    
    Route::prefix('emp')->group(function(){
        Route::post('register', [PassportAuthController::class, 'empRegister']);
        Route::post('/login/request/post',[GuestRequestController::class,'empRequestLoginPost']);
        Route::post('login', [PassportAuthController::class, 'empLogin']);
    });
    
    Route::post('list/mst_regions',[MasterController::class, 'masterRegionList']);
        /* api/v1/admin */
    Route::group(['prefix' => 'list'], function(){ 
        Route::group(['prefix' =>'{prefix}'],function(){
            $tables = config('constants.master_table_names');
            Route::get('/',[MasterController::class, 'masterList'])->where('prefix', $tables);
        });
    });

    // Route::post('countries/list', [ListController::class, 'countriesGet']);
    Route::post('countries/list', [ListController::class, 'countriesGet']);
    Route::post('states/list', [ListController::class, 'statesGet']);
    Route::post('cities/list', [ListController::class, 'citiesGet']);
    
    Route::get('master_tables_list',[MasterController::class, 'masterTablesList']);

    Route::post('contactus', [ContactUsController::class, 'send']);

    Route::post('notification/{uuid}', [CandidateController::class, 'notfication']);
});

Route::group(['middleware' => ['cors' ] , 'prefix' => 'v1'], function(){ 
    
    Route::group(['prefix' =>'{role}'],function(){
        $roles = config('constants.prefix_names');
        Route::post('forget/password', [ForgotPasswordController::class, 'sentForgetPasswordLink'])->where('role', $roles);
        Route::post('new/password/update', [ForgotPasswordController::class, 'emailTokenVerification'])->where('role', $roles);
    });
});

Route::get('v1/tech_tools', [ListController::class, 'legalTechTools']);