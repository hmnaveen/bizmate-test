<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use App\Models\SumbChartAccountsTypeParticulars;
use App\Models\SumbChartAccountsType;
use App\Models\SumbChartAccounts;
use App\Models\SumbInvoiceTaxRates;
use App\Models\SumbInvoiceParticulars;
use App\Models\SumbInvoiceDetails;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class ProfitAndLossController extends Controller
{
    public function __construct() {

    }

    public function index(Request $request) {
        $userinfo =$request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Profit and Loss'
        );

        $today = Carbon::now()->firstOfMonth(); //Current Date and Time 
        $todays = Carbon::parse($today)->toDateString();
        $start_date = Carbon::createFromFormat('Y-m-d', $todays); 
        $last_date_of_month = Carbon::parse($today)->endOfMonth()->toDateString(); 
        $end_date = Carbon::createFromFormat('Y-m-d', $last_date_of_month);

        $request->start_date = !empty($request->start_date) ? Carbon::createFromFormat('m/d/Y', $request->start_date)->format('Y-m-d') : ($start_date)->format('Y-m-d');
        $request->end_date = !empty($request->end_date) ? Carbon::createFromFormat('m/d/Y', $request->end_date)->format('Y-m-d') : ($end_date)->format('Y-m-d');

        $datas = SumbChartAccountsTypeParticulars::selectRaw('sumb_chart_accounts_particulars.id as account_id, 
                sumb_chart_accounts_particulars.*, transactions.id as particulars_id, transactions.*, 
                transaction_collections.id as primary_invoice_id, transaction_collections.*')
                ->leftJoin('transactions', 'transactions.parts_chart_accounts_id', '=', 'sumb_chart_accounts_particulars.id')
                ->leftJoin('transaction_collections', 'transaction_collections.id', '=', 'transactions.transaction_collection_id')
                ->where('transaction_collections.user_id', $userinfo[0])
                ->whereIn('transaction_collections.status', ['Paid'])
                ->whereBetween('transaction_collections.issue_date', [$request->start_date, $request->end_date])
                ->where('transaction_collections.is_active', 1)
                ->get();
             
        $datas = $datas->toArray();
        
        $invoice_details = [];  
        $invoice_total = 0;$expense_total = 0;$total_net_profit = 0;
        foreach($datas as $key => $val){
            if(!isset($invoice_details[$val['transaction_type']])){
                $invoice_details[$val['transaction_type']] = [
                    'transaction_type' => $val['transaction_type'],
                    'accounts' => [],
                ];
            }

            if(isset($invoice_details[$val['transaction_type']])){
                if(!in_array($val['account_id'], array_column($invoice_details[$val['transaction_type']]['accounts'], 'account_id'))){
                    $invoice_details[$val['transaction_type']]['accounts'][] = [
                        'account_id' => $val['account_id'],
                        'chart_accounts_particulars_code' => $val['chart_accounts_particulars_code'],
                        'chart_accounts_particulars_name' => $val['chart_accounts_particulars_name'],
                        'particulars' => [],
                    ];
                }
            }
            
            if(in_array($val['account_id'], array_column($invoice_details[$val['transaction_type']]['accounts'], 'account_id'))){
                $particular_invoice_id_index = array_search($val['account_id'], array_column($invoice_details[$val['transaction_type']]['accounts'], 'account_id'));
                if($particular_invoice_id_index !== false && isset($invoice_details[$val['transaction_type']]['accounts'][$particular_invoice_id_index]['particulars'])){
                    if($val['transaction_type'] == 'invoice'){
                        $invoice_total += $val['parts_amount'];
                    }
                    if($val['transaction_type'] == 'expense'){
                        $expense_total += $val['parts_amount'];
                    }
                    $total_net_profit = $invoice_total-$expense_total;

                    $invoice_details[$val['transaction_type']]['accounts'][$particular_invoice_id_index]['particulars'][] = [
                        'particulars_id' => $val['particulars_id'],
                        'transaction_collection_id' => $val['transaction_collection_id'],
                        'parts_amount' => $val['parts_amount'],
                    ];
                }
            }
        }
        sort($invoice_details);
        $total_profit_loss = ['total_cost_of_sale' => $invoice_total, 'total_operating_expenses' => $expense_total, 'total_net_profit' => $total_net_profit ];



        // $data = SumbChartAccountsTypeParticulars::with(['transactions', 'transactions.transactionCollection'])
        //     ->whereHas('transactions', function($query) use($userinfo) {
        //         $query->where('user_id', $userinfo[0]);
        //     })
        //     ->whereHas('transactions.transactionCollection', function($query) use($userinfo, $request) {
        //         $query->where('user_id', $userinfo[0])
        //         ->whereBetween('transaction_collections.issue_date', [$request->start_date, $request->end_date]);
        //     })
        //     ->groupBy('id')
        //     ->where('user_id', $userinfo[0])->get();
            
        $pagedata['profit_loss_details'] = !empty($invoice_details) ? $invoice_details : '';
        echo "<pre>"; var_dump($pagedata['profit_loss_details']); echo "</pre>";
die();
        $pagedata['total_profit_loss'] = $total_profit_loss;
        $pagedata['start_date'] = $request->start_date ? Carbon::createFromFormat('Y-m-d', $request->start_date)->format('m/d/Y') : '';
        $pagedata['end_date'] = $request->end_date ? Carbon::createFromFormat('Y-m-d', $request->end_date)->format('m/d/Y') : '';

        return view('reports.profitandloss', $pagedata); 
    }

    public function reports(Request $request){
        $userinfo =$request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Transactions'
        );
        $today = Carbon::now()->firstOfMonth(); //Current Date and Time 
        $todays = Carbon::parse($today)->toDateString();
        $start_date = Carbon::createFromFormat('Y-m-d', $todays); 
        $last_date_of_month = Carbon::parse($today)->endOfMonth()->toDateString(); 
        $end_date = Carbon::createFromFormat('Y-m-d', $last_date_of_month);

        $request->report_start_date = !empty($request->report_start_date) ? Carbon::createFromFormat('m/d/Y', $request->report_start_date)->format('Y-m-d') : ($start_date)->format('Y-m-d');
        $request->report_end_date = !empty($request->report_end_date) ? Carbon::createFromFormat('m/d/Y', $request->report_end_date)->format('Y-m-d') : ($end_date)->format('Y-m-d');
       
        if(!empty($request->account_id)){
            $request->report_chart_accounts_ids = [$request->account_id];
        }
        
        $account_parts = SumbChartAccountsTypeParticulars::get();
            $data = SumbChartAccountsTypeParticulars::selectRaw('sumb_chart_accounts_particulars.id as account_id, sumb_chart_accounts_particulars.*, transactions.id as particulars_id, transactions.*, transaction_collections.id as primary_invoice_id, transaction_collections.*, sumb_invoice_tax_rates.id as tax_rate_id, sumb_invoice_tax_rates.*')
                    ->leftJoin('transactions', 'transactions.parts_chart_accounts_id', '=', 'sumb_chart_accounts_particulars.id')
                    ->leftJoin('transaction_collections', 'transaction_collections.id', '=', 'transactions.transaction_collection_id')
                    ->leftJoin('sumb_invoice_tax_rates', 'sumb_invoice_tax_rates.id', '=', 'transactions.parts_tax_rate_id')
                    ->where('transaction_collections.user_id', $userinfo[0])
                    ->where('transaction_collections.is_active', 1)
                    ->whereIn('transaction_collections.status', ['Paid']);
                    if(!empty($request->report_chart_accounts_ids)){
                        $data->whereIn('transactions.parts_chart_accounts_id', $request->report_chart_accounts_ids);
                    }
                    
                    $data->whereBetween('transaction_collections.issue_date', [$request->report_start_date, $request->report_end_date]);
                    $data =  $data->get();
        
            $data = $data->toArray();

        $invoice_details = [];
        $final_tansaction_amount = [];
        $final_expense_amount = 0;  
        $final_invoice_amount = 0; 
        $final_tax_amount = 0; 
        $final_gross_amount = 0; 
        foreach($data as $key => $val){
            if(!isset($invoice_details[$val['account_id']])){
                $invoice_details[$val['account_id']] = [
                    'account_id' => $val['account_id'],
                    'user_id' => $val['user_id'],
                    'chart_accounts_particulars_code' => $val['chart_accounts_particulars_code'],
                    'chart_accounts_particulars_name' => $val['chart_accounts_particulars_name'],
                    'chart_accounts_particulars_description' => $val['parts_description'],
                    'total_credits_amount' => 0,
                    'total_parts_amount' => 0,
                    'total_tax_amount' => 0,
                    'total_expense_credits_amount' => 0,
                    'total_expense_tax_amount' => 0,
                    'particulars' => [],
                    'type' => $val['transaction_type']
                ];
            }
        
            $gst_amount = 0;$total_gross = 0; $expense_credit_amount=0;$expense_gst_amount=0;$invoice_credit_amount=0;
            if(isset($invoice_details[$val['account_id']])){
                if(!in_array($val['particulars_id'], array_column($invoice_details[$val['account_id']]['particulars'], 'particulars_id'))){
                    
                    if($val['transaction_type'] == 'invoice'){
                        if($val['default_tax'] == 'tax_inclusive'){
                            $gst_amount = ($val['parts_amount'] - $val['parts_amount'] / (1 + $val['tax_rates']/100));
                            $invoice_credit_amount = $val['parts_amount'];
                        }
                        else if($val['default_tax'] == 'tax_exclusive'){
                            $gst_amount = ($val['parts_amount'] * $val['tax_rates']/100);
                            $invoice_credit_amount = $val['parts_amount'] - $gst_amount;
                        }else if($val['default_tax'] == 'no_tax'){
                            $gst_amount = 0;
                            $invoice_credit_amount = $val['parts_amount'];
                        }
                    }

                    if($val['transaction_type'] == 'expense'){
                        if($val['default_tax'] == 'tax_inclusive'){
                            $expense_gst_amount = ($val['parts_amount'] - $val['parts_amount'] / (1 + $val['tax_rates']/100));
                            $expense_credit_amount = $val['parts_amount'];
                        }
                        else if($val['default_tax'] == 'tax_exclusive'){
                            $expense_gst_amount = ($val['parts_amount'] * $val['tax_rates']/100);
                            $expense_credit_amount = $val['parts_amount'] - $expense_gst_amount;
                        }else if($val['default_tax'] == 'no_tax'){
                            $expense_gst_amount = 0;
                            $expense_credit_amount = $val['parts_amount'];
                        }
                    }
                    $invoice_details[$val['account_id']]['particulars'][] = [
                        'particulars_id' => $val['particulars_id'],
                        'invoice_id' => $val['transaction_collection_id'],
                        'invoice_parts_amount' => $val['parts_amount'],
                        'invoice_parts_credit_amount' => $invoice_credit_amount,
                        'invoice_parts_expense_credit_amount' => $expense_credit_amount,
                        'invoice_parts_description' => $val['parts_description'],
                        'invoice_default_tax' => $val['default_tax'],
                        'invoice_gst' => $gst_amount,
                        'expense_gst' => $expense_gst_amount,
                        'invoice_tax_rates' => [],
                        'type' => $val['transaction_type'],
                        'invoice' => [],
                    ];
                    
                    $final_expense_amount +=$expense_credit_amount;
                    $final_invoice_amount +=$invoice_credit_amount;

                    $final_tax_amount +=$gst_amount + $expense_gst_amount;
                    $final_gross_amount += $val['parts_amount'];

                    $invoice_details[$val['account_id']]['total_parts_amount'] += $val['parts_amount'];
                    $invoice_details[$val['account_id']]['total_credits_amount'] += $invoice_credit_amount;
                    $invoice_details[$val['account_id']]['total_tax_amount'] += $gst_amount + $expense_gst_amount;
                    $invoice_details[$val['account_id']]['total_expense_credits_amount'] += $expense_credit_amount;
                    $invoice_details[$val['account_id']]['total_expense_tax_amount'] += $expense_gst_amount;
                    // $invoice_details[$val['account_id']]['final_expense_amount'] += $expense_credit_amount;
                    
                }

                if(in_array($val['particulars_id'], array_column($invoice_details[$val['account_id']]['particulars'], 'particulars_id'))){
                    $particular_invoice_id_index = array_search($val['particulars_id'], array_column($invoice_details[$val['account_id']]['particulars'], 'particulars_id'));
                    if($particular_invoice_id_index !== false && isset($invoice_details[$val['account_id']]['particulars'][$particular_invoice_id_index]['invoice'])){
                        $invoice_details[$val['account_id']]['particulars'][$particular_invoice_id_index]['invoice'] = [
                            'primary_invoice_id' => $val['primary_invoice_id'],
                            'invoice_issue_date' => $val['issue_date'],
                            'invoice_due_date' => $val['due_date'],
                            'invoice_sub_total' => $val['sub_total'],
                            'invoice_total_gst' => $val['total_gst'],
                            'invoice_default_tax' => $val['default_tax'],
                        ];
                    }
                }

                if(in_array($val['particulars_id'], array_column($invoice_details[$val['account_id']]['particulars'], 'particulars_id'))){
                    $particular_id_index = array_search($val['particulars_id'], array_column($invoice_details[$val['account_id']]['particulars'], 'particulars_id'));
                    if($particular_id_index !== false && isset($invoice_details[$val['account_id']]['particulars'][$particular_id_index]['invoice_tax_rates'])){
                        $invoice_details[$val['account_id']]['particulars'][$particular_id_index]['invoice_tax_rates'] = [
                            'tax_rate_id' => $val['tax_rate_id'],
                            'tax_rates' => $val['tax_rates'],
                            'tax_rates_name' => $val['tax_rates_name'],
                        ];
                    }
                }
            }
        }
        
        $final_tansaction_details = ['final_expense_amount' => $final_expense_amount, 'final_invoice_amount' => $final_invoice_amount,
            'final_tax_amount' => $final_tax_amount, 'final_gross_amount'=>$final_gross_amount ];

        sort($invoice_details);

        // echo "<pre>"; var_dump($invoice_details);echo "</pre>"; die();
        $pagedata['transaction_details'] = !empty($invoice_details) ? $invoice_details : '';
        $pagedata['final_tansaction_details'] = !empty($final_tansaction_details) ? $final_tansaction_details : '';

        $pagedata['account_parts_code'] = $request->report_chart_accounts_ids ? $request->report_chart_accounts_ids : [];
        $pagedata['report_start_date'] = $request->report_start_date ? Carbon::createFromFormat('Y-m-d', $request->report_start_date)->format('m/d/Y') : '';
        $pagedata['report_end_date'] = $request->report_end_date ? Carbon::createFromFormat('Y-m-d', $request->report_end_date)->format('m/d/Y') : '';
        $pagedata['account_parts'] = $account_parts ? $account_parts->toArray() : '';

        return view('reports.reportslist', $pagedata);
    }
}

?>