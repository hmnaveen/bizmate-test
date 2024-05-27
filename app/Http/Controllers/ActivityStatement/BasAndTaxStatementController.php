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
use App\Models\SumbInvoiceSettings;
use App\Models\ActivityStatements;
use App\Models\GstActivityStatement;
use App\Models\PaygWithheldActivityStatement;
use App\Models\PaygIncomeTaxInstalment;
use App\Models\AdjustmentG1;
use App\Models\Adjustment1A;
use App\Models\Adjustment1B;
use App\Helper\NumberFormat;

class BasAndTaxStatementController extends Controller
{

    public function index(Request $request)
    {
        $userinfo = $request->get('userinfo');

        $pagedata = array(
            'userinfo' => $userinfo,
            'pagetitle' => 'Bas and Tax Statement'
        );
        
        $request->validate([
            'date_filters' => 'bail|required',
        ],
        ['date_filters' => 'Select tax year field is required']
    );

        $arr = explode(',', $request->date_filters);
        $start_date = isset($arr[0]) ? $arr[0] : '';
        $end_date = isset($arr[1]) ? $arr[1] : '';

        $request->start_date = $start_date;
        $request->end_date = $end_date;

        $response = $this->getBasAndTax($request);
        
        if(!empty($response['statement']))
        {
            return view('activity_statement.bas-and-tax-statement', $response);
        }

        $bas_and_tax_settings = BasAndTaxSettings::where('user_id', $userinfo[0])->first();
        $invoice_settings = SumbInvoiceSettings::where('user_id', $userinfo[0])->first('business_abn');

        if(empty($bas_and_tax_settings))

            return redirect()->route('bas/settings'); 

        $gst_details = [];
        $paygw_details = [];
        $paygi_details = [];
                    
        $gst = '';
        $paygw = '';
        $paygi = '';
        array_map(function ($v) use(&$gst, &$paygw, &$paygi){
            $v = urldecode($v);
            ($v == 'GST' ?  $gst = 'GST' : '');
            ($v == 'PAYG W' ?  $paygw = 'PAYG W' : '');
            ($v == 'PAYG I' ?  $paygi = 'PAYG I' : '');
        }, $arr);

        $gst_start_date = '';
        $gst_end_date = '';

        if($gst){

            $gst_end_date = $end_date;

            if($bas_and_tax_settings->gst_calculation_period  == 'monthly')
            {
                $gst_start_date = Carbon::createFromFormat('Y-m-d', $end_date)->startOfMonth()->format('Y-m-d');
                $gst_details = [
                    "gst_activity" => ["gst_calculation_period" => 'monthly', "date" => Carbon::createFromFormat('Y-m-d', $end_date)->format('M').'-'.Carbon::createFromFormat('Y-m-d', $end_date)->format('Y') ]
                ];
            }
            if($bas_and_tax_settings->gst_calculation_period  == 'quarterly')
            {
                $gst_start_date = Carbon::createFromFormat('Y-m-d', $end_date)->subMonth(2)->startOfMonth()->format('Y-m-d');
                
                $gst_details = [
                    "gst_activity" => ["gst_calculation_period" => 'quarterly', "date" => Carbon::createFromFormat('Y-m-d', $start_date)->format('M').'-'.Carbon::createFromFormat('Y-m-d', $end_date)->format('M').''.Carbon::createFromFormat('Y-m-d', $end_date)->format('Y')]
                ];
            }
            if($bas_and_tax_settings->gst_calculation_period  == 'annually')
            {
                $gst_start_date = Carbon::createFromFormat('Y-m-d', $end_date)->subMonth(11)->startOfMonth()->format('Y-m-d');
                
                $gst_details = [
                    "gst_activity" => ["gst_calculation_period" => 'annually', "date" => Carbon::createFromFormat('Y-m-d', $start_date)->format('M').''.Carbon::createFromFormat('Y-m-d', $start_date)->format('Y').'-'.
                                    Carbon::createFromFormat('Y-m-d', $end_date)->format('M').''.Carbon::createFromFormat('Y-m-d', $end_date)->format('Y')
                    ]
                ];
            }
        }


        if($paygw){
            
            if($bas_and_tax_settings->payg_withhold_period  == 'monthly')
            {
                $paygw_details = [
                    "paygw_activity" => ["payg_withhold_period" => 'monthly', "date" => Carbon::createFromFormat('Y-m-d', $end_date)->format('M').'-'.Carbon::createFromFormat('Y-m-d', $end_date)->format('Y')]
                ];
            }
            if($bas_and_tax_settings->payg_withhold_period  == 'quarterly')
            {
                $paygw_details = [
                    "paygw_activity" => ["payg_withhold_period" => 'quarterly', "date" => Carbon::createFromFormat('Y-m-d', $end_date)->subMonth(2)->startOfMonth()->format('M').'-'.Carbon::createFromFormat('Y-m-d', $end_date)->format('M').' '.Carbon::createFromFormat('Y-m-d', $end_date)->format('Y')] 
                ];
            }
        }

        if($paygi){
        
            if($bas_and_tax_settings->payg_income_tax_method != 'none')
            {
                $paygi_details = [
                    "paygi_activity" => ["payg_income_tax_method" => $bas_and_tax_settings->payg_income_tax_method, "date" => Carbon::createFromFormat('Y-m-d', $end_date)->subMonth(2)->startOfMonth()->format('M').'-'.Carbon::createFromFormat('Y-m-d', $end_date)->format('M').' '.Carbon::createFromFormat('Y-m-d', $end_date)->format('Y')] 
                ];
            }
        }

        $bas_and_tax_settings->gst_accounting_method == 'cash' ? $column = 'payment_date' : $column = 'issue_date';

        $transactions = []; 
        $amounts = [];
        $gst_on_invoice = 0; $gst_on_expense = 0; $total_sales = 0;
        $transaction_reponse = SumbChartAccountsTypeParticulars::selectRaw('sumb_invoice_tax_rates.id as tax_rate_id, sumb_invoice_tax_rates.*, sumb_chart_accounts_particulars.id as account_id, 
                    sumb_chart_accounts_particulars.*, transactions.id as particulars_id, transactions.*, 
                    transaction_collections.id as primary_transaction_id, transaction_collections.*')
                    ->leftJoin('transactions', 'transactions.parts_chart_accounts_id', '=', 'sumb_chart_accounts_particulars.id')
                    ->leftJoin('sumb_invoice_tax_rates', 'sumb_invoice_tax_rates.id', '=', 'transactions.parts_tax_rate_id')
                    ->leftJoin('transaction_collections', 'transaction_collections.id', '=', 'transactions.transaction_collection_id')
                    ->where('transaction_collections.user_id', $userinfo[0])
                    ->whereIn('transaction_collections.status', ['Paid'])
                    ->whereBetween('transaction_collections.'.$column, [$gst_start_date, $gst_end_date])
                    ->where('transaction_collections.is_active', 1)
                    ->get();

                    foreach($transaction_reponse as $transaction){

                    //transaction_type
                    if(!isset($transactions[$transaction['tax_rate_id']])){
                        
                        $transactions[$transaction['tax_rate_id']] = [
                            'tax_rates_name' => $transaction['tax_rates_name'],
                            'tax_rate_id' => $transaction['tax_rate_id'],
                            'total_gross_amount' => 0,
                            'total_gst_amount' => 0,
                            'total_net_amount' => 0,
                            'gst_on_expense' => 0,
                            'gst_on_invoice' => 0,
                            'accounts' => [],
                        ];
                    }
                    

                    $invoice_credit_amount=0;$gst_amount = 0; 
                    if(isset($transactions[$transaction['tax_rate_id']])){

                        //accounts
                        if(!in_array($transaction['particulars_id'], array_column($transactions[$transaction['tax_rate_id']]['accounts'], 'particulars_id'))){
                            
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

                            if($transaction['tax_rates_name'] == 'GST on Expenses (10%)')
                            {
                                $gst_on_expense += $gst_amount;
                                

                            }else if($transaction['tax_rates_name'] == 'GST on Invoice (10%)' || 
                                        $transaction['tax_rates_name'] == 'GST Free Income')
                            {
                                $gst_on_invoice += $gst_amount;
                                $total_sales += $invoice_credit_amount;
                            }
                            
                            $transactions[$transaction['tax_rate_id']]['accounts'][] = [
                                'transaction_id' => $transaction['primary_transaction_id'],
                                'transaction_type' => $transaction['transaction_type'],
                                'transaction_number' => $transaction['transaction_number'],
                                'particulars_id' => $transaction['particulars_id'],
                                'parts_gross_amount' => $transaction['parts_amount'],
                                'parts_gst_amount' => $gst_amount,
                                'parts_net_amount' => $invoice_credit_amount,
                                'parts_description' => $transaction['parts_description'],
                                'date' => date('d/m/Y', strtotime($transaction[$column])),
                                'chart_accounts_particulars_code' => $transaction['chart_accounts_particulars_code'],
                                'chart_accounts_particulars_name' => $transaction['chart_accounts_particulars_name'],
                                
                            ];


                            $transactions[$transaction['tax_rate_id']]['total_gross_amount'] += $transaction['parts_amount'];
                            $transactions[$transaction['tax_rate_id']]['total_net_amount'] += $invoice_credit_amount;
                            $transactions[$transaction['tax_rate_id']]['total_gst_amount'] += $gst_amount;
                            
                        }
                    }
                }

            sort($transactions);
            
            $pagedata['statement'] = ['abn' => $invoice_settings->business_abn, 'gst_accounting_method' => $bas_and_tax_settings->gst_accounting_method , 'gst_calculation_period' => !empty($gst_details['gst_activity']) ? $gst_details['gst_activity']['gst_calculation_period'] : '',
                                        'payg_withhold_period' => !empty($paygw_details['paygw_activity']) ? $paygw_details['paygw_activity']['payg_withhold_period'] : '', 'payg_income_tax_method' => !empty($paygi_details['paygi_activity']) ? $paygi_details['paygi_activity']['payg_income_tax_method'] : '','start_date' => $start_date, 'end_date' => $end_date, 
                                        'payment_type' => ($gst_on_expense - $gst_on_invoice) > 0 ? 1 : (($gst_on_invoice - $gst_on_expense) > 0 ? 0 : 1), 
                                        'payment_amount' => ($gst_on_expense - $gst_on_invoice) > 0 ? $gst_on_expense - $gst_on_invoice : (($gst_on_invoice - $gst_on_expense) > 0 ? $gst_on_invoice - $gst_on_expense : 0),
                                        'total_owed_to_ato' => $gst_on_invoice, 'total_owed_by_ato' => $gst_on_expense,
                                        'gst_activity' => !empty($gst_details['gst_activity']) ? array_merge($gst_details['gst_activity'], ['gst_sales_1a' => $gst_on_invoice, 'gst_purchases_1b' => $gst_on_expense, 'total_sales_g1' => $total_sales]) : [],
                                        'paygw_activity' => !empty($paygw_details['paygw_activity']) ? $paygw_details['paygw_activity'] : '',
                                        'paygi_activity' => !empty($paygi_details['paygi_activity']) ? $paygi_details['paygi_activity'] : ''
                                    ];
                
            $pagedata['tax_rates'] = $transactions;
           
        return view('activity_statement.bas-and-tax-statement', $pagedata);
    }

    public function store(Request $request)
    {
        $userinfo = $request->get('userinfo');
        
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Bas and Tax Statement'
        );
        
        try{
            \DB::beginTransaction();
            $gstadjustments = '';
            $bas_and_tax = ActivityStatements::updateOrCreate([
                    'user_id' => $userinfo[0],
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                ],
                [
                    'user_id' => $userinfo[0],
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'activity_statement_status' => $request->activity_statement_status, 
                    'gst_calculation_period' => $request->gst_calculation_period, 
                    'payg_withhold_period' => !empty($request->payg_withholding_period) ? $request->payg_withholding_period : 'none',
                    'payg_income_tax_method' => !empty($request->payg_income_tax_method) ? $request->payg_income_tax_method : 'none',
                    'payment_amount' => !empty($request->payment_amount) ? $request->payment_amount : 0 ,
                    'payment_type' => !empty($request->payment_type) ? $request->payment_type : '',
                    'abn' => $request->abn,
                    'gst_accounting_method' => $request->gst_accounting_method,
                    'total_owed_by_ato' => $request->total_owed_by_ato,
                    'total_owed_to_ato' => $request->total_owed_to_ato,
                ]);
                
                if(!empty($request->payg_withholding_period) && $request->payg_withholding_period != 'none')
                {
                    PaygWithheldActivityStatement::updateOrCreate([
                        'activity_id' => $bas_and_tax->id,
                        'start_date' => $request->start_date,
                        'end_date' => $request->end_date,
                    ],
                    [
                        'activity_id' => $bas_and_tax->id,
                        'start_date' => $request->start_date,
                        'end_date' => $request->end_date,
                        'payg_tax_withheld' => $request->payg_tax_withheld,
                        'payg_withheld_w1' => $request->payg_withheld_w1,
                        'payg_withheld_w2' => $request->payg_withheld_w2, 
                        'payg_withheld_w3' => $request->payg_withheld_w3,
                        'payg_withheld_w4' => $request->payg_withheld_w4,
                        'payg_withheld_w5' => $request->payg_withheld_w5,
                    
                    ]);
                }
                if(!empty($request->gst_calculation_period)){
                    $gst_activity = GstActivityStatement::updateOrCreate([
                        'activity_id' => $bas_and_tax->id,
                        'start_date' => $request->start_date,
                        'end_date' => $request->end_date,
                    ],
                    [
                        'activity_id' => $bas_and_tax->id,
                        'start_date' => $request->start_date,
                        'end_date' => $request->end_date,
                        'total_sales_g1' => NumberFormat::string_replace($request->total_sales_g1),
                        'gst_sales_1a' => NumberFormat::string_replace($request->gst_sales_1a),
                        'gst_purchases_1b' => NumberFormat::string_replace($request->gst_purchases_1b),

                    ]);

                    if(!empty($gst_activity)){
                        if(!empty( $request->adjustment_g1 = json_decode($request->adjustment_g1, true)))
                        {
                            foreach($request->adjustment_g1 as $adjustment_g1)
                            {
                                $adjustment_g1 = AdjustmentG1::updateOrCreate([
                                    'id' => !empty($adjustment_g1['id']) ? $adjustment_g1['id'] : 0,
                                    'gst_activity_id' => $gst_activity->id,
                                    
                                ],
                                [
                                    'gst_activity_id' => $gst_activity->id,
                                    'adjust_by' => $adjustment_g1['adjust_by'],
                                    'reason' => $adjustment_g1['reason'],
                                    
                                ]); 
                            }
                        }
                        if(!empty( $request->adjustment_1a = json_decode($request->adjustment_1a, true) ))
                        {
                            foreach($request->adjustment_1a as $adjustment_1a)
                            {
                                $adjustment_1a = Adjustment1A::updateOrCreate([
                                    'id' => !empty($adjustment_1a['id']) ? $adjustment_1a['id'] : 0,
                                    'gst_activity_id' => $gst_activity->id,
                                ],
                                [
                                    'gst_activity_id' => $gst_activity->id,
                                    'adjust_by' => $adjustment_1a['adjust_by'],
                                    'reason' => $adjustment_1a['reason'],
                                ]); 
                            }
                        }

                        if(!empty( $request->adjustment_1b = json_decode($request->adjustment_1b, true) ))
                        {
                            foreach($request->adjustment_1b as $adjustment_1b)
                            {
                                $adjustment_1b = Adjustment1B::updateOrCreate([
                                    'id' => !empty($adjustment_1b['id']) ? $adjustment_1b['id'] : 0,
                                    'gst_activity_id' => $gst_activity->id,
                                ],
                                [
                                    'gst_activity_id' => $gst_activity->id,
                                    'adjust_by' => $adjustment_1b['adjust_by'],
                                    'reason' => $adjustment_1b['reason'],
                                ]); 
                            }
                        }

                        $gstadjustments = GstActivityStatement::with(['adjustmentG1', 'adjustment1A', 'adjustment1B'])->where('id', $gst_activity->id)->first();
                    }
                }

                if(!empty($request->payg_income_tax_method) && $request->payg_income_tax_method != 'none'){
                    PaygIncomeTaxInstalment::updateOrCreate([
                        'activity_id' => $bas_and_tax->id,
                        'start_date' => $request->start_date,
                        'end_date' => $request->end_date,
                    ],
                    [
                        'activity_id' => $bas_and_tax->id,
                        'start_date' => $request->start_date,
                        'end_date' => $request->end_date,
                        'reason_code_t4' => $request->reason_code_t4,
                        'payg_income_tax_instalment_credit' => $request->payg_income_tax_instalment_credit,
                        'payg_income_tax_instalment_5a' => $request->payg_income_tax_instalment_5a,
                        'option_1' => ['instalment_t7' => $request->instalment_t7, 'yearly_estimated_tax_t8' => $request->yearly_estimated_tax_t8, 'quarterly_varied_amount_t9' => $request->quarterly_varied_amount_t9 ],
                        'option_2' => ['instalment_t1' => $request->instalment_t1, 'instalment_rate_percentage_t2' => $request->instalment_rate_percentage_t2, 'varied_rate_percentage_t3' => $request->varied_rate_percentage_t3, 'income_tax_instalment_t11' => $request->income_tax_instalment_t11],
                    ]);
                }

            \DB::commit();
            
            return response()->json([

                'message' => 'Activity statement',
                'statement' => $bas_and_tax,
                'adjustments' => $gstadjustments
            ],200);
        
        }catch(\Exceptions $e){

            \DB::rollback();

            \Log::error($e);

        }
    }

