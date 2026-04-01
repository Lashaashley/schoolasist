<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LpoItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'lpo_id',
        'category_id',
        'product_name',
        'quantity',
        'unit_price',
        'total'
    ];

    // LpoItem.php
public function category()
{
    return $this->belongsTo(\App\Models\Category::class, 'category_id','id');
}

 


public function supplyItem()
{
    return $this->belongsTo(SupplyItem::class, 'supply_item_id');
}

}