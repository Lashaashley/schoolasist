<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Examtypes extends Model
{
    use HasFactory;

    protected $table = 'examtypes';
    protected $primaryKey = 'ID';
    protected $fillable = ['examname'];
}
