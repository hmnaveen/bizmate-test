<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class UserBankTransactions extends Model
{
    use HasFactory;

   protected $casts = [
    'links' => 'array',
    'sub_class' => 'array'
    ];

    public function accounts() {
        return $this->belongsTo(UserBankAccounts::class, 'account_id', 'account_id');
    }
}