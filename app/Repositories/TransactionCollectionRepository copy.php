<?php

namespace App\Repositories;

use App\Interfaces\TransactionCollectionRepositoryInterface;
use App\Models\Transactions;
use App\Models\TransactionCollections;
use App\Models\SumbChartAccountsTypeParticulars;
use App\Models\InvoiceHistory;
use DB;
use Carbon\Carbon;
use App\Interfaces\ClientsRepositoryInterface;
use App\Interfaces\PaymentHistoryRepositoryInterface;
use App\Interfaces\InvoiceHistoryRepositoryInterface;
use App\Mail\RecallInvoiceMail;
use Illuminate\Support\Facades\Mail;
use App\Models\ReconciledTransactions;


class TransactionCollectionRepository implements TransactionCollectionRepositoryInterface 
{
    private ClientsRepositoryInterface $clientsRepository;
    private PaymentHistoryRepositoryInterface $paymentHistoryRepository;
    private InvoiceHistoryRepositoryInterface $invoiceHistoryRepository;
    private TransactionCollections $transactionCollectionModel;
   
    public function __construct(
        ClientsRepositoryInterface $clientsRepository,
        PaymentHistoryRepositoryInterface $paymentHistoryRepository,
        InvoiceHistoryRepositoryInterface $invoiceHistoryRepository,
        TransactionCollections $transactionCollectionModel,
    )
    {
        $this->clientsRepository = $clientsRepository;
        $this->paymentHistoryRepository = $paymentHistoryRepository;
        $this->invoiceHistoryRepository = $invoiceHistoryRepository;
        $this->transactionCollectionModel = $transactionCollectionModel;
    }

    public function getTransactionsByFilter($filters, $isFilterOn)
    {
        if($isFilterOn)
        {
            $invoicedata = $this->transactionCollectionModel::where('user_id', $filters['userinfo'][0])->where('is_active', 1);
                    // $invoicedata->where(function($query) use($filters) {
                    //     return $query->whereIn('payment_option', ['over_payment', 'pre_payment'])
                    //         ->where('user_id', $filters['userinfo'][0])
                    //         ->orWhereNull('payment_option');
                        
                    // });
                    $invoicedata->whereIn('transaction_type', ['invoice','arprepayment','aroverpayment']);

                    if(!empty($filters['search_number_email_amount']))
                    {
                        $invoicedata->where(function($query) use($filters){
                            $query->where('transaction_number', 'LIKE', "%{$filters['invoice_number']}%")
                                ->orWhere('total_amount', 'LIKE', "%{$filters['total_amount']}%")
                                ->orWhere('amount_paid', 'LIKE', "%{$filters['amount_paid']}%");
                        });
                    }
                    
                    if($filters['start_date'] && $filters['end_date'])
                    {
                        $invoicedata->whereBetween(DB::raw("(DATE_FORMAT(issue_date,'%d/%m/%Y'))"), [$filters['start_date'],  $filters['end_date']]);
                    }
                    if($filters['orderBy'])
                    {
                        $invoicedata->orderBy($filters['orderBy'], $filters['direction']);
                    }
                    if($filters['filterBy'])
                    {
                        $invoicedata->where('status', $filters['filterBy']);
                    }
            return $invoicedata->paginate($filters['itemsperpage'])->toArray();
             
        }
        else
        {
            return $this->transactionCollectionModel::where('user_id', $filters['userinfo'][0])->where('is_active', 1)->whereIn('transaction_type', ['invoice','arprepayment','aroverpayment'])
                // ->orWhere(function($query) use($filters) {
                //     return $query->where('user_id', $filters['userinfo'][0])->where('transaction_type', 'receive_money')
                //         ->whereIn('payment_option', ['over_payment', 'pre_payment']);
                // })
                ->orderBy('issue_date', 'DESC')
                ->orderBy('transaction_number', 'DESC')
                ->paginate($filters['itemsperpage'])->toArray();
        }
    }

    public function getTransactionCount($userId)
    {
        return $this->transactionCollectionModel::groupBy('status')
            ->select( DB::raw('status, COUNT(*) as status_count, sum(total_amount+amount_paid) as total') )
            ->where('is_active', 1)
            ->where('transaction_type', 'invoice')
            ->where('user_id', $userId)
            ->orderBy('status')
            ->get();
    }
    
