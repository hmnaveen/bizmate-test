<?php

namespace App\Repositories;

use App\Interfaces\ReconcileTransactionRepositoryInterface;
use App\Interfaces\TransactionCollectionRepositoryInterface;
use App\Interfaces\BankRepositoryInterface;
use App\Interfaces\MinorAdjustmentRepositoryInterface;
use App\Interfaces\ChartAccountRepositoryInterface;
use App\Interfaces\TaxRateRepositoryInterface;
use App\Interfaces\BankTransactionsRepositoryInterface;
use App\Interfaces\InvoiceHistoryRepositoryInterface;
use App\Interfaces\PaymentRepositoryInterface;
use App\Interfaces\ReconcileDiscussionRepositoryInterface;
use App\Models\TransactionCollections;
use App\Models\Payment;
use App\Models\DiscussionPayment;
use App\Models\ReconciledTransactions;
use Carbon\Carbon;
use DB;

class ReconcileTransactionRepository implements ReconcileTransactionRepositoryInterface
{
    protected $reconciledModel;
    private TransactionCollectionRepositoryInterface $transactionCollectionRepository;
    private BankRepositoryInterface $bankRepository;
    private MinorAdjustmentRepositoryInterface $minorAdjustmentRepository;
    private ChartAccountRepositoryInterface $chartAccountRepository;
    private TaxRateRepositoryInterface $taxRateRepository;
    private BankTransactionsRepositoryInterface $bankTransactionsRepository;
    private InvoiceHistoryRepositoryInterface $invoiceHistoryRepository;

    private PaymentRepositoryInterface $paymentRepository;
    private ReconcileDiscussionRepositoryInterface $reconcileDiscussionRepository;

    public function __construct(
        TransactionCollectionRepositoryInterface $transactionCollectionRepository,
        BankRepositoryInterface $bankRepository,
        MinorAdjustmentRepositoryInterface $minorAdjustmentRepository,
        ChartAccountRepositoryInterface $chartAccountRepository,
        TaxRateRepositoryInterface $taxRateRepository,
        BankTransactionsRepositoryInterface $bankTransactionsRepository,
        InvoiceHistoryRepositoryInterface $invoiceHistoryRepository,
        PaymentRepositoryInterface $paymentRepository,
        ReconciledTransactions $reconciledModel,
        ReconcileDiscussionRepositoryInterface $reconcileDiscussionRepository,
    ){
        $this->transactionCollectionRepository = $transactionCollectionRepository;
        $this->minorAdjustmentRepository = $minorAdjustmentRepository;
        $this->bankRepository = $bankRepository;
        $this->chartAccountRepository = $chartAccountRepository;
        $this->taxRateRepository = $taxRateRepository;
        $this->bankTransactionsRepository = $bankTransactionsRepository;
        $this->invoiceHistoryRepository = $invoiceHistoryRepository;
        $this->paymentRepository = $paymentRepository;
        $this->reconciledModel = $reconciledModel;
        $this->discussionRepository = $reconcileDiscussionRepository;
    }

