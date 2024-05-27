<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ReconcileDiscuss extends Model
{
    use HasFactory;


    protected $table = 'reconcile_discuss';

    protected $fillable = ['transaction_id', 'user_id', 'date_time', 'discuss', 'discuss_history', 'is_active'];

    public $timestamps = false;

    public function bankTransaction() {
        return $this->belongsTo(BankTransaction::class);
    }

    protected function Description(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $attributes['discuss_history'],
        );
    }

    public function discussionPayment() {
        return $this->hasMany(DiscussionPayment::class, 'discussion_id');
    }

    public function payment()
    {
        return $this->belongsToMany(Payment::class, 'discussion_payment','discussion_id', 'payment_id');
    }
}
