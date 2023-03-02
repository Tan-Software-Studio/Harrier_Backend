<?php

namespace App\Http\Controllers\API\Emp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Traits\EmployerTrait;
use Illuminate\Support\Facades\DB;

class EmpNotificationController extends Controller
{
    use  EmployerTrait;

    /*User Unread notifications*/
    public function unreadNotifications()
    {
        // $notifications = auth()->user()->unreadNotifications;

        $notifications = employer(auth()->user()->email)->unreadNotifications;
        $notifications->makeHidden(['created_at', 'updated_at']);
        $response = [
            'count' =>  $notifications->count(),
            'notifications' => $notifications
        ]; 
        return sendDataHelper('Notifications', $response, ok());
    }

    /*Delete notifications */
    public function deleteNotification(Request $request, $notification_id)
    {
        $request['notification_id'] = $notification_id;
        DB::beginTransaction();
        $request->validate([
            'notification_id' => 'required|exists:notifications,id'
        ]);

        try {
            
            $user = employer(auth()->user()->email);
            $notify = $user->notifications->find($notification_id);
            if(isset($notify))
            {   
                $notify->delete();
                DB::commit();
                return sendDataHelper('Notification removed success.', [], ok());
            }
            DB::commit();
            return sendErrorHelper('Data not found', [], error());
        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::info($th);
            return sendErrorHelper('Error', $th->getMessage(), error());
        }
    }

}
