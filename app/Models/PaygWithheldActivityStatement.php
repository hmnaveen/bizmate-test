<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaygWithheldActivityStatement extends Model
{
    // use HasFactory;

    protected $table= 'payg_withheld_activity_statement';

    public $timestamps = false;

    protected $fillable = [
                            'start_date','end_date','activity_id','payg_tax_withheld','payg_withheld_w1',
                            'payg_withheld_w2','payg_withheld_w3','payg_withheld_w4','payg_withheld_w5'
                        ];

    public function activityStatement()
    {
        return $this->belongsTo(ActivityStatements::class);
    }

}
