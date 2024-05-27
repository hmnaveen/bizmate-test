<?php

namespace App\Repositories;

use App\Interfaces\PaymentHistoryRepositoryInterface;
use App\Models\PaymentHistory;
use DB;
use Carbon\Carbon;

class PaymentHistoryRepository implements PaymentHistoryRepositoryInterface 
{
    
    public function destroy($invoiceId, $userId)
    {
        return PaymentHistory::where('user_id', $userId)->where('transaction_collection_id', $invoiceId)->delete();
    }

    public function store($paymentHistory)
    {
        try{
            return PaymentHistory::create($paymentHistory);
        }
        catch(\Exceptions $e)
        {
            \Log::error($e);
        }
    }
}
?>