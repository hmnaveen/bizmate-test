<?php 
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use App\Models\SumbChartAccountsTypeParticulars;
use App\Models\SumbChartAccountsType;
use App\Models\SumbChartAccounts;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Excel;
use App\Exports\TransactionExport;
use App\Exports\ProfitAndLossExport;

class ProfitAndLossController extends Controller
{
    public function __construct() {
        $this->middleware('invoice_seetings');
    }

    public function index(Request $request) {

        $data = $this->profitAndLoss($request);
        return view('reports.profitandloss', $data); 
    }


    public function exportProfitAndLoss(Request $request) 
    {
        $request->start_date = $request->export_start_date;
        $request->end_date = $request->export_end_date;
        $request->compare_with_type = $request->export_compare_with_type;
        $request->compare_period = $request->export_compare_period;

        $data = $this->profitAndLoss($request);

        return Excel::download(new ProfitAndLossExport($data), 'profitandloss.'.$request->export_file_type);
    }

    private function profitAndLoss($request)
    {
        $userinfo =$request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Profit and Loss'
        );
        //---------Drop-down filters starts-----//
            $filters = [];

            //Current month start and end date
            $current_month = Carbon::now()->firstOfMonth(); //Current Date and Time 
            $current_month = Carbon::parse($current_month)->toDateString();
            $current_month_start_date = Carbon::createFromFormat('Y-m-d', $current_month)->format('d/m/Y'); 
            $current_month_end_date = Carbon::parse($current_month)->endOfMonth()->format('d/m/Y'); 

            //Current quarter start and end date
            $current_quarter_start_date = Carbon::now()->firstOfQuarter()->format('d/m/Y');
            $current_quarter_end_date = Carbon::now()->lastOfQuarter()->format('d/m/Y');

            //Current year start and end date
            // $current_year_start_date = Carbon::now()->firstOfYear()->format('d/m/Y');
            // $current_year_end_date = Carbon::now()->lastOfYear()->format('d/m/Y');
            
            $current_year_start_date = Carbon::createFromFormat('Y-m-d', date('Y').'-07-01')->format('d/m/Y');
            $current_year_end_date = Carbon::createFromFormat('Y-m-d', date('Y').'-06-30')->addYear(1)->format('d/m/Y');

            //Last month start and end date
            $last_month_start_date = Carbon::now()->subMonth(1)->startOfMonth()->format('d/m/Y');
            $last_month_end_date = Carbon::now()->subMonth(1)->endOfMonth()->format('d/m/Y');

            //Last quarter start and end date
            $last_quarter_start_date = Carbon::createFromFormat('d/m/Y', $current_quarter_start_date)->subQuarter(1)->format('d/m/Y');
            $last_quarter_end_date = Carbon::createFromFormat('d/m/Y', $current_quarter_start_date)->subQuarter(1)->format('d/m/Y');

            //Last year start and end date
            $last_year_start_date = Carbon::createFromFormat('d/m/Y', $current_year_start_date)->subYears(1)->format('d/m/Y');
            $last_year_end_date = Carbon::createFromFormat('d/m/Y', $current_year_end_date)->subYears(1)->format('d/m/Y');
            
            //Month to date start and end date
            $month_to_end_date = Carbon::parse(Carbon::now())->format('d/m/Y');        
            
