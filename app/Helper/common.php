<?php

use App\Models\Master\MstRegion;
use App\Models\Unique\City;
use App\Models\Unique\Country;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

function encryptData($decryptMessage)
{   
	$decryptMessage = json_encode($decryptMessage);
	return openssl_encrypt($decryptMessage, env('OPENSSL_CIPHER_NAME'), env('ENCRYPT_KEY'),0, env('ENCRYPT_IV'));
}

function decryptData($encryptedMessage)
{   
	$decrypt = openssl_decrypt($encryptedMessage, env('OPENSSL_CIPHER_NAME'), env('ENCRYPT_KEY'),0, env('ENCRYPT_IV'));
	return json_decode($decrypt, true);
}

function sendErrorHelper($error, $errorMessages = [], $code = 404)
{
    $error =  encryptData($error);
    $errorMessages =  encryptData($errorMessages);
    $response = [
        'success' => false,
        'code' => $code,
        'message' => $error
    ];
    if(!empty($errorMessages)){
        $response['data'] = $errorMessages;
    }
    return response()->json($response, 200);
}

function sendDataHelper($message, $result, $code = 200, $extra = null)
{
    $message =  encryptData($message);
    $result =  encryptData($result);
    $response = [
        'success' => true,
        'code' => $code,
        'message' => $message,
        'data'    => $result
    ];

    if($extra)
    {
        $response['accessToken']  = $extra;
    }
    return response()->json($response, 200);
}

function sendError($error, $errorMessages = [], $code = 404)
{
    $error =  encryptData($error);
    $errorMessages =  encryptData($errorMessages);
    $response = [
        'success' => false,
        'code' => $code,
        'message' => $error
    ];
    if(!empty($errorMessages)){
        $response['data'] = $errorMessages;
    }
    return response()->json($response, 200);
}

function respValid($request)
{
    $validation = Validator::make($request->all(),[
        'response' => 'required',
    ]);

    if($validation->fails()){
        return sendError('Validation Error', $validation->errors()->first());
    }
}


function guidv4($data = null) {
    // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
    $data = $data ?? random_bytes(16);
    assert(strlen($data) == 16);

    // Set version to 0100
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    // Set bits 6-7 to 10
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    // Output the 36 character UUID.
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

function rand_string( $length ) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    return substr(str_shuffle($chars),0,$length);
}

function ok()           {   return 200; }
function error()        {   return 400; }
function mScopeEx()     {   return 403; }
function unAuth()       {   return 404; }
function methNotAlwd()  {   return 405; }
function errorValid()   {   return 422; }
function intSerError()  {   return 500; }

// Upload files
function uploadFile($file, $dir, $filecount = null)
{
    $fileName = time() . $filecount . '.' . $file->getClientOriginalExtension();
    Storage::disk('public')->putFileAs($dir, $file, $fileName);
    return $fileName;
}
//remove file
function removeFile($file, $dir) {
    $existCV = storage_path() . '/app/public/' . $dir . '/' . $file;
    if (File::exists($existCV)) {
        File::delete($existCV);
    }
}

/*CV File Show*/
function cv_file_show($file) {     return asset('/').'storage/uploads/cv/'.$file; }

/*CV file path*/
function cv_public_path() {     return public_path('storage/uploads/cv/'); }


/*Attach File Show*/
function attach_file_show($file) {     return asset('/').'storage/uploads/attach_file/'.$file; }

/*Attach file file path*/
function attach_file_public_path() {     return public_path('storage/uploads/attach_file/'); }



/*Profile Show*/
function profile_file_show($file) {     return asset('/').'storage/uploads/profile/'.$file; }

/*Profile path*/
function profile_public_path() {     return public_path('storage/uploads/profile/'); }

/*Logo Show*/
function logo_file_show($file) {     return asset('/').'storage/uploads/logo/'.$file; }

/*Logo path*/
function logo_public_path() {     return public_path('storage/uploads/logo/'); }

/*Model data list */
function country_list($country_id){  return Country::where('id', $country_id)->first();  }
function region_list($state_id)  {   return MstRegion::where('id', $state_id)->first();  }
function city_list($city_id)    {   return City::where('id', $city_id)->first();    }