    public function edit(Request $request)
    {
        $userinfo = $request->get('userinfo');
        $pagedata = array(
            'userinfo' => $userinfo,
            'pagetitle' => 'Bas and Tax Statement'
        );
        $response = $this->getBasAndTax($request);

        if(!empty($response['statement']))
        {
            return view('activity_statement.bas-and-tax-statement', $response);
        }
        return redirect()->route('bas/overview');
    }

    private function getBasAndTax(Request $request)
    {
        $userinfo = $request->get('userinfo');
        
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Bas and Tax Statement'
        );

        $response = ActivityStatements::with(['gstActivity', 'gstActivity.adjustmentG1', 'gstActivity.adjustment1A', 'gstActivity.adjustment1B', 'paygwActivity', 'paygiActivity'])
                        ->where('user_id', $userinfo[0])
                        ->where('start_date', $request->start_date)
                        ->where('end_date', $request->end_date)->first();

        $transactions = []; 
        $amounts = [];
            if(!empty($response)){
               
                $response = $response->toArray();
                
                $gst_start_date = '';
                $gst_end_date = !empty($response['gst_activity']) ? $response['gst_activity']['end_date'] : '';
                if(!empty($response['gst_activity']) && $response['gst_calculation_period']  == 'monthly')
                {
                    $gst_start_date = Carbon::createFromFormat('Y-m-d', $response['gst_activity']['end_date'])->startOfMonth()->format('Y-m-d');
                    $response['gst_activity']['date'] = Carbon::createFromFormat('Y-m-d', $response['gst_activity']['end_date'])->format('M').'-'.Carbon::createFromFormat('Y-m-d', $response['gst_activity']['end_date'])->format('Y');
                }
                if(!empty($response['gst_activity']) && $response['gst_calculation_period'] == 'quarterly')
                {
                    $gst_start_date = Carbon::createFromFormat('Y-m-d', $response['gst_activity']['end_date'])->subMonth(2)->startOfMonth()->format('Y-m-d');

                    $response['gst_activity']['date'] = Carbon::createFromFormat('Y-m-d', $response['gst_activity']['start_date'])->format('M').'-'.Carbon::createFromFormat('Y-m-d', $response['gst_activity']['end_date'])->format('M').''.Carbon::createFromFormat('Y-m-d', $response['gst_activity']['end_date'])->format('Y');
                }
                if(!empty($response['gst_activity']) && $response['gst_calculation_period'] == 'annually')
                {
                    $gst_start_date = Carbon::createFromFormat('Y-m-d', $response['gst_activity']['start_date'])->subMonth(11)->startOfMonth()->format('Y-m-d');
                    $response['gst_activity']['date'] = Carbon::createFromFormat('Y-m-d', $response['gst_activity']['start_date'])->format('M').''.Carbon::createFromFormat('Y-m-d', $response['gst_activity']['start_date'])->format('Y').'-'.
                                                        Carbon::createFromFormat('Y-m-d', $response['gst_activity']['end_date'])->format('M').''.Carbon::createFromFormat('Y-m-d', $response['gst_activity']['end_date'])->format('Y');
                }

                if(!empty($response['paygw_activity']) && $response['payg_withhold_period']  == 'monthly')
                {
                    $response['paygw_activity']['date'] = Carbon::createFromFormat('Y-m-d', $response['paygw_activity']['end_date'])->format('M').'-'.Carbon::createFromFormat('Y-m-d', $response['paygw_activity']['end_date'])->format('Y');
                }
                if(!empty($response['paygw_activity']) && $response['payg_withhold_period']  == 'quarterly')
                {
                    $response['paygw_activity']['date'] = Carbon::createFromFormat('Y-m-d', $response['paygw_activity']['start_date'])->format('M').'-'.Carbon::createFromFormat('Y-m-d', $response['paygw_activity']['end_date'])->format('M').' '.Carbon::createFromFormat('Y-m-d', $response['paygw_activity']['end_date'])->format('Y');
                }

                if(!empty($response['paygi_activity']) && $response['payg_income_tax_method']  != 'none')
                {
                    $response['paygi_activity']['date'] = Carbon::createFromFormat('Y-m-d', $response['paygi_activity']['start_date'])->format('M').'-'.Carbon::createFromFormat('Y-m-d', $response['paygi_activity']['end_date'])->format('M').' '.Carbon::createFromFormat('Y-m-d', $response['paygi_activity']['end_date'])->format('Y');
                    
                }

                $response['gst_accounting_method'] == 'cash' ? $column = 'payment_date' : $column = 'issue_date';

                $gst_on_invoice = 0; $gst_on_expense = 0; $total_sales = 0;
                $transaction_reponse = SumbChartAccountsTypeParticulars::selectRaw('sumb_invoice_tax_rates.id as tax_rate_id, sumb_invoice_tax_rates.*, sumb_chart_accounts_particulars.id as account_id, 
                    sumb_chart_accounts_particulars.*, transactions.id as particulars_id, transactions.*, 
                    transaction_collections.id as primary_transaction_id, transaction_collections.*')
                    ->leftJoin('transactions', 'transactions.parts_chart_accounts_id', '=', 'sumb_chart_accounts_particulars.id')
                    ->leftJoin('sumb_invoice_tax_rates', 'sumb_invoice_tax_rates.id', '=', 'transactions.parts_tax_rate_id')
                    ->leftJoin('transaction_collections', 'transaction_collections.id', '=', 'transactions.transaction_collection_id')
                    ->where('transaction_collections.user_id', $userinfo[0])
                    ->whereIn('transaction_collections.status', ['Paid'])
                    ->whereBetween('transaction_collections.'.$column, [$gst_start_date, $gst_end_date])
                    ->where('transaction_collections.is_active', 1)
                    ->get();

                    foreach($transaction_reponse as $transaction){

                        //transaction_type
                        if(!isset($transactions[$transaction['tax_rate_id']])){
                            
                            $transactions[$transaction['tax_rate_id']] = [
                                'tax_rates_name' => $transaction['tax_rates_name'],
                                'tax_rate_id' => $transaction['tax_rate_id'],
                                'total_gross_amount' => 0,
                                'total_gst_amount' => 0,
                                'total_net_amount' => 0,
                                'gst_on_expense' => 0,
                                'gst_on_invoice' => 0,
                                'accounts' => [],
                            ];
                        }
                        

                        $invoice_credit_amount=0;$gst_amount = 0; 
                        if(isset($transactions[$transaction['tax_rate_id']])){

                            //accounts
                            if(!in_array($transaction['particulars_id'], array_column($transactions[$transaction['tax_rate_id']]['accounts'], 'particulars_id'))){
                                
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

                                if($transaction['tax_rates_name'] == 'GST on Expenses (10%)')
                                {
                                    $gst_on_expense += $gst_amount;
                                    

                                }else if($transaction['tax_rates_name'] == 'GST on Invoice (10%)' || 
                                            $transaction['tax_rates_name'] == 'GST Free Income')
                                {
                                    $gst_on_invoice += $gst_amount;
                                    $total_sales += $invoice_credit_amount;
                                }

                                $transactions[$transaction['tax_rate_id']]['accounts'][] = [
                                    'transaction_id' => $transaction['primary_transaction_id'],
                                    'transaction_type' => $transaction['transaction_type'],
                                    'transaction_number' => $transaction['transaction_number'],
                                    'particulars_id' => $transaction['particulars_id'],
                                    'parts_gross_amount' => $transaction['parts_amount'],
                                    'parts_gst_amount' => $gst_amount,
                                    'parts_net_amount' => $invoice_credit_amount,
                                    'parts_description' => $transaction['parts_description'],
                                    'date' => date('d/m/Y', strtotime($transaction[$column])),
                                    'chart_accounts_particulars_code' => $transaction['chart_accounts_particulars_code'],
                                    'chart_accounts_particulars_name' => $transaction['chart_accounts_particulars_name'],
                                    
                                ];


                                $transactions[$transaction['tax_rate_id']]['total_gross_amount'] += $transaction['parts_amount'];
                                $transactions[$transaction['tax_rate_id']]['total_net_amount'] += $invoice_credit_amount;
                                $transactions[$transaction['tax_rate_id']]['total_gst_amount'] += $gst_amount;
                                
                            }
                        }
                    }
                    
                sort($transactions);
            }
            
        $pagedata['tax_rates'] = $transactions;
        $pagedata['statement'] = !empty($response) ? $response : '';

        return $pagedata;
    }

    public function destroy(Request $request)
    {
        try{
            $userinfo = $request->get('userinfo');
            $delete_statement = ActivityStatements::where('user_id', $userinfo[0])
                        ->where('id', $request->activity_id)
                        ->where('start_date', $request->start_date)
                        ->where('end_date', $request->end_date)
                        ->delete();
            if($delete_statement)
            {
                return response()->json([

                    'message' => 'Activity statement deleted',

                ],200);
            }
            else
            {
                return response()->json([

                    'message' => 'Invalid details',

                ],422);
            }
        }
        catch(\Exceptions $e){

            \Log::error($e);

        }
    }
}
