<?php

namespace App\Http\Traits;

use App\Http\Traits\Candidate\CandMultipleSelectTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Candidate;
use App\Models\Candidate\CandLegalTechTools;
use App\Models\Emp\EmpCandidate;
use App\Models\Employer;
use App\Models\JobCondidate;
use App\Models\Master\MstRegion;
use App\Models\Unique\Country;
use App\Notifications\CVRequested;
use App\Notifications\CVUpdate;
use Illuminate\Support\Facades\DB;

trait CandidateTrait {

    use CandDesEmpTypesTrait, CandMultipleSelectTrait;
    public function formOneValidate(Request $request)
    {
        try {
            if(respValid($request)) { return respValid($request); }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/
            
            $data = Validator::make($request, [
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email|unique:candidates,email',
                'phone' => 'required|unique:candidates,phone',
                'job_title' => 'required',
                'employer' => 'required',
                'current_company_url' => 'required',
                'employer_type' => 'required',
                'time_in_current_role' => 'required|date',
                'time_in_industry' => 'required|date',
                'line_management' => 'required',
                'desired_employer_type' => 'required',
                
                'current_country' => 'required',
                
            ],[
                'first_name.required' => 'First name required',
                'last_name.required' => 'Last name required',
                'email.required' => 'Email Address required',
                'email.unique' => 'Already this email submitted. Please contact us Harrier!',
                'phone.required' => 'Phone number required',
                'job_title.required' => 'Job title required',
                'current_company_url.required' => 'Current company url required',
                'employer.required' => 'Current employer required',
                'employer_type.required' => 'Employer type required',
                'time_in_current_role.required' => 'Start this role date required',
                'time_in_current_role.date' => 'Start this role date invalid',
                'time_in_industry.required' => 'Join this industry date required',
                'time_in_industry.date' => ' Join this industry date invalid',
                'line_management.required' => 'Line Management required',   
                'desired_employer_type.required' => 'Select are you open to what roles with',
                'current_country.required' => 'Select country are you based in',
                
            ]);

            if ($data->fails()) {

                $errors_data = [
                   'errors' => $data->messages()->getMessages()
                ];
                return sendError($data->errors()->first(), $errors_data, errorValid());
            }

            if(@$request['current_country'] != null)
            {
                Country::where('id', $request['current_country'])->first();
                $state = MstRegion::where('country_id', $request['current_country'])->first();
                if($state)
                {
                    $data = Validator::make($request, [
                        'current_region' => 'required',
                    ],[
                        'current_region.required' => 'Current region, city required',
                    ]);
                    if ($data->fails()) {
                        return sendError($data->errors()->first(), [], errorValid());
                    }
                }
            }

            $data = Validator::make($request, [
                'desired_country' => 'required',
                'desired_region' => 'required',

                'current_salary' => 'required',
                'current_salary_symbol' => 'required',
                
                'current_bonus_or_commission' => 'required',
                'current_bonus_or_commission_symbol' => 'required',
                
                'desired_salary' => 'required',
                'desired_salary_symbol' => 'required',
                
                'desired_bonus_or_commission' => 'required',
                'desired_bonus_or_commission_symbol' => 'required',
                
                'notice_period' => 'required',
                'status' => 'required|in:1,2,3,4',
                'working_arrangements' => 'required',
                'desired_working_arrangements' => 'required',

                'freelance_current' => 'required',
                'freelance_future' => 'required',
               
            ],[
                'desired_country.required' => 'Are you open to working in other countries? required.',
                'desired_region.required' => 'Where in your country are you willing to be based? required.',

                'current_salary.required' => 'Current salary required',
                'current_salary_symbol.required' => 'Current salary currency required',

                'current_bonus_or_commission.required' => 'Current bonus required',
                'current_bonus_or_commission_symbol.required' => 'Current bonus currency required',

                'desired_salary.required' => 'Desired salary required',
                'desired_salary_symbol.required' => 'Desired salary currency required',

                'desired_bonus_or_commission.required' => 'Desired bonus required',
                'desired_bonus_or_commission_symbol.required' => 'Desired bonus currency required',

                'notice_period.required' => 'Notice period required',
                'status.required' => 'Current job-seeking status required',

                'working_arrangements.required' => 'Current Working arrangement required',
                'desired_working_arrangements.required' => 'What working arrangements would you consider? required',

                'freelance_current.required' => 'Current work freelance as a contractor required',
                'freelance_future.required' => 'Open to freelance work required',
                
            ]);

            if ($data->fails()) {
                return sendError($data->errors()->first(), [], errorValid());
            }


            if($request['freelance_future'] == yes() )
            {
                $data = Validator::make($request, [
                    'freelance_daily_rate' => 'required',
                    'freelance_daily_rate_symbol' => 'required',
                ],[
                    'freelance_daily_rate.required' => 'Desired daily rate required',
                    'freelance_daily_rate_symbol.required' => 'Freelance daily rate currency required',
                ]);
                if ($data->fails()) {
                    return sendError($data->errors()->first(), [], errorValid());
                }
            }
           
            return sendDataHelper('Validation done.', [], ok());
        } catch (\Throwable $th) {
            // throw $th;
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }
    }

