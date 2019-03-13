<?php

namespace App\Models;

use Config;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Cviebrock\EloquentSluggable\Sluggable;

class User extends Authenticatable
{
    public $timestamps = false;
    use Notifiable, HasApiTokens;
    use Sluggable;

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'full_name'
            ]
        ];
    }
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['role_id', 'full_name', 'slug','dob',
        'email', 'password', 'about', 'registration_type', 'device_id', 'socket_id', 'image', 'facebook','instagram','youtube','verify_token',
        'forgot_password_token', 'is_email_verify','secondary_email', 'is_secondary_email_verify','is_first_login','authentication_token', 'login', 'notifications', 'online','is_dual_profile', 'last_seen', 'status', 'remember_token', 'device_type','is_profile_complete','is_mangopay_account',
        'created_at', 'updated_at'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    
}
