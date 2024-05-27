<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BasicInvoiceSettings extends Model
{
    protected $table = 'basic_invoice_settings';

    protected $fillable = ['user_id', 'business_logo', 'business_invoice_format', 'business_name', 'business_email', 'business_phone', 'business_abn', 'business_address', 'business_terms_conditions'];

}
