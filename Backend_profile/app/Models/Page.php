<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{

    protected $fillable = ['meta_key', 'version', 'name', 'meta_value', 'status', 'created_at'];

    public $timestamps = false;

    /**
     * Always capitalize the meta_key when we retrieve it
     */
    public function getMetaKeyAttribute($value)
    {
        return $value;
    }
}