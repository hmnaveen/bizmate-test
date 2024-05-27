<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SumbUsers;

class SumbUserDetails extends Model
{
    
    protected $table = 'sumb_user_details';

    protected $fillable = ['sumb_user', 'address', 'state', 'city', 'suburb', 'zip', 'photo', 'country_code'];

    protected $primaryKey = 'sumb_user';
    
    public $timestamps = false;

    public $incrementing = false;

    public function user()
    {

        return $this->hasOne(SumbUsers::class,'id');

    }

}
