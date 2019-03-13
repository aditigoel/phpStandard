<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class UserBankAccount extends Authenticatable
{
    public $timestamps = false;
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'bank_id', 'type', 'tag', 'addressline1', 'addressline2', 'city', 'region', 'postal_code', 'country', 'owner_name', 'iban', 'bic', 'account_number', 'aba', 'deposit_account_type', 'branch_code', 'institution_number', 'bank_name', 'sort_code', 'status', 'created_at', 'updated_at'];

}
