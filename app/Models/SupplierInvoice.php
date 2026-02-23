<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'invoice_number',
        'total_amount',
        'amount_paid',
        'balance',
        'due_date',
        'status', // pending, approved, paid
    ];

    protected $attributes = [
        'amount_paid' => 0,
        'balance' => 0,
    ];

    // An invoice belongs to a supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // An invoice can have multiple payments
    public function payments()
    {
        return $this->hasMany(SupplierPayment::class, 'invoice_id');
    }
}