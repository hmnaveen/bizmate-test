<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionRefernce extends Model
{
    use HasFactory;

    protected $table = 'transaction_collection_refernce';

    protected $fillable = ['transaction_collection_id', 'parent_id'];
}
