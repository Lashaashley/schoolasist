<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Managefee extends Model
{
    use HasFactory;

    protected $table = 'managefee';

    protected $fillable = ['admno', 'classid', 'feeid', 'amount', 'paid', 'balance', 'status', 'period'];
}

