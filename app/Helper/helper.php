<?php

use App\Models\Candidate;
use App\Models\Employer;
use App\Models\Job;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Http\Traits\JobTrait;
use App\Models\Master\MstCurrency;
use App\Models\Master\MstCustomerType;
use App\Models\Master\MstEmployerType;
use App\Models\Master\MstLanguage;
use App\Models\Master\MstLegalTechTool;
use App\Models\Master\MstQualification;
use App\Models\Master\MstRegion;
use App\Models\Master\MstTechTools;
use App\Models\Master\MstWorkingArrangements;
use App\Models\Unique\Country;
use Illuminate\Support\Facades\DB;

function project($name){
	if($name == 'app_name')			{ 	return config('app.name'); 	}
	if($name == 'app_favicon_path') { 	return asset('/assets/admin/images/logo/favicon.ico'); 	}
	if($name == 'app_logo_path') 	{ 	return asset('/assets/admin/images/logo/logo.png'); 	}
}

function role($role) { 	
	return config('constants.role.'.$role.'.value');
}

/* Role Admin ---------------------*/
function roleAdmin() { 	return config('constants.role.admin.value'); }
function roleGuest() { 	return config('constants.role.guest.value'); }
function roleEmp()   { 	return config('constants.role.emp.value');   }
function rolePE()   { 	return config('constants.role.emp.is_pe');   }
/*-----------
------------*/
function adminTable() {  	return User::where('role', roleAdmin())->first(); }
/*------------------------------------------*/
function canGUEST() {	return auth()->user()->tokenCan(roleGuest()); }
function canADMIN()   {	return auth()->user()->tokenCan(roleAdmin()); }
function canEMP()   {	return auth()->user()->tokenCan(roleEmp());   }
function canPE()   {	return auth()->user()->tokenCan(rolePE());   }
/*-----------------------*/
function active()   { 	return true;  }
function inactive() {	return false; }

function yes() { 	return true;  }
function no()  { 	return false; }

function prefer_yes() { 	return 1; }
function prefer_no()  { 	return 2; }
function prefer_not_say()  { 	return 3; }


/* is_cv ---------------------*/
function is_requested()	{ 	return config('constants.is_cv.requested.value');	}
function is_accepted() 	{ 	return config('constants.is_cv.accepted.value'); 	}
function is_rejected()	{	return config('constants.is_cv.rejected.value');	}

/* is_candidate_status ---------------------*/
function c_status_active()	{ 	return config('constants.is_candidate_status.active.value');	}
function c_status_passive()	{ 	return config('constants.is_candidate_status.passive.value');	}
function c_status_very_passive()	{ 	return config('constants.is_candidate_status.very_passive.value');	}
function c_status_closed()	{ 	return config('constants.is_candidate_status.closed.value');	}


/* is_cv ---------------------*/
function login_requested()	{ 	return config('constants.login_request.requested.value');	}
function login_expired() 	{ 	return config('constants.login_request.expired.value'); 	}
function login_active()	{	return config('constants.login_request.active.value');	}

function role_name($role)
{
	switch ($role) {
		case config('constants.role.admin.value'):	return 'admin'; break;
		case config('constants.role.guest.value'):	return 'guest'; break;
		case config('constants.role.emp.value'):	return 'emp';	break;
		default:			/* code */			break;
	}
}

function employer($email)
{
	// if(!Employer::withTrashed()->where('email', $email)->first())
	// {
	// 	$in = new Employer;
	// 	$in->uuid = Str::uuid()->toString();
	// 	$in->email = $email;
	// 	$in->email_verified_at = now();
	// 	$in->password = bcrypt(Str::random(6));
	// 	$in->save();  
	// }
	return Employer::where('email', $email)->with(['emp_office_locations'])->first() ?? null;
}

function employer_uuid($emp_uid = null)
{
	if($emp_uid)
	{
		return Employer::where('uuid', $emp_uid)->first() ?? null;
	}else{
		return null;
	}
}

function emp_uuid_list($emp_uid)
{
	return Employer::withTrashed()->where('uuid', $emp_uid)->first() ?? null;
}

function c_uuid_list($c_uid)
{
	return Candidate::withTrashed()->where('uuid', $c_uid)->first() ?? null;
}

/* not use now */
function type_name_Helper()
{
	$tbl = Schema::hasTable('types');
	if($tbl)
	{
		$type = [];    
		$tp = null;
		if(count($type) > 0)
		{
			foreach ($type as $key => $value) {
				$tp = $value->type_name.'|'.$tp;   
			}
			return $tp ? Str::of($tp)->replaceLast('|', '') : null;
		}
	}
	return true;
}

function requestHelper($is_request)
{
	switch ($is_request) {
		case is_accepted():
			return config('constants.is_cv.accepted.name');
			break;
		case is_rejected():
			return config('constants.is_cv.rejected.name');
			break;
		default:
			return config('constants.is_cv.requested.name');
			break;
	}
}

function single_job_list($job_id)
{
	return Job::withTrashed()->where('id', $job_id)->first() ?? null;
}

function jobEmployer($job_id)
{
	return JobTrait::jobEmployer($job_id);
} 

function env_emp_url()
{
	return env('EMP_APP_URL') ?? env('APP_API_URL');
}

function env_guest_url()
{
	return env('FRONT_APP_URL') ?? env('APP_API_URL');
}

function mst_employer_types($ids)
{
	return MstEmployerType::find($ids);
}

function mst_regions($ids)
{
	return MstRegion::find($ids);
}

function mst_countries($ids)
{
	return Country::find($ids);
}

function mst_currencies($ids)
{
	return MstCurrency::find($ids);
}

function mst_working_arrangements($ids)
{
	return MstWorkingArrangements::find($ids);
}

function mst_customer_types($ids)
{
	return MstCustomerType::find($ids);
}

function mst_legal_tech_tools($ids)
{
	return MstLegalTechTool::find($ids);
}

function mst_tech_tools($ids)
{
	return MstTechTools::find($ids);
}

function mst_qualifications($ids)
{
	return MstQualification::find($ids);
}

function mst_languages($ids)
{
	return MstLanguage::find($ids);
}