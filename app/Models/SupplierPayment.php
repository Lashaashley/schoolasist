<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'supplier_id',
        'amount_paid',
        'payment_method',
        'payment_reference',
        'payment_date',
        'recorded_by',
    ];

    // Payment belongs to an invoice
    public function invoice()
    {
        return $this->belongsTo(SupplierInvoice::class, 'invoice_id');
    }

    // Payment belongs to a supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}