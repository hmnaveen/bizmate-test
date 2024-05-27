<?php

namespace App\Http\Services;

use Carbon\Carbon;
use App\Models\SumbUsers;
use App\Models\SumbInvoiceSettings;
use App\Models\SumbExpenseSettings;
use App\Http\Services\GoogleService;
use Illuminate\Database\QueryException;
use App\Notifications\UserSignUpNotification;



class AuthService{

	public function loginGoogle(SumbUsers $user, array $callbackPayload) : SumbUsers {

		return (new GoogleService())->logIn($user, $callbackPayload);

	}
	// public function logInFb()

	public static function storeUserSession(SumbUsers $user) : void {
		$verifiedUser = !empty($user->email_verified_at) ? 'verified' : 'unverified';
			$profilePic = $user->profilepic ;
			$userkey = [
				
				$user->id, 
				$user->fullname, 
				$user->email, 
				$user->accountype, 
				$verifiedUser,
				$profilePic, 
				date('ymdHis'),
				$user->encId,
				$user->active,
				$user->basiq_user_id
			];

			$userId = encrypt(implode("|&", $userkey));
			
			session(['keysumb' => $userId]);
	
		}

	public static function generateRandomString($length = 10) : string {

	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[random_int(0, $charactersLength - 1)];
	    }
	    return $randomString;

	}
	public function resendNewGeneratedPassword($user,string $pass) : void {
		try{
			\DB::beginTransaction();
			
			$user->update( ['password' => md5($pass)] );

			$user->first()->notify( new UserSignUpNotification($user->first()->toArray(),$pass) );
			\DB::commit();
		}
		catch(\Exceptions $e){

			\DB::rollback();

		}
	}
	public static function register(array $data, $generated = null) : SumbUsers {
		$pass = null;
		try{
			
			if( $generated ){

				$pass = static::generateRandomString(16);
				$data = array_merge( $data, ['password' => md5($pass)] );

			}
			
			\DB::beginTransaction();
			$sumbUser = SumbUsers::create($data);
			
			SumbInvoiceSettings::create([

			   	'user_id'=>$sumbUser->id

			]);

			SumbExpenseSettings::create([

			    'user_id'=>$sumbUser->id

			]);
			if( isset($data['createdBy']) && $data['createdBy'] == 'admin' ){
				
				$data['pass'] = $pass;
				$sumbUser->notify( new UserSignUpNotification($data,$pass = null) );
			}
			\DB::commit();
			
		}
		catch(\Exceptions $e){
			\Log::error('exception');
			\DB::rollback();
		}
		catch(QueryException $q){
			\Log::error('Queryexception');
			\DB::rollback();
		}

		return $sumbUser;

	}
	

}