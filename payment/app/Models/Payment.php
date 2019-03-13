<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Payment extends Authenticatable
{
    public $timestamps = false;
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = ['payer_id', 'payee_id', 'total_amount', 'amount_debited', 'transaction_id', 'amount_paid', 'commission_amount', 'payment_date', 'payment_status', 'status', 'created_at', 'updated_at'];

}