    public function formTwoValidate(Request $request)
    {
        try {
            if(respValid($request)) { return respValid($request); }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/
            
            $data = Validator::make($request, [
                'law_degree' => 'required|in:1,0',
                'qualified_lawyer' => 'required|in:1,0',

            ],[
                'law_degree.required' => 'Law degree required',
                'qualified_lawyer.required' => 'Qualified Lawyer required',
            ]);

            if ($data->fails()) {
                return sendError($data->errors()->first(), [], errorValid());
            }else{
                if($request['law_degree'] == yes() && $request['qualified_lawyer'] == yes())
                {
                    $data = Validator::make($request, [
                        'jurisdiction' => 'required',   // required
                        'pqe' => 'required|numeric',   // required numeric
                        'area_of_law' => 'required',  // required
                        'legal_experience' => 'required', // required
                    ],[
                        'jurisdiction.required' => 'Jurisdiction required',
                        'pqe.required' => 'Years PQE required',
                        'area_of_law.required' => 'Area of law practice required',
                        'legal_experience.required' => 'Specialist experience within your area of law required',
                    ]);
                    if ($data->fails()) {
                        return sendError($data->errors()->first(), [], errorValid());
                    }
                }
                $data = Validator::make($request, [
                    'legaltech_vendor_or_consultancy' => 'nullable|in:1,0'
                ],[
                    'legaltech_vendor_or_consultancy.required' => 'Do you work for a LegalTech vendor/consultancy? required',
                ]);
                if ($data->fails()) {
                    return sendError($data->errors()->first(), [], errorValid());
                }else{
                    if(@$request['legaltech_vendor_or_consultancy'] == yes())
                    {
                        $data = Validator::make($request, [
                            'customer_type' => 'required',
                            'deal_size' => 'required',
                            'deal_size_symbol' => 'required',
                            'sales_quota' => 'nullable',
                            'sales_quota_symbol' => 'nullable'     
                        ],[
                            'customer_type.required' => 'Select What type of customer do you work with?',
                            'deal_size.required' => 'Deal size required',
                            'deal_size_symbol.required' => 'Deal size currency required',
                            // 'sales_quota.required' => 'Sales quota required',
                            // 'sales_quota_symbol.required' => 'Sales quota currency required',
                        ]);
                        if ($data->fails()) {
                            return sendError($data->errors()->first(), [], errorValid());
                        }
                    }
                }

                $data = Validator::make($request, [
                    'legal_tech_tools' => 'required',
                    'tech_tools' => 'required',
                    'qualification' => 'required',
                    'languages' => 'required',
                    'profile_about' => 'required|min:300',
                ],[
                    'legal_tech_tools.required' => 'Legal tech tools required',
                    'tech_tools.required' => 'Other tech tools or coding languages required',
                    'qualification.required' => 'Other relevant qualifications field required',
                    'languages.required' => 'Languages required',
                    'profile_about.required' => 'Profile about required',
                    'profile_about.min' => 'Profile about at least 300 characters required.',
                    
                ]);
                
                if ($data->fails()) {
                    return sendError($data->errors()->first(), [], errorValid());
                }
            }
            return sendDataHelper('Validation done.', [], ok());
        } catch (\Throwable $th) {
            // throw $th;
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }
    }

