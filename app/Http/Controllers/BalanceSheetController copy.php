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


class BalanceSheetController extends Controller
{
    public function __construct() {
    }

    public function index(Request $request) { //this is interesting
        $userinfo =$request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Profit and Loss'
        );

        $today = Carbon::now()->firstOfMonth(); //Current Date and Time
        $todays = Carbon::parse($today)->toDateString();
        $start_date = Carbon::createFromFormat('Y-m-d', $todays);
        $last_date_of_month = Carbon::parse($today)->toDateString();
        $end_date = Carbon::createFromFormat('Y-m-d', $last_date_of_month);


        // $request->start_date = !empty($request->start_date) ? Carbon::createFromFormat('m/d/Y', $request->start_date)->format('Y-m-d') : ($start_date)->format('Y-m-d');
        $request->end_date = !empty($request->end_date) ? Carbon::createFromFormat('m/d/Y', $request->end_date)->format('Y-m-d') : ($end_date)->format('Y-m-d');

        $year=Carbon::createFromFormat('Y-m-d', $request->end_date)->format('Y');
        $request->start_date = Carbon::create($year)->startOfMonth()->format('Y-m-d');

        $transaction_dates = [];

            $current_date = Carbon::now();

            $month=Carbon::createFromFormat('Y-m-d', $request->start_date)->format('m');
            $year=Carbon::createFromFormat('Y-m-d', $request->start_date)->format('Y');

            // echo  $current_date->format('Y');die();

            $compare_start_date = Carbon::create($year, $month)->startOfMonth()->format('Y-m-d');
            $compare_end_date = Carbon::create($year, $month)->endOfMonth()->format('Y-m-d');


        if($compare_start_date == $request->start_date && $compare_end_date == $request->end_date){
            $display_date = Carbon::createFromFormat('Y-m-d', $request->start_date)->format('M Y');
            $transaction_dates[] = ['start_date' => $request->start_date, 'end_date' => $request->end_date, 'display_date' => $display_date];
        }else{
            $display_date = Carbon::createFromFormat('Y-m-d', $request->start_date)->format('d M').'-'.Carbon::createFromFormat('Y-m-d', $request->end_date)->format('d M Y');
            $transaction_dates[] = ['start_date' => $request->start_date, 'end_date' => $request->end_date, 'display_date' => $display_date];
        }

        $current_month_year = false;
        $current_month_year = $current_date->format('m Y') == $month.' '.$year ? true : '';
        if(!empty($request->compare_with_type && $request->compare_period) && $request->compare_with_type == 'months'){
            $counts = $request->compare_period;

            for($i=1; $i<=$counts; $i++){

                if($compare_start_date == $request->start_date && $compare_end_date == $request->end_date){
                    $start_date = Carbon::now()->subMonth($current_month_year ? $i : $i+1)->startOfMonth()->toDateString();
                    $end_date = Carbon::now()->subMonth($current_month_year ? $i : $i+1)->endOfMonth()->toDateString();
                    $display_date = Carbon::createFromFormat('Y-m-d', $start_date)->format('M Y');

                }else{
                    $start_date = date('Y-m-d', strtotime($request->start_date. ' -'.$i.' months'));
                    $end_date = date('Y-m-d', strtotime($request->end_date. ' -'.$i.' months'));

                    $display_date = Carbon::createFromFormat('Y-m-d', $start_date)->format('d M').'-'.Carbon::createFromFormat('Y-m-d', $end_date)->format('d M Y');
                }
                $transaction_dates[] = ['start_date' => $start_date, 'end_date' => $end_date, 'display_date' => $display_date];
            }
        }

