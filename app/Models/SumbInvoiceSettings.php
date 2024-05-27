<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SumbInvoiceSettings extends Model
{
    protected $table = 'sumb_invoice_settings';

    public $timestamps = true;


    protected $fillable = ['user_id', 'business_logo', 'business_invoice_format', 'business_name', 'business_email', 'business_phone', 'business_abn', 'business_address', 'business_terms_conditions'];

}
