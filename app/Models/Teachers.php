<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teachers extends Model
{
    protected $table = 'tblteachers';
    
    // Specify the correct primary key name
    protected $primaryKey = 'ID'; // Your DB uses uppercase ID
    
    protected $fillable = [
        'fname',
        'surname', 
        'workno',
        'gender',
        'trtype',
        'phoneno',
        'dateemployed',
        'email'
    ];
}