    public function formThreeValidate(Request $request)
    {
        try {
            if(respValid($request)) { return respValid($request); }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/
            
            $data = Validator::make($request, [
                'cultural_background' => 'required',
                'sex' => 'required',
                'gender_identity' => 'required',
                'sexual_orientation' => 'required',
                'disability' => 'required',
                'disability_specific' => 'nullable',
            ],[
                'cultural_background.required' => 'Cultural background required',
                'sex.required' => 'Sex required',
                'gender_identity.required' => 'Gender identity required',
                'sexual_orientation.required' => 'Sexual orientation required',
                'disability.required' => 'Disability required',
            ]);

            if ($data->fails()) {
                return sendError($data->errors()->first(), [], errorValid());
            }

            // $req = $request;
            // $request = (object) $req;
            // return $request['disability'];
            if($request['disability'] == prefer_yes())
            {
                $data = Validator::make($request, [
                    'disability_specific' => 'required'
                ],[
                    'disability_specific.required' => 'Disability specific required',
                ]);
    
                if ($data->fails()) {
                    return sendError($data->errors()->first(), [], errorValid());
                }
            }

            $data = Validator::make($request, [
                'first_gen_he' => 'required',
                'parents_he' => 'required',
                'free_school_meals' => 'required',
                'main_earner_occupation' => 'required',
                'school_type' => 'required',
                'faith' => 'required',
                'visa' => 'required',
            ],[
                'first_gen_he.required' => 'Your family to receive a higher education required',
                'parents_he.required' => 'Parent(s) complete a university degree required',
                'free_school_meals.required' => 'Eligible for free school meals or your household received income support? required',
                'main_earner_occupation.required' => 'The occupation of your main household earner required',
                'school_type.required' => 'What type of school did you attend for the majority of your schooling required',
                'faith.required' => 'Please indicate your Religion required',
                'visa.required' => 'Select future require sponsorship for an employment Visa',
            ]);

            if ($data->fails()) {
                return sendError($data->errors()->first(), [], errorValid());
            }
            return sendDataHelper('Validation done.', [], ok());
        } catch (\Throwable $th) {
            // throw $th;
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }
    }

