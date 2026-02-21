<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipts extends Model
{
    use HasFactory;
 
    protected $table = 'tblreciept';
    protected $fillable = ['admno', 'period', 'receiptno', 'receiptdate', 'balanceasof', 'amount', 'pmode', 'tocde', 'chequeno', 'bankn'];
}
