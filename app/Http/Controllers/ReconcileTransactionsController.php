<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Interfaces\ChartAccountRepositoryInterface;
use App\Http\Services\BankApiService;
use App\Interfaces\TaxRateRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\BankAccountsRepositoryInterface;
use App\Interfaces\TransactionCollectionRepositoryInterface;
use App\Interfaces\BankTransactionsRepositoryInterface;
use App\Interfaces\InvoiceItemRepositoryInterface;
use App\Interfaces\ReconcileTransactionRepositoryInterface;
use App\Interfaces\ReconcileDiscussionRepositoryInterface;
use App\Interfaces\InvoiceHistoryRepositoryInterface;
use App\Models\SumbExpensesClients;
use Carbon\Carbon;
use App\Helper\NumberFormat;

class ReconcileTransactionsController extends Controller
{

    private ChartAccountRepositoryInterface $chartAccountRepository;
    private TaxRateRepositoryInterface $taxRateRepository;
    private UserRepositoryInterface $userRepository;
    private BankAccountsRepositoryInterface $bankAccountsRepository;
    private TransactionCollectionRepositoryInterface $transactionCollectionRepository;
    private BankTransactionsRepositoryInterface $bankTransactionsRepository;
    private InvoiceItemRepositoryInterface $invoiceItemRepository;
    private ReconcileTransactionRepositoryInterface $reconcileTransactionRepository;
    private ReconcileDiscussionRepositoryInterface $reconcileDiscussionRepository;
    private InvoiceHistoryRepositoryInterface $invoiceHistoryRepository;


    public function __construct(
        ChartAccountRepositoryInterface $chartAccountRepository,
        BankApiService $bankApiService,
        TaxRateRepositoryInterface $taxRateRepository,
        UserRepositoryInterface $userRepository,
        BankAccountsRepositoryInterface $bankAccountsRepository,
        TransactionCollectionRepositoryInterface $transactionCollectionRepository,
        BankTransactionsRepositoryInterface $bankTransactionsRepository,
        InvoiceItemRepositoryInterface $invoiceItemRepository,
        ReconcileTransactionRepositoryInterface $reconcileTransactionRepository,
        ReconcileDiscussionRepositoryInterface $reconcileDiscussionRepository,
        InvoiceHistoryRepositoryInterface $invoiceHistoryRepository,
    )
    {
        $this->middleware('invoice_seetings');
        $this->chartAccountRepository = $chartAccountRepository;
        $this->taxRateRepository = $taxRateRepository;
        $this->userRepository = $userRepository;
        $this->bankAccountsRepository = $bankAccountsRepository;
        $this->transactionCollectionRepository = $transactionCollectionRepository;
        $this->bankTransactionsRepository = $bankTransactionsRepository;
        $this->invoiceItemRepository = $invoiceItemRepository;
        $this->reconcileTransactionRepository = $reconcileTransactionRepository;
        $this->discussionRepository = $reconcileDiscussionRepository;
        $this->invoiceHistoryRepository = $invoiceHistoryRepository;

    }

    //***********************************************
    //*
    //* Reconcile Transactions Page
    //*
    //***********************************************
    public function index(Request $request){
        $userinfo = $request->get('userinfo');

        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Banking Transactions'
        );
        $id = $request->accountID;
        $particulars = $this->chartAccountRepository->showChartAccountParticular($userinfo[0], [610, 800]);

