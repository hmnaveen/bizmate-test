<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdjustmentG1 extends Model
{
    // use HasFactory;

    protected $table = 'adjustment_g1';

    protected $fillable = ['gst_activity_id', 'adjust_by', 'reason'];

    public function gstActivity()
    {
        return $this->belongsTo(GstActivityStatement::class);
    }
}
