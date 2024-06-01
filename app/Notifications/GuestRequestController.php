<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Employer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use App\Notifications\EmpolerWelcomeEmailNotification;
use App\Notifications\EmpolerWelcomeEmailNotificationToCS;
use Illuminate\Support\Facades\Validator;
use App\Notifications\LoginRequest;
use App\Notifications\RequestAccept;

class GuestRequestController extends Controller
{
    /* Guest request login  post*/
    public function requestLoginPost(Request $request)
    {
        return $this->request($request, roleGuest());
    }

    
    /* Employer request login post   -- ---------- -- - - ----------------------------- ------------------*/
    public function empRequestLoginPost(Request $request)
    {
        return $this->requestEmp($request, roleEmp());
    }

    
    /* Login request --                    ---              ----    ---------------------------------------*/
    public static function request($request, $role)
    {
        DB::beginTransaction();
        try {
            if(respValid($request)) { return respValid($request); }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/
            
            $data = Validator::make($request, [
                'email' => 'required|email',
            ]);

            if ($data->fails()) {
                return sendError($data->errors()->first(), [], errorValid());
            }else{
                if($in = User::where('role', $role)->where('email', @$request['email'])->first())
                {
                    if ($in->isLogin()) {
                        return sendError('The email has already been login request sent.', [], error());
                    }
                    
                    // if ($in->isRequest()) {
                    //     return sendError('The login request already sent.', [], error());
                    // }
                }else{
                    $data = Validator::make($request, [
                        'email' => 'required|unique:users,email',
                    ],[
                        'email.unique' => 'This email not valid for guest login'
                    ]);
        
                    if ($data->fails()) {
                        return sendError($data->errors()->first(), [], errorValid());
                    }

                    $in = new User;
                    $in->email = @$request['email'];
                    $in->role = $role;
                    if(@$request['name'])
                    {
                        $in->name = @$request['name'];    
                    }
                }
                $passwordGenerate =  Str::random(6);
                $in->is_request = login_requested();
                $in->is_login = false;
                $in->status = false;
                $in->password = bcrypt($passwordGenerate);
                $in->save(); 

                if($admin = adminTable())
                {
                    $list =  $in;
                    $list['password'] = $passwordGenerate;
                    $admin['email'] = env('CV_REQUEST_RECIEVE_MAIL');
                    $admin['new_email'] = @$request['email'];
                    $admin['role_login'] =  $role;
                    $admin->notify(new LoginRequest($in));
                }
                
                $response = [
                    'details' => $in
                ];
                if ($response) {
                    DB::commit();
                    return sendDataHelper('Thank you for contact us. We will email contact you shortly.', $response, ok());
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

    /* Login request --                    ---              ----    ---------------------------------------*/
    public static function requestEmp($request, $role, $url = null)
    {
        
        $req = $request;
        DB::beginTransaction();
        try {
            
            if(respValid($req)) { return respValid($req); }  /* response required validation */
            $request = decryptData($req['response']); /* Dectrypt  **/
            
            if($url === 'register')
            {   
                $data = Validator::make($request, [
                    'name' =>  'required|unique:employers,name',
                    'uk_address' =>  'required',
                    'hq_address' =>  'nullable',
                    'billing_address' =>  'required',
                    'contact_details' =>  'required|unique:employers,contact_details',
                    'email' => 'required|unique:users,email',
                ],[
                    'name.required' => 'Enter full legal name',
                    'name.unique' => 'Already Legal Name submitted. Please contact us Harrier!',
                    'uk_address.required' => 'Enter UK address',
                    'billing_address.required' => 'Enter billing address',
                    'uk_address.required' => 'Enter UK address',
                    'contact_details.required' =>  'Enter point of contact for Invoices details.',
                    'contact_details.unique' =>  'Point of Contact already submitted. Please contact us Harrier!',
                    'email.required' => 'Enter super-user email address',
                    'email.unique' => 'Already Super-User email address submitted. Please contact us Harrier!',
                    
                ]);
                if ($data->fails()) {
                    return sendError($data->errors()->first(), [], errorValid());
                }

                if($url === 'register')
                { 
                    $data = Validator::make($req->all(), [
                        'logo' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:10048',
                    ]);
                    if ($data->fails()) {   return sendError($data->errors()->first(), [], errorValid()); }                   
                }

                $data = Validator::make($request, [
                    'url' =>  'required|unique:employers,url',
                    'is_terms_and_conditions'  =>  'required',
                ],[
                    'url.required' => 'Enter url',
                    'url.unique' =>  'Url already submitted. Please contact us Harrier!',
                ]);
            }else{
                $data = Validator::make($request, [
                    'name' =>  'required|unique:employers,name',
                    'email' => 'required|unique:users,email',
                ],[
                    'name.unique' => 'Already Legal Name submitted. Please contact us Harrier!',
                    'email.unique' => 'Already Super-User email address submitted. Please contact us Harrier!',
                ]);
            }

            if ($data->fails()) {
                return sendError($data->errors()->first(), [], errorValid());
            }else{
                $passwordGenerate =  Str::random(6);
                
                
                
                $request = (object) $request;
                $actual_url = @$request->url;
                if(@$request->url)
                {
                    $actual_url = actual_url($request->url);
                    $actual_url = Str::of($actual_url)->rtrim('/');
                }
                $check_url['url'] = $actual_url;
                $check_url['is_terms_and_conditions'] = @$request->is_terms_and_conditions; 
                $data = Validator::make($check_url, [
                    'url' =>  'required|unique:employers,url',
                    'is_terms_and_conditions'  =>  'required',
                ],[
                    'url.required' => 'Enter super-user email address',
                    'url.unique' =>  'Url already submitted. Please contact us Harrier!',
                ]);

                if ($data->fails()) {
                    return sendError($data->errors()->first(), [], errorValid());
                }

               
                
                $in = new User;
                $in->email = @$request->email;
                $in->role = roleEmp();
                $in->is_request = inactive();
                $in->is_login = false;
                $in->status = false;
                $in->password = bcrypt($passwordGenerate);
                $in->save();
                if(!Employer::where('email', $request->email)->first())
                {
                    $emp = new Employer;
                    $emp->status = false;
                    $emp->uuid = Str::uuid()->toString();
                    $emp->email = @$request->email;
                    $emp->name = @$request->name;
                    $emp->email_verified_at = now();
                    if($url === 'register')
                    {
                        $emp->uk_address = (@$request->uk_address ? $request->uk_address  : null);
                        $emp->hq_address = @$request->hq_address ?? null;
                        $emp->billing_address = @$request->billing_address ?? null;
                        $emp->contact_details = @$request->contact_details ?? null;
                        $emp->url = $actual_url ?? null;
                        $emp->is_terms_and_conditions = @$request->contact_details ?? null;
                        
                        
                        if($req->hasFile('logo'))
                        {   
                            $emp->logo = uploadFile($req->logo, 'uploads/logo') ?? null;
                        }
                    }
                    
                    $emp->save(); 
                    $email = @$request->email;
                    $name =@$request->name;
                    $emp->notify(new EmpolerWelcomeEmailNotification($email)); 
                    $emp->notify(new EmpolerWelcomeEmailNotificationToCS($name)); 
                }

                // if(auth()->check() && !canADMIN())
                // {
                //     if($admin = adminTable())
                //     {
                //         return $admin->email;
                //         $admin->notify(new LoginRequest($in));
                //     }
                // }else{
                //     $list =  $in;
                //     $list['password'] = $passwordGenerate;
                //     $list['role'] = 'emp';

                //     $in->notify(new RequestAccept($in));
                // }

                if($admin = adminTable())
                {
                    $admin['email'] = env('MAIL_FROM_ADDRESS');                    
                    $admin->notify(new LoginRequest($in));
                }
                
                $response = [
                    'details' => $in
                ];
                if ($response) {
                    DB::commit();
                    if($role == roleAdmin())
                    {
                        return sendDataHelper('List Added.', $response, ok());
                    }else{
                        return sendDataHelper('Thank you for choose our '.project('app_name').'. We will contact you shortly.', $response, ok());
                    }
                } else {
                    return sendError('Something went wrong', [], error());
                }
            }
        }catch(\Swift_TransportException $transportExp){
            $bug = $transportExp->getMessage();
            DB::rollBack();
            return sendErrorHelper('Invalid email', $bug, error());
        } catch (\Throwable $th) {
            DB::rollBack();
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }
    }

    
    /* Guest list */
    public function guestList(Request $request)
    {   
        $req = $request;
        try {
            if(respValid($request)) { return respValid($request); }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/
            $request = (object) $request;
            $query = User::query();
            $query->where('role', roleGuest());
            $query->orderBy('created_at', 'Desc');
            if($s = $req->input('search'))
            {
                $query->where('name', 'LIKE', '%'. $s. '%')
                ->orWhere('email', 'LIKE', '%' . $s . '%')
                ->orWhere('created_at', 'LIKE', '%' . $s . '%')
                ->orWhere('updated_at', 'LIKE', '%' . $s . '%');
            }
            
            if($req->input('status') != null)
            {
                $query->where('status', $req->status);
            }

            // $response['data'] = $query->get();
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

    
}