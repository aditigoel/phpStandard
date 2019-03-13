<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDeleteLog extends Model
{

    public $timestamps = false;
    protected $fillable = ['role_id', 'email', 'status', 'created_at', 'updated_at'];

}
