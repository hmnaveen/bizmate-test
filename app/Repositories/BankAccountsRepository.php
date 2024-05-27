<?php

namespace App\Repositories;

use App\Interfaces\BankAccountsRepositoryInterface;
use App\Models\BankAccount;
use App\Models\UserBankTransactions;
use App\Models\SumbUsers;
use DB;
use Carbon\Carbon;
use Arr;
use Illuminate\Support\Collection;

class BankAccountsRepository implements BankAccountsRepositoryInterface 
{

    public function __construct(BankAccount $bankAccountModel, 
        UserBankTransactions $bankTransactionsModel,
        SumbUsers $userModel
    ){
        $this->bankAccountModel = $bankAccountModel;
        $this->bankTransactionsModel = $bankTransactionsModel;
        $this->userModel = $userModel;
    }
    
    public function createOrUpdateBankAcount($basiqUserId, $account)
    {
        try{
            
            return $this->bankAccountModel::updateOrCreate([
                'basiq_user_id' => $basiqUserId,
                'account_id' => $account['id']
            ],
            [
                'basiq_user_id' => $basiqUserId,
                'account_type' => $account['type'],
                'account_id' => $account['id'],
                'account_number' => $account['accountNo'],
                'account_name' => $account['name'],
                'currency' => $account['currency'],
                'balance' => $account['balance'],
                'avaialable_funds' => $account['availableFunds'],
                'instituition' => $account['institution'],
                'credit_limit' => (!empty($account['creditLimit']) ? $account['creditLimit'] : NULL),
                'class' => $account['class'],
                'transaction_intervals' => json_encode($account['transactionIntervals']),
                'account_holder' => $account['accountHolder'],
                'connection_id' => $account['connection'],
                'status' => $account['status'],
                'links' => json_encode($account['links']),
                'bank_last_updated' => Carbon::createFromTimeString($account['lastUpdated']),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

        }
        catch(\Exceptions $e)
        {
            \DB::rollback();
            \Log::error($e);
        }
    }

    public function show($basiqUserId, $accountId)
    {
        return $this->bankAccountModel::where('basiq_user_id', $basiqUserId)->where('account_id', $accountId)->first();
    }

    
    public function showAll($userId)
    {
        return $this->userModel::with(['bankAccounts' => function($query) use($userId) {
                $query->where('is_active', 1)->orderby('id');
        }])->where('id', $userId)->first();
    }

    public function disableBankAccount($accountId, $basqUserId)
    {
        return $this->bankAccountModel::where('basiq_user_id', $basqUserId)->where('account_id', $accountId)->where('is_active',1)->update([ 'is_active' => 0 ]);
    }
}
?>