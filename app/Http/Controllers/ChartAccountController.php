<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use DB;
use App\Models\SumbChartAccountsTypeParticulars;
use App\Models\SumbChartAccountsType;
use App\Models\SumbChartAccounts;
use App\Models\SumbInvoiceTaxRates;
use App\Interfaces\ChartAccountRepositoryInterface;
use App\Interfaces\TaxRateRepositoryInterface;


class ChartAccountController extends Controller
{
    private ChartAccountRepositoryInterface $chartAccountRepository;
    private TaxRateRepositoryInterface $taxRateRepositoryInterface;

    public function __construct(
        ChartAccountRepositoryInterface $chartAccountRepository,
        TaxRateRepositoryInterface $taxRateRepository,
    ) {

        $this->middleware('invoice_seetings');
        $this->chartAccountRepository = $chartAccountRepository;
        $this->taxRateRepository = $taxRateRepository;
    }

    public function chartAccountsPartsById(Request $request)
    {
        if ($request->ajax())
        {
            $userinfo = $request->get('userinfo');

            $chart_account = $this->chartAccountRepository->showChartAccountParticularById($userinfo[0], $request->id);
            
            if($chart_account){

                return response()->json([
                
                    'data' => $chart_account
                
                ], 200);
            }

            return response()->json([

                'message' => 'No item found'

            ], 404);
        }
    }

    public function chartAccountsPartsList(Request $request)
    {
        if ($request->ajax())
        {
            $userinfo = $request->get('userinfo');

            $chart_account_parts = $this->chartAccountRepository->getChartAccountTypesAndParts($userinfo[0]);

            if($chart_account_parts->isNotEmpty()){

                return response()->json([

                    'data' => $chart_account_parts

                ], 200);
            }

            return response()->json([

                'message' => 'No item found'

            ], 404);
        }
    }

    public function index(Request $request) {
        $userinfo = $request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Create Invoice'
        );
        
        if($request->orderBy || $request->search_code_name_desc){
            $pagedata['orderBy'] = $request->orderBy;
                if($request->direction == 'ASC')
                {
                    $pagedata['direction'] = 'DESC';
                }
                if($request->direction == 'DESC')
                {
                    $pagedata['direction'] = 'ASC';
                }
                $pagedata['search_code_name_desc'] = $request->search_code_name_desc;
        }else{
            $pagedata['orderBy'] = 'chart_accounts_particulars_code';
            $pagedata['direction'] = 'ASC';
            $request->orderBy = 'chart_accounts_particulars_code';
            $request->direction = 'DESC';
        }

        $tax_rates = $this->taxRateRepository->index();
        if ($tax_rates->isNotEmpty()) {
            $pagedata['tax_rates'] = $tax_rates->toArray();
        }

        $chart_account = $this->chartAccountRepository->getChartAccountAndTypes();

        $chart_account_particulars = $this->chartAccountRepository->index($userinfo[0], $request);
       

        $pagedata['chart_accounts_types'] = $chart_account->isNotEmpty() ? $chart_account->toArray() : [];
        $pagedata['tab'] = $request->tab ? $request->tab : 'all_accounts';
        $pagedata['accounts_id'] = $request->id ? $request->id : '';
        $pagedata['chart_account'] = $chart_account->isNotEmpty() ? $chart_account->toArray() : [];
        $pagedata['chart_account_particulars'] = $chart_account_particulars->isNotEmpty() ? $chart_account_particulars->toArray() : [];

        return view('invoice.chartaccounts', $pagedata);
    }

    public function update(Request $request){
        $userinfo = $request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Create Invoice'
        );
        if ($request->ajax())
        {
            $validated = $request->validate([
                'chart_accounts_part_id' => 'required|exists:sumb_chart_accounts_particulars,id,user_id,'.$userinfo[0],
                'chart_accounts_id' => 'bail|required|max:255',
                'chart_accounts_type_id' => 'bail|required|max:255',
                'chart_accounts_parts_code' => 'bail|required',
                'chart_accounts_parts_name' => 'bail|required',
                'chart_accounts_description' => 'bail|required|max:255',
                'chart_accounts_tax_rate' => 'bail|required|max:255',
            ]);
            
            $userinfo = $request->get('userinfo');

            $chart_account_exists = $this->chartAccountRepository->showChartAccountParticular($userinfo[0], [$request->chart_accounts_parts_code]);

            if($chart_account_exists->isNotEmpty() && $chart_account_exists[0]['id'] != $request->chart_accounts_part_id){
                
                return response()->json([
                
                    'message' => 'Code already exists'
                
                ], 409);
    
            }else{

                $particular = array(
                    'user_id' => trim($userinfo[0]), 
                    'chart_accounts_id' => $request->chart_accounts_id,
                    'chart_accounts_type_id' => trim($request->chart_accounts_type_id),
                    'chart_accounts_particulars_code' => trim($request->chart_accounts_parts_code),
                    'chart_accounts_particulars_name' => trim($request->chart_accounts_parts_name),
                    'chart_accounts_particulars_description' => trim($request->chart_accounts_description),
                    'chart_accounts_particulars_tax' => trim($request->chart_accounts_tax_rate),
                    'accounts_tax_rate_id' => trim($request->chart_accounts_tax_rate)
                );

                $chart_account_particulars = $this->chartAccountRepository->updateChartAccountParticular($userinfo[0], $request->chart_accounts_part_id, $particular); 
                if($chart_account_particulars)
                {
                    return response()->json([
                
                        'message' => 'Chart account updated'
                    
                    ], 201);
                }
            }
        }
    }

    public function create(Request $request){
        $userinfo = $request->get('userinfo');
        $pagedata = array(
            'userinfo' => $userinfo,
            'pagetitle' => 'Create Invoice'
        );

        $chart_account_exists = $this->chartAccountRepository->showChartAccountParticular($userinfo[0], [$request->chart_accounts_parts_code]);

        if($chart_account_exists->isNotEmpty()){
            
            return response()->json([
            
                'message' => 'Code already exists'
            
            ], 409);

        }else{
            $particular = array(
                'user_id' => trim($userinfo[0]),
                'chart_accounts_id' => $request->chart_accounts_id,
                'chart_accounts_type_id' => trim($request->chart_accounts_type_id),
                'chart_accounts_particulars_code' => trim($request->chart_accounts_parts_code),
                'chart_accounts_particulars_name' => trim($request->chart_accounts_parts_name),
                'chart_accounts_particulars_description' => trim($request->chart_accounts_description),
                // 'chart_accounts_particulars_tax' => trim($request->chart_accounts_tax_rate),
                'accounts_tax_rate_id' => trim($request->chart_accounts_tax_rate)
            );
            $chart_account_particular = $this->chartAccountRepository->createChartAccountParts($particular);
            if($chart_account_particular)
            {
                $chart_account = $this->chartAccountRepository->getChartAccountTypesAndParts($userinfo[0]);
                return response()->json([
                    
                    'message' => 'Chart account created',
                    'data' => $chart_account,
                    'id' => $chart_account_particular->id

                ], 200);
            }
        }
    }
}
