<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserSkill extends Model
{
    //

      public $timestamps = false;
      protected $fillable = ['user_id', 'skill_id','created_at', 'updated_at'];
}
