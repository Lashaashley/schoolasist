<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jranks extends Model
{
    use HasFactory;

    protected $table = 'tbljoinrank';
    protected $primaryKey = 'ID';
    protected $fillable = ['admno', 'classid', 'stream', 'examtype', 'examyear', 'Marks','rankno'];
}