    public function reconcileTransaction($request, $userinfo)
    {
        try{

            $totalTransactionAmount = 0;
            $today = Carbon::now()->format('Y-m-d'); //Current Date and Time
            $minorAdjustment = $request->minor_adjustment;
            $request->transaction_collection_id = explode(",", $request->transaction_collection_id);
            $history = array();

            \DB::beginTransaction();

            $invoiceExpensePayment = [];$invoiceExpenseTotalAmount = 0;$invoiceTotal = 0;$expenseTotal = 0;$grandTotal = 0;
            $bankData = $this->bankTransactionsRepository->showTransaction($request->bank_transaction_id, $request->account_id);
            for($i=0; $i<count($request->transaction_collection_id); $i++)
            {
                $request->validate([
                    'transaction_money_'.$request->transaction_collection_id[$i] => 'required|gt:0'
                ]);
                $totalTransactionAmount += $request->input('transaction_money_'.$request->transaction_collection_id[$i]);
                $collection = $this->transactionCollectionRepository->showReconcileCalculation($userinfo[0], $request->transaction_collection_id[$i], $request->input('transaction_money_'.$request->transaction_collection_id[$i]));
                if($collection)
                {
                    $reconcileTransaction = [
                        'user_id' => $userinfo[0],
                        'bank_transaction_id' => $request->bank_transaction_id,
                        'is_active' => 1,
                        'is_reconciled' => $request->is_reconciled,
                        'reconciled_at' => $today
                    ];

                    $collection->amount_paid = DB::raw('amount_paid +'.$request->input('transaction_money_'.$request->transaction_collection_id[$i]));
                    if($collection->transaction_type == 'invoice' || $collection->transaction_type == 'expense')
                    {
                        $collection->status = $collection->total_amount == $request->input('transaction_money_'.$request->transaction_collection_id[$i]) ? "Paid" : "PartlyPaid";
                        $collection->total_amount = DB::raw('total_amount -'.$request->input('transaction_money_'.$request->transaction_collection_id[$i]));

                        $invoiceExpenseTotalAmount += $request->input('transaction_money_'.$request->transaction_collection_id[$i]);
                        $invoiceExpensePayment[] = array(
                            'client_name' => $collection->client_name,
                            'transaction_collection_id' => $collection->id,
                            'payment_id' => 0,
                            'payment' => $request->input('transaction_money_'.$request->transaction_collection_id[$i]),
                            'amount_due' => $request->input('transaction_money_'.$request->transaction_collection_id[$i])
                        );
                    }else
                    {
                        $reconcileTransaction['payment_id'] = $collection['payment'][0]['id'];
                        $this->storeReconcileTransaction($reconcileTransaction);
                    }
                    $collection->save();

                    if($collection->transaction_type == 'invoice')
                    {
                        $invoiceTotal+= $request->input('transaction_money_'.$request->transaction_collection_id[$i]);
                    }
                    if($collection->transaction_type == 'expense')
                    {
                        $expenseTotal+= $request->input('transaction_money_'.$request->transaction_collection_id[$i]);
                    }

                    $history = [
                        'user' => $userinfo,
                        'amount' => $request->input('transaction_money_'.$request->transaction_collection_id[$i]),
                        'bank_transaction' => $bankData->toArray(),
                        'collection' => $collection->toArray()
                    ];
                    $this->createTransactionHistory($history);

                }else{
                    return response()->json( [

                        'message' => "Something went wrong"

                    ], 401);
                }
            }
            if(!empty($invoiceExpensePayment))
            {
                $grandTotal = abs($invoiceTotal - $expenseTotal);
                $paymentReference = TransactionCollections::create([
                    'user_id' => trim($userinfo[0]),
                    'client_name' => count($invoiceExpensePayment) > 1 ? 'multiple' : $invoiceExpensePayment[0]['client_name'],
                    'issue_date' => trim($request->issue_date),
                    'transaction_number' => 0,
                    'sub_total' => $grandTotal,
                    'total_gst' => 0,
                    'total_amount' => $grandTotal,
                    'transaction_type' => 'payment',
                    'invoice_ref_number' => 0,
                    'bank_account_id' => $request->account_id,
                    'payment_date' => trim($request->issue_date),
                    'transaction_sub_type' => $invoiceTotal > $expenseTotal ? 'received' : ($expenseTotal > $invoiceTotal ? 'spent' : ''),
                ]);
                if($paymentReference)
                {
                    $payment = array(
                        'account_id' => $bankData->account_id,
                        'payment_date' => $bankData->post_date,
                        'total_amount' => $invoiceExpenseTotalAmount,
                        'reference_id' => $paymentReference->id
                    );
                    $payment = $this->paymentRepository->create($paymentReference->id, $payment, $invoiceExpensePayment);
                    $history = [
                        'user' => $userinfo,
                        'amount' => $invoiceExpenseTotalAmount,
                        'collection' => $paymentReference,
                        'bank_transaction' => $bankData->toArray(),
                    ];
                    $this->createTransactionHistory($history);

                    $reconcileTransaction = [
                        'user_id' => $userinfo[0],
                        'bank_transaction_id' => $request->bank_transaction_id,
                        'payment_id' => $payment->id,
                        'is_active' => 1,
                        'is_reconciled' => $request->is_reconciled,
                        'reconciled_at' => $today
                    ];
                    $this->storeReconcileTransaction($reconcileTransaction);

                }
            }
            $totalTransactionAmount += $minorAdjustment;
            $transactionAmount = abs(str_replace(',', '', $bankData->amount));

            if(trim($transactionAmount) != trim($totalTransactionAmount))
            {
                return response()->json([

                    'message' => "Total does not match"

                ], 422);
            }

            $bankData->is_reconciled = $request->is_reconciled;
            $bankData->save();

            if($minorAdjustment >0 || $minorAdjustment <0)
            {
                $adjustmentDetails = [
                    'user' => $userinfo,
                    'minor_adjustment' => $minorAdjustment,
                    'date' => $today,
                    'bank_details' => $bankData->toArray()
                ];
                $this->storeMinorAdjustment($request, $adjustmentDetails);
            }

            \DB::commit();
            return response()->json( [

                'message' => "reconciled"

            ], 201);
        }
        catch(\Exceptions $e)
        {
            \DB::rollback();
            \Log::error($e);

            return response()->json( [
                'message' => "Something went wrong!"
            ], 500);
        }
    }