    public function formStore(Request $request)
    {
        DB::beginTransaction();
        try {

            $data = Validator::make($request->all(), [
                'cv'=> 'nullable|mimes:pdf,doc,docx|max:10000',
                'profile_image' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            ]);

            if ($data->fails()) {
                return sendError($data->errors()->first(), [], errorValid());
            }
            $cv_upload = null;
            $profile_upload = null;
            if ($request->hasFile('cv'))    {   $cv_upload = $request->cv;  }
            if ($request->hasFile('profile_image')) {    $profile_upload= $request->profile_image;   }
            
            if(respValid($request)) { return respValid($request); }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/
            
            $data = Validator::make($request, [
                'privacy_policy' => 'nullable',
                'harrier_search' => 'required',
                'harrier_candidate' => 'nullable',
                'channel' => 'nullable|exists:mst_channels,id',
                'channel_other' => 'nullable',
                'referral' => 'nullable',
            ],[
                'harrier_search.required' => 'Are you happy to share your data with Harrier Search required',
                'privacy_policy.required' => 'Privacy policy required',
                'channel.exists' => 'How did you hear about us not found',
            ]);

            if ($data->fails()) {
                return sendError($data->errors()->first(), [], errorValid());   
            }else{
              
                $request = (object) $request;
                
                $actual_url = @$request->current_company_url;
                if(@$request->current_company_url)
                {
                    $actual_url = actual_url($actual_url);
                    $actual_url = Str::of($actual_url)->rtrim('/');
                }
                $check_url['url'] = $actual_url; 
                $data = Validator::make($check_url, [
                    'current_company_url' =>  'nullable|unique:candidates,current_company_url',
                ]);

                if ($data->fails()) {
                    return sendError($data->errors()->first(), [], errorValid());
                }

                $in = new Candidate();

                if(@$request->time_in_current_role)
                {
                    $in->time_in_current_role = date('Y-m-d', strtotime(@$request->time_in_current_role));
                }else{
                    $in->time_in_current_role = null;
                }

                if(@$request->time_in_industry)
                {
                    $in->time_in_industry = date('Y-m-d', strtotime(@$request->time_in_industry));
                }else{
                    $in->time_in_industry = null;
                }
                
                $in->uuid = Str::uuid()->toString();
                $in->name = @$request->name;
                $in->first_name  = @$request->first_name;
                $in->last_name = @$request->last_name;
                $in->phone = @$request->phone;
                $in->email = @$request->email;
                $in->password = bcrypt(Str::random(6));
                $in->status = @$request->status;
                $in->job_title = @$request->job_title;
                $in->employer = @$request->employer;
                if(@$request->employer_type)
                {
                    $in->employer_type = @$request->employer_type;
                }

                if(@$request->line_management)
                {
                    $in->line_management = @$request->line_management ?? 0;
                }

                $in->desired_employer_type = @$request->desired_employer_type;
                if(@$request->current_region)   { $in->current_region = @$request->current_region;  }
                if(@$request->current_country) {    $in->current_country = @$request->current_country;  }
                $in->desired_region = @$request->desired_region;
                $in->desired_country = @$request->desired_country;

                $in->current_salary = (@$request->current_salary ? @$request->current_salary : 0);
                if(@$request->current_salary_symbol)
                {
                    $in->current_salary_symbol = @$request->current_salary_symbol;
                }
                $in->current_bonus_or_commission = (@$request->current_bonus_or_commission ? @$request->current_bonus_or_commission : 0);
                $in->current_bonus_or_commission_symbol = (@$request->current_bonus_or_commission_symbol ? @$request->current_bonus_or_commission_symbol : null);
                if(@$request->desired_salary_symbol)
                {
                    $in->desired_salary_symbol  = @$request->desired_salary_symbol;
                }


                $in->desired_salary = (@$request->desired_salary ? @$request->desired_salary : 0);
                if(@$request->desired_bonus_or_commission_symbol)
                {
                    $in->desired_bonus_or_commission_symbol = @$request->desired_bonus_or_commission_symbol;
                }

                $in->desired_bonus_or_commission = (@$request->desired_bonus_or_commission ? @$request->desired_bonus_or_commission : 0);
                
                if(@$request->desired_bonus_or_commission_symbol)
                {
                    $in->desired_bonus_or_commission_symbol = @$request->desired_bonus_or_commission_symbol;
                }

                if(@$request->notice_period != null)
                {
                    $in->notice_period = @$request->notice_period;
                }else{
                    $in->notice_period = null;
                }
                if(@$request->working_arrangements) {   $in->working_arrangements = @$request->working_arrangements; }
                $in->desired_working_arrangements = @$request->desired_working_arrangements;
                
                if(@$request->law_degree)
                {
                    if(@$request->law_degree == 1 || @$request->law_degree == 0)    {   $in->law_degree = @$request->law_degree;  }
                }

                if(@$request->qualified_lawyer){
                    if(@$request->qualified_lawyer == 1 || @$request->qualified_lawyer == 0)    {   $in->qualified_lawyer = @$request->qualified_lawyer;  }

                }

                $in->jurisdiction =@$request->jurisdiction;
                $in->pqe =@$request->pqe;
                $in->area_of_law = @$request->area_of_law;
                $in->legal_experience =@$request->legal_experience;
                $in->customer_type =@$request->customer_type;
                
                $in->deal_size =@$request->deal_size;
                if(@$request->deal_size_symbol)
                {
                    $in->deal_size_symbol =@$request->deal_size_symbol;
                }
                
                $in->sales_quota =@$request->sales_quota;
                if(@$request->sales_quota_symbol)
                {
                    $in->sales_quota_symbol = @$request->sales_quota_symbol;
                }

                $in->legal_tech_tools =@$request->legal_tech_tools;
                $in->tech_tools =@$request->tech_tools;
                $in->qualification =@$request->qualification;
                $in->languages =@$request->languages;
                $in->profile_about =@$request->profile_about;
                $in->cultural_background =@$request->cultural_background;
                $in->first_gen_he =@$request->first_gen_he;
                $in->gender =@$request->gender;
                $in->disability =@$request->disability;
                $in->disability_specific =@$request->disability_specific;
                $in->free_school_meals =@$request->free_school_meals;
                $in->parents_he =@$request->parents_he;

                $in->school_type = @$request->school_type;
                
                $in->faith =@$request->faith;
                $in->sex =@$request->sex;
                $in->gender_identity = @$request->gender_identity;
                $in->sexual_orientation =@$request->sexual_orientation;
                $in->visa =@$request->visa;
                $in->privacy_policy =@$request->privacy_policy;
                $in->harrier_search =@$request->harrier_search;
                $in->harrier_candidate =@$request->harrier_candidate;
                if(@$request->channel)
                {
                    $in->channel = @$request->channel;
                }
                $in->channel_other =@$request->channel_other;
                $in->referral =@$request->referral;

                if(@$request->freelance_current)
                {
                    if(@$request->freelance_current == 1 || @$request->freelance_current == 0) { $in->freelance_current = @$request->freelance_current; }
                }
                
                if(@$request->freelance_future)
                {
                    if(@$request->freelance_future == 1 || @$request->freelance_future == 0)
                    {
                        $in->freelance_future = @$request->freelance_future;
                    }
                }
                
                if(@$request->legaltech_vendor_or_consultancy)
                {
                    if(@$request->legaltech_vendor_or_consultancy == 1 || @$request->legaltech_vendor_or_consultancy == 0)
                    {
                        $in->legaltech_vendor_or_consultancy = @$request->legaltech_vendor_or_consultancy;
                    }    
                }
                
                $in->freelance_daily_rate = @$request->freelance_daily_rate;
                if(@$request->freelance_daily_rate_symbol)
                {
                    $in->freelance_daily_rate_symbol = @$request->freelance_daily_rate_symbol;
                }
                
                $in->current_company_url = $actual_url;                
                
                if($cv_upload)  {    $in->cv = uploadFile($cv_upload, 'uploads/cv') ?? null;    }
                if($profile_upload) {   $in->profile_image = uploadFile($profile_upload, 'uploads/profile') ?? null;    }
                
                $in->save(); 

                if($in)
                {
                    if(@$request->desired_employer_type)
                    {
                        $this->updateAndCreateDesiredEmployerTypes($in->uuid, @$request->desired_employer_type);
                    }
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
                    return sendDataHelper('Thank you for create your candidate profile. we will email contact you shortly.', $response, ok());
                } else {
                    DB::rollBack();
                    return sendError('Something went wrong', [], unAuth());
                }
            }
        } catch (\Throwable $th) {
            // throw $th;
            DB::rollBack();
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }
    }
    
