<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Invoice extends Authenticatable
{
    public $timestamps = false;
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = ['payer_id', 'payee_id', 'total_amount', 'paid_amount', 'commission_amount', 'date_of_payment', 'discount', 'charge', 'discount_charge', 'payment_type', 'payment_method', 'invitation_id', 'status', 'created_at', 'updated_at'];

}
