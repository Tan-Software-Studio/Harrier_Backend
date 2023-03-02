<?php

namespace App\Http\Traits;
use App\Models\Unique\Country;

trait CountryTrait {
    
    public function countriesList($request, $role = null) /* Countries list */
    {   
        try {
            $query = Country::query();
            $query->orderBy('country_name', 'Asc');
            switch ($role) {
                case roleAdmin():
                    if($status = $request->input('status'))
                    {
                        $query->where('status', $status);
                    }
                    break;
                
                default:
                    $query->select('id', 'sortname', 'country_name', 'currency_name', 'currency_code', 'symbol', 'phonecode');
                    break;
            }
            if($country_id = $request->input('country_id'))
            {
                $query->where('id', '=', $country_id);
            }
            if($s = $request->input('search')) {    $query->whereRaw("country_name LIKE '%". $s. "%'"); }
            
            if($request->input('paginate'))
            {
                $list = $query->paginate(
                    $perPage = 10, $columns = ['*'], $pageName = 'page'
                );
            }else{
                $list = $query->get();
            }

            $response = [
                'list' => $list
            ];
            return sendDataHelper("List", $response, ok());
            
        } catch (\Throwable $th) {
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }
    }
}