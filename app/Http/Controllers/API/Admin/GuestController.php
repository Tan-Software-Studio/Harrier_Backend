<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\User;
use App\Notifications\RequestAccept;
use Illuminate\Validation\Rule;
use App\Notifications\LoginRequest;

class GuestController extends Controller
{
    /** * Guest login credential recieve via email*/
    public function statusRequest(Request $request)
    {
        DB::beginTransaction();
        try {
            if(respValid($request)) { return respValid($request); }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/
           
            $data = Validator::make($request, [
                'email' => 'required|exists:users,email|'.Rule::exists('users')->where(function ($query, $role) {
                    return $query->where('role', $role);
                }),
                'is_request' => 'required|in:1,2,3'
            ]);

            if ($data->fails()) {
                return sendError($data->errors()->first(), [], errorValid());
            }else{
                $passwordGenerate  = Str::random(6);
                $request = (object) $request;
                if($in = User::where('role', roleGuest())->where('email', @$request->email)->first())
                {
                    $active = $in->is_request;
                    $in->is_request = $request->is_request;
                    $in->is_login = false;
                    $in->password = bcrypt($passwordGenerate);
                    $in->email_verified_at = now();
                    
                    if(@$request->is_request == login_active()) {   $in->status = active(); }

                    $in->save();  
                    if($active != login_active() && @$request->is_request == login_active())
                    {
                        $list =  $in;
                        $list['password'] = $passwordGenerate;
                        $list['role'] = 'guest';
                        $in->notify(new RequestAccept($in));
                    }
                    $in = User::find($in->id);
                    DB::commit();
                    return sendDataHelper('Guest status updated.', $in->toArray(), ok());
                }else{
                    return sendError('List not found.', [], error());
                }
            }
        }catch(\Swift_TransportException $transportExp){
            //$transportExp->getMessage();
            DB::rollBack();
            return sendErrorHelper('Invalid email', [], error());
        } catch (\Throwable $th) {
            DB::rollBack();
            return sendErrorHelper('Error', $th->getMessage(), error());
        }
    }

    /* Guest create */
    public static function guestCreate(Request $request)
    {
        DB::beginTransaction();
        try {
            if(respValid($request)) { return respValid($request); }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/
            
            $data = Validator::make($request, [
                'email' => 'required|email|unique:users,email',
                'name' => 'required',
            ]);

            if ($data->fails()) {
                return sendError($data->errors()->first(), [], errorValid());
            }else{
                $passwordGenerate = Str::random(6);
                $in = new User;
                $in->name = @$request['name'];       
                $in->email = @$request['email'];
                $in->role = roleGuest();
                $in->is_request = login_requested();
                $in->is_login = false;
                $in->status = true;
                $in->password = bcrypt( $passwordGenerate);
                $in->save(); 

                if($in->is_request == login_active())
                {
                    $list =  $in;
                    $list['password'] = $passwordGenerate;
                    $list['role'] = 'guest';                    
                    $in->notify(new RequestAccept($list));
                }
                
                $response = [
                    'details' => User::where('role', roleGuest())->find($in->id)
                ];
                if ($response) {
                    DB::commit();
                    return sendDataHelper('Guest created.', $response, ok());
                } else {
                    return sendError('Something went wrong', [], error());
                }
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }     
    }

    /** * Guest profle update*/
    public function guestProfileUpdate(Request $request)
    {
        $role = roleGuest();
        DB::beginTransaction();
        try {
            if(respValid($request)) { return respValid($request); }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/
           
            $data = Validator::make($request, [
                'email' => 'required|exists:users,email|'.Rule::exists('users')->where(function ($query, $role) {
                    return $query->where('role', $role);
                }),
                'name' => 'required|',
                'status' => 'required|in:1,0'
            ]);

            if ($data->fails()) {
                return sendError($data->errors()->first(), [], errorValid());
            }else{
                $passwordGenerate  = Str::random(6);
                $request = (object) $request;
                if($in = User::where('role', $role)->where('email', @$request->email)->first())
                {
                    if(@$request->new_email)
                    {
                        
                        if(User::where('role', $role)->where('email', @$request->new_email)->whereNotIn('id', [$in->id])->first())
                        {
                            return sendError('The email has already been taken.', [], error());
                        }   
                        $in->email = $request->new_email;
                    }
                    $in->name = $request->name;
                    $in->is_request = $request->is_request;
                    $in->save(); 
                    DB::commit();
                    return sendDataHelper('List updated.', $in->toArray(), ok());
                }else{
                    return sendError('List not found.', [], error());
                }
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return sendErrorHelper('Error', $th->getMessage(), error());
        }
    }

     /**
     * Remove
     */
    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $id =  $request->route('id');
            $result = User::where('role', roleGuest())->where('id', $id)->delete();
            if($result)
            {
                DB::commit();
                return sendDataHelper("Guest deleted successfully", [], $code = 200);
            } else {
                return sendError("Guest not deleted", [], 422);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, 400);
        }
    }
    
}
