<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Adjustment1A extends Model
{
    // use HasFactory;

    protected $table= 'adjustment_1a';

    protected $fillable = ['gst_activity_id', 'adjust_by', 'reason'];

    public function gstActivity()
    {
        return $this->belongsTo(GstActivityStatement::class);
    }
}
