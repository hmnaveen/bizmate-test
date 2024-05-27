<?php

namespace App\Repositories;

use App\Interfaces\ClientsRepositoryInterface;
use App\Models\SumbClients;
use DB;
use Carbon\Carbon;

class ClientsRepository implements ClientsRepositoryInterface 
{
    
    public function getClients($userId)
    {
        try{
            return SumbClients::where('user_id', $userId)->orderBy('client_name')->get();
        }
        catch(\Exceptions $e){
            \Log::error($e);
        }
    }

    public function createOrUpdateClient($client, $userId)
    {
        try{
            \DB::beginTransaction();
                $client = SumbClients::updateOrCreate([
                    'user_id' => $userId,
                    'client_name' => $client['client_name'],
                    'client_email' => $client['client_email'],
                    'client_phone' => $client['client_phone']
                ],
                [
                    'user_id' => $userId,
                    'client_name' => $client['client_name'],
                    'client_email' => $client['client_email'],
                    'client_phone' => $client['client_phone']
                ]
                );
                
            if(!$client->wasChanged())
            {
                \DB::commit();
            }
        }
        catch(\Exceptions $e)
        {
            \DB::rollback();
            \Log::error($e);
        }
    }
}
?>