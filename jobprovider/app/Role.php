<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{

    public $timestamps = false;


    # function for relation with User model
    public function user() {
         return $this->belongsTo('App\User');
    }


    # function for get date time
    public function getCreatedAtAdminAttribute($value) {
        $value = date("m/d/Y h i A", $value);
        return $value;
    }


}
