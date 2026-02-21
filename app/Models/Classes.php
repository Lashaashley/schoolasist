<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    use HasFactory;

    protected $table = 'tblclasses';
    protected $primaryKey = 'ID';
    protected $fillable = ['caid','claname','clarank','clateach'];
}
