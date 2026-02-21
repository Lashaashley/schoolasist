<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subassign extends Model
{
    use HasFactory;

    protected $table = 'tblsubassign';
    protected $primaryKey = 'ID';
    protected $fillable = ['subid','classid','classtr', 'stdid', 'classident'];
}
