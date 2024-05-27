<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SumbChartAccounts extends Model
{
    // use HasFactory;

    protected $table = 'sumb_chart_accounts';

    protected $fillable = ['user_id', 'chart_accounts_name'];

    public function chartAccountsTypes() {
        return $this->hasMany(SumbChartAccountsType::class, 'chart_accounts_id');
    }

    public function chartAccountsParticulars() {
        return $this->hasMany(SumbChartAccountsTypeParticulars::class, 'chart_accounts_id');
    }
}
