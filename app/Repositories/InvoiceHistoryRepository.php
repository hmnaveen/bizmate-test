<?php

namespace App\Repositories;

use App\Interfaces\InvoiceHistoryRepositoryInterface;
use App\Models\InvoiceHistory;
use DB;

class InvoiceHistoryRepository implements InvoiceHistoryRepositoryInterface 
{
    protected $historyModel;

    public function __construct(
        InvoiceHistory $historyModel
    )
    {
        $this->historyModel = $historyModel;
    }
    
    public function store($invoiceHistory)
    {
        try{
            return $this->historyModel::create($invoiceHistory);
        }
        catch(\Exceptions $e)
        {
            \Log::error($e);
        }
    }

    public function index($userId, $transactionId)
    {
        try{
            return $this->historyModel::where('user_id', $userId)->where('invoice_id', $transactionId)->latest('id')->get();
        }
        catch(\Exceptions $e)
        {
            \Log::error($e);
        }
    }


    
}
