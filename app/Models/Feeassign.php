<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feeassign extends Model
{
    use HasFactory;

    protected $table = 'feeassign';
    protected $primaryKey = 'ID';
    protected $fillable = ['feeid','feeamount','classid'];
}
