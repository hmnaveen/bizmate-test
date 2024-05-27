<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityStatements extends Model
{
    // use HasFactory;
    protected $table= 'activity_statements';

    public $timestamps = false;

    protected $fillable = ['user_id','start_date','end_date','activity_statement_status','gst_calculation_period',
                            'payg_withhold_period','payg_income_tax_method','payment_amount','payment_type','abn','total_owed_to_ato', 'total_owed_by_ato',
                            'gst_accounting_method'
                        ];

    public function gstActivity()
    {
        return $this->hasOne(GstActivityStatement::class, 'activity_id');
    }

    public function paygwActivity()
    {
        return $this->hasOne(PaygWithheldActivityStatement::class, 'activity_id');
    }

    public function paygiActivity()
    {
        return $this->hasOne(PaygIncomeTaxInstalment::class, 'activity_id');
    }

    public function getPaymentTypeAttribute($type)
    {
        return $this->attributes['payment_type'] = ($type == 'refund' ? 1 : ($type == 'payment' ? 0 : 1));
        
    }

    public function setPaymentTypeAttribute($value)
    {
        return $this->attributes['payment_type'] = ($value == 1 ? 'refund' : ($value == 0 ? 'payment' : 'refund'));
        
    }
}
