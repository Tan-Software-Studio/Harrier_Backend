<?php

use App\Http\Controllers\CronJobController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/cache', function() {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('optimize:clear');
    // Artisan::call('clear-compiled'); 

   return "Cleared!";
});

Route::get('/linkstorage', function () {
    Artisan::call('storage:link');
    return "Storage link generated successfully.";
});

Route::get('/passportinstall', function () {
    dd(Artisan::call('passport:install'));
});

Route::get('/passport-install', function () {
    dd(shell_exec('php artisan passport:install'));
});

Route::get('/migrate', function() {
    
    return Artisan::call('migrate',
        array(
        '--path' => 'database/migrations',
        '--database' => 'mysql',
        '--force' => true));
});

Route::get('/composer-update', function() {
    dd(shell_exec('composer update'));
});

Route::get('/migrate-refresh', function() {

    return Artisan::call('migrate:refresh',
        array(
        '--path' => 'database/migrations',
        '--database' => 'mysql',
        '--seed' => true,
        '--force' => true));
});


Route::get('cron/job', [CronJobController::class, 'cronjob']);

Route::get('api_call', [CronJobController::class, 'saveApiData']);

Route::get('guestexpired', [CronJobController::class, 'expiredMail']);