    public function getTransaction($filters, $userId)
    {
        try{
            if(isset($filters['type']) && $filters['type'] == 'edit')
            {
                return $this->transactionCollectionModel::with(['transactions', 'transactions.chartAccountsParticulars', 'transactions.invoiceTaxRates', 'reconcileTransaction' => function($q){
                                $q->where('is_active', 1);
                        }])
                        ->whereHas('transactions', function($query) use($userId) {
                                $query->where('user_id', $userId);
                        })
                        ->where('id', $filters['id'])
                        // ->where('transaction_type', $filters['transaction_type'])
                        ->where('is_active', 1)->where('user_id', $userId)->first();
            }else{
                return $this->transactionCollectionModel::where('user_id', $userId)->whereIn('transaction_type',  $filters['transaction_type'])->orderBy('transaction_number', 'desc')->pluck('transaction_number')->first();
            }
        }
        catch(\Exceptions $e){
            \Log::error($e);
        }
    }

    public function createOrUpdateTransaction($transactionCollections, $transactions, $userDetails, $invoiceId, $type)
    {
        try{
            \DB::beginTransaction();

            $date = Carbon::now()->toDateString();
            $time = Carbon::now()->toTimeString();

            if($type == 'edit')
            {
                $invoice = $this->transactionCollectionModel::where('user_id', trim($userDetails[0]))
                    ->where('id', $invoiceId)
                    ->where('transaction_type', $transactionCollections['transaction_type'])
                    ->update($transactionCollections);

                    Transactions::insert($transactions);

                    $ids = Transactions::where('transaction_collection_id', $invoiceId)
                            ->where('user_id', trim($userDetails[0]))
                            ->orderBy('id', 'desc')->take(count($transactions))
                            ->pluck('id')->toArray();
                if(!empty($transactions)){
                    Transactions::whereNotIn('id', $ids)
                        ->where('transaction_collection_id', $invoiceId)
                        // ->where('user_id', trim($userDetails[0]))
                        ->delete();
                    if( $transactionCollections['transaction_type'] != 'minor_adjustment')
                    {
                        InvoiceHistory::create([
                            "invoice_id" => trim($invoiceId),
                            "invoice_number" => trim($transactionCollections['transaction_number']),
                            "user_id" => trim($userDetails[0]),
                            "user_name" => trim($userDetails[1]),
                            "action" => "Edited",
                            "description" => "INV-".str_pad($transactionCollections['transaction_number'], 6, '0', STR_PAD_LEFT).' to '.trim(ucfirst($transactionCollections['client_name'])).' for $'.trim($transactionCollections['total_amount']),
                            "date" => $date,
                            "time" => $time
                        ]);
                    }
                }
                \DB::commit();

                return $invoice;

            }else{
                
                $invoice = $this->transactionCollectionModel::create($transactionCollections);
                if($invoice)
                {
                    if(!empty($transactions))
                    {
                        $transactions = collect($transactions)
                            ->map(function ($attributes) {
                                return new Transactions($attributes);
                            });

                        $invoice->transactions()->saveMany($transactions);
                    }
                }
                if( $transactionCollections['transaction_type'] != 'minor_adjustment')
                {
                    InvoiceHistory::create([
                        "invoice_id" => trim($invoice['id']),
                        "invoice_number" => trim($transactionCollections['transaction_number']),
                        "user_id" => trim($userDetails[0]),
                        "user_name" => trim($userDetails[1]),
                        "action" => "Created",
                        "description" => $invoice['transaction_type'] == 'invoice' ? "INV-".str_pad($transactionCollections['transaction_number'], 6, '0', STR_PAD_LEFT).' to '.trim(ucfirst($transactionCollections['client_name'])).' for $'.trim($transactionCollections['total_amount']) : '',
                        "date" => $date,
                        "time" => $time
                    ]);
                }

                if($transactionCollections['transaction_type'] != 'invoice' || $transactionCollections['transaction_type'] != 'expense' )
                {

                    
                    ReconciledTransactions::updateOrCreate(
                        [
                            'user_id' => $userDetails[0],
                            // 'bank_transaction_id' => 0,
                            'transaction_collection_id' => $invoice['id'],
                            'is_reconciled' => 0,
                        ],
                        [
                            'user_id' => $userDetails[0],
                            'bank_transaction_id' => 0,
                            'transaction_collection_id' => $invoice['id'],
                            'is_reconciled' => 0,
                        ]
                    );
                    // $this->reconcileTransactionRepository->test();
                }

                \DB::commit();
              
                return $invoice->find($invoice->id);
            }
        }
        catch(\Exceptions $e)
        {
            \DB::rollback();
            \Log::error($e);
        }
    }

