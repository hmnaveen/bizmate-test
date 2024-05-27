<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\SignupMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use URL;
use DB;
use App\Models\SumbExpensesClients;
use App\Models\TransactionCollections;
use App\Models\Transactions;
use Illuminate\Database\QueryException;
use App\Helper\NumberFormat;
use App\Interfaces\TransactionCollectionRepositoryInterface;
use App\Http\Services\BankApiService;
use App\Interfaces\BankRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\ChartAccountRepositoryInterface;
use App\Interfaces\TaxRateRepositoryInterface;
use App\Interfaces\InvoiceItemRepositoryInterface;
use App\Interfaces\ReconcileTransactionRepositoryInterface;


class BankAccountsController extends Controller {

    private TransactionCollectionRepositoryInterface $transactionCollectionRepository;
    private BankRepositoryInterface $bankRepository;
    private UserRepositoryInterface $userRepositoryInterface;
    private ChartAccountRepositoryInterface $chartAccountRepository;
    private TaxRateRepositoryInterface $taxRateRepositoryInterface;
    private InvoiceItemRepositoryInterface $invoiceItemRepositoryInterface;
    private ReconcileTransactionRepositoryInterface $reconcileTransactionRepositoryInterface;

    public function __construct(
        TransactionCollectionRepositoryInterface $transactionCollectionRepository,
        BankRepositoryInterface $bankRepository,
        UserRepositoryInterface $userRepositoryInterface,
        ChartAccountRepositoryInterface $chartAccountRepository,
        TaxRateRepositoryInterface $taxRateRepositoryInterface,
        InvoiceItemRepositoryInterface $invoiceItemRepositoryInterface,
        ReconcileTransactionRepositoryInterface $reconcileTransactionRepositoryInterface,
        BankApiService $bankApiService
    )
    {
        $this->middleware('invoice_seetings');
        $this->transactionCollectionRepository = $transactionCollectionRepository;
        $this->bankRepository = $bankRepository;
        $this->userRepositoryInterface = $userRepositoryInterface;
        $this->chartAccountRepository = $chartAccountRepository;
        $this->taxRateRepositoryInterface = $taxRateRepositoryInterface;
        $this->invoiceItemRepositoryInterface = $invoiceItemRepositoryInterface;
        $this->reconcileTransactionRepositoryInterface = $reconcileTransactionRepositoryInterface;
        $this->bankApiService = $bankApiService;
    }

    //***********************************************
    //*
    //* Bank Accounts Page
    //*
    //***********************************************
    public function index(Request $request) {
        $userinfo = $request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Banking'
        );
        $pagedata["type"] = "addBank";
        $accts = [];
        $pagedata["accounts"] = [];

        $accounts = $this->bankRepository->showBankAccounts($userinfo[0]);

        if($accounts->isEmpty())
        {
            $get_user = $this->userRepositoryInterface->show($userinfo[0]);
            if(!empty($get_user['bank_user_id'])){
                $accts = $this->getAllAccounts($get_user['bank_user_id']);
            }

            if(!empty($accts) && count($accts) > 0){
                $accounts = [];
                foreach($accts['data'] as $acct){
                    $accounts[] = array(
                        'user_id' => $userinfo[0],
                        'bank_user_id' => $get_user['bank_user_id'],
                        'account_type' => $acct['type'],
                        'account_id' => $acct['id'],
                        'account_number' => $acct['accountNo'],
                        'account_name' => $acct['name'],
                        'currency' => $acct['currency'],
                        'balance' => $acct['balance'],
                        'avaialable_funds' => $acct['availableFunds'],
                        'instituition' => $acct['institution'],
                        'credit_limit' => (!empty($acct['creditLimit']) ? $acct['creditLimit'] : NULL),
                        'class' => json_encode($acct['class']),
                        'transaction_intervals' => json_encode($acct['transactionIntervals']),
                        'account_holder' => $acct['accountHolder'],
                        'connection_id' => $acct['connection'],
                        'status' => $acct['status'],
                        'links' => json_encode($acct['links']),
                        'bank_last_updated' => Carbon::createFromTimeString($acct['lastUpdated']),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    );
                }

                $response = $this->bankRepository->createOrUpdateBankAcounts($accounts);

                $accounts = $this->bankRepository->showBankAccounts($userinfo[0]);

                $pagedata["accounts"] = !empty($accounts) ? $accounts->toArray() : [];
            }
        }else{
            $pagedata["accounts"] = !empty($accounts) ? $accounts->toArray() : [];

        }



