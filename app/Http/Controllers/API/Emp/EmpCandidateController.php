<?php

namespace App\Http\Controllers\API\Emp;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Emp\EmpCandidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EmpCandidateController extends Controller
{
    public function shortListAddREmove(Request $request)
    {
        DB::beginTransaction();
        try {
            if(respValid($request)) { return respValid($request); }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/
           
            $data = Validator::make($request, [
                'c_uuid' => 'required|exists:candidates,uuid',
            ],[
               'c_uuid.required' => 'Candidate details required (c_uuid).',
               'c_uuid.exists' => 'Candidate not valid.',
            ]);

            if ($data->fails()) {
                return sendError($data->errors()->first(), [], errorValid());
            }else{
                $request = (object) $request;
                $candidate_uuid = $request->c_uuid;
                $emp_uuid = employer(auth()->user()->email)->value('uuid');
                $emp_candidate = EmpCandidate::where('c_uuid', $candidate_uuid)->where('emp_uuid', $emp_uuid)->first();
                if($emp_candidate)
                {
                    $emp_candidate->delete();
                    DB::commit();
                    return sendDataHelper('Short list remove done.', [], ok());
                }else{
                    $in = new EmpCandidate();
                    $in->c_uuid = $candidate_uuid;
                    $in->emp_uuid = $emp_uuid;
                    $in->save();
                    DB::commit();
                    return sendDataHelper('Short list added done.', [], ok());
                }
                
               
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            // throw $th;
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }
    } 
}
