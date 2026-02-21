<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $primaryKey = 'StudentID';
    use HasFactory;

    protected $fillable = ['admno','sirname', 'othername', 'gender', 'dateob', 'admdate', 'caid', 'claid','stream', 'border', 'houseid',  'parent', 'photo'];
}

