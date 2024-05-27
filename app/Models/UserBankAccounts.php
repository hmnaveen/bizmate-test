<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBankAccounts extends Model
{
    use HasFactory;
    protected $fillable  = ['account_type','account_id','account_number','account_name','currency','balance','avaialable_funds','instituition','credit_limit','class','transaction_intervals','account_holder','connection_id','status','links','bank_last_updated', 'is_active']; //the field name inside the array is mass-assignable

   protected $casts = [
    'class' => 'array',
    'transaction_intervals' => 'array',
    'links' => 'array'
    ];

    public function bankTransactions() {
        return $this->hasMany(UserBankTransactions::class, 'account_id', 'account_id');
    }
}