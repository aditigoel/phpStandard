<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProJobSkill extends Model {

    public $timestamps = false;
    protected $fillable = ['pro_job_id', 'skill_id', 'created_at', 'updated_at'];
    
    

}
