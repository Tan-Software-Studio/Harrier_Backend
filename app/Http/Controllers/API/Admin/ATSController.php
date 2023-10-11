<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\CandidateTrait;
use App\Models\AtsHistory;
use App\Models\ATSView;
use App\Models\JobCondidate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ATSController extends Controller
{
    use CandidateTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        viewATSDataMigrations();
        try {
            $response = ATSView::with(['salary_range_start_symbol_list', 'salary_range_end_symbol_list', 'offer_salary_symbol_list', 'offer_bonus_commission_symbol_list','ats_history'])->get();
            return sendDataHelper("List", $response, ok());
            
        } catch (\Throwable $th) {
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }
    }

    public function allIndex(Request $request)
    {
        viewATSDataMigrations();
        try {
            $response = ATSView::with(['salary_range_start_symbol_list', 'salary_range_end_symbol_list', 'offer_salary_symbol_list', 'offer_bonus_commission_symbol_list','ats_history'])->get();
            return sendDataHelper("List", $response, ok());
            
        } catch (\Throwable $th) {
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     *Sinlge Candidate ATS list
     */
    public function show($c_uuid)
    {
        viewATSDataMigrations();
        try {
            $response = ATSView::where('c_uuid', $c_uuid)->with(['salary_range_start_symbol_list', 'salary_range_end_symbol_list', 'offer_salary_symbol_list', 'offer_bonus_commission_symbol_list','ats_history'])->get();
            return sendDataHelper("List", $response, ok());
            
        } catch (\Throwable $th) {
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        DB::beginTransaction();
        $req = $request;
        try {
            if(respValid($request)) { return respValid($request); }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/
            
            $data = Validator::make($request, [
                'c_job_id' => 'required|exists:job_candidates,id',
                'c_job_status' => 'nullable|exists:mst_candidate_job_statuses,id',
                'offer_accepted_date' => 'nullable|date',
                'offer_salary' => 'nullable|numeric',
                'offer_salary_symbol' => 'nullable|numeric',
                'bonus_or_commission' => 'nullable',
                'offer_bonus_commission_symbol' => 'nullable|numeric',
                'interview_request' => 'nullable',
                'interview_request_date' => 'nullable|date', 
                'start_date' => 'nullable|date',
            ],[
                'c_job_id.required' => 'Select ATS details not found',
            ]);

            if ($data->fails()) {
                return sendError($data->errors()->first(), [], errorValid());
            }else{
                $request = (object) $request;
                $in = JobCondidate::where('id', $request->c_job_id)->first();
                if(@$request->request_date) { $in->request_date = date('Y-m-d', strtotime(@$request->request_date)); }
                
                if(@$request->offer_accepted_date)  {
                    $in->offer_accepted_date = date('Y-m-d', strtotime(@$request->offer_accepted_date));
                }
                if(@$request->c_job_status){
                    $in->c_job_status = @$request->c_job_status;
                }
                if(@$request->offer_salary) {   
                    $in->offer_salary = @$request->offer_salary;
                }
                
                if(@$request->offer_salary_symbol)  {
                    $in->offer_salary_symbol = @$request->offer_salary_symbol;
                }

                if(@$request->offer_bonus_commission)   {
                    $in->offer_bonus_commission = @$request->offer_bonus_commission;
                }
                if(@$request->offer_bonus_commission_symbol)    {   
                    $in->offer_bonus_commission_symbol = @$request->offer_bonus_commission_symbol;  
                }

                if(@$request->interview_request == 1 || @$request->interview_request == 2 || @$request->interview_request == 3)  {
                    if(@$request->c_job_status == 10){
                        $in->interview_request = 2;
                    }else if(@$request->c_job_status == 11){
                        $in->interview_request = 3;
                    }
                    else{
                        $in->interview_request = @$request->interview_request;
                    }
                }
                
                if(@$request->interview_request_date)   {
                    $in->interview_request_date = @$request->interview_request_date;
                }
                
                if(@$request->start_date)   {   
                    $in->start_date = @$request->start_date;
                }                
                $in->update(); 
                viewATSDataMigrations();
                // $response = JobCondidate::where('id', $request->c_job_id)->first();
                $response = ATSView::where('c_job_id', $request->c_job_id)->first();

                if($response->c_job_status)
                {
                    $atsH = new AtsHistory;
                    $atsH->job_candidate_id = $response->c_job_id;
                    $atsH->c_job_status = $response->c_job_status;
                    $atsH->date = ($response->offer_accepted_date ? $response->offer_accepted_date : $request->interview_request_date);
                    $atsH->save();
                }
                
                $response = ATSView::with(['ats_history'])->where('c_job_id', $request->c_job_id)->first();
                $in = JobCondidate::where('id', $request->c_job_id)->first();
                self::notfication($in->c_uid, 'ATS updated');
                if ($response) {
                    DB::commit();
                    return sendDataHelper('Details updated.', $response, ok());
                } else {
                    DB::rollBack();
                    return sendError('Something went wrong', [], error());
                }
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /* Candidate selected ATS details get*/
    public function showSingleATS($c_job_id)
    {
        try {
            $response = DB::table('job_candidates')
            ->select('id as c_job_id', 'interview_request', 'interview_request_date', 'c_job_status', 'offer_accepted_date', 'offer_salary', 'offer_salary_symbol', 'offer_bonus_commission', 'offer_bonus_commission_symbol','start_date')    
            ->where('id', $c_job_id)->first();
            if ($response) {
                return sendDataHelper('List.', $response, ok());
            } else {
                return sendError('Something went wrong', [], error());
            }
            
        } catch (\Throwable $th) {
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }
    }
}