<?php

namespace App\Http\Services;


class FbService{

	private $appId;

	public function __construct(){

		$this->appId = env('FB_APP_ID');
		
	}
	
	// public function me()
	

}