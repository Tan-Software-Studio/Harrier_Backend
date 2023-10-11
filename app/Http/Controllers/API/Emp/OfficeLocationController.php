<?php

namespace App\Http\Controllers\API\Emp;

use App\Http\Controllers\Controller;
use App\Models\Emp\JobOfficeLocation;
use App\Models\Emp\JobWorkingSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class OfficeLocationController extends Controller
{
    /**All data show
     */
    public function index(Request $request, $tblname)
    {
        // dd($tblname);
        try {
            $emp_uuid = employer(auth()->user()->email)->uuid;
            // dd($emp_uuid);
            $results = DB::table($tblname)->where('emp_uuid', $emp_uuid)->get();
            // dd($results);
            return sendDataHelper('List.', $results, ok());
        } catch (\Throwable $th) {
            return sendErrorHelper('Error', $th->getMessage(), error());
        }
    }

    /**Single data store 
    */
    public function store(Request $request, $tblname)
    {   
        DB::beginTransaction();
        try {
            
            if (respValid($request)) { return respValid($request); }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/
            $request = (object) $request;
            $tblname;
            
            $message = null;
            $data['emp_uuid'] = employer(auth()->user()->email)->uuid;

            if('emp_office_locations' === $tblname)
            {
                $validation = Validator::make((array)$request, [
                    "location" => "required"
                ]);
                if ($validation && $validation->fails()) {
                    return sendError($validation->errors()->first(), [], 422);
                } 

                $message = 'Office location';
                $data['location'] = $request->location;
                
                if(DB::table($tblname)->where('location', $request->location)->where('emp_uuid', $data['emp_uuid'])->first())
                {
                    return sendError("The ".$request->location." has already been taken.", [], errorValid());
                }
                
            }elseif('emp_working_schedules' === $tblname)
            {
                $validation = Validator::make((array)$request, [
                    "schedule" => "required"
                ]);
                if ($validation && $validation->fails()) {
                    return sendError($validation->errors()->first(), [], 422);
                }

                $message = 'Working schedule';
                $data['schedule'] = $request->schedule;

                if(DB::table($tblname)->where('schedule', $request->schedule)->where('emp_uuid', $data['emp_uuid'])->first())
                {
                    return sendError("The ".$request->schedule." has already been taken.", [], errorValid());
                }
            }
            

            $response = DB::table($tblname)->insert($data);
                
            if ($response) {
                DB::commit();
                return sendDataHelper("$message added successfully", [], $code = 200);
            } else {
                return sendError("$message not added", [], 422);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, 400);
        }
    }

    /**Single selected data show 
     */
    public function show($tblname, $id)
    {
        try {
            $emp_uuid = employer(auth()->user()->email)->uuid;
            $results = DB::table($tblname)->where('id', $id)->where('emp_uuid', $emp_uuid)->first();
            return sendDataHelper('List.', $results, ok());
        } catch (\Throwable $th) {
            return sendErrorHelper('Error', $th->getMessage(), error());
        }
    }

     /**Single selected data update 
     */

    public function update(Request $request, $tblname)
    {
        DB::beginTransaction();
        try {
            if (respValid($request)) {
                return respValid($request);
            }

            $request = decryptData($request['response']); /* Dectrypt  **/
            $request = (object) $request;

            $validation = Validator::make((array)$request, [
                "id" => "required|exists:$tblname,id"
            ]);   
            if ($validation && $validation->fails()) {
                return sendError($validation->errors()->first(), [], 422);
            } 
            $id = $request->id;

            $error = self::validationTable($request, $tblname, $id);
            if($error){  return $error; }
            
            $data['id'] = $id;
            $data['emp_uuid'] = employer(auth()->user()->email)->uuid;
            $tbl = DB::table($tblname)->where('emp_uuid', $data['emp_uuid'])->find($id);

            $message = null;
            if($tbl)
            {
                if('emp_office_locations' === $tblname)
                {
                    $message = 'Office location';
                    $data['location'] = $request->location;
                }elseif('emp_working_schedules' === $tblname)
                {
                    $message = 'Working schedule';
                    $data['schedule'] = $request->schedule;
                }

                $result = DB::table($tblname)->where('id', $id)->update($data);
                if ($result) {
                    DB::commit();
                }
                return sendDataHelper("$message updated successfully", [], $code = 200);
            } else {
                return sendError("$message not updated", [], 422);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, 400);
        }
    }

    /**
     * Remove
     */
    public function destroy(Request $request, $tblname)
    {
        DB::beginTransaction();
        try {
            if (respValid($request)) {
                return respValid($request);
            }

            $request = decryptData($request['response']); /* Dectrypt  **/
            $request = (object) $request;
            
            $validation = Validator::make((array)$request, [
                "id" => "required|exists:$tblname,id"
            ]);   
            if ($validation && $validation->fails()) {
                return sendError($validation->errors()->first(), [], 422);
            } 
            $id = $request->id;
            $message = null;
            if('emp_office_locations' === $tblname)
            {
                $message = 'Office location';
                if(JobOfficeLocation::where('office_location_id', $id)->first())
                {
                    return sendError("$message not deleted. because Job created already taken this office location used.", [], 422);
                }
            }elseif('emp_working_schedules' === $tblname)
            {
                $message = 'Working schedule';
                if(JobWorkingSchedule::where('working_schedule_id', $id)->first())
                {
                    return sendError("$message not deleted. because Job created already taken this work schedule used.", [], 422);
                }
            }

            $result = DB::table($tblname)->where('id', $id)->where('emp_uuid', employer(auth()->user()->email)->uuid)
            ->delete();
            
            if($result)
            {
                DB::commit();
                return sendDataHelper("$message deleted successfully", [], $code = 200);
            } else {
                return sendError("$message not deleted", [], 422);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, 400);
        }
    }

    /**
     * Remove
     */
    public function statusActiveInactive(Request $request, $tblname)
    {
        DB::beginTransaction();
        try {
            if (respValid($request)) {
                return respValid($request);
            }

            $request = decryptData($request['response']); /* Dectrypt  **/
            $request = (object) $request;
            
            $validation = Validator::make((array)$request, [
                "id" => "required|exists:$tblname,id"
            ]);   
            if ($validation && $validation->fails()) {
                return sendError($validation->errors()->first(), [], 422);
            } 
            $id = $request->id;

            $message = null;
            $result = DB::table($tblname)->find($id);
            if($result)
            {   
                if($result->status == active())
                {
                    $status = inactive();
                }else{
                    $status = active();
                }

                if('emp_office_locations' === $tblname)
                {
                    $message = 'Office location';
                }elseif('emp_working_schedules' === $tblname)
                {
                    $message = 'Working schedule';
                }

                DB::table($tblname)->where('id', $id)->update(['status' => $status]);
                DB::commit();
                $result = DB::table($tblname)->find($id);
                return sendDataHelper("$message status updated successfully", $result, $code = 200);
            } else {
                return sendError("$message not updated", [], 422);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, 400);
        }
    }

    public function validationTable($request, $tblname, $id = null)
    {
        $validation = null;
        
        if('emp_office_locations' === $tblname)
        {
            $validation = Validator::make((array)$request, [
                "location" => "required|unique:$tblname,location,".$id
            ]);
            
        }elseif('emp_working_schedules' === $tblname)
        {
            $validation = Validator::make((array)$request, [
                "schedule" => "required|unique:$tblname,schedule,".$id
            ]);
        }

        if ($validation && $validation->fails()) {
            return sendError($validation->errors()->first(), [], 422);
        }
    }

    public function validationStatus($request)
    {
        $validation = Validator::make((array)$request, [
            "status" => "required|".Rule::in([1, 0]),
        ]);

        if ($validation->fails()) {
            return sendError($validation->errors()->first(), [], 422);
        }
    }
}
