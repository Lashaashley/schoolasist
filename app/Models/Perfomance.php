<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perfomance extends Model
{
    use HasFactory;

    protected $table = 'performancetbl';
    protected $primaryKey = 'ID';
    protected $fillable = ['admno', 'examtype','examcount','examperiod', 'subid', 'clateach', 'classid','classident', 'marks', 'mstatus'];
}
