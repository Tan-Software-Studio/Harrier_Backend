<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\ContactUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ContactUsController extends Controller
{
    public function send(Request $request)
    {
        // return $adminEmail = config('mail.contact_mail.address');
        DB::beginTransaction();
        try {
            if(respValid($request)) { return respValid($request); }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/
            
            $data = Validator::make($request, [
                'name' => 'required',
                'email' => 'required|email',
                'phone' => 'required',
                'subject' => 'required',
                'message' => 'required',
            ]);

            if ($data->fails()) {
                return sendError($data->errors()->first(), [], errorValid());
            }else{
                $request = (object) $request;
                $input['name'] = $request->name;
                $input['email'] = $request->email;
                $input['phone'] = $request->phone;
                $input['subject'] = $request->subject;
                $input['message'] = $request->message;
                $input['created_at'] = now();
                $input['updated_at'] = now();

                $response = ContactUs::create($input);
                if ($response) {
                    DB::commit();
                    return sendDataHelper('Thank you, We will touch shortly.', $response, ok());
                } else {
                    DB::rollBack();
                    return sendError('Something went wrong', [], error());
                }
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }
    }

    public function index(Request $request)
    {
        try {
            $response = ContactUs::orderBy('id', 'Desc')->get();
            return sendDataHelper("List", $response, ok());
        } catch (\Throwable $th) {
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }
    }

    public function unreadIndex(Request $request)
    {
        try {
            $response = ContactUs::where('is_read', '0')->orderBy('id', 'Desc')->get();
            return sendDataHelper("List", $response, ok());
        } catch (\Throwable $th) {
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }
    }

    

    /* Show contact us list */
    public function show($id)
    {
        try {
            DB::table('contact_us')->where('id', $id)->update(['is_read' => '1']);
            $results = DB::table('contact_us')->where('id', $id)->first();
            return sendDataHelper('List.', $results, ok());
        } catch (\Throwable $th) {
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
            $result = DB::table('contact_us')->where('id', $id)->delete();
            if($result)
            {
                DB::commit();
                return sendDataHelper("Contact us deleted successfully", [], $code = 200);
            } else {
                return sendError("Contact us not deleted", [], 422);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, 400);
        }
    }
}