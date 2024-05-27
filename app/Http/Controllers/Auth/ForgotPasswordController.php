<?php

namespace App\Http\Controllers\Auth;

use App\Models\SumbUsers;
use Illuminate\Http\Request;
use App\Http\Services\AuthService;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use App\Notifications\ForgotPasswordNotification;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use App\Http\Requests\Auth\{ResetPasswordRequest,NewPasswordRequest};


class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */
    use SendsPasswordResetEmails;

    public function __construct(SumbUsers $usrs){

        $this->usrs = $usrs;

    }

    public function index(Request $req){

        try{
            
            if( !$user = $this->usrs::where(['id' => decrypt($req->query()['id']), 'email'=> $req->query()['email']])->first() )

                abort(403);



            $data = [
                "enc_id" => $req->query()['id'],
                "email" => $req->query()['email']
            ];
            
            return view('auth.forgot-password-mod', $data );

        }catch(\Exception $e){
            $req->session()->flush();
            abort(403);

        }
        
        
    }

    public function submitPassword(NewPasswordRequest $req,$id){

        $payload = $req->payload();
        try{

            if($payload['password'] !== $payload['password_confirmation'])

                return response()->json([
                    "message" => "Password did not match"
                ],400);

            \DB::beginTransaction();
            $this->usrs::where('id',decrypt($id))
            ->update([ 'password' => md5($payload['password']) ]);
            $req->session()->flush();
            \DB::commit();
            return  response()->json([
                'message' => 'You have succesfully update your password',
            ]);

        }catch(QueryException $qe){
            $req->session()->flush();
            \DB::rollback();
            return;

        }

    }
    public function verify(ResetPasswordRequest $req){

        try{

            if( $usrData = $this->usrs->GetEmail($req->payload()['email'])->first() ){
                AuthService::storeUserSession($usrData);
                $usrData->notify( (new ForgotPasswordNotification()) );
            }
            
            return response()->json(['message' => 'We have sent you an email, please check your email account, if not please try again.']);

        }catch(\Exceptions $e){

            return $e;

        }


    }

}
