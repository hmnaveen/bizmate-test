<?php

namespace App\Http\Controllers\ActivityStatement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BasAndTaxSettings;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\TransactionCollections;
use App\Models\SumbChartAccountsTypeParticulars;
use App\Models\SumbChartAccountsType;
use App\Models\SumbChartAccounts;
use App\Models\SumbInvoiceTaxRates;
use App\Models\SumbInvoiceParticulars;
use App\Models\ActivityStatements;

class BasAndTaxOverviewController extends Controller
{
    public function index(Request $request)
    {

        $userinfo = $request->get('userinfo');

        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Bas and Tax Overview'
        );

        $bas_and_tax_settings = BasAndTaxSettings::where('user_id', $userinfo[0])->first();

        if(empty($bas_and_tax_settings))

            return redirect()->route('bas/settings'); 

            $years = collect([]);
            $dates = [];
            if(!empty($bas_and_tax_settings)){
                for($i = -1; $i <= 5; $i++)
                {
                    if($bas_and_tax_settings['gst_calculation_period'] == 'monthly' || $bas_and_tax_settings['payg_withhold_period'] == 'monthly')
                    {
                        $period = CarbonPeriod::create(
                            now()->subYear($i)->month(7)->subMonths(12)->startOfMonth()->format('Y-m-d'),
                            '1 month',
                            now()->subYear($i)->month(6)->endOfMonth()->format('Y-m-d')
                        );
                        
                        $gst = $bas_and_tax_settings['gst_calculation_period'] == 'monthly' ? 'GST' : '';
                        $payg = $bas_and_tax_settings['payg_withhold_period'] == 'monthly' ? 'PAYG W' : '';

                        $first = collect($period)->first()->toArray();
                        $last = collect($period)->last()->toArray();

                        foreach ($period as $p) 
                        {
                            if($p->format('Y-m-d') <= date('Y-m-d'))
                            {
                                $years->push($first['year']."-".$last['year']); 

                                $dates[$i+1][] = ['year' => $first['year']."-".$last['year'], "start_date" => $p->format('Y-m-d'), "end_date" => Carbon::createFromFormat('Y-m-d', $p->format('Y-m-d'))->endOfMonth()->format('Y-m-d'), "type" => $gst.",".$payg, 'display_date' => $p->format('F Y') ];
                            }
                        }
                    }

                    if($bas_and_tax_settings['gst_calculation_period'] == 'quarterly' || $bas_and_tax_settings['payg_withhold_period'] == 'quarterly' || $bas_and_tax_settings['payg_income_tax_method'] != 'none')
                    {
                        
                        $gst_payg = '';
                        if($bas_and_tax_settings['payg_withhold_period'] == 'none' && $bas_and_tax_settings['payg_income_tax_method'] == 'none'){
                            $gst_payg = 'GST,';
                        }else if($bas_and_tax_settings['gst_calculation_period'] == 'annually' && $bas_and_tax_settings['payg_withhold_period'] == 'quarterly')
                        {
                            $gst_payg = 'PAYG W';
                        }
                        else if(
                                (
                                    $bas_and_tax_settings['gst_calculation_period'] == 'quarterly' || 
                                    $bas_and_tax_settings['gst_calculation_period'] == 'monthly'
                                )
                                    &&
                                (
                                    $bas_and_tax_settings['payg_withhold_period'] == 'quarterly' || 
                                    $bas_and_tax_settings['payg_withhold_period'] == 'monthly'
                                )
                            ){
                                $gst_payg = 'GST,PAYG W';
                            }
                        if($bas_and_tax_settings['payg_income_tax_method'] != 'none')
                        {
                            $gst_payg .= ',PAYG I';
                        }    
                        $quarters = CarbonPeriod::create(
                            now()->addYear($i)->month(7)->subMonths(12)->startOfMonth()->format('Y-m-d'),
                            '1 quarter',
                            now()->addYear($i)->month(6)->endOfMonth()->format('Y-m-d')
                        );

                        $first = collect($quarters)->first()->toArray();
                        $last = collect($quarters)->last()->toArray();

                        // $quarters_year = [];
                        foreach ($quarters as $q) 
                        {
                            if($q->format('Y-m-d') <= Carbon::now()->lastOfQuarter()->format('Y-m-d'))
                            {
                                $years->push($first['year']."-".$last['year']); 

                                $end_date = Carbon::createFromFormat('Y-m-d', $q->format('Y-m-d'))->addMonth(2)->endOfMonth()->format('Y-m-d');
                                $display_date = ($q->format('F'). '-' .Carbon::createFromFormat('Y-m-d', $q->format('Y-m-d'))->addMonth(2)->endOfMonth()->format('F Y'));
                                $dates[$i+1][] = ['year' => $first['year']."-".$last['year'], "start_date" => $q->format('Y-m-d'), "end_date" => $end_date, "type" => $gst_payg, 'display_date' => $display_date ];
                                
                            }
                        }
                    }

                    if($bas_and_tax_settings['gst_calculation_period'] == 'annually')
                    {
                        
                        $previousYears = CarbonPeriod::create(
                            now()->subYear($i)->month(7)->subMonths(12)->startOfMonth()->format('Y-m-d'),
                            '1 year',
                            now()->subYear($i)->month(6)->endOfMonth()->format('Y-m-d')
                        );
                        
                        $first = collect($previousYears)->first()->toArray();
                        $last = collect($previousYears)->last()->toArray();
                        
                        $gst = $bas_and_tax_settings['gst_calculation_period'] == 'annually' ? 'GST' : '';
                        $payg_w = $bas_and_tax_settings['payg_withhold_period'] != 'none' ? 'PAYG W' : '';
                        $payg_i = $bas_and_tax_settings['payg_income_tax_method'] != 'none' ? 'PAYG I' : '';

                        foreach ($previousYears as $y) 
                        {
                            if($y->format('Y-m-d') <= date('Y-m-d'))
                            {
                                $years->push($first['year']."-".($last['year'] + 1)); 
                               
                                $end_date = Carbon::createFromFormat('Y-m-d', $y->format('Y-m-d'))->addMonth(11)->endOfMonth()->format('Y-m-d');
                                $display_date = $y->format('F Y'). '-' .Carbon::createFromFormat('Y-m-d', $y->format('Y-m-d'))->addMonth(11)->endOfMonth()->format('F Y');
                                $dates[$i+1][] = ['year' => $first['year']."-".($last['year'] + 1), "start_date" => $y->format('Y-m-d'), "end_date" => $end_date, "type"=> $gst.",".$payg_w.",".$payg_i, 'display_date' => $display_date];
                            }
                        }
                    }
                }
                
                $years = $years->unique()->values()->all();
            }


