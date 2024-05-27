<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    // use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     *
     */
    protected $table = 'transactions';

    protected $fillable = ['user_id', 'transaction_collection_id', 'parts_tax_rate_id', 'parts_chart_accounts_id', 
                            'parts_name','parts_code', 'parts_tax_rate', 'parts_quantity', 'parts_description', 
                            'parts_unit_price', 'parts_gst_amount', 'parts_amount'
                        ];

    public function transactionCollection() {
        return $this->belongsTo(TransactionCollections::class);
    }

    public function chartAccountsParticulars() {
        return $this->belongsTo(SumbChartAccountsTypeParticulars::class, 'parts_chart_accounts_id');
    }
    
    public function invoiceTaxRates() {
        return $this->belongsTo(SumbInvoiceTaxRates::class, 'parts_tax_rate_id');
    }
}