        else if(!empty($request->compare_with_type && $request->compare_period) && $request->compare_with_type == 'quarters'){
            $counts = $request->compare_period;
            $j = 3;
            for($i=1; $i<=$counts; $i++){
                if($compare_start_date == $request->start_date && $compare_end_date == $request->end_date){
                    $start_date = Carbon::now()->subMonth($current_month_year ? $j : $j+1)->startOfMonth()->toDateString();
                    $end_date = Carbon::now()->subMonth($current_month_year ? $j : $j+1)->endOfMonth()->toDateString();
                    $display_date = Carbon::createFromFormat('Y-m-d', $start_date)->format('M Y');
                }else{
                    $start_date = Carbon::createFromFormat('Y-m-d', $request->start_date)->subQuarter($i)->toDateString();
                    $end_date = Carbon::createFromFormat('Y-m-d', $request->end_date)->subQuarter($i)->toDateString();

                    $display_date = Carbon::createFromFormat('Y-m-d', $start_date)->format('d M').'-'.Carbon::createFromFormat('Y-m-d', $end_date)->format('d M Y');
                }
                $transaction_dates[] = ['start_date' => $start_date, 'end_date' => $end_date, 'display_date' => $display_date];
                // echo "<pre>"; var_dump($j);echo "</pre>";

                $j+=3;
            }
        }
        else if(!empty($request->compare_with_type && $request->compare_period) && $request->compare_with_type == 'years'){
            $counts = $request->compare_period;
            $j = 12;
            for($i=1; $i<=$counts; $i++){
                if($compare_start_date == $request->start_date && $compare_end_date == $request->end_date){
                    $start_date = Carbon::now()->subMonth($current_month_year ? $j : $j+1)->startOfMonth()->toDateString();
                    $end_date = Carbon::now()->subMonth($current_month_year ? $j : $j+1)->endOfMonth()->toDateString();
                    $display_date = Carbon::createFromFormat('Y-m-d', $start_date)->format('M Y');
                }else{
                    $start_date = Carbon::createFromFormat('Y-m-d', $request->start_date)->subYears($i)->toDateString();
                    $end_date = Carbon::createFromFormat('Y-m-d', $request->end_date)->subYears($i)->toDateString();
                    $display_date = Carbon::createFromFormat('Y-m-d', $start_date)->format('d M').'-'.Carbon::createFromFormat('Y-m-d', $end_date)->format('d M Y');
                }
                $transaction_dates[] = ['start_date' => $start_date, 'end_date' => $end_date, 'display_date' => $display_date];
                $j+=12;
            }
        }


        $pagedata['items'] = [];
        $pagedata['items_metadata'] = ['accounts' => []];

        foreach($transaction_dates as $transaction_date){
            $pagedata['items'][] = $this->getTransactions($request, $userinfo, $transaction_date['start_date'], $transaction_date['end_date'], $pagedata['items_metadata'], $transaction_date['display_date']);
        }

        //  echo "<pre>"; var_dump( $pagedata['items']);echo "</pre>";
        // die();
        $pagedata['start_date'] = $request->start_date ? Carbon::createFromFormat('Y-m-d', $request->start_date)->format('m/d/Y') : '';
        $pagedata['end_date'] = $request->end_date ? Carbon::createFromFormat('Y-m-d', $request->end_date)->format('m/d/Y') : '';
        $pagedata['compare_with_type'] = $request->compare_with_type;
        $pagedata['compare_period'] = $request->compare_period;

