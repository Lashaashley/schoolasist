<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Streams extends Model
{
    use HasFactory;

    protected $table = 'streams';
    protected $primaryKey = 'ID';
    protected $fillable = ['strmname', 'classid', 'classteach'];
}