    public function candDetailsUpdate(Request $request) /* Candidate deatils update */
    {
        
        DB::beginTransaction();
        $req = $request;
        try {
            
            $data = Validator::make($req->all(), [
                'cv'=> 'nullable|mimes:pdf,doc,docx|max:10000',
                'profile_image' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            ]);

            if ($data->fails()) {
                return sendError($data->errors()->first(), [], errorValid());
            }

            $cv = null;
            $profile_image = null;
            
            
            if(respValid($request)) { return respValid($request); }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/
            
            $data = Validator::make($request, [
                'uuid' => 'required|exists:candidates,uuid',

            ],[
                'uuid.required' => 'Select Candidate deatils not found',
                'uuid.exists' => 'Select Candidate deatils not found',
            ]);

            if ($data->fails()) {
                return sendError($data->errors()->first(), [], errorValid());
            }else{
                $in = Candidate::where('uuid', @$request['uuid'])->first();

                $data = Validator::make($request, [
                    'email' => 'required|email|unique:candidates,email,'. $in->id,
                    'first_name' => 'nullable',
                    'last_name' => 'nullable',
                    'phone' => 'nullable|unique:candidates,phone,'. $in->id,
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
                ],[
                    'email.unique' => 'Already this email submitted. please contact us Harrier!',
                    'harrier_search.required' => 'Are you happy to share your data with Harrier Search required',
                ]);
    
                if ($data->fails()) {
                    return sendError($data->errors()->first(), [], errorValid());
                }

                $request = (object) $request;

                $actual_url = @$request->current_company_url;
                if(@$request->url)
                {
                    $actual_url = Str::replace(' ', '', $actual_url);
                    $actual_url = actual_url($actual_url);
                    $actual_url = Str::of($actual_url)->rtrim('/');
                }
                $check_url['url'] = $actual_url;

                $data = Validator::make($check_url, [
                    'current_company_url' => 'nullable|unique:candidates,current_company_url,'.$in->id,
                ]);
    
                if ($data->fails()) {
                    return sendError($data->errors()->first(), [], errorValid());
                }

                if(@$request->time_in_current_role)
                {
                    $in->time_in_current_role = date('Y-m-d', strtotime(@$request->time_in_current_role));
                }else{
                    $in->time_in_current_role = null;
                }
                
                if(@$request->time_in_industry)
                {
                    $in->time_in_industry = date('Y-m-d', strtotime(@$request->time_in_industry));
                }else{
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
                if(@$request->line_management)
                {
                    $in->line_management = @$request->line_management ?? 0;
                }else{
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
                
                $in->notice_period = @$request->notice_period ;
                if(@$request->working_arrangements)    {   $in->working_arrangements = @$request->working_arrangements;  }
                $in->desired_working_arrangements = @$request->desired_working_arrangements;   

                if(@$request->law_degree == 1 || @$request->law_degree == 0)    {   $in->law_degree = @$request->law_degree;  }
                if(@$request->qualified_lawyer == 1 || @$request->qualified_lawyer == 0)    {   $in->qualified_lawyer = @$request->qualified_lawyer;  }
                
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

                if(@$request->school_type)
                {
                    $in->school_type = @$request->school_type;
                }else{
                    $in->school_type = false;
                }
                
                $in->faith = @$request->faith;
                $in->sex = @$request->sex;
                $in->gender_identity = @$request->gender_identity;
                $in->sexual_orientation = @$request->sexual_orientation;
                $in->visa = @$request->visa;
                $in->privacy_policy = @$request->privacy_policy;
                $in->harrier_search = @$request->harrier_search;

                if(@$request->harrier_candidate) {  $in->harrier_candidate = @$request->harrier_candidate;  }
                if(@$request->channel)
                {
                    $in->channel = @$request->channel;
                }
                $in->channel_other =@$request->channel_other;
                $in->referral = @$request->referral;

                if(@$request->is_job_search)    {   $in->is_job_search = @$request->is_job_search;  }
                if(@$request->freelance_current == 1 || @$request->freelance_current == 0)    {   $in->freelance_current = @$request->freelance_current;  }
                if(@$request->freelance_future == 1 || @$request->freelance_future == 0) {   $in->freelance_future = @$request->freelance_future; }
                if(@$request->legaltech_vendor_or_consultancy == 1 || @$request->legaltech_vendor_or_consultancy == 0) {   $in->legaltech_vendor_or_consultancy = @$request->legaltech_vendor_or_consultancy; }
                
                $in->freelance_daily_rate = @$request->freelance_daily_rate;
                $in->freelance_daily_rate_symbol = (@$request->freelance_daily_rate_symbol ? @$request->freelance_daily_rate_symbol : null);
                
                
                $in->current_company_url = $actual_url;
                
                if ($req->hasFile('cv'))    
                {   
                    if($in && !empty($in->cv)){
                        if (File::exists(cv_public_path().$in->cv)) 
                        {
                            unlink(cv_public_path().$in->cv);
                        }
                    }
                    $in->cv  = uploadFile($req['cv'], 'uploads/cv') ?? null;
                }
                if ($req->hasFile('profile_image'))    
                {   
                    if($in && !empty($in->profile_image))
                    {
                        if (File::exists(profile_public_path().$in->profile_image)) 
                        {
                            unlink(profile_public_path().$in->profile_image);
                        }
                    }
                    $in->profile_image = uploadFile($req['profile_image'], 'uploads/profile') ?? null;
                }
                
                $in->update();
                if($in)
                {
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

    public static function notfication($uuid, $message)
    {
        // if($ats = DB::table('candidates')->where('uuid','=', $uuid)
        // ->join('job_candidates', 'job_candidates.c_uid', '=', 'candidates.uuid')
        // ->join('jobs', 'jobs.id', '=', 'job_candidates.job_id')
        // ->select('jobs.emp_uid', 'job_candidates.job_id', 'job_candidates.c_uid', 'candidates.id as c_id')
        //  ->first()){}
     
        $emp_candidates = EmpCandidate::where('c_uuid', $uuid)->get();
        if(count($emp_candidates) > 0)
        {
            foreach($emp_candidates as $emp_cand)
            {   
                $candidate = Candidate::where('uuid', $uuid)->first();
                if($emp_cand && $candidate)
                { 
                    if($emp = employer_uuid($emp_cand->emp_uuid))
                    {   
                        $data = [
                            'c_id' => $candidate->id,
                            'message' => $message
                        ];
                        $data = (object) $data;
                        $emp->notify(new CVUpdate($data));
                    }
                }
            }
        }
    }

    /* candDetailsDelete  */
    public function candDetailsDelete(Request $request) /* Candidate deatils update */
    {
        DB::beginTransaction();
        try {            
            if(respValid($request)) { return respValid($request); }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/        
            $in = Candidate::where('uuid', @$request['c_uuid'])->delete();
            if ($in) {
                DB::commit();
                return sendDataHelper('Details deleted.', [], ok());
            } else {
                DB::rollBack();
                return sendError('Something went wrong', [], error());
            }
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }
    }
}