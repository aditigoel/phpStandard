<?php

namespace App;

use Config;
use App\Models\BlockedUser;
use App\Models\Notification;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    public $timestamps = false;
    use Notifiable, HasApiTokens;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     protected $fillable = ['role_id', 'name', 'slug',
         'email','secondary_email','password', 'image', 'verify_token',
        'forgot_password_token', 'auth_token', 'status', 'remember_token',
        'email_notification', 'push_notification', 'lat', 'lng', 'device_type',
        'device_id', 'social_id', 'signup_type','socket_id','deleted_at', 'created_at', 'updated_at','mobile_no','otp'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function AauthAcessToken()
    {
        return $this->hasMany('\App\OauthAccessToken');
    }

    public function role()
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }
    

    // path of documet
    public function getImageAttribute($value)
    {   
       $result = substr($value, 0, 4);
      
       if($result == 'http')  # if image from facebook
       {
         return $value;
       }

        $server_url = Config::get('variable.SERVER_URL');
        if (!empty($value) && file_exists(storage_path() . '/app/public/user/thumb/' . $value)) {
            return $server_url . '/storage/user/thumb/' . $value;
        } else {
            return $server_url . '/images/user-default.png';
        }
    }

    
     # function for relation with notification model
    public function un_read_noti() {
        return $this->hasMany(Notification::class,'receiver_id','id')->where('is_read',0)->where('status',1);
    }

    # my blocks
    public function blockMyusers()
    {
        return $this->hasMany(BlockedUser::class, 'blocked_to', 'id')->where('blocked_by',Auth::user()->id);
    }

    # blocks tp me 
    public function blockMeusers()
    {
        return $this->hasMany(BlockedUser::class, 'blocked_by', 'id')->where('blocked_to',Auth::user()->id);
    }


    
}
