<?php

namespace App\Http\Services;

use Google_Client;
use Carbon\Carbon;
use App\Models\SumbUsers;
use App\Http\Services\AuthService;


class GoogleService{

	private $googleClient;

	public function __construct(){

		$this->googleClient = new Google_Client([

            'client_id' => env('GOOGLE_CLIENT_ID')

        ]);

	}

	public function logIn(SumbUsers $user, array $callbackPayload) : SumbUsers {

		\Log::error(Carbon::now());
		$payload = $this->googleClient->verifyIdToken( $callbackPayload['credential'] );
		$authService = (new AuthService());
		
		if( !$sumbUser = $user->getEmail($payload['email'])->first() ){
			$sumbUser = $authService->register(
					[

					'email' => $payload['email'],
					'fullname' => $payload['name'],
					'password' => md5($authService::generateRandomString()),
					//default user usertype for now
					'accountype' => 'user',
					'remember_token' => md5(Carbon::now())

				]
			);
		}
		
		$authService::storeUserSession($sumbUser);
		
		return $sumbUser;
		
	}
}