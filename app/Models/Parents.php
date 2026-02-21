<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parents extends Model
{
    use HasFactory;

    protected $table = 'tblparents';
    protected $fillable = ['surname','othername','typpe','phoneno','email','workplace', 'emergencyphone','address'];
}