        $filters = 
        [
            'current_month' => json_encode(['start_date' => $current_month_start_date, 'end_date' => $current_month_end_date ]), 
            'current_month_display_date' => Carbon::createFromFormat('d/m/Y', $current_month_start_date)->format('M Y'),
            
            'current_quarter' => json_encode(['start_date' => $current_quarter_start_date, 'end_date' => $current_quarter_end_date ]),
            'current_quarter_display_date' => Carbon::createFromFormat('d/m/Y', $current_quarter_start_date)->format('j M') ." - ". Carbon::createFromFormat('d/m/Y', $current_quarter_end_date)->format('d M Y'), 
            
            'current_year' => json_encode(['start_date' => $current_year_start_date, 'end_date' => $current_year_end_date ]),
            'current_year_display_date' => Carbon::createFromFormat('d/m/Y', $current_year_start_date)->format('j M Y') ." - ". Carbon::createFromFormat('d/m/Y', $current_year_end_date)->format('d M Y'), 

            'last_month' => json_encode(['start_date' => $last_month_start_date, 'end_date' => $last_month_end_date ]),
            'last_month_display_date' => Carbon::createFromFormat('d/m/Y', $last_month_start_date)->format('M Y'), 

            'last_quarter' => json_encode(['start_date' => $last_quarter_start_date, 'end_date' => $last_quarter_end_date ]),
            'last_quarter_display_date' => Carbon::createFromFormat('d/m/Y', $last_quarter_start_date)->format('j M') ." - ". Carbon::createFromFormat('d/m/Y', $last_quarter_end_date)->format('d M Y'), 

            'last_year' => json_encode(['start_date' => $last_year_start_date, 'end_date' => $last_year_end_date ]),
            'last_year_display_date' => Carbon::createFromFormat('d/m/Y', $last_year_start_date)->format('j M Y') ." - ". Carbon::createFromFormat('d/m/Y', $last_year_end_date)->format('d M Y'), 

            'month_to_date' => json_encode(['start_date' => $current_month_start_date, 'end_date' => $month_to_end_date ]),
            'month_to_date_display_date' => Carbon::createFromFormat('d/m/Y', $current_month_start_date)->format('j M') ." - ". Carbon::createFromFormat('d/m/Y', $month_to_end_date)->format('d M Y'), 

            'quarter_to_date' => json_encode(['start_date' => $current_quarter_start_date, 'end_date' => $month_to_end_date ]),
            'quarter_to_date_display_date' => Carbon::createFromFormat('d/m/Y', $current_quarter_start_date)->format('j M') ." - ". Carbon::createFromFormat('d/m/Y', $month_to_end_date)->format('d M Y'), 

            'year_to_date' => json_encode(['start_date' => $current_year_start_date, 'end_date' => $month_to_end_date ]),
            'year_to_date_display_date' => Carbon::createFromFormat('d/m/Y', $current_year_start_date)->format('j M') ." - ". Carbon::createFromFormat('d/m/Y', $month_to_end_date)->format('d M Y'), 

        ];

        $pagedata['filters'] = $filters;

        //------------------Drop-down filters ends---------------//


        $today = Carbon::now()->firstOfMonth(); //Current Date and Time 
        $todays = Carbon::parse($today)->toDateString();
        $start_date = Carbon::createFromFormat('Y-m-d', $todays); 
        $last_date_of_month = Carbon::parse($today)->endOfMonth()->toDateString(); 
        $end_date = Carbon::createFromFormat('Y-m-d', $last_date_of_month);

        $request->start_date = !empty($request->start_date) ? Carbon::createFromFormat('d/m/yy', $request->start_date)->format('Y-m-d') : ($start_date)->format('Y-m-d');
        $request->end_date = !empty($request->end_date) ? Carbon::createFromFormat('d/m/yy', $request->end_date)->format('Y-m-d') : ($end_date)->format('Y-m-d');


        $transaction_dates = [];
        
        $current_date = Carbon::now();
        
        $month=Carbon::createFromFormat('Y-m-d', $request->start_date)->format('m');
        $year=Carbon::createFromFormat('Y-m-d', $request->start_date)->format('Y');

        // echo  $current_date->format('m Y');die();

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

        !empty($request->compare_with_type) && empty($request->compare_period) ? $request->compare_period = 1 : '';
        
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
                // echo "<pre>"; var_dump($transaction_dates);echo "</pre>";die();
                
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
                // echo "<pre>"; var_dump($j);echo "</pre>";
                