        return view('reports.balancesheet', $pagedata);
    }

    private function getTransactions($request, $userinfo, $start_date, $end_date, &$items_metadata, $display_date)
    {
        $transactions = [];

        $transaction_reponse = SumbChartAccounts::selectRaw('sumb_chart_accounts.id as chart_accounts_id, sumb_chart_accounts.*, sumb_chart_accounts_type.id as account_type_id, sumb_chart_accounts_type.*, sumb_chart_accounts_particulars.id as account_parts_id,
                        sumb_chart_accounts_particulars.*, transactions.id as particulars_id, transactions.*,
                        transaction_collections.id as primary_invoice_id, transaction_collections.*')
                    ->leftJoin('sumb_chart_accounts_type', 'sumb_chart_accounts_type.chart_accounts_id', '=', 'sumb_chart_accounts.id')
                    ->leftJoin('sumb_chart_accounts_particulars', 'sumb_chart_accounts_particulars.chart_accounts_type_id', '=', 'sumb_chart_accounts_type.id')
                    ->leftJoin('transactions', 'transactions.parts_chart_accounts_id', '=', 'sumb_chart_accounts_particulars.id')
                    ->leftJoin('transaction_collections', 'transaction_collections.id', '=', 'transactions.transaction_collection_id')
                    ->where('transaction_collections.user_id', $userinfo[0])
                    ->whereIn('transaction_collections.status', ['Paid'])
                    ->whereBetween('transaction_collections.issue_date', [$start_date, $end_date])
                    ->where('transaction_collections.is_active', 1)
                    ->orderBy('chart_accounts_name')
                    ->get();


        if(!empty($transaction_reponse)){
            $transaction_reponse = $transaction_reponse->toArray();
            $total_invoice = 0;
            $total_expense = 0;
            $total_net_profit = 0;
            foreach($transaction_reponse as $transaction){

                //accounts
                if(!isset($transactions[$transaction['chart_accounts_id']])){
                    $transactions[$transaction['chart_accounts_id']] = [
                        'chart_accounts_id' => $transaction['chart_accounts_id'],
                        'chart_accounts_name' => $transaction['chart_accounts_name'],
                        'start_date' => Carbon::createFromFormat('Y-m-d', $start_date)->format('m/d/Y'),
                        'end_date' => Carbon::createFromFormat('Y-m-d', $end_date)->format('m/d/Y'),
                        'chart_accounts_types' => [],
                    ];
                }

                //account_types
                if(!in_array($transaction['account_type_id'], array_column($transactions[$transaction['chart_accounts_id']]['chart_accounts_types'], 'account_type_id'))){
                    $transactions[$transaction['chart_accounts_id']]['chart_accounts_types'][$transaction['account_type_id']] = [
                        'account_type_id' => $transaction['account_type_id'],
                        'chart_accounts_type' => $transaction['chart_accounts_type'],
                        'start_date' => Carbon::createFromFormat('Y-m-d', $start_date)->format('m/d/Y'),
                        'end_date' => Carbon::createFromFormat('Y-m-d', $end_date)->format('m/d/Y'),
                        'chart_account_particulars' => [],
                    ];
                }

                if(!isset($transactions[$transaction['chart_accounts_id']]['chart_accounts_types'][$transaction['account_type_id']]['chart_account_particulars'][$transaction['account_parts_id']]))
                {
                    $transactions[$transaction['chart_accounts_id']]['chart_accounts_types'][$transaction['account_type_id']]['chart_account_particulars'][$transaction['account_parts_id']] = ['account_parts_id' => $transaction['account_parts_id'], 'chart_accounts_particulars_name' => $transaction['chart_accounts_particulars_name'], 'chart_accounts_particulars_code' => $transaction['chart_accounts_particulars_code'], 'parts_amount' => 0];
                }

                $transactions[$transaction['chart_accounts_id']]['chart_accounts_types'][$transaction['account_type_id']]['chart_account_particulars'][$transaction['account_parts_id']]['parts_amount'] += $transaction['parts_amount'];

                /*
                 * metaadata
                 */
                if(!isset($items_metadata['accounts'][$transaction['chart_accounts_id']])){
                    $items_metadata['accounts'][$transaction['chart_accounts_id']] = ['chart_accounts_id' =>$transaction['chart_accounts_id'],  'chart_accounts_name' =>$transaction['chart_accounts_name'], 'chart_accounts_types' => []];
                }

                if(!isset($items_metadata['accounts'][$transaction['chart_accounts_id']]['chart_accounts_types'][$transaction['account_type_id']])){
                    $items_metadata['accounts'][$transaction['chart_accounts_id']]['chart_accounts_types'][$transaction['account_type_id']] = [
                        'account_type_id' => $transaction['account_type_id'],
                        'chart_accounts_type' => $transaction['chart_accounts_type']
                    ];
                }

                if(!isset($items_metadata['accounts'][$transaction['chart_accounts_id']]['chart_accounts_types'][$transaction['account_type_id']]['chart_account_particulars'][$transaction['account_parts_id']])){
                    $items_metadata['accounts'][$transaction['chart_accounts_id']]['chart_accounts_types'][$transaction['account_type_id']]['chart_account_particulars'][$transaction['account_parts_id']] = ['account_parts_id' => $transaction['account_parts_id'], 'chart_accounts_particulars_name' => $transaction['chart_accounts_particulars_name'], 'chart_accounts_particulars_code' => $transaction['chart_accounts_particulars_code']];
                }
            }
        }

        sort($transactions);
        // echo "<pre>"; var_dump($transactions);echo "</pre>";
        // die();
        $total_net_profit = ($total_invoice - $total_expense);
        return ['start_date'=>$start_date, 'end_date'=>$end_date, 'display_date' => $display_date, 'transactions'=> $transactions, 'total_invoice' => $total_invoice, 'total_expense' => $total_expense, 'total_net_profit' => $total_net_profit];
    }
}

?>
