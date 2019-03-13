<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class proJobApplication extends Model
{
    //

    public $timestamps = false;
  	protected $fillable = ['user_id', 'pro_job_id','created_at', 'updated_at'];
}