    public function storeMinorAdjustment($request, $adjustmentDetails)
    {
        $this->minorAdjustmentRepository->create([
            'bank_transaction_id' => $request->bank_transaction_id,
            'adjustments' => $adjustmentDetails['minor_adjustment']
        ]);

        $minorAdjustmentAccountExist = $this->chartAccountRepository->showChartAccountParticular($adjustmentDetails['user'][0], [860]);

        $tax_rates = $this->taxRateRepository->show();

        if($minorAdjustmentAccountExist->isEmpty()){
            $chart_account = $this->chartAccountRepository->showChartAccount();
            if($chart_account){
                $chart_account_type = $this->chartAccountRepository->showChartAccountType(['Current Liability']);
                if($chart_account_type->isNotEmpty()){
                    $chart_account_type = $chart_account_type->toArray();

                    $minorAdjustmentAccountCreated = $this->chartAccountRepository->createChartAccountParts(
                        [
                            'user_id' => trim($adjustmentDetails['user'][0]),
                            'chart_accounts_id' => $chart_account['id'],
                            'chart_accounts_type_id' => $chart_account_type[0]['id'],
                            'chart_accounts_particulars_code' => 860,
                            'chart_accounts_particulars_name' => 'Minor Adjustment',
                            'chart_accounts_particulars_description' => 'Minor Adjustment' ,
                            'chart_accounts_particulars_tax' => trim($tax_rates['id']),
                            'accounts_tax_rate_id' => trim($tax_rates['id'])
                        ]
                    );
                }
            }
        }
        $transactionSubType = '';
        if($adjustmentDetails['minor_adjustment'] <0 && $adjustmentDetails['bank_details']['direction'] == 'debit')
        {
            $transactionSubType = 'received';
        }else if($adjustmentDetails['minor_adjustment'] <0 && $adjustmentDetails['bank_details']['direction'] == 'credit')
        {
            $transactionSubType = 'spent';
        }else{
            $transactionSubType = $adjustmentDetails['bank_details']['direction'] == 'debit' ? 'spent' : 'received';
        }

        $transactionCollection = [
            'user_id' => trim($adjustmentDetails['user'][0]),
            'issue_date' => trim($request->issue_date),
            'transaction_number' => 0,
            'sub_total' => $adjustmentDetails['minor_adjustment'],
            'total_gst' => 0,
            'total_amount' => $adjustmentDetails['minor_adjustment'],
            'transaction_type' => 'minor_adjustment',
            'invoice_ref_number' =>  0,
            'status' => 'Paid',
            'transaction_sub_type' => $transactionSubType,
            'bank_account_id' => $request->account_id
        ];

        $transactions[] = [
            'user_id' => trim($adjustmentDetails['user'][0]),
            'parts_unit_price' => trim($adjustmentDetails['minor_adjustment']),
            'parts_amount' => trim($adjustmentDetails['minor_adjustment']),
            'parts_description' => 'Reconciliation adjustment',
            'parts_chart_accounts_id' => $minorAdjustmentAccountExist->isNotEmpty() ? $minorAdjustmentAccountExist[0]['id'] : $minorAdjustmentAccountCreated['id'],
            'parts_tax_rate_id' => $tax_rates['id'],
            'parts_gst_amount' => 1,
        ];

        $minorAdjustmentTransaction = $this->transactionCollectionRepository->createOrUpdateTransaction($transactionCollection, $transactions, $adjustmentDetails['user'], $request->invoice_id = 0, $type='create');
        if(!empty($minorAdjustmentTransaction) && $minorAdjustmentTransaction->id)
        {
            $reconcileTransaction = [
                'user_id' => $adjustmentDetails['user'][0],
                'bank_transaction_id' => $request->bank_transaction_id,
                'payment_id' => $minorAdjustmentTransaction->payment[0]['id'],
                'is_active' => 1,
                'is_reconciled' => $request->is_reconciled,
                'reconciled_at' => $adjustmentDetails['date']
            ];
            $reconciled = $this->storeReconcileTransaction($reconcileTransaction);
            if(!empty($reconciled)){
                $history = [
                    'user' => $adjustmentDetails['user'],
                    'collection' => $minorAdjustmentTransaction,
                    'action' => 'Reconciled',
                    'bank_transaction' => $adjustmentDetails['bank_details']
                ];

                return $this->createTransactionHistory($history);
            }
        }
    }