    public function updateInvoiceStatus($invoiceId, $userInfo, $status, $invoiceHistory)
    {
        $date = Carbon::now()->toDateString();
        $time = Carbon::now()->toTimeString();

        try{
            \DB::beginTransaction();

            $statusUpdate = $this->transactionCollectionModel::where('id', $invoiceId)
                    ->where('user_id', $userInfo[0])
                    ->update($status);
            if(!empty($invoiceHistory))
            {
                $invoiceHistory = [
                    "invoice_id" => trim($invoiceId),
                    "invoice_number" => trim($invoiceHistory['invoice_number']),
                    "user_id" => trim($userInfo[0]),
                    "user_name" => trim($userInfo[1]),
                    "action" => "Invoice sent",
                    "description" => "This invoice has been sent to ".$invoiceHistory['email'],
                    "date" => $date,
                    "time" => $time
                ];
                
                $this->invoiceHistoryRepository->store($invoiceHistory);
            }

            \DB::commit();

            return $statusUpdate;
        }
        catch(\Exceptions $e)
        {
            \DB::rollback();
            \Log::error($e);
        }
    }

    public function updateInvoicePaymentStatus($invoiceId, $userinfo, $invoiceStatus, $paymentHistory, $invoiceHistory, $isDeleted)
    {
        $date = Carbon::now()->toDateString();
        $time = Carbon::now()->toTimeString();

        try{
            \DB::beginTransaction();

                $updateStatus = $this->transactionCollectionModel::where('id', $invoiceId);
                    if($invoiceStatus['status'] == 'Paid' || $invoiceStatus['status'] == 'PartlyPaid')
                        $updateStatus->whereIn('status', ['Unpaid', 'Recalled', 'PartlyPaid']);
                $updateStatus->update($invoiceStatus);

                if($isDeleted)
                {
                    $this->paymentHistoryRepository->destroy($invoiceId, $userinfo[0]);
                }
                if(!empty($paymentHistory))
                {
                    $this->paymentHistoryRepository->store($paymentHistory);
                }

                $invoiceHistoryCreated = $this->invoiceHistoryRepository->store($invoiceHistory);

            \DB::commit();

            return $updateStatus;
               
        }
        catch(\Exceptions $e)
        {
            \DB::rollback();
            \Log::error($e);
        }
    }

    public function getTransactionsByStatus($userId, $status, $bankAccountId)
    {
        // return $this->transactionCollectionModel::where('user_id', $userId)->whereIn('status', $status)->where('is_active', 1)->get();
        $response = $this->transactionCollectionModel::with(['reconcileTransaction'])->where('user_id', $userId)->where('is_active', 1);
        $response->where(function($q){
            $q->whereIn('transaction_type', ['invoice', 'expense'])->whereIn('status', ['PartlyPaid', 'Unpaid']);
        })->orWhere(function($q) use($bankAccountId){
            $q->whereIn('transaction_type', ['receive_money', 'spend_money', 'payment','arprepayment','apprepayment','aroverpayment','apoverpayment'])
            // ->whereIn('status', ['PartlyPaid', 'Unpaid'])
            ->where('bank_account_id', $bankAccountId);

            $q->whereHas('reconcileTransaction', function($query){
                $query->where('is_reconciled', 0)->where('is_active', 1);
            });
        });
        return $response = $response->get();
        // return dd($response = $response->get()->toArray());
    }

    public function showReconcileCalculation($userId, $transactionId, $transactionAmount)
    {
        return $this->transactionCollectionModel::where('user_id', $userId)
                    ->where('id', $transactionId)
                    ->where('is_active', 1)->whereIn('status', ['Unpaid', 'PartlyPaid'])
                    ->whereRaw("total_amount + amount_paid >= ?", $transactionAmount)
                    ->first();
    }

    public function destroy($userInfo, $transactionId, $status)
    {
        try{
            $transaction = $this->transactionCollectionModel::where('user_id', $userInfo[0])
                    ->where('id', $transactionId)
                    ->whereIn('status', $status)
                    ->where('is_active', 1)->first();
            if($transaction)
            {
                $transaction->is_active = 0;
                $transaction->save();

                $date = Carbon::now()->toDateString();
                $time = Carbon::now()->toTimeString();
    
                $invoiceHistory = array(
                    "invoice_id" => trim($transactionId),
                    "invoice_number" => trim($transaction->transaction_number),
                    "user_id" => trim($userInfo[0]),
                    "user_name" => trim($userInfo[1]),
                    "action" => 'Deleted',
                    "description" => '',
                    "date" => $date,
                    "time" => $time
                );

                $this->invoiceHistoryRepository->store($invoiceHistory);
            }
        }
        catch(\Exceptions $e)
        {
            \Log::error($e);
        }
    }
    public function reCallInvoice($userInfo, $transactionId)
    {
        try{

            $invoiceDetail = $this->transactionCollectionModel::where('id', $transactionId)
                    ->where('invoice_sent', 1)
                    ->where('status', 'Unpaid')
                    ->where('user_id', $userInfo[0])
                    ->first();

            if ($invoiceDetail) {
                $invpdf['inv'] = [];
                $email = $invoiceDetail->client_email;
                $issueDate = $invoiceDetail->issue_date;

                $invpdf['inv']['subject'] = 'Invoice INV-'.str_pad($invoiceDetail->transaction_number, 6, '0', STR_PAD_LEFT).' has been recalled.';
                $invpdf['inv']['message'] = 'The invoice INV-'.str_pad($invoiceDetail->transaction_number, 6, '0', STR_PAD_LEFT).' sent to you on '.$issueDate. ' has been recalled. 
                            
                            A new invoice will be sent to you. 
                            
                            If you have paid the invoice, please reply to this email: '. $userInfo[2];

                Mail::to($email)->send(new RecallInvoiceMail($invpdf['inv']));
                unset($invoiceDetail->issue_date);
                $invoiceDetail->update(['invoice_sent' => 0, 'status' => 'Recalled']);

                return $invoiceDetail;
            }
        }
        catch(\Exceptions $e)
        {
            \Log::error($e);
        }
    }

