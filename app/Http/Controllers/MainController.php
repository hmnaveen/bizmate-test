<?php

namespace App\Http\Controllers;

use Session;
use Carbon\Carbon;
use App\Mail\SignupMail;
use App\Models\SumbUsers;
use Illuminate\Http\Request;
use App\Http\Services\AuthService;
use App\Models\SumbInvoiceSettings;
use App\Models\SumbExpenseSettings;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Database\Eloquent\Model;
use App\Notifications\VerificationNotification;


class MainController extends Controller {

    public function __construct(SumbUsers $users,AuthService $authService) {
        $this->users = $users;
        $this->authService = $authService;
    }
    
    //***********************************************
    //*
    //*  Index Page - Login Form
    //*
    //***********************************************
    public function index(Request $request) {
        $pagedata = array(
            'request'   => $request->all(),
            'pagetitle' => 'Sign In'
        );
        $errors = array(
            1 => ['Sorry your form is incomplete, please fill out the information correctly.', 'danger'],
            2 => ['Verification details does not exist, its either its already verified or expired, please log in and try it again.', 'danger'],
            3 => ['Both password forms are not the same, Please try again.', 'danger'],
            4 => ['Your email has been verified.', 'primary'],
            5 => ['You are now registered, please check your email for verification.', 'primary'],
            6 => ['Invalid Username or Password. Please try again.', 'danger'],
            7 => ['You are offline. Please log in again.', 'warning'],
            8 => ['You are now logged out.', 'primary'],
        );
        $pagedata['errors'] = $errors;
        if (!empty($request->input('err'))) { $pagedata['err'] = $request->input('err'); }
        return view('index', $pagedata); 
    }
    
    //***********************************************
    //*
    //*  Login Process
    //*
    //***********************************************

    //log in using google
    public function loginGoogle(Request $req){
        
        $user = $this->authService->loginGoogle($this->users,$req->only([
            'clientId',
            'credential',
        ]));

        if(!$user)

            return response()->json([
                'message' => "Unathorized",
            ],401);

   
        return response()->json([

            'redirect_uri' => '\dashboard',

        ],200);
        
    }


    public function login(Request $request) {
                
        $pagedata = array('request'=>$request->all());
        
        $upass = md5($request->password);
        $userdata = SumbUsers::where('email', $request->input('email'))
            ->where('password', $upass)->where('active',1)
            ->first();
                
        if (empty($userdata)) {
            $oriform['err'] = 6;
            return redirect()->route('index', $oriform);
        }
        
        $this->authService::storeUserSession($userdata);

        return redirect()->route('dashboard'); 
    }
    
    //***********************************************
    //*
    //*  Signup Page
    //*
    //***********************************************
    public function signup(Request $request) {
        $pagedata = array('pagetitle' => 'Sign up now for free');        
        $errors = array(
            1 => ['Verification cannot be completed, requirements are not complete.', 'danger'],
            2 => ['Your email you registered is already in our system, Please try to login.', 'danger'],
            3 => ['Both password forms are not the same, Please try again.', 'danger'],
            4 => ['You are now registered, please check your email for verification.', 'primary'],
        );
        $pagedata['errors'] = $errors;
        if (!empty($request->input('err'))) { $pagedata['err'] = $request->input('err'); }
        if (!empty($request->input('accountant'))) { $pagedata['form_accountant'] = $request->input('accountant'); }
        if (!empty($request->input('email'))) { $pagedata['form_email'] = $request->input('email'); }
        if (!empty($request->input('fullname'))) { $pagedata['form_fullname'] = $request->input('fullname'); }
        return view('signup', $pagedata); 
    }
    
    
    //***********************************************
    //*
    //*  User Registration Process
    //*
    //***********************************************
    public function register(Request $request) {
        $predata = array();
        $oriform = array('email'=>$request->input('email'), 'fullname'=>$request->input('fullname'), 'accountant' => empty($request->input('accountant')) ? 0 : 1);
        
        //===== Data checks
        if (empty($request->input('accountant'))) { $predata['accountype'] = 'user'; } else { $predata['accountype'] = 'accountant'; }
        
        if (empty($request->input('fullname')) || 
            empty($request->input('password1')) || 
            empty($request->input('password2'))) {
            $oriform['err'] = 1;
            return redirect()->route('signup', $oriform); die();
        }
        $predata['fullname'] = $request->input('fullname');
        if ($request->input('password1') != $request->input('password2')) {
            $oriform['err'] = 3;
            return redirect()->route('signup', $oriform); die();
        } else {
            $predata['password'] = md5($request->input('password2'));
        }
        if (empty($request->input('email'))) { 
            $oriform['err'] = 1;
            return redirect()->route('signup', $oriform); die();
        } else {
            $emaildata = SumbUsers::where('email', $request->input('email'))->first();
            //print_r($emaildata);
            if (!empty($emaildata)) {
                $oriform['err'] = 2;
                return redirect()->route('signup', $oriform); die();
            } else {
                $predata['email'] = $request->input('email');
            }
        }
        
        //===== saving data
        $dtnow = Carbon::now();
        $predata['created_at'] = $dtnow;
        $predata['updated_at'] = $dtnow;
        $predata['remember_token'] = md5($dtnow);
       
        $sumbUser = $this->authService::register($predata);
        
        $this->authService::storeUserSession($sumbUser);

        $oriform['err'] = 5;
        
        return redirect()->route('basic/invoice', $oriform);
    }
    
    //***********************************************
    //*
    //*  User Verification Process
    //*
    //***********************************************
    public function verify(Request $request,$encId) {
        
        if(!$encId)

            abort(403);
        
        $id = decrypt($encId);
        $query = $this->users::where([

            'id'=>$id,
            'email_verified_at'=>null

        ]);

        if( !$user = $query->first() )

            abort(403);


        // \DB::beginTransaction();
        $bool = $query->update([

            'email_verified_at' => Carbon::now()

        ]);
        
        // :(
        if( Session::has('keysumb') ){
            
            if($bool){

                $this->authService::storeUserSession(
                    $this->users::where('id',$id)->first()
                );
                redirect()->route('dashboard');

            }

        }
        $oriform['err'] = 4;
        return redirect()->route('index', $oriform);

    }

    public function upgrade(Request $request)
    {
        $userinfo = $request->get('userinfo');
        $upgrade_pro =  SumbUsers::where('id', $userinfo[0])->where('accountype', 'user')->update(['accountype' => 'user_pro']);
        if($upgrade_pro){
            $userdata = SumbUsers::where('id', $userinfo[0])->first();
            $this->authService::storeUserSession(
                $userdata
            );
            return redirect()->route('dashboard')->with('success', 'Upgraded to pro version');
        }else{
            return redirect()->route('dashboard');
        }
    }
    
    public function sendNewVerification(Request $request){
        try{
            $sumbUser = $this->users::GetEmail($request->email)
            ->whereNull('email_verified_at')->first();

            $sumbUser->notify( new VerificationNotification() );    
        }catch(\Exceptions $e){

            \Log::error($e);

        }

        return response()->json([

            'message' => "Please Check your Email"

        ]);
    }
    //***********************************************
    //*
    //*  Logout Process
    //*
    //***********************************************
    public function logout(Request $request) {
        $request->session()->flush();
        $oriform['err'] = 8;
        return redirect()->route('index', $oriform); die();
    }
}
