<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Employer;
use App\Models\Job;
use App\Models\User;
use App\Notifications\InactiveNotification;
use App\Notifications\LoginRequest;
use Illuminate\Http\Request;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\DB;

class CronJobController extends Controller
{
    // date('d-m-Y H:i:s', strtotime("-2 minutes"));
    // date('d-m-Y H:i:s', strtotime("+2 minutes"));
    // date('d-m-Y H:i:s', strtotime("+24 hours"));
    // return date('d-m-Y H:i:s', strtotime(now()) );
    
    /* 24 hours after login expired, Check every hour */
    public function cronjob(Request $request)
    {
        // return date('Y-m-d H:i:s', 1666161023);
        $this->oneHourExpire();
        $this->inactiveCandidatesList();
    }

    public static function oneHourExpire()
    {
        $now = strtotime("now");
        $details = User::where('role', roleGuest())->where('is_request', login_active())->where('expire_datetime', '<', $now)->get();
        if(count($details) > 0)
        {
            foreach ($details as $key => $usr) 
            { 
                    // $usr->tokens->each(function($token, $key) {
                    //     $token->delete();
                    // });
                    $usr->is_request = login_expired();
                    $usr->is_login = inactive();
                    $usr->expire_datetime = null;
                    $usr->update();
            }
        }
    }

    public static function inactiveCandidatesList()
    {
        $threeMonthAfter = date('Y-m-d', strtotime("+3 months"));
        $monthAfter = date('Y-m-d', strtotime("-1 months"));
        
        $details = Candidate::select('id','uuid', 'email', 'updated_at')->whereNull('is_inactive_mail')->whereDate('updated_at', '<=', $threeMonthAfter)->get();
        if(count($details) > 0)
        {
            foreach ($details as $key => $usr) 
            { 
                $updated_cand = Candidate::select('id','uuid', 'email', 'updated_at')->whereNull('is_inactive_mail')->whereDate('updated_at', '>=', $monthAfter)->get();
                $updated_cand->is_inactive_mail = strtotime(date('Y-m-d'));
                $updated_cand->save();

                $data = [
                    'email' => $usr->email,
                    'uuid' => $usr->uuid,
                    'role' =>  roleGuest(),
                    'is_inactive_mail' =>  strtotime(date('Y-m-d'))
                ];
                $usr->notify(new InactiveNotification($data));
            }
        }
    }

    public function saveApiData(Request $request)
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'AccessToken' => 'key',
            // 'Authorization' => 'Bearer ',
        ];
        
        $client = new GuzzleClient([
            'headers' => $headers
        ]);
        
        $res = $client->request('POST', 'http://192.168.43:8000/api/v1/login', [
            'form_params' => [
                'email' => 'admin@123',
                'password' => 'Admin@123',
            ]
        ]);

        $result= $res->getBody();
        dd($result);
    }   

    /**expiredMail */
    public function expiredMail()
    {
       return $details = User::where('role', roleGuest())->whereNull('expire_datetime')->first();
        if($details)
        {

        }
    }
}
