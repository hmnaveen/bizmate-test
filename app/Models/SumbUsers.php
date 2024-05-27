<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use App\Models\SumbUserDetails;
use App\Models\TransactionCollections;
use App\Models\BankAccount;

class SumbUsers extends Model
{
    use HasFactory,Notifiable;
    
    protected $table = 'sumb_users';

    protected $fillable = ['accountype', 'fullname', 'email', 'email_verified_at', 'password', 'remember_token', 'profilepic', 'session_id', 'active', 'bank_user_id', 'basiq_user_id'];


    public $timestamps = true;
    
    protected $appends = ['encId'];

    public function scopeCheckEmail($emailadd) {

        return $query->where('email', $emailadd);
        
    }

    ///:(

    public function getEncIdAttribute()
    {
        return $this->attributes['encId'] = encrypt($this->id);  
    }
    public function scopeGetEmail($q,$val) {
        return $q->where('email', $val);
    }
    public function scopeAdminUser($q){
        return $q->where('accountype','admin');
    }
    public function scopeNotAdmin($q){
        return $q->where('accountype','<>','admin');
    }
    public function userDetails()
    {
        return $this->hasOne(SumbUserDetails::class,'sumb_user');
    }
    public function transactionCollections() {
        return $this->hasMany(TransactionCollections::class, 'user_id');
    }
    public function bankAccounts() {
        return $this->hasMany(BankAccount::class, 'basiq_user_id', 'basiq_user_id');
    }
}
