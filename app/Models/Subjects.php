<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subjects extends Model
{
    protected $table = 'tblsubjects';
    
    // Specify the correct primary key name
    protected $primaryKey = 'ID'; // Your DB uses uppercase ID
    
    protected $fillable = [
        'sname',
        'scode', 
        'sdept',
        'isall'
    ];
}
