<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class InvoiceHistory extends Model
{
    // use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     *
     */
    protected $table = 'invoice_history';

    public $timestamps = false;

    protected $fillable = ['user_id', 'invoice_id', 'user_name', 'invoice_number', 'action', 'description', 'date', 'time'];


    protected function DateTime(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => ($attributes['date'].' '.$attributes['time']),
        );
    }
}
