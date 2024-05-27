<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaygIncomeTaxInstalment extends Model
{
    // use HasFactory;

    protected $table= 'payg_income_tax_instalment';

    public $timestamps = false;

    protected $fillable = [
                            'start_date','end_date','activity_id','reason_code_t4','payg_income_tax_instalment_5a',
                            'payg_income_tax_instalment_credit','option_1','option_2'
                        ];

    public function activityStatement()
    {
        return $this->belongsTo(ActivityStatements::class);
    }

    protected $casts = [
        'option_1' => 'array',
        'option_2' => 'array'
    ];
}
