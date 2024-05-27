<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SumbUsers;
use App\Casts\ReadableNumber;

class BankAccount extends Model
{
    use HasFactory;
    protected $fillable  = ['basiq_user_id','account_id','account_type','account_number','account_name','instituition','currency','balance','avaialable_funds','credit_limit','class','transaction_intervals','account_holder','connection_id','status','links','bank_last_updated', 'is_active', 'transaction_url']; 
    
    //the field name inside the array is mass-assignable
    protected $casts = [
        'class' => 'array',
        'transaction_intervals' => 'array',
        'links' => 'array',
        'avaialable_funds' => ReadableNumber::class,
    ];

    public function bankTransactions() {
        return $this->hasMany(BankTransaction::class, 'account_id', 'account_id');
    }

    public function users() {
        return $this->belongsTo(SumbUsers::class, 'basiq_user_id');
    }
}
