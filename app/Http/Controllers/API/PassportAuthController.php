<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PassportAuthController extends Controller
{
    // date('d-m-Y H:i:s', strtotime("-2 minutes"));
    // date('d-m-Y H:i:s', strtotime("+2 minutes"));
    // date('d-m-Y H:i:s', strtotime("+24 hours"));
    // return date('d-m-Y H:i:s', strtotime(now()) );
    /**
     * Login Admin
     */
    public function login(Request $request)
    {
        try {
            if(respValid($request)) { return respValid($request); }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/
            $data = Validator::make($request, [
                'email' => 'required|email|exists:users,email',
                'password' => 'required'
            ]);

            if ($data->fails()) {
                return sendError($data->errors()->first(), [], errorValid());
            }else{
                $request = (object) $request;        
                // $passwordGenerate  = Str::random(6);
                
                $credential = [
                    'email' => $request->email,
                    'password' => $request->password
                ];
                
                if (auth()->attempt($credential)) {
                        
                    if(auth()->user()->isAdmin())
                    {
                        // auth()->user()->tokens->each(function($token, $key) {
                        //     $token->delete();
                        // });
                        $token = auth()->user()->createToken('Admin Token',  [roleAdmin()])->accessToken;
                        
                        $response = [
                            'access_token' => $token
                        ];
                        return sendDataHelper('Login Sucess.', $response, ok());
                    }
                }
                return sendError('These credentials do not match our records.', [], error());
            }
        } catch (\Throwable $th) {
            // throw $th;
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }
    }

    /*  Guest Login Candidate  */
    public function guestLogin(Request $request)
    {
       return $this->loginUser($request, roleGuest());
    }

    /*  Employer Login Candidate  */
    public function empLogin(Request $request)
    {
       return $this->loginUser($request, roleEmp());
    }

    /** Login */
    public static function loginUser($request, $role)
    {
        try {
            if(respValid($request)) { return respValid($request); }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/
            $data = Validator::make($request, [
                'email' => 'required|exists:users,email|'.Rule::exists('users')->where(function ($query, $role) {
                    return $query->where('role', $role);
                }),
                'password' => 'required'
            ],[
               'email.required' => 'Enter email address',
               'email.exists' => 'Inavalid email address.',
               'password.required' => 'Enter password' 
            ]);

            if ($data->fails()) {
                return sendError($data->errors()->first(), [], errorValid());
            }else{
                $request = (object) $request;        
                $passwordGenerate  = Str::random(6);
                
                $credential = [
                    'email' => $request->email,
                    'password' => $request->password
                ];
                
                $userLog = User::where('email', $request->email)->where('role', $role)->first();
                if($userLog)
                {
                    if($role === roleGuest() && $userLog->is_request != login_active())
                    {
                        return sendError('Account not activated. Please request guest access.', [], error());       
                    }elseif ($role === roleEmp() && $userLog->status != active()) {
                        return sendError('Your Account has been deactivated. Contact to Harrier.', [], error());   
                    }
                }
                if ($userLog && auth()->attempt($credential)) {
                    if(auth()->user()->role == $role)
                    {
                        if($role === roleEmp()) {   $actEmp = auth()->user()->isActive();   }
                        
                        $user = User::where('id',auth()->user()->id)->first();
                        if($user)
                        {
                            if(!$user->expire_datetime)
                            {
                                // $user->expire_datetime = strtotime("+1 minutes");
                                $user->expire_datetime = strtotime("+24 hours");
                            }
                            $user->is_login = true;
                            $user->save();
                        }
                        $is_pe = null;
                        if (auth()->user()->isEmp() && employer(auth()->user()->email)->is_pe == true) {
                            $is_pe = rolePE();
                        }
                        // auth()->user()->tokens->each(function($token, $key) {
                        //     $token->delete();
                        // });
                        $token = auth()->user()->createToken($role.' Token',  [$role, $is_pe])->accessToken;

                        $response = [
                            'access_token' => $token
                        ];
                        return sendDataHelper('Login success.', $response, ok());
                    }
                    auth()->logout();
                }
                return sendError('These credentials do not match our records.', [], error());
            }
        } catch (\Throwable $th) {
            // throw $th;
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }
    }

    /* Emp register request --                    ---              ----    ---------------------------------------*/
    public static function empRegister(Request $request)
    {
        return GuestRequestController::requestEmp($request, roleEmp(), $url = 'register');
    }

    public function userInfo() 
    {
        $response = auth()->user();
        return sendDataHelper('Login success.', $response, ok());
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return sendDataHelper('You have successfully logout.', [], ok());
    }
}