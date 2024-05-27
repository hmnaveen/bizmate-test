<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MinorAdjustments extends Model
{

    protected $table = 'minor_adjustments';

    public $timestamps = false;

    protected $fillable = ['bank_transaction_id', 'adjustments'];

}