        return view('banking.bankaccounts', $pagedata);
    }

    //***********************************************
    //*
    //* Add Bank Accounts
    //*
    //***********************************************
    public function addBankAccount(Request $request) {

        $userinfo = $request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Banking'
        );
        //to get user-id
        $get_user = $this->userRepositoryInterface->show($userinfo[0]);

        if(!empty($get_user['bank_user_id'])){
            $userID = $get_user['bank_user_id'];
        }else{

            $userID = $this->createUser();
            $user_data_array = array("bank_user_id" => $userID);
            $get_user->update($user_data_array);
        }


        $clientToken = $this->generateClientToken($userID);
        $token = "https://consent.basiq.io/home?token=$clientToken&action=connect";

        $pagedata["token"] = $token;
        $pagedata["type"] = "consentUI";

        return view('banking.banksearch',$pagedata);
     }

    public function createUser(){

        $token = $this->generateToken();

        $params['headers'] = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' =>  "Bearer " .$token,
        ];

        $url = config('services.bank-api.uri').'users';
        $params['json'] = ["email" => "gavin.belson@hooli.com", "firstName" => "Gavin Belson"];

        $user = $this->bankApiService->createUser($url, $params);

        if(!empty($user))
        {
            return $user["id"];
        }
    }

    public function generateToken(){

        $params['headers'] = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => config('services.bank-api.token'),
            'basiq-version' => '3.0',
        ];
        // $params['form_params'] = array('scope'=> 'SERVER_ACCESS');
        $url = config('services.bank-api.uri').'token';

        $reponse = $this->bankApiService->getToken($url, $params);

        return $reponse["access_token"];
    }

    public function generateClientToken($userId){

        $params['headers'] = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => config('services.bank-api.token'),
            'basiq-version' => '3.0',
        ];

        $url = config('services.bank-api.uri').'token';
        $params['form_params'] = array('scope'=> 'CLIENT_ACCESS', 'userId' => $userId);

        $data = $this->bankApiService->getToken($url, $params);
        return $data["access_token"];
    }

    //***********************************************
    //*
    //* Bank Transactions Page
    //*
    //***********************************************
    public function transactions(Request $request){
        $userinfo = $request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Banking Transactions'
        );
        //$id = $request->accountID;
        // $userinfo =$request->get('userinfo');
        $accts = [];
        $pagedata['transactions'] = [];
        $pagedata["accounts"] = [];
        $pagedata['bankAccount'] = '';

        $filter = $request->query();

        $itemsperpage = 10;
        if (!empty($request->input('ipp'))) { $itemsperpage = $request->input('ipp'); }
        $pagedata['ipp'] =  $filter['ipp'] = $itemsperpage;

        $purl = $oriform = $request->all();
        unset($purl['ipp']);
        $pagedata['myurl'] = route('bankTransactions');
        $pagedata['ourl'] = route('bankTransactions', $purl);
        $pagedata['npurl'] = http_build_query(['ipp'=>$itemsperpage]);

        //to get user-id
        $get_user = $this->userRepositoryInterface->show($userinfo[0]);
        if(!empty($get_user['bank_user_id'])){

            $bankUserId = $get_user['bank_user_id'];
            $accts = $this->bankRepository->showBankAccounts($userinfo[0]);
            $pagedata["accounts"] = $accts;

            if(!empty($accts) && count($accts) > 0){

                if($request->bankAccount || $request->search_desc_class || $request->start_date || $request->end_date || $request->min_amt || $request->max_amt){

                    $request->validate([
                        'bankAccount' => 'required|exists:user_bank_accounts,account_id,user_id,'.$userinfo[0].',is_active,1',
                    ]);

                    $bankAccount = $request->bankAccount;
                    $transactionData = $this->bankRepository->getTransactionsByFilter($filter, $userinfo[0]);

                    $pagedata['bankAccount'] = $bankAccount;
                    $pagedata['search_desc_class'] = $request->search_desc_class;
                    $pagedata['start_date'] = $request->start_date;
                    $pagedata['end_date'] = $request->end_date;
                    $pagedata['min_amt'] = $request->min_amt;
                    $pagedata['max_amt'] = $request->max_amt;
                }
                else
                {
                    $userAccountTransactions = $this->bankRepository->showBankTransactions($userinfo[0]);
                    if($userAccountTransactions < 1){
                        $overallTransactions = $this->getTransactions($bankUserId);
                        if(!empty($overallTransactions)){

                            $this->bankRepository->createOrUpdateBankTransactions($overallTransactions['data'], $bankUserId, $userinfo[0]);

                            $pagedata['bankAccount'] = $accts[0]['account_id'];
                            $filter['bankAccount'] = $accts[0]['account_id'];
                            $filter['orderby'] = 'id';

                            $transactionData = $this->bankRepository->getTransactionsByFilter($filter, $userinfo[0]);
                        }
                    }else
                    {
                        $pagedata['bankAccount'] = $accts[0]['account_id'];

                        $filter['bankAccount'] = $accts[0]['account_id'];
                        $filter['orderby'] = 'id';

                        $transactionData = $this->bankRepository->getTransactionsByFilter($filter, $userinfo[0]);

                    }
                }

                $allrequest = $request->all();
                $pagedata['transactions'] = $transactionData;
                $pfirst = $allrequest; $pfirst['page'] = 1;
                $pprev = $allrequest; $pprev['page'] = $transactionData['current_page']-1;
                $pnext = $allrequest; $pnext['page'] = $transactionData['current_page']+1;
                $plast = $allrequest; $plast['page'] = $transactionData['last_page'];
                $pagedata['paging'] = [
                    'current' => url()->current().'?'.http_build_query($allrequest),
                    'starpage' => url()->current().'?'.http_build_query($pfirst),
                    'first' => ($transactionData['current_page'] == 1) ? '' : url()->current().'?'.http_build_query($pfirst),
                    'prev' => ($transactionData['current_page'] == 1) ? '' : url()->current().'?'.http_build_query($pprev),
                    'now' => 'Page '.$transactionData['current_page']." of ".$transactionData['last_page'],
                    'next' => ($transactionData['current_page'] >= $transactionData['last_page']) ? '' : url()->current().'?'.http_build_query($pnext),
                    'last' => ($transactionData['current_page'] >= $transactionData['last_page']) ? '' : url()->current().'?'.http_build_query($plast),
                ];
            }
        }

        return view('banking.banktransactions', $pagedata);
    }

    public function getTransactions($userID){
        $token = $this->generateToken();

        $params['headers'] = [
            'Authorization' => "Bearer " .$token,
        ];

        $url = config('services.bank-api.uri').'users/'.$userID.'/transactions';

        $transactions = $this->bankApiService->getBankTransactionsOrAcounts($url, $params);

        return $transactions;
    }

    public function getAllAccounts($userId){

        $token = $this->generateToken();

        $params['headers'] = [
            "Authorization" => "Bearer " .$token,
            "Accept : application/json"
        ];

        $url = config('services.bank-api.uri').'users/'.$userId.'/accounts';

        return $data = $this->bankApiService->getBankTransactionsOrAcounts($url, $params);

    }


    //***********************************************
    //*
    //* Reconcile Transactions Page
    //*
    //***********************************************
    public function reconcileTransactions(Request $request){
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
                $tax_rates = $this->taxRateRepositoryInterface->show();

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
        $pagedata['transactions'] = [];
        $pagedata['appTransactions'] = [];
        $pagedata["accounts"] = [];
        $pagedata['exp_clients'] = [];

        $filter = [];

        $itemsperpage = 10;
        if (!empty($request->input('ipp'))) { $itemsperpage = $request->input('ipp'); }
        $pagedata['ipp'] = $filter['ipp'] = $itemsperpage;

        $purl = $oriform = $request->all();
        unset($purl['ipp']);
        $pagedata['myurl'] = route('bankTransactions');
        $pagedata['ourl'] = route('bankTransactions', $purl);
        $pagedata['npurl'] = http_build_query(['ipp'=>$itemsperpage]);

        //to get user-id
        $get_user = $this->userRepositoryInterface->show($userinfo[0]);

        if(!empty($get_user['basiq_user_id'])){

            $bankUserId = $get_user['basiq_user_id'];

            //Get Bank Accounts
            $accts = $this->bankRepository->showBankAccounts($userinfo[0]);
            $pagedata["accounts"] = $accts;

            $appTransactions = $this->transactionCollectionRepository->getTransactionsByStatus($userinfo[0], ['Unpaid','PartlyPaid']);

            if(!empty($appTransactions)){
                $pagedata['appTransactions'] = $appTransactions->toArray();
            }

            if(count($accts) > 0){
                if(empty($id))
                {
                    $userAccountTransactions = $this->bankRepository->showBankTransactions($userinfo[0]);
                    if($userAccountTransactions < 1){
                        $overallTransactions = $this->getTransactions($bankUserId);
                        if(!empty($overallTransactions)){

                            $this->bankRepository->createOrUpdateBankTransactions($overallTransactions['data'], $bankUserId, $userinfo[0]);

                            $pagedata['id'] = $accts[0]['account_id'];
                            $filter['bankAccount'] = $accts[0]['account_id'];
                            $filter['orderby'] = 'id';
                            $filter['isReconciled'] = 0;

                            $transactionData = $this->bankRepository->getTransactionsByFilter($filter, $userinfo[0]);
                        }
                    }else{

                        $pagedata['id'] = $accts[0]['account_id'];

                        $filter['bankAccount'] = $accts[0]['account_id'];
                        $filter['orderby'] = 'id';
                        $filter['isReconciled'] = 0;

                        $transactionData = $this->bankRepository->getTransactionsByFilter($filter, $userinfo[0]);

                    }
                }
                else{

                    $request->validate([
                        'accountID' => 'required|exists:user_bank_accounts,account_id,user_id,'.$userinfo[0].',is_active,1',
                    ]);

                    $pagedata['id'] = $id;

                    $filter['bankAccount'] = $id;
                    $filter['orderby'] = 'id';
                    $filter['isReconciled'] = 0;

                    $transactionData = $this->bankRepository->getTransactionsByFilter($filter, $userinfo[0]);
                }

                $chart_accounts_types = $this->chartAccountRepository->getChartAccountAndTypes();
                if (!empty($chart_accounts_types)) {
                    $pagedata['chart_accounts_types'] = $chart_accounts_types->toArray();
                }

                $chart_account = $this->chartAccountRepository->getChartAccountTypesAndParts($userinfo[0]);

                if (!empty($chart_account)) {
                    $pagedata['chart_account'] = $chart_account->toArray();
                }

                $tax_rates = $this->taxRateRepositoryInterface->index();
                if (!empty($tax_rates)) {
                    $pagedata['tax_rates'] = $tax_rates->toArray();
                }

                //Get Invoice Items
                $get_items = $this->invoiceItemRepositoryInterface->index($userinfo[0]);

                if (!empty($get_items)) {
                    $pagedata['invoice_items'] = $get_items->toArray();
                }

                $allrequest = $request->all();
                $pagedata['transactions'] = $transactionData;
                $pfirst = $allrequest; $pfirst['page'] = 1;
                $pprev = $allrequest; $pprev['page'] = $transactionData['current_page']-1;
                $pnext = $allrequest; $pnext['page'] = $transactionData['current_page']+1;
                $plast = $allrequest; $plast['page'] = $transactionData['last_page'];
                $pagedata['paging'] = [
                    'current' => url()->current().'?'.http_build_query($allrequest),
                    'starpage' => url()->current().'?'.http_build_query($pfirst),
                    'first' => ($transactionData['current_page'] == 1) ? '' : url()->current().'?'.http_build_query($pfirst),
                    'prev' => ($transactionData['current_page'] == 1) ? '' : url()->current().'?'.http_build_query($pprev),
                    'now' => 'Page '.$transactionData['current_page']." of ".$transactionData['last_page'],
                    'next' => ($transactionData['current_page'] >= $transactionData['last_page']) ? '' : url()->current().'?'.http_build_query($pnext),
                    'last' => ($transactionData['current_page'] >= $transactionData['last_page']) ? '' : url()->current().'?'.http_build_query($plast),
                ];
            }
        }
        $get_expclients = SumbExpensesClients::where('user_id', $userinfo[0])->orderBy('client_name')->get();
        if (!empty($get_expclients)) {
            $pagedata['exp_clients'] = $get_expclients->toArray();
        }

        return view('banking.reconcileTransactions',$pagedata);
    }

    public function getReconcileMatches(Request $request){

        if ($request->ajax())
        {
            $userinfo = $request->get('userinfo');
            $pagedata = array(
                'userinfo'=>$userinfo,
                'pagetitle' => 'Banking Transactions'
            );
            $request->accountID;
            $amount = $request->amount;

            $transactionData = TransactionCollections::where('user_id', $userinfo[0])->where('is_active', 1)->where(DB::raw('ABS(total_amount)'),$amount)->get();

            if(!empty($transactionData)){
                $response = [
                    'status' => 'success',
                    'err' => '',
                    'data' => $transactionData
                ];
                echo json_encode($response);
            }
            else{
                $response = [
                    'status' => 'error',
                    'err' => 'No items found',
                    'data' => ''
                ];
                echo json_encode($response);
            }
        }
        else
        {
            $response = [
                'status' => 'error',
                'err' => 'Something went wrong',
                'data' => ''
            ];
            echo json_encode($response);
        }
    }

    public function reconcileTransaction(Request $request)
    {
        $userinfo = $request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Banking Transactions'
        );

        $request->validate([
            'bank_transaction_id' => 'required|exists:user_bank_transactions,id,user_id,'.$userinfo[0].',account_id,'.$request->account_id,
            'account_id' => 'required|exists:user_bank_accounts,account_id,user_id,'.$userinfo[0].',is_active,1',
        ]);

        $this->reconcileTransactionRepositoryInterface->reconcileTransaction($request, $userinfo);

    }

    public function saveTransaction(Request $request)
    {

        if ($request->ajax())
        {
            $userinfo = $request->get('userinfo');
            $pagedata = array(
                'userinfo'=>$userinfo,
                'pagetitle' => 'Banking Transactions'
            );


            $id = $request->transaction_index;
            $total_rows = json_decode($request->input('reconcile_transaction_part_total_count_'.$id), true);

            $transaction_type = '';
            if($request->input('transaction_type_'.$id) == 'credit')
            {
                $transaction_type = "receive_money";
            }
            else if($request->input('transaction_type_'.$id) == 'debit')
            {
                $transaction_type = "spend_money";
            }

            $transactionCollection =
                [
                    'user_id' => trim($userinfo[0]),
                    'client_name' => trim($request->input('client_name_'.$id)),
                    'issue_date' => Carbon::createFromFormat('d/m/yy', $request->input('issue_date_'.$id))->format('Y-m-d'),
                    'transaction_number' => 0,
                    'default_tax' => trim($request->input('invoice_default_tax_'.$id)),
                    'sub_total' => NumberFormat::string_replace(trim($request->input('sub_total_'.$id))),
                    'total_gst' =>  $request->input('total_gst_'.$id) ? NumberFormat::string_replace(trim($request->input('total_gst_'.$id))) : 0 ,
                    'total_amount' => NumberFormat::string_replace(trim($request->input('total_amount_'.$id))),
                    'transaction_type' =>  $transaction_type,
                    'invoice_ref_number' => trim($request->invoice_ref_number) ? : 0,
                    'payment_option' => trim($request->input('payment_option_'.$id)),
                ];

            $transactions = [];
            if(count($total_rows) >= 0)
            {
                foreach($total_rows as $row_id)
                {
                    $validatedData = $request->validate([
                        'invoice_parts_chart_accounts_parts_id_'.$id.'_'.$row_id => 'required',
                        'invoice_parts_tax_rate_id_'.$id.'_'.$row_id => 'required',
                    ],
                    [
                        'invoice_parts_chart_accounts_parts_id_'.$id.'_'.$row_id.'.required'=> 'The account field is required', // custom message
                        'invoice_parts_tax_rate_id_'.$id.'_'.$row_id.'.required'=> 'The tax rate field is required' // custom message
                    ]
                    );

                    $transactions[] = [
                        "user_id" => $userinfo[0],
                        'parts_quantity' => trim($request->input('invoice_parts_quantity_'.$id.'_'.$row_id) ? $request->input('invoice_parts_quantity_'.$id.'_'.$row_id) : 0 ),
                        'parts_description' => trim($request->input('invoice_parts_description_'.$id.'_'.$row_id)),
                        'parts_unit_price' => NumberFormat::string_replace(trim($request->input('invoice_parts_unit_price_'.$id.'_'.$row_id))),
                        'parts_amount' => NumberFormat::string_replace(trim($request->input('invoice_parts_amount_'.$id.'_'.$row_id))),
                        'parts_code' => (!empty($request->input('invoice_parts_code_'.$id.'_'.$row_id)) ? $request-> input('invoice_parts_code_'.$id.'_'.$row_id) : $request->input('invoice_parts_name_'.$id.'_'.$row_id)),
                        'parts_name' => trim($request->input('invoice_parts_name_'.$id.'_'.$row_id)),
                        'parts_chart_accounts_id' => trim($request->input('invoice_parts_chart_accounts_parts_id_'.$id.'_'.$row_id)),
                        'parts_tax_rate_id' => trim($request->input('invoice_parts_tax_rate_id_'.$id.'_'.$row_id)),
                    ];
                }
            }

            $response = $this->transactionCollectionRepository->createOrUpdateTransaction($transactionCollection, $transactions, $userinfo, $request->invoice_id = 0, 'create');

            if(!empty($response))
            {
                return response()->json( [

                    'status' => 'success',
                    'data' => $response

                ], 201);
            }

            return response()->json( [

                'message' => "Something went wrong!"

            ], 500);

        }
    }

    public function getMatchTransaction(Request $request)
    {
        $userinfo = $request->get('userinfo');

        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Reconcile Bank Transactions'
        );
        try{
            $transaction_collection = TransactionCollections::where('user_id', $userinfo[0])
                        ->whereIn('transaction_type', $request->transaction_type)
                        ->whereIn('status', ['Unpaid','PartlyPaid'])
                        ->where('is_active', 1)
                        ->get();


            return response()->json([
                'message' => '',
                'transactions' => $transaction_collection,
            ],200);

        }catch(\Exceptions $e){
            \Log::error($e);

        }
    }

    public function createAndReconcileTransaction(Request $request)
    {
        $userinfo = $request->get('userinfo');

        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Reconcile Bank Transactions'
        );

        $transaction_type = '';
        if($request->transaction_type == 'credit')
        {
            $transaction_type = "receive_money";
        }
        else if($request->transaction_type == 'debit')
        {
            $transaction_type = "spend_money";
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
            'transaction_type' =>  $transaction_type,
            'invoice_ref_number' => trim($request->invoice_ref_number) ? $request->invoice_ref_number : 0,
            'payment_option' => trim($request->payment_option),
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
            $this->reconcileTransaction($request);
        }
    }

    public function disableBankAccount(Request $request, $id)
    {
        $userinfo = $request->get('userinfo');
        $request->validate([
            'id' => 'required|exists:user_bank_accounts,account_id,user_id,'.$userinfo[0].',is_active,1',
        ]);

        $accountDisabled = $this->bankRepository->archiveBankAccount($request->id, $userinfo[0]);

        if(!empty($accountDisabled))
        {
            return response()->json( [

                'message' => 'Account has been successfully disabled',

            ], 201);
        }

        return response()->json( [

            'message' => "Something went wrong!"

        ], 500);

    }
}
