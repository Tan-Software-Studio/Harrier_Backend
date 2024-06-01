<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Traits\CandidateFilterTrait;
use App\Http\Traits\CandidateTrait;
use App\Http\Traits\FilterTrait;
use App\Models\Candidate;
use App\Models\Candidate\CandDesiredEmployerTypes;
use App\Models\GuestMater;
use App\Models\Master\MstEmployerType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CandidateController extends Controller
{
    use CandidateTrait, FilterTrait, CandidateFilterTrait;

    /* Guest get a candidates list */
    public function guestCandidatesList(Request $request)
    {
        return self::candidatesList($request, auth()->user()->role);
    }


    /* Admin get a candidates list */
    public function adminCandidatesList(Request $request)
    {
        return self::candidatesListOnlyAdmin($request, auth()->user()->role);
    }

    /* Emp get a candidates list */
    public function empCandidatesList(Request $request)
    {
        return self::candidatesList($request, auth()->user()->role);
    }


    /* Emp get a candidates short list */
    public function empCandidatesShortList(Request $request)
    {
        return self::candidatesShortList($request, auth()->user()->role);
    }

    /* Admin get a All candidates list at that time*/
    public function adminAllCandidatesList(Request $request)
    {
        return self::allCandidatesList($request, auth()->user()->role);
    }

    /* Get Candidates list */
    public function allCandidatesList($request, $role)
    {

        $req = $request;
        try {
            if (respValid($request)) {
                return respValid($request);
            }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/
            $request = (object) $request;
            $query = Candidate::query();

            $query->with('desired_employer_types.desired_employer_types_view');
            $query->with([
                'employer_type_list', 'current_country_list', 'current_salary_symbol_list', 'current_bonus_or_commission_symbol_list',
                'desired_salary_symbol_list', 'desired_bonus_or_commission_symbol_list', 'deal_size_symbol_list',
                'sales_quota_symbol_list', 'is_cv_list' => function ($query) {
                    return $query->select('id', 'job_id', 'c_uid', 'is_cv');
                }
            ]);

            $response = $query->limit(1)->get();

            return sendDataHelper("List", $response, ok());
        } catch (\Throwable $th) {
            throw $th;
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }
    }



    /* Inactive candidates list call*/
    public function candidatesInactivelist(Request $request)
    {
        try {
            $query = Candidate::query();
            $query->select('id', 'updated_at');
            $query->whereDate('updated_at', '<', now()->subMonth(3));
            $query->select('uuid', 'name', 'job_title', 'employer_type', 'current_salary', 'desired_salary', 'current_bonus_or_commission', 'desired_bonus_or_commission', 'updated_at');
            if ($s = $request->input('search')) {
                $query->whereRaw("name LIKE '%" . $s . "%'")
                    ->orWhereRaw("email LIKE '%" . $s . "%'");
            }

            if ($sort = $request->input('sort')) {
                $query->orderBy('created_at', $sort);
            }

            $response = $query->paginate(
                $perPage = 10,
                $columns = ['*'],
                $pageName = 'page'
            );
            return sendDataHelper("List", $response, ok());
        } catch (\Throwable $th) {
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }
    }

    public function GuestcandDetailsUpdate(Request $request) /* Candidate deatils update */
    {
        DB::beginTransaction();
        $req = $request;
        try {

            $data = Validator::make($req->all(), [
                'cv' => 'nullable|mimes:pdf,doc,docx|max:10000',
                'profile_image' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            ]);

            if ($data->fails()) {
                return sendError($data->errors()->first(), [], errorValid());
            }

            $cv = null;
            $profile_image = null;


            if (respValid($request)) {
                return respValid($request);
            }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/

            $data = Validator::make($request, [
                'uuid' => 'required|exists:candidates,uuid',

            ], [
                'uuid.required' => 'Select Candidate deatils not found',
                'uuid.exists' => 'Select Candidate deatils not found',
            ]);

            if ($data->fails()) {
                return sendError($data->errors()->first(), [], errorValid());
            } else {
                $in = Candidate::where('uuid', @$request['uuid'])->first();

                $data = Validator::make($request, [
                    'email' => 'required|email|unique:candidates,email,' . $in->id,
                    'first_name' => 'nullable',
                    'last_name' => 'nullable',
                    'phone' => 'nullable|unique:candidates,phone,' . $in->id,
                    'job_title' => 'nullable',
                    'employer' => 'nullable',
                    'employer_type' => 'nullable',
                    'time_in_current_role' => 'nullable|date',
                    'time_in_industry' => 'nullable|date',
                    'line_management' => 'nullable',
                    'desired_employer_type' => 'nullable',
                    'current_region' => 'nullable',
                    'current_country' => 'nullable',
                    'desired_region' => 'nullable',
                    'desired_country' => 'nullable',

                    'current_salary' => 'nullable',
                    'current_salary_symbol' => 'nullable',

                    'current_bonus_or_commission' => 'nullable',
                    'current_bonus_or_commission_symbol' => 'nullable',

                    'desired_salary' => 'nullable',
                    'desired_salary_symbol' => 'nullable',

                    'desired_bonus_or_commission' => 'nullable',
                    'desired_bonus_or_commission_symbol' => 'nullable',

                    'notice_period' => 'nullable',
                    'status' => 'required|in:1,2,3,4',
                    'working_arrangements' => 'nullable',
                    'desired_working_arrangements' => 'nullable',
                    'law_degree' => 'nullable',
                    'qualified_lawyer' => 'nullable',
                    'jurisdiction' => 'nullable',
                    'pqe' => 'nullable|numeric',
                    'area_of_law' => 'nullable',
                    'legal_experience' => 'nullable',
                    'customer_type' => 'nullable',

                    'deal_size' => 'nullable',
                    'deal_size_symbol' => 'nullable',

                    'sales_quota' => 'nullable',
                    'sales_quota_symbol' => 'nullable',

                    'legal_tech_tools' => 'nullable',
                    'tech_tools' => 'nullable',
                    'qualification' => 'nullable',
                    'languages' => 'nullable',
                    'profile_about' => 'nullable',
                    'cultural_background' => 'nullable',
                    'first_gen_he' => 'nullable',
                    'gender' => 'nullable',
                    'disability' => 'nullable',
                    'disability_specific' => 'nullable',
                    'free_school_meals' => 'nullable',
                    'parents_he' => 'nullable',
                    'school_type' => 'nullable',
                    'faith' => 'nullable',
                    'sex' => 'nullable',
                    'gender_identity' => 'nullable',
                    'sexual_orientation' => 'nullable',
                    'visa' => 'nullable',
                    'privacy_policy' => 'nullable',
                    'harrier_search' => 'nullable', // required
                    'harrier_candidate' => 'nullable',
                    'channel' => 'nullable|exists:mst_channels,id',
                    'channel_other' => 'nullable',
                    'referral' => 'nullable',
                    'is_job_search' => 'nullable',
                    'freelance_current' => 'nullable',
                    'freelance_future' => 'nullable',

                    'freelance_daily_rate' => 'nullable',
                    'freelance_daily_rate_symbol' => 'nullable',

                    'legaltech_vendor_or_consultancy' => 'nullable',
                    'current_company_url' => 'nullable',
                ], [
                    'email.unique' => 'Already this email submitted. please contact us Harrier!',
                    'harrier_search.required' => 'Are you happy to share your data with Harrier Search required',
                ]);

                if ($data->fails()) {
                    return sendError($data->errors()->first(), [], errorValid());
                }

                $request = (object) $request;

                $actual_url = @$request->current_company_url;
                if (@$request->url) {
                    $actual_url = Str::replace(' ', '', $actual_url);
                    $actual_url = actual_url($actual_url);
                    $actual_url = Str::of($actual_url)->rtrim('/');
                }
                $check_url['url'] = $actual_url;

                $data = Validator::make($check_url, [
                    'current_company_url' => 'nullable|unique:candidates,current_company_url,' . $in->id,
                ]);

                if ($data->fails()) {
                    return sendError($data->errors()->first(), [], errorValid());
                }

                if (@$request->time_in_current_role) {
                    $in->time_in_current_role = date('Y-m-d', strtotime(@$request->time_in_current_role));
                } else {
                    $in->time_in_current_role = null;
                }

                if (@$request->time_in_industry) {
                    $in->time_in_industry = date('Y-m-d', strtotime(@$request->time_in_industry));
                } else {
                    $in->time_in_industry = null;
                }

                $in->first_name = @$request->first_name;
                $in->last_name = @$request->last_name;
                $in->phone = @$request->phone;
                $in->email = @$request->email ?? $in->email;
                $in->email = @$request->email ?? $in->email;
                $in->password = null;

                $in->status = @$request->status;

                $in->job_title = @$request->job_title ??  $in->job_title;
                $in->employer = @$request->employer ?? $in->employer;
                $in->employer_type = @$request->employer_type ?? $in->employer_type;
                if (@$request->line_management) {
                    $in->line_management = @$request->line_management ?? 0;
                } else {
                    $in->line_management = 0;
                }
                $in->desired_employer_type = @$request->desired_employer_type;
                $in->current_region = (@$request->current_region ? @$request->current_region : null);
                $in->current_country = @$request->current_country;
                $in->desired_region = @$request->desired_region;
                $in->desired_country = @$request->desired_country;

                $in->current_salary = (@$request->current_salary ? @$request->current_salary : 0);
                $in->current_salary_symbol = (@$request->current_salary_symbol ? @$request->current_salary_symbol : null);

                $in->current_bonus_or_commission = (@$request->current_bonus_or_commission ? @$request->current_bonus_or_commission : 0);
                $in->current_bonus_or_commission_symbol = (@$request->current_bonus_or_commission_symbol ? @$request->current_bonus_or_commission_symbol : null);

                $in->desired_salary = (@$request->desired_salary ? $request->desired_salary : 0);
                $in->desired_salary_symbol = (@$request->desired_salary_symbol ? @$request->desired_salary_symbol : null);

                $in->desired_bonus_or_commission = (@$request->desired_bonus_or_commission ? @$request->desired_bonus_or_commission : 0);
                $in->desired_bonus_or_commission_symbol = (@$request->desired_bonus_or_commission_symbol ? @$request->desired_bonus_or_commission_symbol : null);

                $in->notice_period = @$request->notice_period;
                if (@$request->working_arrangements) {
                    $in->working_arrangements = @$request->working_arrangements;
                }
                $in->desired_working_arrangements = @$request->desired_working_arrangements;

                if (@$request->law_degree == 1 || @$request->law_degree == 0) {
                    $in->law_degree = @$request->law_degree;
                }
                if (@$request->qualified_lawyer == 1 || @$request->qualified_lawyer == 0) {
                    $in->qualified_lawyer = @$request->qualified_lawyer;
                }

                $in->jurisdiction = @$request->jurisdiction;
                $in->pqe = @$request->pqe;
                $in->area_of_law = @$request->area_of_law;
                $in->legal_experience = @$request->legal_experience;
                $in->customer_type = @$request->customer_type;

                $in->deal_size = @$request->deal_size;
                $in->deal_size_symbol = (@$request->deal_size_symbol ? @$request->deal_size_symbol : null);

                $in->sales_quota = @$request->sales_quota;
                $in->sales_quota_symbol = (@$request->sales_quota_symbol ? @$request->sales_quota_symbol : null);

                $in->legal_tech_tools = @$request->legal_tech_tools;
                $in->tech_tools = @$request->tech_tools;
                $in->qualification = @$request->qualification;
                $in->languages = @$request->languages;
                $in->profile_about = @$request->profile_about;
                $in->cultural_background = @$request->cultural_background;
                $in->first_gen_he = @$request->first_gen_he;
                $in->gender = @$request->gender;

                $in->disability = @$request->disability;
                $in->disability_specific = @$request->disability_specific;
                $in->free_school_meals = @$request->free_school_meals;
                $in->parents_he = @$request->parents_he;

                if (@$request->school_type) {
                    $in->school_type = @$request->school_type;
                } else {
                    $in->school_type = false;
                }

                $in->faith = @$request->faith;
                $in->sex = @$request->sex;
                $in->gender_identity = @$request->gender_identity;
                $in->sexual_orientation = @$request->sexual_orientation;
                $in->visa = @$request->visa;
                $in->privacy_policy = @$request->privacy_policy;
                $in->harrier_search = @$request->harrier_search;

                $in->harrier_candidate = @$request->harrier_candidate;
                if (@$request->channel) {
                    $in->channel = @$request->channel;
                }
                $in->channel_other = @$request->channel_other;
                $in->referral = @$request->referral;

                if (@$request->is_job_search) {
                    $in->is_job_search = @$request->is_job_search;
                }
                if (@$request->freelance_current == 1 || @$request->freelance_current == 0) {
                    $in->freelance_current = @$request->freelance_current;
                }
                if (@$request->freelance_future == 1 || @$request->freelance_future == 0) {
                    $in->freelance_future = @$request->freelance_future;
                }
                if (@$request->legaltech_vendor_or_consultancy == 1 || @$request->legaltech_vendor_or_consultancy == 0) {
                    $in->legaltech_vendor_or_consultancy = @$request->legaltech_vendor_or_consultancy;
                }

                $in->freelance_daily_rate = @$request->freelance_daily_rate;
                $in->freelance_daily_rate_symbol = (@$request->freelance_daily_rate_symbol ? @$request->freelance_daily_rate_symbol : null);


                $in->current_company_url = $actual_url;

                if ($req->hasFile('cv')) {
                    if ($in && !empty($in->cv)) {
                        if (File::exists(cv_public_path() . $in->cv)) {
                            unlink(cv_public_path() . $in->cv);
                        }
                    }
                    $in->cv  = uploadFile($req['cv'], 'uploads/cv') ?? null;
                }
                if ($req->hasFile('profile_image')) {
                    if ($in && !empty($in->profile_image)) {
                        if (File::exists(profile_public_path() . $in->profile_image)) {
                            unlink(profile_public_path() . $in->profile_image);
                        }
                    }
                    $in->profile_image = uploadFile($req['profile_image'], 'uploads/profile') ?? null;
                }

                $in->update();
                if ($in) {
                    $this->updateAndCreateDesiredEmployerTypes($in->uuid, @$request->desired_employer_type);
                    $this->multipleSelectUpsertTitle('cand_legal_tech_tools', $in->uuid, @$request->legal_tech_tools);
                    $this->multipleSelectUpsertTitle('cand_tech_tools', $in->uuid, @$request->tech_tools);
                    $this->multipleSelectUpsertTitle('cand_qualifications', $in->uuid, @$request->qualification);
                    $this->multipleSelectUpsertId('cand_working_arrangements', $in->uuid, @$request->desired_working_arrangements);
                    $this->multipleSelectUpsertId('cand_mst_cultural_backgrounds', $in->uuid, @$request->cultural_background);
                    $this->multipleSelectUpsertId('cand_desired_countries', $in->uuid, @$request->desired_country);
                    $this->multipleSelectUpsertId('cand_mst_customer_types', $in->uuid, @$request->customer_type);
                    $this->multipleSelectUpsertId('cand_mst_languages', $in->uuid, @$request->languages);
                }

                $response = [
                    'details' => $in
                ];

                $in->makeHidden('uuid', 'id')->toArray();
                if ($response) {
                    DB::commit();
                    return sendDataHelper('Details updated.', $response, ok());
                } else {
                    DB::rollBack();
                    return sendError('Something went wrong', [], error());
                }
            }
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }
    }

    /* */
    public function detailsUpdate(Request $request)
    {
        try {
            $data = Validator::make($request->all(), [
                'cv' => 'required|mimetypes:application/pdf|max:10000',
                'profile_image' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            ], [
                'cv.required' => 'Please upload CV.'
            ]);

            if ($data->fails()) {
                return sendError($data->errors()->first(), [], errorValid());
            }

            $in = Candidate::where('email', auth()->user()->email)->first();
            if (!$in) {
                $in = new Candidate();
                $in->uuid = Str::uuid()->toString();
                $in->email = auth()->user()->email;
                $in->password = null;
            }
            if ($request->hasFile('cv')) {
                if (!empty($in->cv)) {
                    if (File::exists(cv_public_path() . $in->cv)) {
                        unlink(cv_public_path() . $in->cv);
                    }
                }
                $in->cv = uploadFile($request['cv'], 'uploads/cv') ?? null;
            }
            if ($request->hasFile('profile_image')) {
                if (!empty($in->profile_image)) {
                    if (File::exists(profile_public_path() . $in->profile_image)) {
                        unlink(profile_public_path() . $in->profile_image);
                    }
                }
                $in->profile_image = uploadFile($request['profile_image'], 'uploads/profile') ?? null;
            }
            $in->save();

            $response = [
                'details' => $in
            ];
            $in->makeHidden('uuid', 'id')->toArray();
            if ($response) {
                return sendDataHelper('Details updated.', $response, ok());
            } else {
                return sendError('Something went wrong', [], unAuth());
            }
        } catch (\Throwable $th) {
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }
    }

    public function guestCandidatescheck(Request $request)
    {
  
        try {
            if (respValid($request)) {
                return respValid($request);
            }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/

            $request = (object) $request;
            // $gmst = GuestMater::where("email", auth()->user()->email)->withTrashed()->first();
            // if (empty($gmst)) {
            //     $gmst = new GuestMater();
            //     $gmst->uuid = Str::uuid()->toString();
            //     $gmst->email = auth()->user()->email;
            //     $gmst->save();
            // } 
            $email = Auth::User()->email;
            $response = Candidate::where("email",$email)->first();
            return sendDataHelper("List", $response, ok());
        } catch (\Throwable $th) {
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }
    }
    /*
        Single  candidates list
    */

    /* Guest get a single candidates list */
    public function guestSingleCandidatesList(Request $request)
    {
        return self::singleCandidatesList($request, auth()->user()->role);
    }

    /* Admin get a single candidates list */
    public function adminSingleCandidatesList(Request $request)
    {
        return self::singleCandidatesList($request, auth()->user()->role);
    }

    /* Emp get a single candidates list */
    public function empSingleCandidatesList(Request $request)
    {
        return self::singleCandidatesList($request, auth()->user()->role);
    }

    /* Get Single Candidates list */
    public function singleCandidatesList($request, $role)
    {

        try {
            if (respValid($request)) {
                return respValid($request);
            }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/

            $data = Validator::make($request, [
                'uuid' => 'required|exists:candidates,uuid',
            ]);
            if ($data->fails()) {
                return sendError($data->errors()->first(), [], errorValid());
            }
            $request = (object) $request;

            $query = Candidate::query();

            if ($role == roleGuest() || $role ==  roleEmp()) {
                $query->where('harrier_candidate', yes());
            }
            if ($role ==  roleEmp()) {
                $query->with([
                    'is_cv_list_same_emp'  => function ($query) {
                        return $query->select('id', 'job_id', 'c_uid', 'is_cv', 'c_job_status', 'interview_request');
                    }
                ]);
                $query->with('emp_short_list', function ($q) {
                    $emp_uuid = employer(auth()->user()->email)->value('uuid');
                    $q->where('emp_uuid', $emp_uuid);
                });
            }

            if ($uuid = $request->uuid) {
                $query->where('uuid', $uuid);
            }
            switch ($role) {
                case roleAdmin():
                    break;
                case roleGuest():
                    break;
                case roleEmp():
                    // employer_type
                    break;

                default:
                    $query->select('job_title');
                    break;
            }
            $response = $query->first();
            if ($role == roleEmp()) {
                // dd($role); 
                if (mst_employer_types($response->employer_type)) {
                    $response->employer_type = mst_employer_types($response->employer_type)->title;
                }

                if (count(mst_employer_types($response->desired_employer_type)) > 0) {
                    $response->desired_employer_type = mst_employer_types($response->desired_employer_type)->pluck('title');
                }

                if (mst_regions($response->current_region)) {
                    $response->current_region = mst_regions($response->current_region)->title;
                }

                if (mst_countries($response->current_country)) {
                    $response->current_country = mst_countries($response->current_country)->country_name;
                }

                if (count(mst_countries($response->desired_country)) > 0) {
                    $response->desired_country = mst_countries($response->desired_country)->pluck('country_name');
                }

                if (mst_working_arrangements($response->working_arrangements)) {
                    $response->working_arrangements = mst_working_arrangements($response->working_arrangements)->title;
                }

                if (count(mst_working_arrangements($response->desired_working_arrangements)) > 0) {
                    $response->desired_working_arrangements = mst_working_arrangements($response->desired_working_arrangements)->pluck('title');
                }

                if (count(mst_customer_types($response->customer_type)) > 0) {
                    $response->customer_type = mst_customer_types($response->customer_type)->pluck('title');
                }

                if (count(mst_legal_tech_tools($response->legal_tech_tools)) > 0) {
                    $response->legal_tech_tools = mst_legal_tech_tools($response->legal_tech_tools)->pluck('title');
                }

                if (count(mst_tech_tools($response->tech_tools)) > 0) {
                    $response->tech_tools = mst_tech_tools($response->tech_tools)->pluck('title');
                }
                if (count(mst_qualifications($response->qualification)) > 0) {
                    $response->qualification = mst_qualifications($response->qualification)->pluck('title');
                }
                if (count(mst_languages($response->languages)) > 0) {
                    $response->languages = mst_languages($response->languages)->pluck('title');
                }

                if (mst_currencies($response->current_salary_symbol)) {
                    $response->current_salary_symbol = mst_currencies($response->current_salary_symbol)->currency_code;
                }
                if (mst_currencies($response->desired_salary_symbol)) {
                    $response->desired_salary_symbol = mst_currencies($response->desired_salary_symbol)->currency_code;
                }
                if (mst_currencies($response->current_bonus_or_commission_symbol)) {
                    $response->current_bonus_or_commission_symbol = mst_currencies($response->current_bonus_or_commission_symbol)->currency_code;
                }
                if (mst_currencies($response->desired_bonus_or_commission_symbol)) {
                    $response->desired_bonus_or_commission_symbol = mst_currencies($response->desired_bonus_or_commission_symbol)->currency_code;
                }
                if (mst_currencies($response->deal_size_symbol)) {
                    $response->deal_size_symbol = mst_currencies($response->deal_size_symbol)->currency_code;
                }
                if (mst_currencies($response->sales_quota_symbol)) {
                    $response->sales_quota_symbol = mst_currencies($response->sales_quota_symbol)->currency_code;
                }
                if (mst_currencies($response->freelance_daily_rate_symbol)) {
                    $response->freelance_daily_rate_symbol = mst_currencies($response->freelance_daily_rate_symbol)->currency_code;
                }
            }
            if ($response) {
                $response->toArray();
            } else {
                $response = [];
            }
            return sendDataHelper("List", $response, ok());
        } catch (\Throwable $th) {
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }
    }

    /* */
    public function candidatesStatusChange(Request $request)
    {
        try {
            if (respValid($request)) {
                return respValid($request);
            }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/

            $data = Validator::make($request, [
                'uuid' => 'required|exists:candidates,uuid',
                'status' => 'required|numeric'
            ]);
            if ($data->fails()) {
                return sendError($data->errors()->first(), [], errorValid());
            }

            $request = (object) $request;
            $in = Candidate::where('uuid', @$request->uuid)->first();
            $in->status = $request->status;
            $in->save();

            self::notfication($in->uuid, 'Status updated');

            $response = [
                'details' => $in
            ];
            if ($response) {
                return sendDataHelper('Status updated.', $response, ok());
            } else {
                return sendError('Something went wrong', [], unAuth());
            }
        } catch (\Throwable $th) {
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }
    }



    /* Get Guest to Candidates list */
    public function guestCandidatesListFilter(Request $request)
    {
        $role = roleGuest();
        $req = $request;
        try {
            if (respValid($request)) {
                return respValid($request);
            }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/
            $request = (object) $request;
            $query = Candidate::query();
            $query->where('harrier_candidate', yes());

            if ($job_title = @$request->job_title) {
                $query->where('job_title', $job_title);
            }

            if ($mst_employer_types = @$request->mst_employer_types) {
                $query->where('employer_type', $mst_employer_types);
            }
            if ($desired_employer_type = @$request->desired_employer_type) {
                $query->whereIn('desired_employer_type', [$desired_employer_type]);
            }

            if ($mst_legal_tech_tools = @$request->mst_legal_tech_tools) {
                $query->whereIn('legal_tech_tools', [$mst_legal_tech_tools]);
            }
            if ($mst_tech_tools = @$request->mst_tech_tools) {
                $query->whereIn('tech_tools', [$mst_tech_tools]);
            }
            if ($mst_channels = @$request->mst_channels) {
                $query->where('channel', $mst_channels);
            }
            if ($mst_cultural_backgrounds = @$request->mst_cultural_backgrounds) {
                $query->whereIn('cultural_background', [$mst_cultural_backgrounds]);
            }
            if ($mst_customer_types = @$request->mst_customer_types) {
                $query->whereIn('customer_type', [$mst_customer_types]);
            }
            if ($mst_faiths = @$request->mst_faiths) {
                $query->where('faith', $mst_faiths);
            }
            if ($mst_genders = @$request->mst_genders) {
                $query->where('gender', $mst_genders);
            }
            if ($mst_qualifications = @$request->mst_qualifications) {
                $query->whereIn('qualification', [$mst_qualifications]);
            }

            if ($mst_regions = @$request->mst_regions) {
                $query->where('current_region', $mst_regions);
            }
            if ($desired_region = @$request->desired_region) {
                $query->whereIn('desired_region', [$desired_region]);
            }

            if ($mst_school_types = @$request->mst_school_types) {
                $query->where('school_type', $mst_school_types);
            }
            if ($mst_sexes = @$request->mst_sexes) {
                $query->where('sex', $mst_sexes);
            }
            if ($mst_sexual_orientations = @$request->mst_sexual_orientations) {
                $query->where('sexual_orientation', $mst_sexual_orientations);
            }
            if ($mst_tech_tools = @$request->mst_tech_tools) {
                $query->whereIn('tech_tools', [$mst_tech_tools]);
            }

            if ($mst_working_arrangements = @$request->mst_working_arrangements) {
                $query->where('working_arrangements', $mst_working_arrangements);
            }
            if ($desired_working_arrangements = @$request->desired_working_arrangements) {
                $query->whereIn('desired_working_arrangements', [$desired_working_arrangements]);
            }

            if ($mst_countries = @$request->mst_countries) {
                $query->where('current_country', $mst_countries);
            }
            if ($desired_country = @$request->desired_country) {
                $query->whereIn('current_country', [$desired_country]);
            }

            // if($notice_period = @$request->notice_period) {   $query->whereBetween('notice_period', [100, 200]);  }

            // if($time_in_industry = @$request->time_in_industry) {   $query->where('time_in_industry', $time_in_industry);   }
            // if($time_in_industry = @$request->time_in_industry) {   $query->where('time_in_industry', $time_in_industry);   }

            switch ($role) {
                case roleAdmin():
                    break;
                case roleGuest():
                    $query->select(
                        'id',
                        'uuid',
                        'job_title',
                        'employer_type',
                        'time_in_current_role',
                        'customer_type',
                        'time_in_industry',
                        'current_salary',
                        'current_bonus_or_commission',
                        'desired_salary',
                        'desired_bonus_or_commission',
                        'notice_period',
                        'pqe',
                        'legal_tech_tools',
                        'current_company_url',
                        'current_salary_symbol',
                        'current_bonus_or_commission_symbol',
                        'desired_salary_symbol',
                        'desired_bonus_or_commission_symbol',
                        'deal_size',
                        'deal_size_symbol',
                        'sales_quota',
                        'sales_quota_symbol',
                        'law_degree',
                        'qualified_lawyer',
                        'jurisdiction',
                        'pqe',
                        'area_of_law',
                        'qualification'
                    );
                    break;
                case roleEmp():
                    $query->select(
                        'id',
                        'status',
                        'uuid',
                        'job_title',
                        'employer_type',
                        'time_in_current_role',
                        'time_in_industry',
                        'current_salary',
                        'current_bonus_or_commission',
                        'desired_salary',
                        'desired_bonus_or_commission',
                        'notice_period',
                        'pqe',
                        'legal_tech_tools',
                        'current_company_url',
                        'current_salary_symbol',
                        'current_bonus_or_commission_symbol',
                        'desired_salary_symbol',
                        'desired_bonus_or_commission_symbol',
                        'deal_size_symbol',
                        'sales_quota_symbol'
                    );
                    $query->where(function ($query) {
                        $query->where('current_company_url', '!=', employer(auth()->user()->email)->url)
                            ->orWhere('current_company_url', null);
                    });
                    break;
                default:
                    $query->select('job_title');
                    break;
            }
            $query->with([
                'employer_type_list', 'current_country_list', 'current_salary_symbol_list', 'current_bonus_or_commission_symbol_list',
                'desired_salary_symbol_list', 'desired_bonus_or_commission_symbol_list', 'deal_size_symbol_list',
                'sales_quota_symbol_list', 'is_cv_list' => function ($query) {
                    return $query->select('id', 'job_id', 'c_uid', 'is_cv');
                }
            ]);

            $list = $query->paginate(
                $perPage = 10,
                $columns = ['*'],
                $pageName = 'page'
            );

            $response = [
                $this->fields('#', 'Candidate UUID', 'uuid',  $list),
                $this->fields(1, 'Job Title', 'job_title',  $list),
                $this->fields(2, 'Employer Type', 'employer_type', $list)
            ];
            // return $response;
            return sendDataHelper("List", $response, ok());

            /*
                [
                    { id: 1, title: "Job Title" },
                    { id: 2, title: "Employer Type" },
                    { id: 3, title: "Time in Current Role" },
                    { id: 4, title: "Time in Industry" },
                    { id: 5, title: "Line Management" },
                    { id: 6, title: "Desired Employer Type" },
                    { id: 7, title: "Current Country" },
                    { id: 8, title: "Current Region" },
                    { id: 9, title: "Current Salary" },
                    { id: 10, title: "Current Bonus / Commission" },
                    { id: 11, title: "Desired Salary" },
                    { id: 12, title: "Desired Bonus / Commission" },
                    { id: 13, title: "Notice Period" },
                    { id: 14, title: "Working Arrangements" },
                    { id: 15, title: "Desired Working Arrangements" },
                    { id: 16, title: "Law Degree" },
                    { id: 17, title: "Qualified Lawyer" },
                    { id: 18, title: "Jurisdiction" },
                    { id: 19, title: "PQE" },
                    { id: 20, title: "Area of Law" },
                    { id: 21, title: "Legal Experience" },
                    { id: 22, title: "Customer Type" },
                    { id: 23, title: "Deal Size" },
                    { id: 24, title: "Sales quota" },
                    { id: 25, title: "LegalTech Tools" },
                    { id: 26, title: "Tech Tools" },
                    { id: 27, title: "Qualifications" },
                ];
            */
        } catch (\Throwable $th) {
            // throw $th;
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }
    }

    /* Get Emp to Candidates list */
    public function empCandidatesListFilter(Request $request)
    {
        $role = roleGuest();
        $req = $request;
        try {
            if (respValid($request)) {
                return respValid($request);
            }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/
            $request = (object) $request;
            $query = Candidate::query();
            $query->where('harrier_candidate', yes());

            if ($job_title = @$request->job_title) {
                $query->where('job_title', $job_title);
            }

            if ($mst_employer_types = @$request->mst_employer_types) {
                $query->where('employer_type', $mst_employer_types);
            }
            if ($desired_employer_type = @$request->desired_employer_type) {
                $query->whereIn('desired_employer_type', [$desired_employer_type]);
            }

            if ($mst_legal_tech_tools = @$request->mst_legal_tech_tools) {
                $query->whereIn('legal_tech_tools', [$mst_legal_tech_tools]);
            }
            if ($mst_tech_tools = @$request->mst_tech_tools) {
                $query->whereIn('tech_tools', [$mst_tech_tools]);
            }
            if ($mst_channels = @$request->mst_channels) {
                $query->where('channel', $mst_channels);
            }
            if ($mst_cultural_backgrounds = @$request->mst_cultural_backgrounds) {
                $query->whereIn('cultural_background', [$mst_cultural_backgrounds]);
            }
            if ($mst_customer_types = @$request->mst_customer_types) {
                $query->whereIn('customer_type', [$mst_customer_types]);
            }
            if ($mst_faiths = @$request->mst_faiths) {
                $query->where('faith', $mst_faiths);
            }
            if ($mst_genders = @$request->mst_genders) {
                $query->where('gender', $mst_genders);
            }
            if ($mst_qualifications = @$request->mst_qualifications) {
                $query->whereIn('qualification', [$mst_qualifications]);
            }

            if ($mst_regions = @$request->mst_regions) {
                $query->where('current_region', $mst_regions);
            }
            if ($desired_region = @$request->desired_region) {
                $query->whereIn('desired_region', [$desired_region]);
            }

            if ($mst_school_types = @$request->mst_school_types) {
                $query->where('school_type', $mst_school_types);
            }
            if ($mst_sexes = @$request->mst_sexes) {
                $query->where('sex', $mst_sexes);
            }
            if ($mst_sexual_orientations = @$request->mst_sexual_orientations) {
                $query->where('sexual_orientation', $mst_sexual_orientations);
            }
            if ($mst_tech_tools = @$request->mst_tech_tools) {
                $query->whereIn('tech_tools', [$mst_tech_tools]);
            }

            if ($mst_working_arrangements = @$request->mst_working_arrangements) {
                $query->where('working_arrangements', $mst_working_arrangements);
            }
            if ($desired_working_arrangements = @$request->desired_working_arrangements) {
                $query->whereIn('desired_working_arrangements', [$desired_working_arrangements]);
            }

            if ($mst_countries = @$request->mst_countries) {
                $query->where('current_country', $mst_countries);
            }
            if ($desired_country = @$request->desired_country) {
                $query->whereIn('current_country', [$desired_country]);
            }

            // if($notice_period = @$request->notice_period) {   $query->whereBetween('notice_period', [100, 200]);  }

            // if($time_in_industry = @$request->time_in_industry) {   $query->where('time_in_industry', $time_in_industry);   }
            // if($time_in_industry = @$request->time_in_industry) {   $query->where('time_in_industry', $time_in_industry);   }

            switch ($role) {
                case roleAdmin():
                    break;
                case roleGuest():
                    $query->select(
                        'id',
                        'uuid',
                        'job_title',
                        'employer_type',
                        'time_in_current_role',
                        'customer_type',
                        'time_in_industry',
                        'current_salary',
                        'current_bonus_or_commission',
                        'desired_salary',
                        'desired_bonus_or_commission',
                        'notice_period',
                        'pqe',
                        'legal_tech_tools',
                        'current_company_url',
                        'current_salary_symbol',
                        'current_bonus_or_commission_symbol',
                        'desired_salary_symbol',
                        'desired_bonus_or_commission_symbol',
                        'deal_size',
                        'deal_size_symbol',
                        'sales_quota',
                        'sales_quota_symbol',
                        'law_degree',
                        'qualified_lawyer',
                        'jurisdiction',
                        'pqe',
                        'area_of_law',
                        'qualification'
                    );
                    break;
                case roleEmp():
                    $query->select(
                        'id',
                        'status',
                        'uuid',
                        'job_title',
                        'employer_type',
                        'time_in_current_role',
                        'time_in_industry',
                        'current_salary',
                        'current_bonus_or_commission',
                        'desired_salary',
                        'desired_bonus_or_commission',
                        'notice_period',
                        'pqe',
                        'legal_tech_tools',
                        'current_company_url',
                        'current_salary_symbol',
                        'current_bonus_or_commission_symbol',
                        'desired_salary_symbol',
                        'desired_bonus_or_commission_symbol',
                        'deal_size_symbol',
                        'sales_quota_symbol'
                    );
                    $query->where(function ($query) {
                        $query->where('current_company_url', '!=', employer(auth()->user()->email)->url)
                            ->orWhere('current_company_url', null);
                    });
                    break;
                default:
                    $query->select('job_title');
                    break;
            }
            $query->with([
                'employer_type_list', 'current_country_list', 'current_salary_symbol_list', 'current_bonus_or_commission_symbol_list',
                'desired_salary_symbol_list', 'desired_bonus_or_commission_symbol_list', 'deal_size_symbol_list',
                'sales_quota_symbol_list', 'is_cv_list' => function ($query) {
                    return $query->select('id', 'job_id', 'c_uid', 'is_cv');
                }
            ]);

            $list = $query->paginate(
                $perPage = 10,
                $columns = ['*'],
                $pageName = 'page'
            );

            $response = [
                $this->fields('#', 'Candidate UUID', 'uuid',  $list),
                $this->fields(1, 'Job Title', 'job_title',  $list),
                $this->fields(2, 'Employer Type', 'employer_type', $list)
            ];
            // return $response;
            return sendDataHelper("List", $response, ok());

            /*
                [
                    { id: 1, title: "Job Title" },
                    { id: 2, title: "Employer Type" },
                    { id: 3, title: "Time in Current Role" },
                    { id: 4, title: "Time in Industry" },
                    { id: 5, title: "Line Management" },
                    { id: 6, title: "Desired Employer Type" },
                    { id: 7, title: "Current Country" },
                    { id: 8, title: "Current Region" },
                    { id: 9, title: "Current Salary" },
                    { id: 10, title: "Current Bonus / Commission" },
                    { id: 11, title: "Desired Salary" },
                    { id: 12, title: "Desired Bonus / Commission" },
                    { id: 13, title: "Notice Period" },
                    { id: 14, title: "Working Arrangements" },
                    { id: 15, title: "Desired Working Arrangements" },
                    { id: 16, title: "Law Degree" },
                    { id: 17, title: "Qualified Lawyer" },
                    { id: 18, title: "Jurisdiction" },
                    { id: 19, title: "PQE" },
                    { id: 20, title: "Area of Law" },
                    { id: 21, title: "Legal Experience" },
                    { id: 22, title: "Customer Type" },
                    { id: 23, title: "Deal Size" },
                    { id: 24, title: "Sales quota" },
                    { id: 25, title: "LegalTech Tools" },
                    { id: 26, title: "Tech Tools" },
                    { id: 27, title: "Qualifications" },
                ];
            */
        } catch (\Throwable $th) {
            // throw $th;
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }
    }
}
