<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class GstActivityStatement extends Model
{
    // use HasFactory;

    protected $table= 'gst_activity_statement';

    public $timestamps = false;

    protected $fillable = ['activity_id','start_date','end_date','total_sales_g1','gst_sales_1a','gst_purchases_1b'];

    public function activityStatement()
    {
        return $this->belongsTo(ActivityStatements::class);
    }
    
    public function adjustmentG1()
    {
        return $this->hasMany(AdjustmentG1::class, 'gst_activity_id');
    }

    public function adjustment1A()
    {
        return $this->hasMany(Adjustment1A::class, 'gst_activity_id');
    }

    public function adjustment1B()
    {
        return $this->hasMany(Adjustment1B::class, 'gst_activity_id');
    }
}
