<?php

namespace App\Repositories;

use App\Interfaces\InvoiceItemRepositoryInterface;
use App\Models\SumbInvoiceItems;
use DB;

class InvoiceItemRepository implements InvoiceItemRepositoryInterface 
{
    protected $itemModel;
    public function __construct(SumbInvoiceItems $itemModel){
        $this->itemModel = $itemModel;
    }

    public function index($userId)
    {
        try{
            return $this->itemModel::where('user_id', $userId)->orderBy('invoice_item_name')->get();
        }
        catch(\Exceptions $e)
        {
            \Log::error($e);
        }
    }

    public function create($item)
    {
        try {
            $itemExists = $this->itemModel::where('user_id', $item['user_id'])->where('invoice_item_code', $item['invoice_item_code'])->exists();
            if($itemExists)
            {
                return response()->json( [
                    
                    'message' => "Item code already exists"
                
                ], 422);
            }

            $createItem = $this->itemModel::create($item);

            if($createItem)
            {
                $items =  $this->itemModel::with(['taxRates'])->where('user_id', $item['user_id'])->get();

                return response()->json( [
                    
                    'data' => $items
                
                ], 200);
            }
        }
        catch(\Exceptions $e)
        {
            \Log::error($e);
        }

    }
    public function show($userId, $id)
    {
        try{
            return $this->itemModel::with(['taxRates', 'chartAccountsParts'])->where('user_id', $userId)->where('id', $id)->first();
        }
        catch(\Exceptions $e)
        {
            \Log::error($e);
        }
    }
}
?>