<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProJobInvite extends Model {

    public $timestamps = false;
    protected $fillable = ['pro_job_id', 'user_id', 'status', 'created_at', 'updated_at'];
    
    

}
