<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SumbInvoiceTaxRates extends Model
{

    // use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'sumb_invoice_tax_rates';

    protected $fillable = ['tax_rates', 'tax_rates_name'];

    public function invoiceItems() {
        return $this->hasMany(SumbInvoiceItems::class, 'invoice_item_tax_rate_id');
    }

    public function invoiceParts() {
        return $this->hasMany(SumbInvoiceParticulars::class, 'invoice_parts_tax_rate_id');
    }

    public function chartAccountsParts() {
        return $this->hasMany(SumbChartAccountsTypeParticulars::class, 'accounts_tax_rate_id');
    }
}