    public function storeReconcileTransaction($reconcileTransaction)
    {
        try{
            $reconcileTransaction = $this->reconciledModel::updateOrCreate(
                [
                    'user_id' => $reconcileTransaction['user_id'],
                    // 'bank_transaction_id' => $reconcileTransaction['bank_transaction_id'],
                    'payment_id' => $reconcileTransaction['payment_id'],
                    'is_reconciled' => 0
                ],
                [
                    'user_id' => $reconcileTransaction['user_id'],
                    'bank_transaction_id' => $reconcileTransaction['bank_transaction_id'],
                    'payment_id' => $reconcileTransaction['payment_id'],
                    'is_reconciled' => $reconcileTransaction['is_reconciled'],
                    'reconciled_at' => $reconcileTransaction['reconciled_at']
                ]
            );
            if($reconcileTransaction && $reconcileTransaction['reconciled_at'])
            {
                $discussions = $this->discussionRepository->getDiscussionIds($reconcileTransaction['user_id'], $reconcileTransaction['bank_transaction_id']);

                foreach ($discussions->pluck('id') as $discussionId)
                {
                    DiscussionPayment::updateOrCreate([
                        'discussion_id' => $discussionId,
                        'payment_id' => $reconcileTransaction['payment_id'],
                    ],
                    [
                        'discussion_id' => $discussionId,
                        'payment_id' => $reconcileTransaction['payment_id'],
                    ]
                    );
                }
            }
        }
        catch(\Exceptions $e)
        {
            \Log::error($e);
        }
    }

    public function createTransactionHistory($transaction)
    {
        if(!empty($transaction))
        {
            $date = Carbon::now()->toDateString();
            $time = Carbon::now()->toTimeString();

            $paymentDate = Carbon::createFromFormat('Y-m-d H:s:i', $transaction['bank_transaction']['post_date'])->format('d F Y');

            if($transaction['collection']['transaction_type'] == 'invoice'){
                $invoiceDesc = $transaction['collection']['status'] == 'Paid' ? 'This invoice has been fully paid.' : 'This invoice has been partly paid.';

                $transaction['description'] = 'Payment received from '. $transaction['bank_transaction']['description']. ' on '.$paymentDate.' for '.$transaction['amount'].' '.$invoiceDesc;
                $transaction['action'] = $transaction['collection']['status'];
                $transaction['invoice_number'] = $transaction['collection']['transaction_number'];
            }

            if($transaction['collection']['transaction_type'] == 'expense'){
                $invoiceDesc = $transaction['collection']['status'] == 'Paid' ? 'This invoice has been fully paid.' : 'This invoice has been partly paid.';

                $transaction['description'] = 'Payment made to '. $transaction['bank_transaction']['description']. ' on '.$paymentDate.' for '.$transaction['amount'].' '.$invoiceDesc;
                $transaction['action'] = $transaction['collection']['status'];
                $transaction['invoice_number'] = $transaction['collection']['transaction_number'];
            }

            if($transaction['collection']['transaction_type'] == 'spend_money' || $transaction['collection']['transaction_type'] == 'apprepayment'|| $transaction['collection']['transaction_type'] == 'apoverpayment'){
                $transaction['description'] = 'Debit payment to '. $transaction['bank_transaction']['description']. ' on '.$paymentDate.' for -'.$transaction['bank_transaction']['amount'];
                $transaction['action'] = 'Reconciled';
            }

            if($transaction['collection']['transaction_type'] == 'receive_money' || $transaction['collection']['transaction_type'] == 'arprepayment' || $transaction['collection']['transaction_type'] == 'aroverpayment'){
                $transaction['description'] = 'Direct Deposit payment from '. $transaction['bank_transaction']['description']. ' on '.$paymentDate.' for '.$transaction['bank_transaction']['amount'];
                $transaction['action'] = 'Reconciled';
            }

            if($transaction['collection']['transaction_type'] == 'minor_adjustment'){
                $transaction['description'] = 'Debit payment to '. $transaction['bank_transaction']['description']. ' on '.$paymentDate.' for '.$transaction['bank_transaction']['amount'];
                $transaction['action'] = 'Reconciled';
            }

            if($transaction['collection']['transaction_type'] == 'payment'){
                $transaction['description'] = 'Debit payment to '. $transaction['bank_transaction']['description']. ' on '.$paymentDate.' for '.$transaction['bank_transaction']['amount'];
                $transaction['action'] = 'Reconciled';
            }

            $transactionHistory = array(
                'user_id' => $transaction['user'][0],
                'user_name' => $transaction['user'][1],
                'invoice_id' => $transaction['collection']['id'],
                'invoice_number' => !empty($transaction['invoice_number']) ? $transaction['invoice_number'] : 0,
                'action' => $transaction['action'],
                'description' => $transaction['description'],
                'date' => $date,
                'time' => $time
            );

           return $this->invoiceHistoryRepository->store($transactionHistory);
        }
    }

