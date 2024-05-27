<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BasicTransactions extends Model
{
    // use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     *
     */
    protected $table = 'basic_transactions';

    protected $fillable = ['user_id', 'transaction_collection_id', 'parts_tax_rate_id', 
                            'parts_name','parts_code', 'parts_tax_rate', 'parts_quantity', 'parts_description', 
                            'parts_unit_price', 'parts_gst_amount', 'parts_amount'
                        ];

    public function transactionCollection() {
        return $this->belongsTo(BasicTransactionCollections::class);
    }

    public function invoiceTaxRates() {
        return $this->belongsTo(SumbInvoiceTaxRates::class, 'parts_tax_rate_id');
    }
}
?>