<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SupplierInvitation extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'supplier_invitations';

    // Mass assignable fields
    protected $fillable = [
        'supplier_id',
        'invoice_id',
        'category',
        'message',
        'expires_at',
        'responded', // boolean: 0 = pending, 1 = responded
    ];

    // Default values
    protected $attributes = [
        'responded' => false,
    ];

    // Cast fields to proper types
    protected $casts = [
        'expires_at' => 'datetime',
        'responded' => 'boolean',
    ];

    // --------------------
    // Relationships
    // --------------------

    /**
     * The supplier related to this invitation
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * The invoice related to this invitation
     */
    public function invoice()
    {
        return $this->belongsTo(SupplierInvoice::class);
    }

    // --------------------
    // Scopes
    // --------------------

    /**
     * Scope to get only pending invitations
     */
    public function scopePending($query)
    {
        return $query->where('responded', false);
    }

    /**
     * Scope to get only responded invitations
     */
    public function scopeResponded($query)
    {
        return $query->where('responded', true);
    }

    // --------------------
    // Accessors
    // --------------------

    /**
     * Check if invitation has expired
     */
    public function getIsExpiredAttribute()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Format the expiry date nicely
     */
    public function getFormattedExpiryAttribute()
    {
        return $this->expires_at ? $this->expires_at->format('d M Y') : '-';
    }

    /**
     * Get the status text for display
     */
    public function getStatusTextAttribute()
    {
        if ($this->is_expired) {
            return 'Expired';
        }

        return $this->responded ? 'Responded' : 'Pending';
    }
}