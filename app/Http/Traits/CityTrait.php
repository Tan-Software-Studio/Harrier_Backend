<?php

namespace App\Http\Traits;

use App\Models\Unique\City;

trait CityTrait {
    
    public function citiesList($request, $role = null) /* Cities list */
    {   
        try {
            $query = City::query();
            switch ($role) {
                case roleAdmin():
                    if($status = $request->input('status'))
                    {
                        $query->where('status', $status);
                    }
                    $query->select('id', 'city_name', 'status', 'state_id', 'updated_at');
                    break;
                
                default:
                    $query->select('id', 'city_name', 'state_id');
                    break;
            }
            if($city_id = $request->input('city_id'))
            {
                $query->where('id', '=', $city_id);
            }
            if($state_id = $request->input('state_id'))
            {
                $query->where('state_id', '=', $state_id);
            }
            if($s = $request->input('search')) {    $query->whereRaw("city_name LIKE '%". $s. "%'"); }
            
            if($request->input('paginate'))
            {
                $list = $query->paginate(
                    $perPage = 10, $columns = ['*'], $pageName = 'page'
                );
            }else{
                $list = $query->get();
                $list->makeHidden(['state_list', 'country_list']);
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