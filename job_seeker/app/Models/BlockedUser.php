<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class BlockedUser extends Model
{
    protected $fillable = ['blocked_by', 'blocked_to', 'created_at'];

    public $timestamps = false;

    public function blockedFrom()
    {
        return $this->belongsTo(User::class, 'blocked_by', 'id');
    }

    public function blockedTo()
    {
        return $this->belongsTo(User::class, 'blocked_to', 'id');
    }

}
