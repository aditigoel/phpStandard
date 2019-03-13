<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;


class UserReport extends Model
{

    protected $fillable = ['reported_by','reported_to','reason','comment','created_at'];

    public $timestamps = false;


		public function reportedByUser()
	 {
			 return $this->belongsTo(User::class,'reported_by','id');
	 }

	 public function reportedToUser()
	{
			return $this->belongsTo(User::class,'reported_to','id');
	}

  

}