    public function matchTransactions($filter, $userId)
    {
        $response = $this->transactionCollectionModel::with(['reconcileTransaction'])->where('user_id', $userId)->where('is_active', 1);
        $response->where(function($q) use($filter){
            if($filter['transaction_type'] == 'credit'){
                $q->whereIn('transaction_type', ['invoice']);
            }
            else if($filter['transaction_type'] == 'debit'){
                $q->whereIn('transaction_type', ['expense']);
            }
            $q->whereIn('status', ['PartlyPaid', 'Unpaid']);
        })->orWhere(function($q) use($filter){
            $q->whereIn('transaction_type', ['receive_money', 'spend_money', 'payment', 'arprepayment','apprepayment','aroverpayment','apoverpayment'])
            // ->whereIn('status', ['PartlyPaid', 'Unpaid'])
            ->where('bank_account_id', $filter['bank_account_id']);

            $q->whereHas('reconcileTransaction', function($query){
                $query->where('is_reconciled', 0)->where('is_active', 1);
            });
        });
        return $response = $response->get();

        // return $this->transactionCollectionModel::where('user_id', $userId)
        //             ->whereIn('transaction_type', $filter['transaction_type'])
        //             ->whereIn('status', $filter['status'])
        //             ->where('is_active', 1)
        //             ->get();
    }

    public function accountTransactions($userId, $filter){
        $response = ReconciledTransactions::with(['transactionCollection','bankTransaction']);
        $response->where('is_active', 1);
        $response->where('user_id', $userId);

        $response->whereHas('transactionCollection', function($query) use($filter) {
            $query->where(function($q) use ($filter) {
                if(isset($filter['start_date'])){
                    $q->whereDate('issue_date', '>=', Carbon::createFromFormat('d/m/Y', $filter['start_date'])->format('Y-m-d'));
                    // ->orWhereDate('payment_date', '>=', Carbon::createFromFormat('d/m/Y', $filter['start_date'])->format('Y-m-d'));
                }
                if(isset($filter['end_date'])){
                    $q->whereDate('issue_date', '<=', Carbon::createFromFormat('d/m/Y', $filter['end_date'])->format('Y-m-d')); 
                    // ->orWhereDate('payment_date', '>=', Carbon::createFromFormat('d/m/Y', $filter['start_date'])->format('Y-m-d'));
                }
                if(isset($filter['min_amt'])){
                    $q->where(DB::raw('total_amount'), '>=', $filter['min_amt']);
                }
                if(isset($filter['max_amt'])){
                    $q->where(DB::raw('total_amount'), '<=', $filter['max_amt']);
                }
                if(isset($filter['search_desc_class'])){
                    $q->where('client_name', 'LIKE', '%'.$filter['search_desc_class'].'%');
                }
                if(isset($filter['orderby'])){
                    $q->orderby('issue_date','asc');   
                }
            })->whereIn('transaction_type', ['receive_money','spend_money','minor_adjustment','payment','arprepayment','apprepayment','aroverpayment','apoverpayment']);
        });
        $response->where(function($q) use ($filter) {
            $q->whereHas('bankTransaction', function($query) use($filter) {
                $query->where('account_id', $filter['bank_account_id']);
            })
            ->orwhereHas('transactionCollection', function($query) use($filter) {
                $query->where('bank_account_id', $filter['bank_account_id']);
            });
        });

        $response = $response->paginate($filter['ipp']);
        return $response;
    }

    public function showReconcileTransaction($userId, $filter)
    {
        return $this->transactionCollectionModel::with(['reconcileTransaction', 'parent', 'transactions', 'transactions.chartAccountsParticulars', 'transactions.invoiceTaxRates'])
            ->where('user_id', $userId)->where('is_active', 1)
            ->where('bank_account_id', $filter['bank_account_id'])
            ->where('id', $filter['reconcileId'])
            ->whereHas('reconcileTransaction', function($query) {
                $query->where('is_active', 1);
            })->first();
    }
}