        if($particulars->isEmpty())
        {
            $chart_account_types = $this->chartAccountRepository->showChartAccountType(['Current Asset', 'Current Liability']);

            if($chart_account_types->isNotEmpty()){
                $tax_rates = $this->taxRateRepository->show();

                $chartAccountParts = [];

                $chartAccountParts[] = [
                    'user_id' => trim($userinfo[0]),
                    'chart_accounts_id' => $chart_account_types[0]['chart_accounts_id'],
                    'chart_accounts_type_id' => $chart_account_types[0]['id'],
                    'chart_accounts_particulars_code' => 610,
                    'chart_accounts_particulars_name' => 'Account Receivable',
                    'chart_accounts_particulars_description' => 'Account Receivable' ,
                    'chart_accounts_particulars_tax' => trim($tax_rates['id']),
                    'accounts_tax_rate_id' => trim($tax_rates['id'])
                ];
                $chartAccountParts[] = [
                    'user_id' => trim($userinfo[0]),
                    'chart_accounts_id' => $chart_account_types[1]['chart_accounts_id'],
                    'chart_accounts_type_id' => $chart_account_types[1]['id'],
                    'chart_accounts_particulars_code' => 800,
                    'chart_accounts_particulars_name' => 'Accounts Payable',
                    'chart_accounts_particulars_description' => 'Accounts Payable' ,
                    'chart_accounts_particulars_tax' => trim($tax_rates['id']),
                    'accounts_tax_rate_id' => trim($tax_rates['id'])
                ];

                $this->chartAccountRepository->createOrUpdateChartAccountsInBulk($chartAccountParts);
            }
        }

        $accts = [];
//        $pagedata = array('bank_transactions' => [], 'appTransactions' => [], 'accounts' => [], 'exp_clients' => [], 'bank_accounts' => []);
        $pagedata['bank_transactions'] = [];
        $pagedata['appTransactions'] = [];
        $pagedata["accounts"] = [];
        $pagedata['exp_clients'] = [];
        $pagedata["bank_accounts"] = [];
        $filter = [];
        $itemsperpage = 10;
        if (!empty($request->input('ipp'))) { $itemsperpage = $request->input('ipp'); }
        $pagedata['ipp'] = $filter['ipp'] = $itemsperpage;

        $purl = $oriform = $request->all();
        unset($purl['ipp']);
        $pagedata['myurl'] = route('bankTransactions');
        $pagedata['ourl'] = route('bankTransactions', $purl);
        $pagedata['npurl'] = http_build_query(['ipp'=>$itemsperpage]);

        //Get Bank Accounts
        $user = $this->bankAccountsRepository->showAll($userinfo[0]);
        if(!empty($user) && !empty($user['basiq_user_id']) && $user['bankAccounts']->isNotEmpty()){
            $pagedata["bank_accounts"] = $user['bankAccounts'];

            $appTransactions = $this->transactionCollectionRepository->getTransactionsByStatus($userinfo[0], ['Unpaid','PartlyPaid'], !empty($request->bank_account_id) ? $request->bank_account_id : $user['bankAccounts'][0]['account_id'] );
            if(!empty($appTransactions)){
                $pagedata['appTransactions'] = $appTransactions->toArray();
            }

            if(!empty($request->bank_account_id)){

                $request->validate([
                    'bank_account_id' => 'required|exists:bank_accounts,account_id,basiq_user_id,'.$user['basiq_user_id'].',is_active,1',
                ]);

                $pagedata['bank_account_id'] = $request->bank_account_id;
                $filter['bank_account_id'] = $request->bank_account_id;
                $filter['orderby'] = 'id';
                $filter['isReconciled'] = 0;

                $bankTransactions = $this->bankTransactionsRepository->getTransactionsByFilter($filter);
            }
            else
            {
                $pagedata['bank_account_id'] = $user['bankAccounts'][0]['account_id'];
                $filter['bank_account_id'] = $user['bankAccounts'][0]['account_id'];
                $filter['orderby'] = 'id';
                $filter['isReconciled'] = 0;

                $bankTransactions = $this->bankTransactionsRepository->getTransactionsByFilter($filter);
            }

            $chartAccountsTypes = $this->chartAccountRepository->getChartAccountAndTypes();
            if (!empty($chartAccountsTypes)) {
                $pagedata['chart_accounts_types'] = $chartAccountsTypes->toArray();
            }

            $chartAccount = $this->chartAccountRepository->getChartAccountTypesAndParts($userinfo[0]);
            if (!empty($chartAccount)) {
                $pagedata['chart_account'] = $chartAccount->toArray();
            }

            $taxRates = $this->taxRateRepository->index();
            if (!empty($taxRates)) {
                $pagedata['tax_rates'] = $taxRates->toArray();
            }

            //Get Invoice Items
            $items = $this->invoiceItemRepository->index($userinfo[0]);
            if (!empty($items)) {
                $pagedata['invoice_items'] = $items->toArray();
            }

            $allrequest = $request->all();
            $pagedata['bank_transactions'] = $bankTransactions;
            $pfirst = $allrequest; $pfirst['page'] = 1;
            $pprev = $allrequest; $pprev['page'] = $bankTransactions['current_page']-1;
            $pnext = $allrequest; $pnext['page'] = $bankTransactions['current_page']+1;
            $plast = $allrequest; $plast['page'] = $bankTransactions['last_page'];
            $pagedata['paging'] = [
                'current' => url()->current().'?'.http_build_query($allrequest),
                'starpage' => url()->current().'?'.http_build_query($pfirst),
                'first' => ($bankTransactions['current_page'] == 1) ? '' : url()->current().'?'.http_build_query($pfirst),
                'prev' => ($bankTransactions['current_page'] == 1) ? '' : url()->current().'?'.http_build_query($pprev),
                'now' => 'Page '.$bankTransactions['current_page']." of ".$bankTransactions['last_page'],
                'next' => ($bankTransactions['current_page'] >= $bankTransactions['last_page']) ? '' : url()->current().'?'.http_build_query($pnext),
                'last' => ($bankTransactions['current_page'] >= $bankTransactions['last_page']) ? '' : url()->current().'?'.http_build_query($plast),
            ];
        }

