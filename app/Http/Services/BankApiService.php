<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use DateTimeImmutable;
use Illuminate\Http\Request;

class BankApiService{

    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }
	
	public function generateAccessToken($url, $params)
	{
		try {
            $response = $this->client->post($url, $params);
           
            if ($response->getStatusCode() == 200) {
               return json_decode($response->getBody()->getContents(), true);
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents(); 
           
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            $response = $e->getResponse();
        }
	}
	
	public function getBankTransactionsOrAcounts($url, $params)
	{
		try {
            $response = $this->client->get($url, $params);
            if ($response->getStatusCode() == 200) {
               return json_decode($response->getBody()->getContents(), true);                
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();            
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            $response = $e->getResponse();
        }
	}

	public function createUser($url, $params)
	{
		try {
            $response = $this->client->post($url, $params);
            if ($response->getStatusCode() == 201) {
               return json_decode($response->getBody()->getContents(), true);
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();            
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            $response = $e->getResponse();            
        }
	}

    public function getConnection($url, $params)
    {
        $response = $this->client->post($url, $params);
        
        return json_decode($response->getBody()->getContents(), true);
    }

    public function event($url, $params)
    {
        $response = $this->client->get($url, $params);

        return json_decode($response->getBody()->getContents(), true);
    }
}
