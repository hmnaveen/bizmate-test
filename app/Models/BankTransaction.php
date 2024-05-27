<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\ReadableNumber;

class BankTransaction extends Model
{
    use HasFactory;

   protected $casts = [
        'links' => 'array',
        'sub_class' => 'array',
        'amount' => ReadableNumber::class,
       

    ];

    public function accounts() {
        return $this->belongsTo(BankAccount::class, 'account_id', 'account_id');
    }

    public function discuss() {
        return $this->hasMany(ReconcileDiscuss::class, 'transaction_id');
    }

    public function reconcileTransaction() {
        return $this->hasMany(ReconciledTransactions::class, 'bank_transaction_id');
    }
}