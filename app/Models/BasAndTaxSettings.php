<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BasAndTaxSettings extends Model
{
    // use HasFactory;

    protected $table = 'bas_and_tax_settings';

    public $timestamps = false;

    protected $fillable = ['user_id', 'gst_calculation_period', 'gst_accounting_method', 'payg_withhold_period', 'payg_income_tax_method'];

}
