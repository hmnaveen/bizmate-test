<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ReconciledTransactions extends Model
{
    use HasFactory;

    protected $table = 'reconciled_transactions';

    protected $fillable = ['user_id', 'bank_transaction_id', 'payment_id', 'reconciled_at', 'is_reconciled', 'adjustments', 'is_active'];


    public function bankTransaction() {
        return $this->belongsTo(BankTransaction::class);
    }

    public function payment() {
        return $this->belongsTo(Payment::class);
    }

    public function getRecociledAtAttribute($date)
    {
        return $this->attributes['reconciled_at'] = Carbon::parse($date)->format('d/m/Y');
    }
}
