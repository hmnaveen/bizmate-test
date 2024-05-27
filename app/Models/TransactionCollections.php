<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TransactionCollections extends Model
{
    // use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     *
    */

    protected $table = 'transaction_collections';

    protected $fillable = ['user_id', 'invoice_ref_number', 'issue_date', 'due_date', 'transaction_number', 'transaction_type', 'client_name', 'client_email', 'client_phone', 'sub_total', 'total_gst', 'total_amount', 'amount_paid', 'payment_date', 'default_tax', 'logo', 'invoice_sent', 'status', 'is_active', 'payment_option', 'parent_id', 'transaction_sub_type', 'bank_account_id'];

    public function transactions()
    {
        return $this->hasMany(Transactions::class, 'transaction_collection_id');
    }

    public function scopeGetMinAndMaxDate($query)
    {
        return $query->selectRaw("MIN(due_date) as start_date, MAX(due_date) as end_date");
    }

    public function getDueDateAttribute($date)
    {
        if (is_null($date)) {
            return null;
        } else {
            return Carbon::parse($date)->format('d/m/Y');
        }
    }


    public function getIssueDateAttribute($date)
    {
        if (is_null($date)) {
            return null;
        } else {
            return Carbon::parse($date)->format('d/m/Y');
        }
    }

    public function users()
    {
        return $this->belongsTo(SumbUsers::class);
    }

    public function parent()
    {
        return $this->belongsTo(TransactionCollections::class, 'parent_id', 'id');
    }

    public function payment()
    {
        return $this->belongsToMany(Payment::class, 'payment_transaction_collection','transaction_collection_id', 'payment_id')->withPivot('payment', 'amount_due');
    }

    public function paymentReference()
    {
        return $this->hasMany(Payment::class, 'reference_id');
    }
}
