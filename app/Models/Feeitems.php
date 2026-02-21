<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feeitems extends Model
{
    use HasFactory;

    protected $table = 'feeitems';
    protected $primaryKey = 'ID';
    protected $fillable = ['feename','category','house','amount'];
}
