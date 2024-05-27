<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BasicInvoiceReports extends Model
{
    // use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
    */
    protected $table = 'basic_invoice_reports';

    protected $fillable = ['user_id', 'transaction_collection_id', 'invoice_report_file'];
}
