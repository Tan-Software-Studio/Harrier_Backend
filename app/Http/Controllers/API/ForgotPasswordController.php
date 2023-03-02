<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\ForgotPasswordLink;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ForgotPasswordController extends Controller
{
    public function sentForgetPasswordLink(Request $request, $role)
    { 
        return $this->createForgetPasswordLink($request, role($role));
    }
    
    /** */
    public function emailTokenVerification(Request $request, $role)
    {
        return $this->newPasswordUpdate($request, role($role));
    }

    /* Forgot Password link sent to email */
    public static function createForgetPasswordLink($request, $role)
    {
        DB::beginTransaction();
        try {
            if(respValid($request)) { return respValid($request); }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/

            $data = Validator::make($request, [
                'email' => 'required|exists:users,email|'.Rule::exists('users')->where(function ($query, $role) {
                    return $query->where('role', $role);
                }),
                'url' => 'required'
            ],[
                'email.required' => 'Enter email address',
                'email.exists' => 'Inavalid email address.',
                'url.required' => 'Url required'
            ]);

            if ($data->fails()) {
                return sendError($data->errors()->first(), [], errorValid());
            }else{
                $request = (object) $request;
                $passwordGenerate  = Str::random(6);
                if($in = User::where('role', $role)->where('email', @$request->email)->first())
                {
                    $token = Str::random(64);
                    DB::table('password_resets')->where(['email'=> $request->email])->delete();
                    DB::table('password_resets')->insert([
                        'email' => $request->email,
                        'token' => $token, 
                        'created_at' => Carbon::now()
                    ]);

                    if(roleAdmin() == $role)
                    {
                        $url = env('APP_API_URL');
                        $url = @$request->url;    
                        $url = $url.'/authentication/reset-password?token='.$token.'&email='.$request->email.'&utype='.role_name($role);
                    }elseif(roleEmp() == $role){
                        $url = env('FRONT_APP_URL');
                        $url = @$request->url;
                        $url = $url.'/authentication/reset-password#'.$token.'#'.$request->email.'#'.role_name($role);
                    }else{
                        $url = env('FRONT_APP_URL');
                        $url = @$request->url;
                        $url = $url.'/authentication/reset-password#'.$token.'#'.$request->email.'#'.role_name($role);
                    }
                    $data = [
                        'token' => $url,
                        'email' => $in->email
                    ];
                    $in->notify(new ForgotPasswordLink($data));
                    DB::commit();
                    return sendDataHelper('We have emailed your password reset link!.', [], ok());
                }else{
                    return sendError('Invalid Email address.', [], error());
                }
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            // throw $th;
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }
    }

    /* Email token verify and New Password update */
    public static function newPasswordUpdate($request, $role)
    {
        DB::beginTransaction();
        try {
            if(respValid($request)) { return respValid($request); }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/

            $data = Validator::make($request, [
                'email' => 'required|exists:users,email|'.Rule::exists('users')->where(function ($query, $role) {
                    return $query->where('role', $role);
                }),
                'password' => 'required|string|min:6|confirmed',
                'password_confirmation' => 'required',
                'token' => 'required'
            ]);

            if ($data->fails()) {
                return sendError($data->errors()->first(), [], errorValid());
            }else{
                $request = (object) $request;
                $updatePassword = DB::table('password_resets')
                            ->where([
                            'email' => $request->email, 
                            'token' => $request->token
                            ])
                            ->first();

                if(!$updatePassword){
                    return sendError('Invalid token!', [], error());
                }
                
                $user = User::where('role', $role)->where('email', $request->email)
                    ->update(['password' => Hash::make($request->password)]);

                DB::table('password_resets')->where(['email'=> $request->email])->delete();
                if($user)
                {
                    DB::commit();
                    return sendDataHelper('Your password has been changed.', [], ok());
                }else{
                    return sendError('Something went wrong.', [], error());
                }
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            // throw $th;
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }
    }
}
