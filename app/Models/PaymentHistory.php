<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PaymentHistory extends Model
{
    // use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
    */
    protected $table = 'payment_history';

    protected $fillable = ['user_id', 'transaction_collection_id', 'date', 'amount_paid'];

    public function getDateAttribute($date)
    {
        return $this->attributes['date'] = Carbon::parse($date)->format('d M Y');
    }
}
