<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ReconcileDiscuss;
class Payment extends Model
{
    // use HasFactory;

    protected $table = 'payments';

    protected $fillable = ['account_id', 'payment_date', 'total_amount', 'reference_id'];

    public function transactionCollection()
    {
        return $this->belongsToMany(TransactionCollections::class, 'payment_transaction_collection', 'payment_id', 'transaction_collection_id')->withPivot('payment', 'amount_due');
    }

//    public function paymentTransactioncollection()
//    {
//        return $this->hasMany(TransactionCollectionPayment::class, 'payment_id');
//    }

    public function reconcileTransaction()
    {
        return $this->hasOne(ReconciledTransactions::class, 'payment_id');
    }

    public function paymentTransaction()
    {
        return $this->belongsTo(TransactionCollections::class, 'reference_id' );
    }

    public function discussion()
    {
        return $this->belongsToMany(ReconcileDiscuss::class, 'discussion_payment', 'payment_id', 'discussion_id');
    }
}
