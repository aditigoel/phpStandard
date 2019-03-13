<?php

namespace App\Models;

use Config;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class UserWallet extends Authenticatable
{
    public $timestamps = false;
    use Notifiable, HasApiTokens;

   
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'wallet_id', 'status','created_at', 'updated_at'];

       
}
