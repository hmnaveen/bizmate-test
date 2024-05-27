<?php

namespace App\Repositories;

use App\Interfaces\BankRepositoryInterface;
use App\Models\UserBankAccounts;
use App\Models\UserBankTransactions;
use DB;
use Carbon\Carbon;
use Arr;
use Illuminate\Support\Collection;

class BankRepository implements BankRepositoryInterface 
{

    public function __construct(UserBankAccounts $bankAccountsModel, 
        UserBankTransactions $bankTransactionsModel
    ){
        $this->bankAccountsModel = $bankAccountsModel;
        $this->bankTransactionsModel = $bankTransactionsModel;
    }
    
    public function createOrUpdateBankAcounts($accounts)
    {
        try{
            return $this->bankAccountsModel::upsert($accounts, ['user_id'], ['account_id']);
        }
        catch(\Exceptions $e)
        {
            \Log::error($e);
        }
    }

    public function getTransactionsByFilter($filter, $userId)
    {
        try{
            $response = $this->bankTransactionsModel::where('user_id', $userId);
                if(isset($filter['start_date'])){
                    $response->whereDate('post_date', '>=', Carbon::createFromFormat('m/d/Y', $filter['start_date'])->format('Y-m-d'));   
                }
                if(isset($filter['end_date'])){
                    $response->whereDate('post_date', '<=', Carbon::createFromFormat('m/d/Y', $filter['end_date'])->format('Y-m-d'));   
                }
                if(isset($filter['min_amt'])){
                    $response->where(DB::raw('ABS(amount)'),'>=',$filter['min_amt']);   
                }
                if(isset($filter['max_amt'])){
                    $response->where(DB::raw('ABS(amount)'),'<=',$filter['max_amt']);   
                }
                if(isset($filter['search_desc_class'])){
                    $response->where(function($query) use ($filter){
                                    $query->where('description', 'LIKE', '%'.$filter['search_desc_class'].'%')
                                    ->orWhere('class', 'LIKE', '%'. $filter['search_desc_class'] .'%');             
                    });
                }
                if(isset($filter['orderby'])){
                    $response->orderby('id');   
                }
                if(isset($filter['isReconciled'])){
                    $response->where('is_reconciled', $filter['isReconciled']);   
                }
                $response->where('account_id', $filter['bankAccount']);
                $response = $response->paginate($filter['ipp'])->toArray();
            return $response;
        }
        catch(\Exceptions $e)
        {
            \Log::error($e);
        }
    }

    public function createOrUpdateBankTransactions($transactions, $bankUserId, $userId)
    {
        try{

            \DB::beginTransaction();

            $response = collect($transactions)
                ->map(function (array $row) use($userId, $bankUserId) {
                    return [
                        'transaction_id' => $row['id'],
                        'user_id' => $userId,
                        'bank_user_id' => $bankUserId,
                        'type' => $row['type'],
                        'status' => $row['status'],
                        'description' => $row['description'],
                        'amount' => $row['amount'],
                        'account_id' => $row['account'],
                        'balance' => (!empty($row['balance']) ? $row['balance'] : NULL),
                        'direction' => $row['direction'],
                        'class' => $row['class'],
                        'instituition_id' => $row['institution'],
                        'connection_id' => $row['connection'],
                        'enrich' => $row['enrich'],
                        'transaction_date' => (!empty($row['transactionDate']) ? Carbon::createFromTimeString($row['transactionDate']) : NULL),
                        'post_date' => (!empty($row['postDate']) ? Carbon::createFromTimeString($row['postDate']) : NULL),
                        'sub_class' => json_encode($row['subClass']),
                        'links' => json_encode($row['links']),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ];
                })
                ->chunk(500)
                ->each(function (Collection $chunk) {
                    $this->bankTransactionsModel::upsert($chunk->all(), ['user_id', 'transaction_id'], ['transaction_id']);
                });
            
            \DB::commit();
        }
        catch(\Exceptions $e)
        {
            \DB::rollback();
            \Log::error($e);
        }
    }

    public function showBankTransactions($userId)
    {
        // return $this->bankAccountsModel::with(['bankTransactions'])->where('user_id', $userId)->where('is_active', 1)->exists();
        return $this->bankTransactionsModel::where('user_id', $userId)->where('is_reconciled', 0)->orderby('id')->count();
    }

    public function showBankAccounts($userId)
    {
        return $this->bankAccountsModel::where('user_id', $userId)->where('is_active',1)->orderby('id')->get();
    }

    public function showTransaction($bankTransactionId, $accountId, $userId)
    {
        return $this->bankTransactionsModel::where('id', $bankTransactionId)
                    ->where('account_id', $accountId)
                    ->where('user_id', $userId)
                    ->first();
    }

    public function archiveBankAccount($accountId, $userId)
    {
        return $this->bankAccountsModel::where('user_id', $userId)->where('account_id', $accountId)->where('is_active',1)->update([ 'is_active' => 0 ]);
    }
}
?>