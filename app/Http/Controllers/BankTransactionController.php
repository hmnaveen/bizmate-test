<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Interfaces\TransactionCollectionRepositoryInterface;
use App\Http\Services\BankApiService;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\ChartAccountRepositoryInterface;
use App\Interfaces\TaxRateRepositoryInterface;
use App\Interfaces\InvoiceItemRepositoryInterface;
use App\Interfaces\ReconcileTransactionRepositoryInterface;
use App\Interfaces\BankAccountsRepositoryInterface;
use App\Interfaces\BankTransactionsRepositoryInterface;

class BankTransactionController extends Controller
{

    private TransactionCollectionRepositoryInterface $transactionCollectionRepository;
    private UserRepositoryInterface $userRepository;
    private ChartAccountRepositoryInterface $chartAccountRepository;
    private TaxRateRepositoryInterface $taxRateRepositoryInterface;
    private InvoiceItemRepositoryInterface $invoiceItemRepositoryInterface;
    private ReconcileTransactionRepositoryInterface $reconcileTransactionRepositoryInterface;
    private BankAccountsRepositoryInterface $bankAccountsRepository;
    private BankTransactionsRepositoryInterface $bankTransactionsRepository;


    public function __construct(
        TransactionCollectionRepositoryInterface $transactionCollectionRepository,
        UserRepositoryInterface $userRepository,
        ChartAccountRepositoryInterface $chartAccountRepository,
        TaxRateRepositoryInterface $taxRateRepositoryInterface,
        InvoiceItemRepositoryInterface $invoiceItemRepositoryInterface,
        ReconcileTransactionRepositoryInterface $reconcileTransactionRepositoryInterface,
        BankAccountsRepositoryInterface $bankAccountsRepository,
        BankTransactionsRepositoryInterface $bankTransactionsRepository,
        BankApiService $bankApiService
    )
    {
        $this->middleware('invoice_seetings');
        $this->transactionCollectionRepository = $transactionCollectionRepository;
        $this->userRepository = $userRepository;
        $this->chartAccountRepository = $chartAccountRepository;
        $this->taxRateRepositoryInterface = $taxRateRepositoryInterface;
        $this->invoiceItemRepositoryInterface = $invoiceItemRepositoryInterface;
        $this->reconcileTransactionRepositoryInterface = $reconcileTransactionRepositoryInterface;
        $this->bankApiService = $bankApiService;
        $this->bankAccountsRepository = $bankAccountsRepository;
        $this->bankTransactionsRepository = $bankTransactionsRepository;

    }

    //***********************************************
    //*
    //* Bank Transactions Page
    //*
    //***********************************************

    public function index(Request $request){
        $userinfo = $request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Banking Transactions'
        );

        $pagedata['bank_transactions'] = [];
        $pagedata["bank_accounts"] = [];
        $pagedata['bank_account_id'] = '';
        $filter = $request->query();

        $itemsperpage = 10;
        if (!empty($request->input('ipp'))) { $itemsperpage = $request->input('ipp'); }
        $pagedata['ipp'] =  $filter['ipp'] = $itemsperpage;

        $purl = $oriform = $request->all();
        unset($purl['ipp']);
        $pagedata['myurl'] = route('bankTransactions');
        $pagedata['ourl'] = route('bankTransactions', $purl);
        $pagedata['npurl'] = http_build_query(['ipp'=>$itemsperpage]);
        
        
        $user = $this->bankAccountsRepository->showAll($userinfo[0]);
        if(!empty($user['basiq_user_id']) && $user['bankAccounts']->isNotEmpty()){
            $pagedata["bank_accounts"] = $user['bankAccounts'];

            if($request->bank_account_id || $request->search_desc_class || $request->start_date || $request->end_date || $request->min_amt || $request->max_amt){

                $request->validate([
                    'bank_account_id' => 'required|exists:bank_accounts,account_id,basiq_user_id,'.$user['basiq_user_id'].',is_active,1',
                ]);
                
                // $expense_number = $request->search_desc_class;
                // $minAmt = $request->min_amt;
                // $maxAmt = $request->max_amt;

                $bankTransactions = $this->bankTransactionsRepository->getTransactionsByFilter($filter);

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
                $bankTransactions = $this->bankTransactionsRepository->getTransactionsByFilter($filter);
            
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
        
        return view('bank.transactions', $pagedata);
    }

}
