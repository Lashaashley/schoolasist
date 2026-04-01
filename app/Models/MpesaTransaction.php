<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MpesaTransaction extends Model
{
    protected $table = 'mpesa_transactions';

    protected $fillable = [
        'admno',
        'phone',
        'amount',
        'checkout_request_id',
        'mpesa_receipt',
        'status'
    ];
}
