<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions'; // explicitly specify table name

    protected $fillable = [
        'admno',
        'stream',
        'school_acc',
        'account_reference',
        'phone',
        'amount',
        'transaction_type',
        'checkout_request_id',
        'mpesa_receipt',
        'result_code',
        'result_desc',
        'response',
    ];
}