        $get_expclients = SumbExpensesClients::where('user_id', $userinfo[0])->orderBy('client_name')->get();
        if (!empty($get_expclients)) {
            $pagedata['exp_clients'] = $get_expclients->toArray();
        }

        return view('bank.reconcile-transactions',$pagedata);
    }

    //***********************************************
    //*
    //* Create Or Update Transactions
    //*
    //***********************************************

    public function storeTransaction(Request $request)
    {
        if ($request->ajax())
        {
            $userinfo = $request->get('userinfo');
            $pagedata = array(
                'userinfo'=>$userinfo,
                'pagetitle' => 'Banking Transactions'
            );

            $transactions = [];
            $id = $request->transaction_index;
            $totalRows = json_decode($request->input('reconcile_transaction_part_total_count_'.$id), true);

            $transactionType = '';$transactionNumber = 0;
            if($request->input('transaction_type_'.$id) == 'credit')
            {
                $transactionType = "receive_money";
                if(trim($request->input('payment_option_'.$id)) == 'pre_payment')
                {
                    $transactionType = "arprepayment";
                    $transactionNumber = $this->transactionCollectionRepository->getTransaction(['transaction_type' => ['invoice','arprepayment']], $userinfo[0]);
                    $transactionNumber+=1;
                }else if(trim($request->input('payment_option_'.$id)) == 'over_payment')
                {
                    $transactionType = "aroverpayment";
                }
                else{
                    $transactionType = "receive_money";
                }
            }
            else if($request->input('transaction_type_'.$id) == 'debit')
            {
                $transactionType = "spend_money";
                if(trim($request->input('payment_option_'.$id)) == 'pre_payment')
                {
                    $transactionType = "apprepayment";
                }else if(trim($request->input('payment_option_'.$id)) == 'over_payment')
                {
                    $transactionType = "apoverpayment";
                }else{
                    $transactionType = "spend_money";
                }
            }

            $transactionCollection =
                [
                    'user_id' => trim($userinfo[0]),
                    'client_name' => trim($request->input('client_name_'.$id)),
                    'issue_date' => Carbon::createFromFormat('d/m/yy', $request->input('issue_date_'.$id))->format('Y-m-d'),
                    'transaction_number' => $transactionNumber,
                    'default_tax' => trim($request->input('invoice_default_tax_'.$id)),
                    'sub_total' => NumberFormat::string_replace(trim($request->input('sub_total_'.$id))),
                    'total_gst' =>  $request->input('total_gst_'.$id) ? NumberFormat::string_replace(trim($request->input('total_gst_'.$id))) : 0 ,
                    'total_amount' => NumberFormat::string_replace(trim($request->input('total_amount_'.$id))),
                    'transaction_type' =>  $transactionType,
                    'invoice_ref_number' => trim($request->invoice_ref_number) ? : 0,
                    'payment_option' => trim($request->input('payment_option_'.$id)),
                    'bank_account_id' => $request->account_id,
                ];
            $transactions = [];
            if(count($totalRows) >= 0)
            {
                foreach($totalRows as $rowId)
                {
                    $request->validate([
                        'invoice_parts_chart_accounts_parts_id_'.$id.'_'.$rowId => 'required',
                        'invoice_parts_tax_rate_id_'.$id.'_'.$rowId => 'required',
                    ],
                    [
                        'invoice_parts_chart_accounts_parts_id_'.$id.'_'.$rowId.'.required'=> 'The account field is required', // custom message
                        'invoice_parts_tax_rate_id_'.$id.'_'.$rowId.'.required'=> 'The tax rate field is required' // custom message
                    ]
                    );

                    $transactions[] = [
                        'user_id' => $userinfo[0],
                        'parts_quantity' => trim($request->input('invoice_parts_quantity_'.$id.'_'.$rowId) ? $request->input('invoice_parts_quantity_'.$id.'_'.$rowId) : 0 ),
                        'parts_description' => trim($request->input('invoice_parts_description_'.$id.'_'.$rowId)),
                        'parts_unit_price' => NumberFormat::string_replace(trim($request->input('invoice_parts_unit_price_'.$id.'_'.$rowId))),
                        'parts_amount' => NumberFormat::string_replace(trim($request->input('invoice_parts_amount_'.$id.'_'.$rowId))),
                        'parts_code' => (!empty($request->input('item_part_code_'.$id.'_'.$rowId)) ? trim($request-> input('item_part_code_'.$id.'_'.$rowId)) : ''),
                        'parts_name' => (!empty($request->input('item_part_name_'.$id.'_'.$rowId)) ? trim($request->input('item_part_name_'.$id.'_'.$rowId)) : ''),
                        'parts_chart_accounts_id' => trim($request->input('invoice_parts_chart_accounts_parts_id_'.$id.'_'.$rowId)),
                        'parts_tax_rate_id' => trim($request->input('invoice_parts_tax_rate_id_'.$id.'_'.$rowId)),
                    ];
                }
            }

            $response = $this->transactionCollectionRepository->createOrUpdateTransaction($transactionCollection, $transactions, $userinfo, $request->invoice_id = 0, 'create');
            if(!empty($response))
            {
                return response()->json( [

                    'data' => $response

                ], 201);
            }

            return response()->json( [

                'message' => "Something went wrong!"

            ], 500);
        }
        return response()->json( [

            'message' => "Something went wrong!"

        ], 500);
    }


    //***********************************************
    //*
    //* Create and Reconcile Transaction
    //*
    //***********************************************

    public function createAndReconcileTransaction(Request $request)
    {
        $userinfo = $request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Reconcile Bank Transactions'
        );

        $transactionType = '';
        if($request->transaction_type == 'credit')
        {
            $transactionType = "receive_money";
        }
        else if($request->transaction_type == 'debit')
        {
            $transactionType = "spend_money";
        }
        $transactionCollection = [
            'user_id' => trim($userinfo[0]),
            'client_name' => trim($request->client_name),
            'issue_date' => $request->issue_date,
            'transaction_number' => 0,
            'default_tax' => trim($request->default_tax),
            'sub_total' => NumberFormat::string_replace(trim($request->sub_total)),
            'total_gst' =>  $request->total_gst ? NumberFormat::string_replace(trim($request->total_gst)) : 0 ,
            'total_amount' => NumberFormat::string_replace(trim($request->total_amount)),
            'transaction_type' =>  $transactionType,
            'invoice_ref_number' => trim($request->invoice_ref_number) ? $request->invoice_ref_number : 0,
            'payment_option' => trim($request->payment_option),
            'bank_account_id' => $request->account_id,
        ];

        $transactions[] = [
            "user_id" => $userinfo[0],
            'parts_quantity' => 1,
            'parts_description' => trim($request->description),
            'parts_unit_price' => NumberFormat::string_replace(trim($request->unit_price)),
            'parts_amount' => NumberFormat::string_replace(trim($request->sub_total)),
            'parts_chart_accounts_id' => trim($request->tax_rate_id),
            'parts_tax_rate_id' => trim($request->chart_accounts_parts_id),
        ];
        $transaction = $this->transactionCollectionRepository->createOrUpdateTransaction($transactionCollection, $transactions, $userinfo, $request->invoice_id = 0, 'create');
        if(!empty($transaction))
        {
            $request->request->set('transaction_collection_id', $transaction->id);
            $request->request->set('transaction_money_'.$transaction->id, NumberFormat::string_replace(trim($request->total_amount)));
            return $this->reconcileTransaction($request);
        }
    }

    public function reconcileTransaction(Request $request)
    {
        $userinfo = $request->get('userinfo');
        $pagedata = array(
            'userinfo' => $userinfo,
            'pagetitle' => 'Banking Transactions'
        );

        $request->validate([
            'bank_transaction_id' => 'required|exists:bank_transactions,id,account_id,'.$request->account_id,
            'account_id' => 'required|exists:bank_accounts,account_id,basiq_user_id,'.$userinfo[9].',is_active,1',
        ]);

       return $this->reconcileTransactionRepository->reconcileTransaction($request, $userinfo);

    }

    //***********************************************
    //*
    //* Reconcile Transaction
    //*
    //***********************************************

    public function matchTransaction(Request $request)
    {
        $userinfo = $request->get('userinfo');

        $filter = array(
            'transaction_type' => $request->transaction_type,
            'status' => array('Unpaid','PartlyPaid'),
            'bank_account_id' => $request->bank_account_id
        );
        $transactions = $this->transactionCollectionRepository->matchTransactions($filter, $userinfo[0]);

        if($transactions->isNotEmpty())
        {
            return response()->json([
                'transactions' => $transactions->toArray(),
            ],200);
        }
    }

    public function accountTransactions(Request $request)
    {
        $userinfo = $request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Account Transactions'
        );

        $pagedata['transactions'] = [];
        $pagedata["bank_accounts"] = [];
        $pagedata['bank_account_id'] = '';
        $filter = $request->query();

        $itemsperpage = 10;
        if (!empty($request->input('ipp'))) { $itemsperpage = $request->input('ipp'); }
        $pagedata['ipp'] =  $filter['ipp'] = $itemsperpage;

        $purl = $oriform = $request->all();
        unset($purl['ipp']);
        $pagedata['myurl'] = route('account/transactions');
        $pagedata['ourl'] = route('account/transactions', $purl);
        $pagedata['npurl'] = http_build_query(['ipp'=>$itemsperpage]);

        $user = $this->bankAccountsRepository->showAll($userinfo[0]);
        if(!empty($user['basiq_user_id']) && $user['bankAccounts']->isNotEmpty()){
            $pagedata["bank_accounts"] = $user['bankAccounts'];

            if($request->bank_account_id || $request->search_desc_class || $request->start_date || $request->end_date || $request->min_amt || $request->max_amt){

                $request->validate([
                    'bank_account_id' => 'required|exists:bank_accounts,account_id,basiq_user_id,'.$user['basiq_user_id'].',is_active,1',
                ]);

                $accountTransactions = $this->transactionCollectionRepository->accountTransactions($userinfo[0], $filter);
                $pagedata['bank_account_id'] = $request->bank_account_id;
                $pagedata['search_desc_class'] = $request->search_desc_class;
                $pagedata['start_date'] = $request->start_date;
                $pagedata['end_date'] = $request->end_date;
                $pagedata['min_amt'] = $request->min_amt;
                $pagedata['max_amt'] = $request->max_amt;
            }
            else
            {
                $pagedata['bank_account_id'] = $user['bankAccounts'][0]['account_id'];
                $filter['bank_account_id'] = $user['bankAccounts'][0]['account_id'];
                $filter['orderby'] = 'id';

                $accountTransactions = $this->transactionCollectionRepository->accountTransactions($userinfo[0], $filter);
            }

            $allrequest = $request->all();
            $pagedata['bank_transactions'] = $accountTransactions = $accountTransactions->toArray();

            $pfirst = $allrequest; $pfirst['page'] = 1;
            $pprev = $allrequest; $pprev['page'] = $accountTransactions['current_page']-1;
            $pnext = $allrequest; $pnext['page'] = $accountTransactions['current_page']+1;
            $plast = $allrequest; $plast['page'] = $accountTransactions['last_page'];
            $pagedata['paging'] = [
                'current' => url()->current().'?'.http_build_query($allrequest),
                'starpage' => url()->current().'?'.http_build_query($pfirst),
                'first' => ($accountTransactions['current_page'] == 1) ? '' : url()->current().'?'.http_build_query($pfirst),
                'prev' => ($accountTransactions['current_page'] == 1) ? '' : url()->current().'?'.http_build_query($pprev),
                'now' => 'Page '.$accountTransactions['current_page']." of ".$accountTransactions['last_page'],
                'next' => ($accountTransactions['current_page'] >= $accountTransactions['last_page']) ? '' : url()->current().'?'.http_build_query($pnext),
                'last' => ($accountTransactions['current_page'] >= $accountTransactions['last_page']) ? '' : url()->current().'?'.http_build_query($plast),
            ];
        }

        return view('bank.account-transactions-list',$pagedata);
    }

    public function showAccountTransaction(Request $request, $accountId, $transactionId)
    {
        $userinfo = $request->get('userinfo');
        $pagedata = array(
            'userinfo' => $userinfo,
            'pagetitle' => 'Account Transactions'
        );
        $request->merge([ 'account_id' => $accountId ]);
        $request->merge([ 'transaction_id' => $transactionId ]);
        $filter = ['ipp' => 1, 'bank_account_id' => $request->account_id, 'reconcileId' => $request->transaction_id];

        $request->validate([
            'transaction_id' => 'required|exists:transaction_collections,id,user_id,'.$userinfo[0].',is_active,1',
            'account_id' => 'required|exists:bank_accounts,account_id,basiq_user_id,'.$userinfo[9].',is_active,1',
        ]);

        $pagedata['bank_account_id'] = $request->account_id;
        $pagedata['transaction_id'] = $request->transaction_id;
        $pagedata['account_transaction'] = $this->transactionCollectionRepository->showReconcileTransaction($userinfo[0], $filter);
        if(empty($pagedata['account_transaction']))
        {
            return redirect('account/transactions?bank_account_id='.$request->account_id);
        }
        return view('bank.account-transaction', $pagedata);
    }

    public function unReconcileTransaction(Request $request, $accountId, $bankTransactionId)
    {
        $userinfo = $request->get('userinfo');

        $request->merge([ 'account_id' => $accountId ]);
//        $request->merge([ 'transaction_id' => $transactionId ]);
        $request->merge([ 'bank_transaction_id' => $bankTransactionId ]);

        $request->validate([
            'bank_transaction_id' => 'required|exists:bank_transactions,id,basiq_user_id,'.$userinfo[9].',account_id,'.$accountId,
//            'transaction_id' => 'required|exists:transaction_collections,id,user_id,'.$userinfo[0].',is_active,1',
//            'account_id' => 'required|exists:bank_accounts,account_id,basiq_user_id,'.$userinfo[9].',is_active,1',

            ]);
        return $this->reconcileTransactionRepository->unReconcileTransaction($request, $userinfo);

    }
}
