<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SumbBasicInvoiceItems extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'basic_invoice_item';

    protected $fillable = ['user_id', 'invoice_item_tax_rate_id', 'invoice_item_code', 'invoice_item_name', 'invoice_item_unit_price', 'invoice_item_description', 'invoice_item_tax_rate', 'invoice_item_quantity'];


    public function taxRates() {
        return $this->belongsTo(SumbInvoiceTaxRates::class, 'invoice_item_tax_rate_id');
    }
}
