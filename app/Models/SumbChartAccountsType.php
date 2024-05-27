<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SumbChartAccountsType extends Model
{
    // use HasFactory;

    protected $table = 'sumb_chart_accounts_type';

    protected $fillable = ['user_id', 'chart_accounts_id', 'chart_accounts_type'];

    public function chartAccounts() {
        return $this->belongsTo(SumbChartAccounts::class);
    }

    public function chartAccountsParticulars() {
        return $this->hasMany(SumbChartAccountsTypeParticulars::class, 'chart_accounts_type_id');
    }
}
