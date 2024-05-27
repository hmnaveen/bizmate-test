<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Eloquent;
class SumbInvoiceDetails extends Model
{
    // use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * 
     */
    protected $table = 'sumb_invoice_details';

    protected $fillable = ['user_id', 'client_name', 'client_email', 'client_phone', 'invoice_issue_date', 'invoice_due_date', 'invoice_number', 'invoice_sub_total', 'invoice_total_gst', 'invoice_total_amount', 'invoice_default_tax', 'invoice_sent', 'invoice_status', 'is_active'];

    public function particulars() {
        return $this->hasMany(SumbInvoiceParticulars::class, 'invoice_id');
    }
}