                $j+=12;
            }
        }
        

        $pagedata['items'] = [];
        $pagedata['items_metadata'] = ['invoice' =>[], 'expense' => [], 'minor_adjustment' => [], 'receive_money' => [], 'spend_money' => []];

        foreach($transaction_dates as $transaction_date){
            $pagedata['items'][] = $this->getTransactions($request, $userinfo, $transaction_date['start_date'], $transaction_date['end_date'], $pagedata['items_metadata'], $transaction_date['display_date']);
        }
        
        $pagedata['items_metadata']['invoice'] = array_unique($pagedata['items_metadata']['invoice']);
        $pagedata['items_metadata']['expense'] = array_unique($pagedata['items_metadata']['expense']);
        $pagedata['items_metadata']['minor_adjustment'] = array_unique($pagedata['items_metadata']['minor_adjustment']);

        $pagedata['start_date'] = $request->start_date ? Carbon::createFromFormat('Y-m-d', $request->start_date)->format('d/m/Y') : '';
        $pagedata['end_date'] = $request->end_date ? Carbon::createFromFormat('Y-m-d', $request->end_date)->format('d/m/Y') : '';
        $pagedata['compare_with_type'] = $request->compare_with_type;
        $pagedata['compare_period'] = $request->compare_period;
        $pagedata['date_filters'] = $request->date_filters;

        return $pagedata;
    }
    private function getTransactions($request, $userinfo, $start_date, $end_date, &$items_metadata, $display_date)
    {
        $transactions = [];

        $transaction_reponse = SumbChartAccountsTypeParticulars::selectRaw('sumb_chart_accounts_particulars.id as account_id, 
                sumb_chart_accounts_particulars.*, transactions.id as particulars_id, transactions.*, 
                transaction_collections.id as primary_invoice_id, transaction_collections.*')
                ->leftJoin('transactions', 'transactions.parts_chart_accounts_id', '=', 'sumb_chart_accounts_particulars.id')
                ->leftJoin('transaction_collections', 'transaction_collections.id', '=', 'transactions.transaction_collection_id')
                ->where('transaction_collections.user_id', $userinfo[0])
                ->whereIn('transaction_collections.status', ['Paid','PartlyPaid'])
                ->whereBetween('transaction_collections.issue_date', [$start_date, $end_date])
                ->where('transaction_collections.is_active', 1)
                ->get();

        if(!empty($transaction_reponse)){
            $transaction_reponse = $transaction_reponse->toArray();
            $total_invoice = 0;
            $total_expense = 0;
            $total_net_profit = 0;
            foreach($transaction_reponse as $transaction){

                //transaction_type
                if(!isset($transactions[$transaction['transaction_type']])){
                    $transactions[$transaction['transaction_type']] = [
                        'transaction_type' => $transaction['transaction_type'],
                        'accounts' => [],
                    ];
                }

                if(isset($transactions[$transaction['transaction_type']])){
                    
                    //accounts
                    if(!in_array($transaction['account_id'], array_column($transactions[$transaction['transaction_type']]['accounts'], 'account_id'))){
                        $transactions[$transaction['transaction_type']]['accounts'][] = [
                            'account_id' => $transaction['account_id'],
                            'start_date' => Carbon::createFromFormat('Y-m-d', $start_date)->format('d/m/Y'),
                            'end_date' => Carbon::createFromFormat('Y-m-d', $end_date)->format('d/m/Y'),
                            'chart_accounts_particulars_code' => $transaction['chart_accounts_particulars_code'],
                            'chart_accounts_particulars_name' => $transaction['chart_accounts_particulars_name'],
                            'particulars' => [],
                        ];

                        $items_metadata[$transaction['transaction_type']][] = $transaction['chart_accounts_particulars_name'];
                     
                    }

                    //particulars
                    $account_index = array_search($transaction['account_id'], array_column($transactions[$transaction['transaction_type']]['accounts'], 'account_id'));
                    $transactions[$transaction['transaction_type']]['accounts'][$account_index]['particulars'][] = [
                        'particulars_id' => $transaction['particulars_id'],
                        'transaction_collection_id' => $transaction['transaction_collection_id'],
                        'parts_amount' => $transaction['parts_amount'],
                    ];

                    if($transaction['transaction_type'] == 'invoice' || $transaction['transaction_type'] == 'receive_money'){
                        $total_invoice += $transaction['parts_amount'];
                    }
                    if($transaction['transaction_type'] == 'expense' || $transaction['transaction_type'] == 'spend_money' || $transaction['transaction_type'] == 'minor_adjustment'){
                        $total_expense += $transaction['parts_amount'];
                    }
                }
            }
        }
        sort($transactions);

        $total_net_profit = ($total_invoice - $total_expense);
        return ['start_date'=>$start_date, 'end_date'=>$end_date, 'display_date' => $display_date, 'transactions'=> $transactions, 'total_invoice' => $total_invoice, 'total_expense' => $total_expense, 'total_net_profit' => $total_net_profit];
    }


    public function reports(Request $request){
        
        $data = $this->transactions($request);

        return view('reports.reportslist', $data);
    }

    public function exportTransactions(Request $request)
    {
        $request->report_start_date = $request->export_start_date;
        $request->report_end_date = $request->export_end_date;
        $request->report_chart_accounts_ids = !empty($request->export_accounts) ? json_decode($request->export_accounts[0], true) : '';
        
        $data = $this->transactions($request);

        // echo "<pre>"; var_dump($pagedata);echo "</pre>"; die();
        return Excel::download(new TransactionExport($data), 'Transactions.'.$request->export_file_type);
    }

    private function transactions($request)
    {
        $userinfo =$request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Business Activity'
        );

        $filters = [];

            //Current month start and end date
            $current_month = Carbon::now()->firstOfMonth(); //Current Date and Time 
            $current_month = Carbon::parse($current_month)->toDateString();
            $current_month_start_date = Carbon::createFromFormat('Y-m-d', $current_month)->format('d/m/Y'); 
            $current_month_end_date = Carbon::parse($current_month)->endOfMonth()->format('d/m/Y'); 

            //Current quarter start and end date
            $current_quarter_start_date = Carbon::now()->firstOfQuarter()->format('d/m/Y');
            $current_quarter_end_date = Carbon::now()->lastOfQuarter()->format('d/m/Y');

            //Current year start and end date
            // $current_year_start_date = Carbon::now()->firstOfYear()->format('d/m/Y');
            // $current_year_end_date = Carbon::now()->lastOfYear()->format('d/m/Y');
            $current_year_start_date = Carbon::createFromFormat('Y-m-d', date('Y').'-07-01')->format('d/m/Y');
            $current_year_end_date = Carbon::createFromFormat('Y-m-d', date('Y').'-06-30')->addYear(1)->format('d/m/Y');

            //Last month start and end date
            $last_month_start_date = Carbon::now()->subMonth(1)->startOfMonth()->format('d/m/Y');
            $last_month_end_date = Carbon::now()->subMonth(1)->endOfMonth()->format('d/m/Y');

            //Last quarter start and end date
            $last_quarter_start_date = Carbon::createFromFormat('d/m/Y', $current_quarter_start_date)->subQuarter(1)->format('d/m/Y');
            $last_quarter_end_date = Carbon::createFromFormat('d/m/Y', $current_quarter_start_date)->subQuarter(1)->format('d/m/Y');

            //Last year start and end date
            $last_year_start_date = Carbon::createFromFormat('d/m/Y', $current_year_start_date)->subYears(1)->format('d/m/Y');
            $last_year_end_date = Carbon::createFromFormat('d/m/Y', $current_year_end_date)->subYears(1)->format('d/m/Y');
            
            //Month to date start and end date
            $month_to_end_date = Carbon::parse(Carbon::now())->format('d/m/Y');        
            
        $filters = 
        [
            'current_month' => json_encode(['start_date' => $current_month_start_date, 'end_date' => $current_month_end_date ]), 
            'current_month_display_date' => Carbon::createFromFormat('d/m/Y', $current_month_start_date)->format('M Y'),
            
            'current_quarter' => json_encode(['start_date' => $current_quarter_start_date, 'end_date' => $current_quarter_end_date ]),
            'current_quarter_display_date' => Carbon::createFromFormat('d/m/Y', $current_quarter_start_date)->format('j M') ." - ". Carbon::createFromFormat('d/m/Y', $current_quarter_end_date)->format('d M Y'), 
            
            'current_year' => json_encode(['start_date' => $current_year_start_date, 'end_date' => $current_year_end_date ]),
            'current_year_display_date' => Carbon::createFromFormat('d/m/Y', $current_year_start_date)->format('j M Y') ." - ". Carbon::createFromFormat('d/m/Y', $current_year_end_date)->format('d M Y'), 

            'last_month' => json_encode(['start_date' => $last_month_start_date, 'end_date' => $last_month_end_date ]),
            'last_month_display_date' => Carbon::createFromFormat('d/m/Y', $last_month_start_date)->format('M Y'), 

            'last_quarter' => json_encode(['start_date' => $last_quarter_start_date, 'end_date' => $last_quarter_end_date ]),
            'last_quarter_display_date' => Carbon::createFromFormat('d/m/Y', $last_quarter_start_date)->format('j M') ." - ". Carbon::createFromFormat('d/m/Y', $last_quarter_end_date)->format('d M Y'), 

            'last_year' => json_encode(['start_date' => $last_year_start_date, 'end_date' => $last_year_end_date ]),
            'last_year_display_date' => Carbon::createFromFormat('d/m/Y', $last_year_start_date)->format('j M Y') ." - ". Carbon::createFromFormat('d/m/Y', $last_year_end_date)->format('d M Y'), 

            'month_to_date' => json_encode(['start_date' => $current_month_start_date, 'end_date' => $month_to_end_date ]),
            'month_to_date_display_date' => Carbon::createFromFormat('d/m/Y', $current_month_start_date)->format('j M') ." - ". Carbon::createFromFormat('d/m/Y', $month_to_end_date)->format('d M Y'), 

            'quarter_to_date' => json_encode(['start_date' => $current_quarter_start_date, 'end_date' => $month_to_end_date ]),
            'quarter_to_date_display_date' => Carbon::createFromFormat('d/m/Y', $current_quarter_start_date)->format('j M') ." - ". Carbon::createFromFormat('d/m/Y', $month_to_end_date)->format('d M Y'), 

            'year_to_date' => json_encode(['start_date' => $current_year_start_date, 'end_date' => $month_to_end_date ]),
            'year_to_date_display_date' => Carbon::createFromFormat('d/m/Y', $current_year_start_date)->format('j M') ." - ". Carbon::createFromFormat('d/m/Y', $month_to_end_date)->format('d M Y'), 

        ];

        $pagedata['filters'] = $filters;


        $today = Carbon::now()->firstOfMonth(); //Current Date and Time 
        $todays = Carbon::parse($today)->toDateString();
        $start_date = Carbon::createFromFormat('Y-m-d', $todays); 
        $last_date_of_month = Carbon::parse($today)->endOfMonth()->toDateString(); 
        $end_date = Carbon::createFromFormat('Y-m-d', $last_date_of_month);

        $request->report_start_date = !empty($request->report_start_date) ? Carbon::createFromFormat('d/m/yy', $request->report_start_date)->format('Y-m-d') : ($start_date)->format('Y-m-d');
        $request->report_end_date = !empty($request->report_end_date) ? Carbon::createFromFormat('d/m/yy', $request->report_end_date)->format('Y-m-d') : ($end_date)->format('Y-m-d');
        
        if(!empty($request->account_id)){
            $request->report_chart_accounts_ids = [$request->account_id];
        }
        
        $account_parts = SumbChartAccountsTypeParticulars::where('user_id', $userinfo[0])->get();
            $data = SumbChartAccountsTypeParticulars::selectRaw('sumb_chart_accounts_particulars.id as account_id, sumb_chart_accounts_particulars.*, transactions.id as particulars_id, transactions.*, transaction_collections.id as primary_invoice_id, transaction_collections.*, sumb_invoice_tax_rates.id as tax_rate_id, sumb_invoice_tax_rates.*')
                ->leftJoin('transactions', 'transactions.parts_chart_accounts_id', '=', 'sumb_chart_accounts_particulars.id')
                ->leftJoin('transaction_collections', 'transaction_collections.id', '=', 'transactions.transaction_collection_id')
                ->leftJoin('sumb_invoice_tax_rates', 'sumb_invoice_tax_rates.id', '=', 'transactions.parts_tax_rate_id')
                ->where('transaction_collections.user_id', $userinfo[0])
                ->where('transaction_collections.is_active', 1)
                ->whereIn('transaction_collections.status', ['Paid','PartlyPaid']);
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
                    
                    if($val['transaction_type'] == 'invoice' || $val['transaction_type'] == 'receive_money' ){
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

                    if($val['transaction_type'] == 'expense' || $val['transaction_type'] == 'spend_money' ){
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
                    if($val['transaction_type'] == 'minor_adjustment')
                    {
                        $expense_credit_amount = $val['parts_amount'];
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
                        'status' => $val['status'],
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
        $pagedata['report_start_date'] = $request->report_start_date ? Carbon::createFromFormat('Y-m-d', $request->report_start_date)->format('d/m/Y') : '';
        $pagedata['report_end_date'] = $request->report_end_date ? Carbon::createFromFormat('Y-m-d', $request->report_end_date)->format('d/m/Y') : '';
        $pagedata['account_parts'] = $account_parts ? $account_parts->toArray() : '';
        $pagedata['date_filters'] = $request->date_filters;

        return $pagedata;
    }
}

?>