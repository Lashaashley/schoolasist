<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
        protected $table = 'suppliers';

    // Allow mass assignment for these fields
    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'address',
        'profile',
        'invoice_no',
        'bank_name',
        'account_name',
        'account_number',
        'mpesa_paybill',
        'mpesa_till',
        'mpesa_phone'

        //'type', // optional: supply type
    ];

    public $timestamps = true;

    public function invoices()
    {
        return $this->hasMany(SupplierInvoice::class);
    }

    // A supplier can have many payments (through invoices)
    public function payments()
    {
        return $this->hasMany(SupplierPayment::class);
    }



}
