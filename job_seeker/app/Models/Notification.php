<?php

namespace App\Models;

use App\User;
use Auth;
use Illuminate\Database\Eloquent\Model;
use DB;

class Notification extends Model {

    public $timestamps = false;
    protected $fillable = [
        'job_id', 'sender_id', 'receiver_id', 'type', 'is_read', 'status', 'created_at'
    ];

    # function for relation with post model
    public function post()
   {
       return $this->belongsTo(CommunityPost::class,'post_id','id');
   }



   # function for get date time
   public function getCreatedAtAttribute($value) {
       $value = date("m/d/Y h i A", $value);
       return $value;
   }


   # function for relation with user model for receiver id
    public function receiver() {
        return $this->belongsTo(User::class,'receiver_id','id');
    }
    # function for relation with user model for sender  id
    public function sender() {
        return $this->belongsTo(User::class,'sender_id','id')->select('id','fullname','image');
    }
    public static function getTypeAttribute($value) {
        if($value == 1)  
        {
          $value = Auth::user()->fullname.' '.'sent you a job invite';
          return $value;
        }
        if($value == 2)  
        {
          $value = Auth::user()->fullname.' '.'has confirmed you for a job.';
          return $value;
        }
        if($value == 3)  
        {
          $value =Auth::user()->fullname.' '.'has canceled the upcomimg job.';
          return $value;
        }
        if($value == 4)     
        {
          $value =Auth::user()->fullname.' '.'has withdrawn from an 
          upcoming job.';
          return $value;
        }
        if($value == 5)  
        {
          $value = Auth::user()->fullname.' '.'has confirmed a job request.You can make payment now';
          return $value;
        }
}
}
