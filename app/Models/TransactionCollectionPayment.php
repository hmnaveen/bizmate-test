<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionCollectionPayment extends Model
{
    // use HasFactory;

    protected $table = 'payment_transaction_collection';

    protected $fillable = ['payment_id', 'transaction_collection_id', 'payment', 'amount_due'];


    public function transactionCollection() {
        return $this->belongsTo(TransactionCollections::class);
    }

    public function payment() {
        return $this->belongsTo(Payment::class);
    }
}
