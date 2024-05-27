<?php

namespace App\Repositories;

use App\Interfaces\ChartAccountRepositoryInterface;
use App\Models\SumbChartAccounts;
use App\Models\SumbChartAccountsType;
use App\Models\SumbChartAccountsTypeParticulars;
use DB;

class ChartAccountRepository implements ChartAccountRepositoryInterface 
{
    public function __construct(SumbChartAccounts $chartAccountModel, 
        SumbChartAccountsType $chartAccountTypeModel,
        SumbChartAccountsTypeParticulars $particularModel
    ){
        $this->chartAccountModel = $chartAccountModel;
        $this->chartAccountTypeModel = $chartAccountTypeModel;
        $this->particularModel = $particularModel;
    }

    public function index($userId, $request)
    {
        $chart_account_particulars = $this->particularModel::with(['chartAccounts', 'chartAccountsTypes', 'invoiceTaxRates'])
                        ->where('user_id', $userId)
                        ->where(function ($query) use ($request) {
                            $query->where('chart_accounts_particulars_code', 'LIKE', "%{$request->search_code_name_desc}%")
                                ->orWhere('chart_accounts_particulars_name', 'LIKE', "%{$request->search_code_name_desc}%")
                                ->orWhere('chart_accounts_particulars_description', 'LIKE', "%{$request->search_code_name_desc}%");
                        })->where(function ($query) use ($request) {
                            if($request->id){
                                $query->where('chart_accounts_id', $request->id);
                            }
                        });
                    if($request->orderBy){
                        $chart_account_particulars->orderBy($request->orderBy, $request->direction);
                    }
        return $chart_account_particulars = $chart_account_particulars->get();
    }


    //Account Particulars
    public function showChartAccountParticular($userId, $chartCode)
    {
        try{
            return $this->particularModel::where('user_id', $userId)->whereIn('chart_accounts_particulars_code', $chartCode)->get();
        }
        catch(\Exceptions $e)
        {
            \Log::error($e);
        }
    }

    public function showChartAccountParticularById($userId, $id)
    {
        try{
            return $this->particularModel::with(['invoiceTaxRates'])->where('user_id', $userId)->where('id', $id)->first();
        }
        catch(\Exceptions $e)
        {
            \Log::error($e);
        }
    }
    public function createChartAccountParts($particulars)
    {
        try{
            
            return $this->particularModel::create($particulars);
        }
        catch(\Exceptions $e)
        {
            \Log::error($e);
        }
    }

    public function updateChartAccountParticular($userId, $chartAccountPartId, $particular)
    {
        try{
            return $this->particularModel::where('id', $chartAccountPartId)->where('user_id', $userId)->update($particular);
        }
        catch(\Exceptions $e)
        {
            \Log::error($e);
        }
    }

    public function createOrUpdateChartAccountsInBulk($chartAccountParts)
    {
        try{            
            return collect($chartAccountParts)->map(function($part){
                return $this->particularModel::updateOrCreate(
                    [
                        'user_id' => $part['user_id'],
                        'chart_accounts_particulars_code' => $part['chart_accounts_particulars_code']
                    ],
                    $part
                );
            });
        }
        catch(\Exceptions $e)
        {
            \Log::error($e);
        }
    }

    //Accounts
    public function getChartAccountAndTypes()
    {
        try{
            return $this->chartAccountModel::with(['chartAccountsTypes'])->get();
        }
        catch(\Exceptions $e)
        {
            \Log::error($e);
        }
    }

    public function showChartAccount()
    {
        try{
            return $this->chartAccountModel::where('chart_accounts_name', 'Liabilities')->first();
        }
        catch(\Exceptions $e)
        {
            \Log::error($e);
        }
    }

    public function getChartAccountTypesAndParts($userId)
    {
        try{
            return $this->chartAccountModel::with(['chartAccountsTypes',
                            'chartAccountsParticulars' => function($query) use($userId) {
                                $query->where('user_id', $userId);
                            }])->get();
        }
        catch(\Exceptions $e)
        {
            \Log::error($e);
        }
    }


    //Account Types
    public function showChartAccountType($chartAccountTypes)
    {
        try{
            return $this->chartAccountTypeModel::whereIn('chart_accounts_type', $chartAccountTypes)->get();
        }
        catch(\Exceptions $e)
        {
            \Log::error($e);
        }
    }
}
?>