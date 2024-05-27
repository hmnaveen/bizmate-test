<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SumbExpenseSettings extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = ['user_id'];
}
