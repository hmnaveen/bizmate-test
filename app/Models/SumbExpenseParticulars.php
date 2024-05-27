<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SumbExpenseParticulars extends Model
{
    use HasFactory;
    protected $fillable  = ['user_id','expense_description','item_quantity','item_unit_price','expense_tax','expense_amount', 'expense_account_code', 'expense_account_name']; //the field name inside the array is not mass-assignable

}
