<?php

namespace App\Http\Controllers\API\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CountryController extends Controller
{
    /**All data show
     */
    public function index()
    {
        try {
            $tblname = 'countries';
            $results = DB::table($tblname)->get();
            return sendDataHelper('List.', $results, ok());
        } catch (\Throwable $th) {
            return sendErrorHelper('Error', $th->getMessage(), error());
        }
    }

    /**Single data store 
    */
    public function store(Request $request)
    {   
        DB::beginTransaction();
        try {
            if (respValid($request)) { return respValid($request); }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/
            $request = (object) $request;
            
            $tblname = 'countries';
            $error = self::validationTable($request, $tblname);
            if($error){  return $error; }

            $singular = Str::singular($tblname);
            $message = ucfirst(Str::replace('_', ' ', $singular));

            $data['title'] = $request->title;
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
    public function show($prefix, $id)
    {
        try {
            $tblname = 'countries';
            $results = DB::table($tblname)->where('id', $id)->first();
            return sendDataHelper('List.', $results, ok());
        } catch (\Throwable $th) {
            return sendErrorHelper('Error', $th->getMessage(), error());
        }
    }

     /**Single selected data update 
     */

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $id =  $request->route('id');
            if (respValid($request)) {
                return respValid($request);
            }

            $request = decryptData($request['response']); /* Dectrypt  **/
            $request = (object) $request;

            $tblname = 'countries';
            $singular = Str::singular($tblname);
            $message = ucfirst(Str::replace('_', ' ', $singular));

            $tbl = DB::table($tblname)->find($id);
            if($tbl)
            {
                $error = self::validationTable($request, $tblname, $tbl->id);
                if($error){  return $error; }
                
                $data['title'] = $request->title;
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
    public function destroy(Request $request, $prefix, $id)
    {
        DB::beginTransaction();
        try {
            $id =  $request->route('id');
            $tblname = 'countries';
            $singular = Str::singular($tblname);
            $message = ucfirst(Str::replace('_', ' ', $singular));
            $result = DB::table($tblname)->where('id', $id)->delete();
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
    public function statusChange(Request $request, $prefix, $id)
    {
        DB::beginTransaction();
        try {
            if (respValid($request)) {
                return respValid($request);
            }
            $id =  $request->route('id'); 
            $tblname = 'countries';

            $request = decryptData($request['response']); /* Dectrypt  **/
            $request = (object) $request;

            
            $error = self::validationStatus($request);
            if($error){  return $error; }

            $singular = Str::singular($tblname);
            $message = ucfirst(Str::replace('_', ' ', $singular));
            $result = DB::table($tblname)->find($id);
            if($result)
            {   
                if($request->status == 1)
                {
                    $status = date('Y-m-d');
                }else{
                    $status = null;
                }
                DB::table($tblname)->where('id', $id)->update(['deleted_at' => $status]);
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
        $validation = Validator::make((array)$request, [
            "title" => "required|unique:$tblname,title,".$id
        ]);

        if ($validation->fails()) {
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
