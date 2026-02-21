<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fcategories extends Model
{
    use HasFactory;
 
    protected $table = 'fcategories';
    protected $fillable = ['catename'];
}
