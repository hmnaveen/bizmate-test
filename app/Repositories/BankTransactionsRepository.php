<?php

namespace App\Repositories;

use App\Interfaces\BankTransactionsRepositoryInterface;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\SumbUsers;
use DB;
use Carbon\Carbon;
use Arr;
use Illuminate\Support\Collection;

class BankTransactionsRepository implements BankTransactionsRepositoryInterface
{

    public function __construct(BankAccount $bankAccountModel,
        BankTransaction $bankTransactionsModel,
        SumbUsers $userModel
    ){
        $this->bankAccountModel = $bankAccountModel;
        $this->bankTransactionsModel = $bankTransactionsModel;
        $this->userModel = $userModel;
    }

    public function createOrUpdateBankTransactions($transactions, $basiqUserId, $accountId, $transactionUrl)
    {
        try{

            \DB::beginTransaction();

            $response = collect($transactions)
                ->map(function (array $transaction) use($basiqUserId) {
                    return [
                        'transaction_id' => $transaction['id'],
                        'basiq_user_id' => $basiqUserId,
                        'type' => $transaction['type'],
                        'status' => $transaction['status'],
                        'description' => $transaction['description'],
                        'amount' => $transaction['amount'],
                        'account_id' => $transaction['account'],
                        'balance' => (!empty($transaction['balance']) ? $transaction['balance'] : NULL),
                        'direction' => $transaction['direction'],
                        'class' => $transaction['class'],
                        'instituition_id' => $transaction['institution'],
                        'connection_id' => $transaction['connection'],
                        'enrich' => $transaction['enrich'],
                        'transaction_date' => (!empty($transaction['transactionDate']) ? Carbon::createFromTimeString($transaction['transactionDate']) : NULL),
                        'post_date' => (!empty($transaction['postDate']) ? Carbon::createFromTimeString($transaction['postDate']) : NULL),
                        'sub_class' => json_encode($transaction['subClass']),
                        'links' => json_encode($transaction['links']),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ];
                })
                ->chunk(500)
                ->each(function (Collection $chunk) {
                    $this->bankTransactionsModel::upsert($chunk->all(), ['basiq_user_id', 'transaction_id'], ['transaction_id']);
                });

            if(!empty($transactionUrl))
            {
                $this->bankAccountModel::where('basiq_user_id', $basiqUserId)->where('account_id', $accountId)->update(['transaction_url' => $transactionUrl]);
            }

            \DB::commit();
        }
        catch(\Exceptions $e)
        {
            \DB::rollback();
            \Log::error($e);
        }
    }

    public function getTransactionsByFilter($filter)
    {
        try{
            $response = $this->bankTransactionsModel::with(['discuss' => function ($query) {
                    $query->orderby('id', 'desc')->first();
                }])
                ->where('account_id', $filter['bank_account_id']);
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
                if(isset($filter['filterBy'])){
                    $response->where('direction', $filter['filterBy']);
                }
                // $response->where('account_id', $filter['bank_account_id']);
                $response = $response->paginate($filter['ipp'])->toArray();
            return $response;
        }
        catch(\Exceptions $e)
        {
            \Log::error($e);
        }
    }

    public function showTransaction($bankTransactionId, $accountId)
    {
        return $this->bankTransactionsModel::where('id', $bankTransactionId)
                    ->where('account_id', $accountId)
                    ->first();
    }

    public function updateReconcileStatus($accountId, $bankTransactionId, $status)
    {
        return $this->bankTransactionsModel::where('id', $bankTransactionId)
            ->where('account_id', $accountId)
            ->update($status);
    }
}
