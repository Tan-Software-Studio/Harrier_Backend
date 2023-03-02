<?php

namespace App\Http\Controllers\API\Master;

use App\Http\Controllers\Controller;
use App\Http\Traits\CityTrait;
use App\Http\Traits\CountryTrait;
use App\Http\Traits\MstTrait;
use App\Http\Traits\StateTrait;
use App\Models\Candidate;
use App\Models\Master\MstRegion;
use App\Models\Unique\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Schema;

class MasterController extends Controller
{
    use CountryTrait, StateTrait, CityTrait, MstTrait;
    /**All data show
     */
    public function index()
    {
        try {
            $tblname = request()->segment(4);
            if($tblname == 'mst_regions')
            {
                // $results = DB::table($tblname)->get();
                $results = MstRegion::get();
            }elseif($tblname == 'candidate_job_statuses')
            {
                $results = DB::table('mst_candidate_job_statuses')->get();
            }else{
                
                $results = DB::table($tblname)->get();
            }
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
            
            $tblname = request()->segment(4);
            if($tblname == 'mst_currencies')
            {
                $error = self::currencyValidationTable($request, $tblname);
                if($error){  return $error; }
                $data['currency_name'] = $request->currency_name;
                $data['currency_code'] = $request->currency_code;
                $data['title'] = $request->currency_name;
            
            }elseif($tblname == 'mst_regions'){
                $validation = Validator::make((array)$request, [
                    "country_name" => "required|exists:countries,country_name"
                ],[
                    'Selected country regions not available'
                ]);
        
                if ($validation->fails()) {
                    return sendError($validation->errors()->first(), [], 422);
                }
                $country = Country::where('country_name', $request->country_name)->first();

                $region = MstRegion::where('country_id', $country->id)->where('state_name', $request->state_name)->first();
                if($region)
                {
                    return sendError($request->state_name. " region name already taken ".$country->name." country.", [], 422);
                }
                
                $data['country_id'] = $country->id;
                $data['state_name'] = $request->state_name;
            }else{
                $error = self::validationTable($request, $tblname);
                if($error){  return $error; }
                $data['title'] = $request->title;
            }

            $singular = Str::singular($tblname);
            $message = ucfirst(Str::replace('_', ' ', $singular));

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
            $tblname = request()->segment(4);
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

            $tblname = request()->segment(4);

            $list = $this->mstCheckTrue($tblname, $id);
            if($error = $this->permission($list))  {   return $error;  }
            
            $singular = Str::singular($tblname);
            $message = ucfirst(Str::replace('_', ' ', $singular));

            $tbl = DB::table($tblname)->find($id);
            if($tbl)
            {
                if($tblname == 'mst_currencies')
                {
                    $error = self::currencyValidationTable($request, $tblname, $tbl->id);
                    if($error){  return $error; }
                    $data['currency_name'] = $request->currency_name;
                    $data['currency_code'] = $request->currency_code;
                    $data['title'] = $request->currency_name;
                }elseif($tblname == 'mst_regions'){
                    $validation = Validator::make((array)$request, [
                        "country_name" => "required|exists:countries,country_name"
                    ],[
                        'Selected country regions not available'
                    ]);
            
                    if ($validation->fails()) {
                        return sendError($validation->errors()->first(), [], 422);
                    }
                    $country = Country::where('country_name', $request->country_name)->first();

                    $region = MstRegion::where('country_id', $country->id)->where('state_name', $request->state_name)->whereNotIn('id', [$id])->first();
                    if($region)
                    {
                        return sendError($request->state_name. " region name already taken ".$country->name." country.", [], 422);
                    }
                    
                    $data['country_id'] = $country->id;
                    $data['state_name'] = $request->state_name;
                }else{
                    $error = self::validationTable($request, $tblname, $id);
                    if($error){  return $error; }
                    $data['title'] = $request->title;
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
    public function destroy(Request $request, $prefix, $id)
    {
        DB::beginTransaction();
        try {
            $id =  $request->route('id');
            $tblname = $prefix;
            $singular = Str::singular($tblname);
            $message = ucfirst(Str::replace('_', ' ', $singular));
            
            $list = $this->mstCheckTrue($tblname, $id);
            if($error = $this->permission($list))  {   return $error;  }
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

    /* Permission desnied*/
    public function permission($list)   {   if($list) { return sendError("Permission denied.", [], 422);    }   }

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
            $tblname = $prefix;

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

    public function currencyValidationTable($request, $tblname, $id = null)
    {
        $validation = Validator::make((array)$request, [
            "currency_code" => "required|unique:$tblname,currency_code,".$id,
            "currency_name" => "required|unique:$tblname,currency_name,".$id,
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

    /**All data show without deleted
     */
    public function masterList(Request $request, $prefix)
    {   
        $data = [];
        try {
            $singular = Str::singular($prefix);
            $message = ucfirst(Str::replace('_', ' ', $singular));
            if($prefix == 'mst_currencies')
            {
               $response = self::mstCurrencyFun();
            }elseif($prefix == 'mst_countries'){
                $response = self::mstCountriesFun();
            }elseif($prefix == 'mst_desired_countries'){
                $response = self::mstDesiredCountriesFun();
            }elseif($prefix == 'mst_regions'){
                $response = self::statesList($request, null);
            }
            else{
                $response = DB::table($prefix)->whereNull('deleted_at')->get(['id', 'title']);
            }
            return sendDataHelper("$message table details", $response, $code = 200);

        } catch (\Throwable $th) {
            return sendErrorHelper('Error', $th->getMessage(), error());
        }
    }

    public function masterRegionList(Request $request)
    {   
        $data = [];
        try {

            if (respValid($request)) { return respValid($request); }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/
            
            $validation = Validator::make((array)$request, [
                "country_id" => "required|exists:countries,id"
            ],[
                'Selected country regions not available'
            ]);
    
            if ($validation->fails()) {
                return sendError($validation->errors()->first(), [], 422);
            }

            return self::statesList($request, null);

        } catch (\Throwable $th) {
            return sendErrorHelper('Error', $th->getMessage(), error());
        }
    }

    /* Master tables list */
    public function masterTablesList(Request $request)
    {
        try {
            $data = [];
            $allTables = config('constants.master_tables');
            if(count($allTables))
            {   
                foreach($allTables as $tblname)
                {
                    if($tblname == 'mst_regions')
                    {
                        $data[$tblname] = DB::table($tblname)->whereNull('deleted_at')->get(['id', 'state_name as title']);
                    }else{
                        $data[$tblname] = DB::table($tblname)->whereNull('deleted_at')->get(['id', 'title']);
                    }
                }
            }
            $data['mst_countries'] = self::mstCountriesFun();
            $data['mst_desired_countries'] = self::mstDesiredCountriesFun();
            $data['mst_currencies'] = self::mstCurrencyFun();
            
            return sendDataHelper('Master tables list.', $data, $code = 200);
        } catch (\Throwable $th) {
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, 400);
        }
    }

    /* country list */
    public static function mstCountriesFun()
    {
        return DB::table('countries')->whereNotIn('country_name', [noContry()])->orderBy('country_name', 'Asc')->where('status', active())->whereNull('deleted_at')
            ->get(['id','sortname', 'country_name', 'currency_name', 'currency_code', 'symbol', 'phonecode']);
    }

    public static function mstDesiredCountriesFun()
    {   
        $no_country = DB::table('countries')->where('country_name', noContry())->get();
        $country = self::mstCountriesFun();
        return array_merge($no_country->toArray(), $country->toArray());
    }

    /* currecny list*/
    public static function mstCurrencyFun()
    {
        return DB::table('mst_currencies')->where('status', active())->whereNull('deleted_at')->orderBy('currency_code', 'Asc')->groupBy('currency_code')
        ->get(['id', 'currency_name', 'currency_code']);
    }
}
