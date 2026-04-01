<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'supply_categories';

    // Only the name is fillable now
    protected $fillable = ['name'];

    // Relationship to items in this category
    public function items()
    {
        return $this->hasMany(SupplyItem::class, 'category_id');
    }
}