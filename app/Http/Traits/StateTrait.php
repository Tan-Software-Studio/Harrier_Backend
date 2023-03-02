<?php

namespace App\Http\Traits;

use App\Models\Master\MstRegion;
use App\Models\Unique\State;

trait StateTrait {
    
    public function statesList($request, $role = null) /* States list */
    {   
        try {
            $request = (object) $request;

            $query = MstRegion::query();
            switch ($role) {
                case roleAdmin():
                    if($status = $request->status)
                    {
                        $query->where('status', $status);
                    }
                    $query->select('id', 'state_name', 'status', 'country_id', 'updated_at');
                    break;
                
                default:
                    $query->select('id', 'state_name', 'country_id');
                    break;
            }
            if($state_id = @$request->state_id)
            {
                $query->where('id', '=', $state_id);
            }
            if($country_id = @$request->country_id)
            {
                $query->where('country_id', $country_id);
            }
            if($s = @$request->search) {  return 12;  $query->whereRaw("state_name LIKE '%". $s. "%'"); }
            
            if(@$request->paginate)
            {
                $list = $query->paginate(
                    $perPage = 10, $columns = ['*'], $pageName = 'page'
                );
            }else{
                $list = $query->get();
                $list->makeHidden(['country_list']);
            }

            $response = [
                'list' => $list
            ];
            return sendDataHelper("Regions table details", $response, ok());
            
        } catch (\Throwable $th) {
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }
    }
}