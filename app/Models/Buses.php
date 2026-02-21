<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buses extends Model
{
    use HasFactory;

    protected $table = 'buses';
    protected $primaryKey = 'ID';
    protected $fillable = ['busna'];
}
