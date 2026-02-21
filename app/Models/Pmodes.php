<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pmodes extends Model
{
    use HasFactory;
 
    protected $table = 'pmethods';
    protected $fillable = ['pname', 'sstatus', 'tcode', 'chequeno', 'bankn'];
}
