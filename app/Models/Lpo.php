<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\LpoItem;

class Lpo extends Model
{
    use HasFactory;

    protected $fillable = [
        'lpo_number',
        'supplier_id',
        'grand_total', 
    ];

    /**
     * Supplier for this LPO
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Items in this LPO
     */
    public function items()
    {
        return $this->hasMany(LpoItem::class);
    }
public function categoryRelation() {
    return $this->belongsTo(\App\Models\SupplyCategory::class, 'category', 'id');
}
}