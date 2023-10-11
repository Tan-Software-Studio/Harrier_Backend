<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\API\GuestRequestController;
use App\Http\Controllers\Controller;
use App\Models\Employer;
use Illuminate\Http\Request;

class AdminEmployerController extends Controller
{
    /* Get employers list */
    public function employersList(Request $request)
    {   
        try {
            $query = Employer::query();
            $query->orderBy('created_at', 'Desc');
            $query->select('uuid', 'email', 'name','status', 'uk_address', 'hq_address', 'billing_address', 'contact_details', 'logo', 'url','created_at', 'updated_at');
            
            if($s = $request->input('search'))
            {
                $query->whereRaw("email LIKE '%". $s. "%'")
                ->orWhereRaw("name LIKE '%". $s . "%'");
            }

            if($status = $request->input('status'))
            {
                $query->where('status', $status);
            }

            
            
            
            // $response = $query->paginate(
            //     $perPage = 10, $columns = ['*'], $pageName = 'page'
            // ); 
            $response = $query->get();   
            $data = [
                'data' => $response
            ];    
            return sendDataHelper("List", $data, ok());
            
        } catch (\Throwable $th) {
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }
    }

    /* Get All employers list */
    public function allEmployersList(Request $request)
    {   
       
        try {
            $query = Employer::query();
            $query->orderBy('created_at', 'Desc');
            $query->select('uuid', 'email', 'name','status', 'uk_address', 'hq_address', 'billing_address', 'contact_details', 'logo', 'url','created_at', 'updated_at');
            
            if($s = $request->input('search'))
            {
                $query->whereRaw("email LIKE '%". $s. "%'")
                ->orWhereRaw("name LIKE '%". $s . "%'");
            }

            if($status = $request->input('status'))
            {
                $query->where('status', $status);
            }
            
            $response = $query->get();
            return sendDataHelper("List", $response, ok());
            
        } catch (\Throwable $th) {
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }
    }
    /* Emp register request --                    ---              ----    ---------------------------------------*/
    public static function empRegister(Request $request)
    {
        return GuestRequestController::requestEmp($request, roleAdmin(), $url = 'register');
    }
}
