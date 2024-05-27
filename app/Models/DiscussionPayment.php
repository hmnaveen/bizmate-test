<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscussionPayment extends Model
{
//    use HasFactory;

    protected $table = 'discussion_payment';
    protected $fillable = ['discussion_id', 'payment_id'];
    public $timestamps = false;

    public function discussion() {
        return $this->belongsTo(ReconcileDiscuss::class);
    }
}
