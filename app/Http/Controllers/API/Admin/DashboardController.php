<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            $data = [
               'employers_count' =>  DB::table('employers')->whereNull('deleted_at')->count(),
               'candidates_count' =>  DB::table('candidates')->whereNull('deleted_at')->count(),
               'guests_count' =>  DB::table('users')->where('role', roleGuest())->count(),
               'today_employer_register_count' =>  DB::table('employers')->whereDate('created_at', \Carbon\Carbon::today())->count(),
               'today_guests_request_count' =>  DB::table('users')->whereDate('updated_at', \Carbon\Carbon::today())->where('role', roleGuest())->where("is_request", active())->where("status", inactive())->count(),
               'today_candidate_profile_count' =>  DB::table('candidates')->whereDate('created_at', \Carbon\Carbon::today())->count()
            ];
            return sendDataHelper("List", $data, ok());
        } catch (\Throwable $th) {
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }
    }
}
