<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
class ProJobFile extends Model {

    public $timestamps = false;
    protected $fillable = ['pro_job_id', 'file_name', 'name','type','created_at', 'updated_at'];
    
    
         
    
    
    
}