function diff_days($date = null)
{
    if($date)
    {
        // Creates DateTime objects
        $datetime1 = date_create(date('Y-m-d', strtotime($date)));
        $datetime2 = date_create(date('Y-m-d'));
        
        // Calculates the difference between DateTime objects
        $interval = date_diff($datetime1, $datetime2);
        
        $y = $interval->format('%y');
        $m = $interval->format('%m');
        if($y < 1 && $m <= 1)
        {
            return $interval->format('%m month');
        }

        if($y < 1 && $m > 1)
        {
            return $interval->format('%m months');
        }

        if($y <= 1 && $m > 1)
        {           
            return $interval->format('%y year %m months');
        }

        if($y > 1 && $m > 1)
        {
            return $interval->format('%y years %m months');
        }

        if($y == 1 && $m == 0)
        {
            return $interval->format('%y year');
        }

        if($y == 0 && $m == 0)
        {
            return $interval->format('%m month');
        }

        // Printing result in years & months format
        if($interval->format('%y') != 0 && $interval->format('%m') != 0)
        {
            return $interval->format('%y years %m months');
        }elseif ($interval->format('%y') != 0) {
            return $interval->format('%y years');
        }else{
            return $interval->format('%m months');
        }
    }
}

function moneyFormatIndia($num) {
    $explrestunits = "" ;
    if(strlen($num)>3) {
        $lastthree = substr($num, strlen($num)-3, strlen($num));
        $restunits = substr($num, 0, strlen($num)-3); // extracts the last three digits
        $restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; // explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping.
        $expunit = str_split($restunits, 2);
        for($i=0; $i<sizeof($expunit); $i++) {
            // creates each of the 2's group and adds a comma to the end
            if($i==0) {
                $explrestunits .= (int)$expunit[$i].","; // if is first value , convert into integer
            } else {
                $explrestunits .= $expunit[$i].",";
            }
        }
        $thecash = $explrestunits.$lastthree;
    } else {
        $thecash = $num;
    }
    return $thecash; // writes the final format where $currency is the currency symbol.
}


function trueFalse($data)
{
    if($data == 1)
    {
        return $yes = [[
                'title' => 'Yes',
                "id" => 1
        ]];
    }else{
        return $no = [[
                'title' => 'No',
                "id" => 0
        ]];
    }
}

function yesNoFetch($data)
{
    return ($data == 'Yes') ? 1 : 0 ;
}

function candidateStatus($status = null)
{
    switch ($status) {
        case c_status_active():
            return c_status_active();
            break;
        case c_status_passive():
            return c_status_passive();
            break;
        case c_status_very_passive():
            return c_status_very_passive();
            break;
        case c_status_closed():
            return c_status_closed();
            break;
        default:
            return c_status_active();
            break;
    }
}


function has_prefix($string, $prefix) {
    return substr($string, 0, strlen($prefix)) == $prefix;
}

function actual_url($url)
{
    $pattern_1 = 'https://www.';
    $pattern_2 = 'https://';
    $pattern_3 = 'http://www.';
    $pattern_4 = 'http://';
    $pattern_5 = 'www.';
    $pattern_6 = '	https://www.';
    $pattern_7 = '	https://';
    $pattern_8 = '	http://www.';
    $pattern_9 = '	http://';
    $pattern_10 = '	www.';

    // $actual_url = str_replace($pattern_5,"",$url);

    switch ($url) {
        case has_prefix($url, $pattern_1):
            $actual_url = str_replace($pattern_1,"",$url);
            break;
        case has_prefix($url, $pattern_2):
            $actual_url = str_replace($pattern_2,"",$url);
            break;
        case has_prefix($url, $pattern_3):
            $actual_url = str_replace($pattern_3,"",$url);
            break;
        case has_prefix($url, $pattern_4):
            $actual_url = str_replace($pattern_4,"",$url);
            break;
        case has_prefix($url, $pattern_5):
            $actual_url = str_replace($pattern_5,"",$url);
            break;
        case has_prefix($url, $pattern_6):
            $actual_url = str_replace($pattern_6,"",$url);
            break;
        case has_prefix($url, $pattern_7):
            $actual_url = str_replace($pattern_7,"",$url);
            break;
        case has_prefix($url, $pattern_8):
            $actual_url = str_replace($pattern_8,"",$url);
            break;
        case has_prefix($url, $pattern_9):
            $actual_url = str_replace($pattern_9,"",$url);
            break;
        case has_prefix($url, $pattern_10):
            $actual_url = str_replace($pattern_10,"",$url);
            break;
        default:
            $actual_url = $url;
            break;
    }
    return $actual_url;
}

function yesNo()
{
    return [ 
        [   'id' => '0', 'title' => 'No' ],[   'id' => '1', 'title' => 'Yes' ]
    ];
}

function amt()
{
    return [ 
        [   'id' => '0', 'title' => '10000' ], [   'id' => '1', 'title' => '20000' ]
    ];
}


