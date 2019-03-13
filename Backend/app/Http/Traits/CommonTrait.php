<?php
namespace App\Http\Traits;

use Image;
use App\User;
use App\Models\AppJob;
use App\Models\Notification;
use Auth;
use DB;
trait CommonTrait
{
    public function imageDynamicName()
    {
        #Available alpha caracters
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $pin = mt_rand(1000000, 9999999)
            . $characters[rand(0, 5)];
        $string = str_shuffle($pin);
        return $string;
    }
    
    
    public function notifications($requested_data)
    {
        //$requested_data = $request->all(); 
        $requested_data['user_id']= Auth::user()->id;
        $notification = Notification::Create([
            'sender_id' => $requested_data['user_id'],
            'receiver_id' =>$requested_data['receiver_id'],
            'job_id' => $requested_data['job_id'],
            'type' =>$requested_data['type'],
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        if ($notification) {
            return true;
        } else {
            return false;
        }
    }
    
               
}
