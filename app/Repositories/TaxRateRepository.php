<?php

namespace App\Repositories;

use App\Interfaces\TaxRateRepositoryInterface;
use App\Models\SumbInvoiceTaxRates;
use DB;

class TaxRateRepository implements TaxRateRepositoryInterface 
{
    public function __construct(SumbInvoiceTaxRates $taxRatesModel){
        $this->taxRatesModel = $taxRatesModel;
    }

    public function index()
    {
        try{
            return $this->taxRatesModel::get();
        }
        catch(\Exceptions $e)
        {
            \Log::error($e);
        }
    }
    public function show()
    {
        try{
            return $this->taxRatesModel::first();
        }
        catch(\Exceptions $e)
        {
            \Log::error($e);
        }
    }
}
?>