    public function createOrUpdateTransaction($userInfo, $request, $collection, $amountPaid, $transactionType)
    {
        $transactionCollection = [
            'user_id' => trim($userInfo[0]),
            'client_name' => $collection->client_name,
            'issue_date' => trim($request->issue_date),
            'due_date' => Carbon::createFromFormat('d/m/Y', $collection->due_date)->format('Y-m-d'),
            'transaction_number' => 0,
            'sub_total' => $amountPaid,
            'total_gst' => 0,
            'total_amount' => $amountPaid,
            'transaction_type' => $transactionType,
            'invoice_ref_number' => 0,
            'parent_id' => $collection->id,
            // 'status' => 'Paid',
            'amount_paid' => $amountPaid,
            'bank_account_id' => $request->account_id,
            'payment_date' => trim($request->issue_date),
            'transaction_sub_type' => $collection->transaction_type == 'invoice' ? 'received' : ($collection->transaction_type == 'expense' ? 'spent' : '')
        ];

        return $this->transactionCollectionRepository->createOrUpdateTransaction($transactionCollection, $transactions = [], $userInfo, $request->invoice_id = 0, $type='create');
    }

    public function show($userId, $transactionId)
    {
        return $this->reconciledModel::where('transaction_collection_id', $transactionId)->get();
    }

    public function unReconcileTransaction($request, $userinfo)
    {
        try {
            $date = Carbon::now()->toDateString();
            $time = Carbon::now()->toTimeString();

            \DB::beginTransaction();
            $paymentIds = $this->reconciledModel::select('payment_id')->where('bank_transaction_id', $request->bank_transaction_id)->get();
            $this->reconciledModel::where('bank_transaction_id', $request->bank_transaction_id)->update(['is_reconciled' => 0, 'bank_transaction_id' => 0]);
            $this->bankTransactionsRepository->updateReconcileStatus($request->account_id, $request->bank_transaction_id, ['is_reconciled' => 0]);

            if ($paymentIds->isNotEmpty()) {
                foreach ($paymentIds as $paymentId) {
                    $deletedPayment = Payment::where('id', $paymentId['payment_id'])->first();
                    if ($deletedPayment) {

                        $updateAdjustment = TransactionCollections::where('id', $deletedPayment->reference_id)->first();
                        if ($updateAdjustment) {
                            if($updateAdjustment->transaction_type == 'minor_adjustment')
                            {
                                $deletedPayment->delete();
                                $updateAdjustment->is_active = 0;
                                $updateAdjustment->save();
                            }
                            $transactionHistory = array(
                                'user_id' => $userinfo[0],
                                'user_name' => $userinfo[1],
                                'invoice_id' => $deletedPayment->reference_id,
                                'action' => 'Unreconciled',
                                'description' => '',
                                'date' => $date,
                                'time' => $time
                            );
                            $this->invoiceHistoryRepository->store($transactionHistory);
                        }
                    }
                }

                \DB::commit();
                return response()->json( [

                    'message' => "unreconciled"

                ], 200);
            }
        }
        catch(\Exceptions $e)
        {
            \DB::rollback();
            \Log::error($e);

            return response()->json( [
                'message' => "Something went wrong!"
            ], 500);
        }
    }
}