function pqe_opt()
{
    return [ 
        [   'id' => '0', 'title' => '2' ], [   'id' => '1', 'title' => '5' ]
    ];
}


function line_manageemnt()
{
    return [ 
        [   'id' => '1', 'title' => '0 People' ],[   'id' => '2', 'title' => '1-4 People' ],[   'id' => '3', 'title' => '5-9 People' ],[   'id' => '4', 'title' => '10-19 People' ]
        ,[   'id' => '5', 'title' => '20-49 People' ],[   'id' => '6', 'title' => '50+ People' ]
    ];
}

function time_in_ind()
{
    return [ 
        [   'id' => '1', 'title' => 'Less than a year' ],[   'id' => '2', 'title' => '1-3 Years' ],[   'id' => '3', 'title' => '3-6 Years' ],
        [ 'id' => '4', 'title' => '6-10 Years' ],[ 'id' => '5', 'title' => '10-15 Years' ],[ 'id' => '6', 'title' => '15-20 Years' ],[ 'id' => '7', 'title' => '20+ Years' ]
    ];
}

function time_in_role_opt()
{
    return [ 
        [   'id' => '1', 'title' => 'Less than a year' ],[   'id' => '2', 'title' => '1-2 Years' ],[   'id' => '3', 'title' => '2-4 Years' ],[   'id' => '4', 'title' => '4-7 Years' ],
        [   'id' => '5', 'title' => '7-10 Years' ],[   'id' => '6', 'title' => '10+ Years' ]
    ];
}

function notice_period_weeks()
{
    return [ 
        [   'id' => '1', 'title' => '0 Weeks' ],[   'id' => '2', 'title' => '1-4 weeks' ],[   'id' => '3', 'title' => '5-8 Weeks' ],
        [   'id' => '4', 'title' => '9-12 Weeks' ],[   'id' => '4', 'title' => '12+ Weeks' ]
    ];
}

function avg_amount()
{
    return [ 
        [   'id' => '1', 'title' => '0-10' ],[   'id' => '2', 'title' => '11-50' ],[   'id' => '3', 'title' => '51-100' ],[   'id' => '4', 'title' => '101-500' ],[   'id' => '5', 'title' => '501-2000' ],[   'id' => '6', 'title' => '2001-10000 & more' ]
    ];
}


function viewATSDataMigrations()
{
    if(!DB::statement("DROP VIEW IF EXISTS view_ats_data;"))
    {
        DB::statement("
        ALTER VIEW view_ats_data AS
        SELECT job_candidates.id as c_job_id, employers.name as employers_name, jobs.job_title, jobs.created_at, jobs.salary_range_start,jobs.salary_range_start_symbol, jobs.salary_range_end, jobs.salary_range_end_symbol, job_candidates.is_cv, job_candidates.request_date, job_candidates.accepted_date, job_candidates.rejected_date, job_candidates.interview_request, job_candidates.interview_request_date, job_candidates.offer_accepted_date, candidates.id as c_id, candidates.uuid as c_uuid, job_candidates.offer_salary, job_candidates.offer_salary_symbol, job_candidates.offer_bonus_commission, job_candidates.offer_bonus_commission_symbol, job_candidates.c_job_status, job_candidates.start_date FROM employers JOIN jobs ON jobs.emp_uid = employers.uuid JOIN job_candidates ON job_candidates.job_id = jobs.id JOIN candidates ON candidates.uuid = job_candidates.c_uid ORDER BY job_candidates.created_at DESC;
        "); 
            
    }else{
        DB::statement("
        CREATE VIEW view_ats_data AS
        SELECT job_candidates.id as c_job_id, employers.name as employers_name, jobs.job_title, jobs.created_at, jobs.salary_range_start,jobs.salary_range_start_symbol, jobs.salary_range_end, jobs.salary_range_end_symbol, job_candidates.is_cv, job_candidates.request_date, job_candidates.accepted_date, job_candidates.rejected_date, job_candidates.interview_request, job_candidates.interview_request_date, job_candidates.offer_accepted_date, candidates.id as c_id, candidates.uuid as c_uuid, job_candidates.offer_salary, job_candidates.offer_salary_symbol, job_candidates.offer_bonus_commission, job_candidates.offer_bonus_commission_symbol, job_candidates.c_job_status, job_candidates.start_date FROM employers JOIN jobs ON jobs.emp_uid = employers.uuid JOIN job_candidates ON job_candidates.job_id = jobs.id JOIN candidates ON candidates.uuid = job_candidates.c_uid ORDER BY job_candidates.created_at DESC;
        ");
    }
}

function noContry() {   return 'No';    }