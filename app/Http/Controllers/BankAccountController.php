<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\BankApiService;
use App\Interfaces\BankRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\BankAccountsRepositoryInterface;
use App\Interfaces\BankTransactionsRepositoryInterface;
use App\Http\Services\AuthService;
use Illuminate\Support\Facades\Redirect;

class BankAccountController extends Controller {

    private BankRepositoryInterface $bankRepository;
    private UserRepositoryInterface $userRepositoryInterface;
    private BankAccountsRepositoryInterface $bankAccountsRepository;
    private BankTransactionsRepositoryInterface $bankTransactionsRepository;


    public function __construct(
        BankRepositoryInterface $bankRepository,
        UserRepositoryInterface $userRepositoryInterface,
        BankAccountsRepositoryInterface $bankAccountsRepository,
        BankTransactionsRepositoryInterface $bankTransactionsRepository,
        BankApiService $bankApiService
    )
    {
        $this->middleware('invoice_seetings');
        $this->bankRepository = $bankRepository;
        $this->userRepository = $userRepositoryInterface;
        $this->bankAccountsRepository = $bankAccountsRepository;
        $this->bankTransactionsRepository = $bankTransactionsRepository;
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

        $accounts = $this->bankAccountsRepository->showAll($userinfo[0]);
        if(!empty($accounts) && !empty($accounts['basiq_user_id']) && $accounts['bankAccounts']->isEmpty())
        {
            $url = url('/basiq/notification');
            $params['json'] = array (
                    'eventTypeId' => 'custom.connection.created',
                    'basiqUserId' => $accounts['basiq_user_id']
                );

            $data = $this->bankApiService->getConnection($url, $params);
        }

        $pagedata["accounts"] = !empty($accounts) ? $accounts->toArray() : [];

        return view('bank.accounts', $pagedata);
    }

    //***********************************************
    //*
    //* Add Bank Accounts
    //*
    //***********************************************
    public function store(Request $request) {

        $userinfo = $request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Banking'
        );
        //to get user-id
        $user = $this->userRepository->show($userinfo[0]);

        if(!empty($user['basiq_user_id'])){
            $basiqUserId = $user['basiq_user_id'];
        }else{

            $basiqUserId = $this->createBasiqUser($request);
            $user->update(array("basiq_user_id" => $basiqUserId));

            AuthService::storeUserSession($user::find($userinfo[0]));
        }

        $clientToken = $this->generateClientToken($basiqUserId);
        // $consentUrl = "https://consent.basiq.io/home?token=$clientToken&action=connect";

        return Redirect::to("https://consent.basiq.io/home?token=$clientToken&action=connect");

        $pagedata["consentUrl"] = $consentUrl;
        $pagedata["type"] = "consentUI";

        return view('bank.add', $pagedata);
    }

    public function accountNotification(Request $request)
    {
        if($request->eventTypeId == 'custom.connection.created')
        {
            $basiqUserId = $request->basiqUserId;

            $this->storeAccountsAndTransactions($basiqUserId);

        }else if($request->eventTypeId == 'connection.created'){
            $url = $request['links']['event'];

            $accessToken = $this->getAccessToken();
            $params['headers'] = [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' =>  "Bearer " .$accessToken,
            ];

            $event = $this->bankApiService->event($url, $params);

            if(!empty($event) && !empty($event['userId']))
            {
                if($this->userRepository->findBasiqUserId($event['userId']))
                {
                    $this->storeAccountsAndTransactions($event['userId']);
                }
            }
        }
    }

    public function storeAccountsAndTransactions($basiqUserId)
    {
        $bankAccounts = $this->getAllAccounts($basiqUserId);
        foreach($bankAccounts['data'] as $bankAccount)
        {
            $bankAccountDetails = $this->bankAccountsRepository->createOrUpdateBankAcount($basiqUserId, $bankAccount);

            if($bankAccountDetails->wasRecentlyCreated)
            {
                $this->updateBankAccountTransactions($basiqUserId, $bankAccount['id']);
            }
        }
    }

    public function updateBankAccountTransactions($basiqUserId, $accountId) {

        $account = $this->bankAccountsRepository->show($basiqUserId, $accountId);

        if(!empty($account))
        {
            $token = $this->getAccessToken();

            $params['headers'] = [
                "Authorization" => "Bearer " .$token
            ];

            if(empty($account['transaction_url']))
            {
                $account['transaction_url'] = config('services.bank-api.uri').'users/'.$account['basiq_user_id'].'/transactions?filter=account.id.eq'.urlencode("('".$account['account_id']."')");
            }

            while(true)
            {
                try
                {
                    $transactions = $this->bankApiService->getBankTransactionsOrAcounts($account['transaction_url'], $params);

                    $account['transaction_url'] = !empty($transactions['links']['next']) ? $transactions['links']['next'] : '';

                    if(!empty($transactions['data']))
                    {
                        $this->bankTransactionsRepository->createOrUpdateBankTransactions($transactions['data'], $account['basiq_user_id'], $account['account_id'], $account['transaction_url']);
                    }

                    if(empty($account['transaction_url']))
                    {
                        break;
                    }
                }
                catch(\Exceptions $e)
                {
                    \Log::error($e);
                }
            }
        }
    }

    public function createBasiqUser($request){

        $userinfo = $request->get('userinfo');

        $accessToken = $this->getAccessToken();

        $params['headers'] = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' =>  "Bearer " .$accessToken,
        ];

        $url = config('services.bank-api.uri').'users';
        $params['json'] = ["email" => $userinfo[2], "firstName" => $userinfo[1]];

        $user = $this->bankApiService->createUser($url, $params);
        if(!empty($user))
        {
            return $user["id"];
        }
    }

    public function getAccessToken(){

        $params['headers'] = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => config('services.bank-api.token'),
            'basiq-version' => '3.0',
        ];
        // $params['form_params'] = array('scope'=> 'SERVER_ACCESS');
        $url = config('services.bank-api.uri').'token';

        $reponse = $this->bankApiService->generateAccessToken($url, $params);

        return $reponse["access_token"];
    }

    public function generateClientToken($basiqUserId){

        $params['headers'] = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => config('services.bank-api.token'),
            'basiq-version' => '3.0',
        ];

        $url = config('services.bank-api.uri').'token';
        $params['form_params'] = array('scope'=> 'CLIENT_ACCESS', 'userId' => $basiqUserId);

        $data = $this->bankApiService->generateAccessToken($url, $params);
        return $data["access_token"];
    }

    public function getAllAccounts($userId){

        $token = $this->getAccessToken();

        $params['headers'] = [
            "Authorization" => "Bearer " .$token,
            "Accept : application/json"
        ];

        $url = config('services.bank-api.uri').'users/'.$userId.'/accounts';

        return $data = $this->bankApiService->getBankTransactionsOrAcounts($url, $params);

    }

    //***********************************************
    //*
    //* Disable Bank Account
    //*
    //***********************************************
    public function disableBankAccount(Request $request, $id)
    {
        $userinfo = $request->get('userinfo');
        $request->validate([
            'id' => 'required|exists:bank_accounts,account_id,basiq_user_id,'.$userinfo[9].',is_active,1',
        ]);

        $accountDisabled = $this->bankAccountsRepository->disableBankAccount($request->id, $userinfo[9]);

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
