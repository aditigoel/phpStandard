<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
class ProJob extends Model
{

	use Sluggable;
	    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }
    protected $fillable = ['user_id', 'title','slug', 'description', 'start_date_time', 'end_date_time', 'status', 'created_at', 'updated_at'];

    public $timestamps = false;

    
}