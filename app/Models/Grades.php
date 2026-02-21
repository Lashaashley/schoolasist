<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grades extends Model
{
    use HasFactory;

    protected $table = 'grading';
    protected $primaryKey = 'ID';
    protected $fillable = ['Grade', 'Min', 'Max', 'Remarks'];
}