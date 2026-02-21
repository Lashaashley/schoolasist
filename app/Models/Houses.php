<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Houses extends Model
{
    use HasFactory;

    protected $table = 'houses';
    protected $primaryKey = 'ID';
    protected $fillable = ['brid', 'housen'];
}
