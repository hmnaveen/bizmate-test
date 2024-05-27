<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SumbClients extends Model
{
    use SoftDeletes;


    // use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'sumb_clients';

    protected $fillable = ['user_id', 'client_name', 'client_email', 'client_phone'];

}