            $pagedata['activity_statements'] = ActivityStatements::where('user_id', $userinfo[0])->get();

            $pagedata['activity_statements'] = !empty($pagedata['activity_statements']) ? $pagedata['activity_statements']->toArray() : '';

            $pagedata['financial_year'] = $dates;

            $pagedata['years'] = $years;

        return view('activity_statement.bas-and-tax-overview', $pagedata);
    }

    public function store(Request $request)
    {
        $userinfo = $request->get('userinfo');

        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Bas and Tax Overview'
        );

        $transactions = [];
        $transaction_reponse = SumbChartAccountsTypeParticulars::selectRaw('sumb_invoice_tax_rates.id as tax_rate, sumb_invoice_tax_rates.*, sumb_chart_accounts_particulars.id as account_id, 
                sumb_chart_accounts_particulars.*, transactions.id as particulars_id, transactions.*, 
                transaction_collections.id as primary_transaction_id, transaction_collections.*')
                ->leftJoin('transactions', 'transactions.parts_chart_accounts_id', '=', 'sumb_chart_accounts_particulars.id')
                ->leftJoin('sumb_invoice_tax_rates', 'sumb_invoice_tax_rates.id', '=', 'transactions.parts_tax_rate_id')
                ->leftJoin('transaction_collections', 'transaction_collections.id', '=', 'transactions.transaction_collection_id')
                ->where('transaction_collections.user_id', $userinfo[0])
                ->whereIn('transaction_collections.status', ['Paid'])
                ->whereBetween('transaction_collections.issue_date', ['2023-08-01', '2023-08-31'])
                ->where('transaction_collections.is_active', 1)
                ->get();

                foreach($transaction_reponse as $transaction){

                    //transaction_type
                    if(!isset($transactions[$transaction['tax_rate']])){
                        $transactions[$transaction['tax_rate']] = [
                            'tax_rates_name' => $transaction['tax_rates_name'],
                            'tax_rate' => $transaction['tax_rate'],
                            'total_credits_amount' => 0,
                            'total_tax_amount' => 0,
                            'accounts' => [],
                        ];
                    }
                    $invoice_credit_amount=0;$gst_amount = 0;
                    if(isset($transactions[$transaction['tax_rate']])){

                        //accounts
                        if(!in_array($transaction['particulars_id'], array_column($transactions[$transaction['tax_rate']]['accounts'], 'particulars_id'))){
                            
                            if($transaction['default_tax'] == 'tax_inclusive'){
                                $gst_amount = ($transaction['parts_amount'] - $transaction['parts_amount'] / (1 + $transaction['tax_rates']/100));
                                $invoice_credit_amount = $transaction['parts_amount'];
                            }
                            else if($transaction['default_tax'] == 'tax_exclusive'){
                                $gst_amount = ($transaction['parts_amount'] * $transaction['tax_rates']/100);
                                $invoice_credit_amount = $transaction['parts_amount'] - $gst_amount;
                            }else if($transaction['default_tax'] == 'no_tax'){
                                $gst_amount = 0;
                                $invoice_credit_amount = $transaction['parts_amount'];
                            }

                            $transactions[$transaction['tax_rate']]['accounts'][] = [
                                'transaction_number' => $transaction['transaction_number'],
                                'particulars_id' => $transaction['particulars_id'],
                                'parts_gross_amount' => $transaction['parts_amount'],
                                'parts_gst_amount' => $gst_amount,
                                'parts_net_amount' => $invoice_credit_amount,
                                'parts_description' => $transaction['parts_description'],
                                // 'start_date' => Carbon::createFromFormat('Y-m-d', $start_date)->format('d/m/Y'),
                                // 'end_date' => Carbon::createFromFormat('Y-m-d', $end_date)->format('d/m/Y'),
                                'chart_accounts_particulars_code' => $transaction['chart_accounts_particulars_code'],
                                'chart_accounts_particulars_name' => $transaction['chart_accounts_particulars_name'],
                                
                                // 'particulars' => [],
                            ];

                            $transactions[$transaction['tax_rate']]['total_credits_amount'] += $invoice_credit_amount;
                            $transactions[$transaction['tax_rate']]['total_tax_amount'] += $gst_amount;
                            // $items_metadata[$transaction['transaction_type']][] = $transaction['chart_accounts_particulars_name'];
                        }
                    }
                }
                sort($transactions);
                $pagedata['statements'] =  $transactions;

        return view('activity_statement.bas-and-tax-overview', $pagedata);
    }
}
