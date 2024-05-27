<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SumbInvoiceReports extends Model
{
    // use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
    */
    protected $table = 'sumb_invoice_reports';

    protected $fillable = ['user_id', 'invoice_id', 'invoice_report_file'];
}
