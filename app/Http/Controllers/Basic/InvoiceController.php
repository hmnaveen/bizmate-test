<?php

namespace App\Http\Controllers\Basic;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMail;
use App\Mail\RecallInvoiceMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use DB;
use URL;

use App\Models\SumbUsers;
use App\Models\SumbClients;
use App\Models\SumbExpensesClients;
use App\Models\SumbInvoiceParticulars;
use App\Models\SumbInvoiceParticularsTemp;
use App\Models\SumbInvoiceDetails;
use App\Models\SumbChartAccounts;
use App\Models\SumbChartAccountsType;
use App\Models\SumbChartAccountsTypeParticulars;
use App\Models\SumbInvoiceTaxRates;
use App\Models\SumbInvoiceSettings;
use App\Models\Transactions;
use App\Models\TransactionCollections;
use App\Models\InvoiceReports;
use App\Models\InvoiceHistory;
use App\Models\SumbInvoiceItems;
use Illuminate\Support\Str;
use App\Helper\NumberFormat;
use App\Models\PaymentHistory;
use File;
use App\Traits\InvoiceAndExpenseGraph;
use App\Notifications\InvoiceNotification;
use Illuminate\Support\Facades\Notification;


class InvoiceController extends Controller {
    public function __construct(TransactionCollections $transaction_collections, Request $request) {
        $this->transaction_collections = $transaction_collections;
        $this->middleware('invoice_seetings');
    }

    use InvoiceAndExpenseGraph;
    
    //***********************************************
    //*
    //* Invoice Page
    //*
    //***********************************************
    
    public function index(Request $request) {
        $userinfo = $request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Invoice'
        );
        $weekly_transaction = []; $monthly_transaction = [];

        $transactions = $this->getInvoiceAndExpenseGraphs($request, 'invoice');
        // echo "<pre>"; var_dump($transactions); echo "</pre>"; die();

        if(!empty($transactions)){
            $weekly_transaction = $transactions['weekly_transaction'];
            $monthly_transaction = $transactions['monthly_transaction'];
        }

        $errors = array(
            1 => ['A new expenses has been saved.', 'primary'],
            2 => ['A new invoice has been saved.', 'primary'],
            3 => ['Invoice does not exists to void or requirements are not complete to do this process, please try again.', 'danger'],
            4 => ['the invoice is now voided.', 'primary'],
            5 => ['the expenses is now voided.', 'primary'],
        );

        $pagedata['errors'] = $errors;
        if (!empty($request->input('err'))) { $pagedata['err'] = $request->input('err'); }
        
        $itemsperpage = 10;
        if (!empty($request->input('ipp'))) { $itemsperpage = $request->input('ipp'); }
        $pagedata['ipp'] = $itemsperpage;
        
        //==== preparing error message
        $pagedata['errors'] = $errors;
        if (!empty($request->input('err'))) { $pagedata['err'] = $request->input('err'); }
        
        $purl = $oriform = $request->all();
        unset($purl['ipp']);
        $pagedata['myurl'] = route('invoice');
        $pagedata['ourl'] = route('invoice', $purl);
        $pagedata['npurl'] = http_build_query(['ipp'=>$itemsperpage]);
        
        $pagedata['search_number_email_amount'] = '';
        $pagedata['start_date'] = '';
        $pagedata['end_date'] = '';
        $pagedata['orderBy'] = '';
        $pagedata['direction'] = '';
        
        //==== get all tranasactions
        $ptype = 'all';
        if (!empty($request->input('type'))) {
            $invoicedata = TransactionCollections::where('user_id', $userinfo[0])->where('is_active', 1)->paginate($itemsperpage)->toArray();  
            $ptype = $request->input('type');
        } else {
            
            if($request->search_number_email_amount || $request->start_date || $request->end_date || $request->orderBy || $request->filterBy){
                if($request->start_date){
                    $start_date = Carbon::createFromFormat('d/m/yy', $request->start_date)->format('Y-m-d');
                }
                if($request->end_date){
                    $end_date = Carbon::createFromFormat('d/m/yy', $request->end_date)->format('Y-m-d');
                }

                $total_amount = $request->search_number_email_amount;
                $amount_paid = $request->search_number_email_amount;
                $invoice_number = $request->search_number_email_amount;

                if($request->search_number_email_amount){
                    if(is_numeric(trim($request->search_number_email_amount))){
                        $total_amount = ltrim($request->search_number_email_amount, '0');
                        $invoice_number = $total_amount;
                        $amount_paid = $total_amount;
                    }
                    else if(is_string(trim($request->search_number_email_amount))){
                        $invoice_number = str_replace('inv-00000', '', trim(strtolower($request->search_number_email_amount)));                        
                    }
                }
                $userinfo = $request->get('userinfo');
                $invoicedata = TransactionCollections::where('user_id', $userinfo[0])->where('is_active', 1)->where('transaction_type', 'invoice');
                                if($request->search_number_email_amount){
                                    $invoicedata->where(function($query) use($invoice_number, $request, $total_amount, $amount_paid){
                                        $query->where('transaction_number', 'LIKE', "%{$invoice_number}%")
                                        ->orWhere('amount_paid', 'LIKE', "%{$amount_paid}%")
                                        ->orWhere('total_amount', 'LIKE', "%{$total_amount}%");
                                    });
                                }
                                if($request->start_date && $request->end_date){
                                    $invoicedata->whereBetween('issue_date', [$start_date, $end_date]);
                                }
                                if($request->orderBy){
                                    $invoicedata->orderBy($request->orderBy, $request->direction);
                                }
                                if($request->filterBy){
                                    $invoicedata->where('status', $request->filterBy);
                                }
                                $invoicedata = $invoicedata->paginate($itemsperpage)->toArray();

                $pagedata['search_number_email_amount'] = $request->search_number_email_amount;
                $pagedata['start_date'] = $request->start_date;
                $pagedata['end_date'] = $request->end_date;
                $pagedata['orderBy'] = $request->orderBy;
                $pagedata['filterBy'] = $request->filterBy;
                if($request->direction == 'ASC')
                {
                    $pagedata['direction'] = 'DESC';
                }
                if($request->direction == 'DESC')
                {
                    $pagedata['direction'] = 'ASC';
                }
            }
            else
            {
                $pagedata['orderBy'] = 'issue_date';
                $pagedata['direction'] = 'ASC';
                $pagedata['filterBy'] = '';
                $invoicedata = TransactionCollections::where('user_id', $userinfo[0])->where('is_active', 1)->where('transaction_type', 'invoice')
                        ->orderBy('issue_date', 'DESC')
                        ->orderBy('transaction_number', 'DESC')
                        ->paginate($itemsperpage)->toArray();
            }
        }

        $total_invoice_counts = TransactionCollections::groupBy('status')
                ->select( DB::raw('status, COUNT(*) as status_count, sum(total_amount+amount_paid) as total') )
                ->where('is_active', 1)
                ->where('transaction_type', 'invoice')
                ->where('user_id', $userinfo[0])
                ->orderBy('status')
                ->get();

        $total_invoice_amount = TransactionCollections::where('is_active', 1)
                ->where('transaction_type', 'invoice')
                ->where('user_id', $userinfo[0])
                ->sum('total_amount');

        $pagedata['total_invoice_amount'] = !empty($total_invoice_amount) ?  $total_invoice_amount : '';
        $pagedata['total_invoice_counts'] = !empty($total_invoice_counts) ?  $total_invoice_counts->toArray() : '';
        $pagedata['invoicedata'] = $invoicedata;

        $pagedata['bar_chart_data'] = $weekly_transaction;
        $pagedata['line_chart_data'] = $monthly_transaction;
       
        $allrequest = $request->all();
        $pfirst = $allrequest; $pfirst['page'] = 1;
        $pprev = $allrequest; $pprev['page'] = $invoicedata['current_page']-1;
        $pnext = $allrequest; $pnext['page'] = $invoicedata['current_page']+1;
        $plast = $allrequest; $plast['page'] = $invoicedata['last_page'];
        $pagedata['paging'] = [
            'current' => url()->current().'?'.http_build_query($allrequest),
            'starpage' => url()->current().'?'.http_build_query($pfirst),
            'first' => ($invoicedata['current_page'] == 1) ? '' : url()->current().'?'.http_build_query($pfirst),
            'prev' => ($invoicedata['current_page'] == 1) ? '' : url()->current().'?'.http_build_query($pprev),
            'now' => 'Page '.$invoicedata['current_page']." of ".$invoicedata['last_page'],
            'next' => ($invoicedata['current_page'] >= $invoicedata['last_page']) ? '' : url()->current().'?'.http_build_query($pnext),
            'last' => ($invoicedata['current_page'] >= $invoicedata['last_page']) ? '' : url()->current().'?'.http_build_query($plast),
        ];
        return view('basic.invoicelist', $pagedata); 
    }

    public function store(Request $request) {
        $request->type = 'create';
        $request->invoice_id = '';

        $pagedata = $this->invoiceForm($request);
        return view('basic.invoicecreate', $pagedata);
    }

    public function update(Request $request) {
        $request->type = 'edit';
        $pagedata = $this->invoiceForm($request);
        return view('basic.invoicecreate', $pagedata);
    }

    public function invoiceForm($request){
        $userinfo = $request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Create Invoice'
        );
        $pagedata['payment_history'] = '';
        $pagedata['invoice_details'] = $request->post();
        // $invoice_details = [];
        $pagedata['invoice_id'] = $request->id ? $request->id : '';
        $pagedata['type'] = $request->type;
        if($request->type == 'edit' && $request->id){
            
            $invoice_details = TransactionCollections::with(['transactions', 'transactions.chartAccountsParticulars', 'transactions.invoiceTaxRates'])
                                ->whereHas('transactions', function($query) use($userinfo) {
                                    $query->where('user_id', $userinfo[0]);
                                })
                                ->where('id', $request->id)
                                ->where('transaction_type', 'invoice')
                                ->where('is_active', 1)
                                ->where('user_id', $userinfo[0])->first();
            if (!empty($invoice_details)) {
                $invoice_details = $invoice_details->toArray();
                $invoice_details['parts'] = $invoice_details['transactions'];
                $invoice_details['invoice_part_total_count'] = "[]";
                unset($invoice_details['transactions']);
                $pagedata['invoice_details'] = $invoice_details;    
            }

            $invoice_history = InvoiceHistory::where('user_id', $userinfo[0])->where('invoice_id', $request->id)->get();
            if (!empty($invoice_history)) {
                $pagedata['invoice_history'] = $invoice_history->toArray();
            }

            $payment_history = PaymentHistory::where('user_id', $userinfo[0])->where('transaction_collection_id', $request->id)->get();
            if (!empty($payment_history)) {
                $pagedata['payment_history'] = $payment_history->toArray();
            }

        }else{ 
                $invoice_details = TransactionCollections::where('user_id', $userinfo[0])->where('transaction_type', 'invoice')->orderBy('transaction_number', 'desc')->first();
                if (!empty($invoice_details)) {
                    $pagedata['transaction_number'] = 000001 + $invoice_details->toArray()['transaction_number'];
                }else{
                    $pagedata['transaction_number'] = 000001;
                }
        }
        $get_clients = SumbClients::where('user_id', $userinfo[0])->orderBy('client_name')->get();
        if (!empty($get_clients)) {
            $pagedata['clients'] = $get_clients = $get_clients->toArray();
        }
        
        $get_items = SumbInvoiceItems::where('user_id', $userinfo[0])->orderBy('invoice_item_name')->get();
        if (!empty($get_items)) {
            $pagedata['invoice_items'] = $get_items->toArray();
        }

        $chart_accounts_types = SumbChartAccounts::with(['chartAccountsTypes'])->get();
        if (!empty($chart_accounts_types)) {
            $pagedata['chart_accounts_types'] = $chart_accounts_types->toArray();
        }

        $chart_account = SumbChartAccounts::with(['chartAccountsTypes',
                        'chartAccountsParticulars' => function($query) use($userinfo) {
                            $query->where('user_id', $userinfo[0]);
                        }])->get();
        if (!empty($chart_account)) {
            $pagedata['chart_account'] = $chart_account->toArray();
        }

        $tax_rates = SumbInvoiceTaxRates::get();
        if (!empty($tax_rates)) {
            $pagedata['tax_rates'] = $tax_rates->toArray();
        }

        $invoice_settings = SumbInvoiceSettings::where('user_id', $userinfo[0])->first();
        if (!empty($invoice_settings)) {
            $pagedata['invoice_settings'] = $invoice_settings->toArray();
        }

        return $pagedata;
    }

    public function createInvoice(Request $request) {
        $userinfo = $request->get('userinfo');
            $pagedata = array(
                'userinfo'=>$userinfo,
                'pagetitle' => 'Create Invoice'
            );
        if($request->save_invoice == 'Save Invoice'){
            
            $validator = Validator::make($request->all(),[
                'client_name' => 'bail|required|max:255',
                'client_email' => 'bail|required|max:255',
                'invoice_issue_date' => 'bail|required',
                'invoice_due_date' => 'bail|required',
                'invoice_number' => 'bail|required|max:255',
            ]);
            
            $pagedata['invoice_id'] = $request->invoice_id;
            $pagedata['type'] = $request->type;
            $invoice_details = [];
            $parts = [];
           
            $invoice_details = array(
                "user_id" => $userinfo[0],
                "client_name" => $request->client_name,
                "client_email" => $request->client_email,
                "client_phone" => $request->client_phone,
                "due_date" => $request->invoice_due_date,
                "issue_date" => $request->invoice_issue_date,
                "transaction_number" => $request->invoice_number,
                "default_tax" => $request->invoice_default_tax,
                "sub_total" =>  NumberFormat::string_replace(trim($request->invoice_sub_total)),
                "total_gst" => NumberFormat::string_replace(trim($request->invoice_total_gst)),
                "total_amount" =>  NumberFormat::string_replace(trim(str_replace("$", "", $request->invoice_total_amount))),
                "amount_paid" =>  NumberFormat::string_replace($request->amount_paid ? trim($request->amount_paid) : 0 ),
                "payment_date" => $request->amount_paid ? trim($request->amount_paid) : '',
                "transaction_type" => 'invoice',
                "invoice_ref_number" => trim($request->invoice_ref_number) ? : 0,
            );
            if(count(json_decode(trim($request->invoice_part_total_count), true)) >= 0){
                $ids = json_decode(trim($request->invoice_part_total_count), true);
                foreach($ids as $id){
                    $parts[] = array(
                        'id' => trim($request->input('invoice_parts_id_'.$id)),
                        'parts_quantity' => $request->input('invoice_parts_quantity_'.$id) ? trim($request->input('invoice_parts_quantity_'.$id)) : 0,
                        'parts_unit_price' => NumberFormat::string_replace($request->input('invoice_parts_unit_price_'.$id) ? trim($request->input('invoice_parts_unit_price_'.$id)) : 0 ),
                        'parts_description' => $request->input('invoice_parts_description_'.$id) ? trim($request->input('invoice_parts_description_'.$id)) : '',
                        'parts_amount' => NumberFormat::string_replace($request->input('invoice_parts_amount_'.$id) ? trim($request->input('invoice_parts_amount_'.$id)) : 0),
                        'parts_tax_rate' => $request->input('invoice_parts_tax_rate_'.$id) ? trim($request->input('invoice_parts_tax_rate_'.$id))[0] : 0,
                        'parts_code' => $request->input('invoice_parts_code_'.$id),
                        'parts_name' => $request->input('invoice_parts_name_'.$id),
                        'parts_name_code' => $request->input('invoice_parts_name_code_'.$id),
                        'parts_chart_accounts_id' => $request->input('invoice_parts_chart_accounts_parts_id_'.$id),
                        'parts_chart_accounts' => trim($request->input('invoice_parts_chart_accounts_'.$id)),
                        'parts_tax_rate_id' => $request->input('invoice_parts_tax_rate_id_'.$id) ? trim($request->input('invoice_parts_tax_rate_id_'.$id)) : 0,
                        'parts_tax_rate_name' => $request->input('invoice_parts_tax_rate_name_'.$id) ? trim($request->input('invoice_parts_tax_rate_name_'.$id)) : '',
                        'invoice_parts_id' => $id
                    );
                    $invoice_details['parts'] = $parts;

                }
            }
            
            $invoice_details['invoice_part_total_count'] = trim($request->input('invoice_part_total_count'));
            $invoice_details['status'] = $request->invoice_status;
            $pagedata['invoice_details'] = $invoice_details;
            
            if ($validator->fails()) {
                $get_items = SumbInvoiceItems::where('user_id', $userinfo[0])->orderBy('invoice_item_name')->get();
                if (!empty($get_items)) {
                    $pagedata['invoice_items'] = $get_items->toArray();
                }

                $get_clients = SumbClients::where('user_id', $userinfo[0])->orderBy('client_name')->get();
                if (!empty($get_clients)) {
                    $pagedata['clients'] = $get_clients = $get_clients->toArray();
                }
                
                $chart_account = SumbChartAccounts::with(['chartAccountsTypes',
                                'chartAccountsParticulars' => function($query) use($userinfo) {
                                    $query->where('user_id', $userinfo[0]);
                                }])->get();
                if (!empty($chart_account)) {
                    $pagedata['chart_account'] = $chart_account->toArray();
                }

                $chart_accounts_types = SumbChartAccounts::with(['chartAccountsTypes'])->get();
                if (!empty($chart_accounts_types)) {
                    $pagedata['chart_accounts_types'] = $chart_accounts_types->toArray();
                }

                $tax_rates = SumbInvoiceTaxRates::get();
                if (!empty($tax_rates)) {
                    $pagedata['tax_rates'] = $tax_rates->toArray();
                }
                return view('basic.invoicecreate')->withErrors($validator)->with($pagedata);
            }

            DB::beginTransaction();
            $client_exists = SumbClients::where('user_id', $userinfo[0])
                                        ->where('client_name', $request->client_name)
                                        ->get();
            if(empty($client_exists->toArray())){
                SumbClients::create([
                    'user_id' => $userinfo[0],
                    'client_name' => $request->client_name,
                    'client_email' => $request->client_email,
                    'client_phone' => $request->client_phone,
                ]);
            }else{
                SumbClients::where('user_id', $userinfo[0])
                    ->where('client_name', $request->client_name)
                    ->update([
                    'client_name' => $request->client_name,
                    'client_email' => $request->client_email,
                    'client_phone' => $request->client_phone,
                ]);
            }
            
            $invoice_details['issue_date'] =  Carbon::createFromFormat('d/m/Y', $request->invoice_issue_date)->format('Y-m-d');
            $invoice_details['due_date'] =  Carbon::createFromFormat('d/m/Y', $request->invoice_due_date)->format('Y-m-d');
            
            $particlars = $invoice_details['parts'];
            
            unset($invoice_details['parts']);
            unset($invoice_details['invoice_part_total_count']);
            $ids = [];

            $sales_account = SumbChartAccountsTypeParticulars::where('chart_accounts_particulars_code', 200)->where('user_id', $userinfo[0])->first();
            if(empty($sales_account)){
                $chart_account = SumbChartAccounts::where('chart_accounts_name', 'Assets')->first();
                if($chart_account){
                    $chart_account_type = SumbChartAccountsType::where('chart_accounts_type', 'Current Asset')->first();
                    if($chart_account_type){
                        $tax_rates = SumbInvoiceTaxRates::first();
                        $sales_account = SumbChartAccountsTypeParticulars::create(
                        [
                            'user_id' => trim($userinfo[0]), 
                            'chart_accounts_id' => $chart_account['id'], 
                            'chart_accounts_type_id' => $chart_account_type['id'], 
                            'chart_accounts_particulars_code' => 200, 
                            'chart_accounts_particulars_name' => 'Sales', 
                            'chart_accounts_particulars_description' => 'Sales' ,
                            'chart_accounts_particulars_tax' => trim($tax_rates['id']),
                            'accounts_tax_rate_id' => trim($tax_rates['id'])
                        ]);
                    }
                }
            }
            if($request->invoice_id && $request->type=='edit'){
                $invoice_update = TransactionCollections::where('user_id', trim($userinfo[0]))
                                ->where('id', $request->invoice_id)
                                ->update(
                                    [
                                        'user_id' => trim($userinfo[0]), 
                                        'client_name' => trim($request->client_name),
                                        'client_email' => trim($request->client_email),
                                        'client_phone' => trim($request->client_phone),
                                        'issue_date' => trim($invoice_details['issue_date']),
                                        'due_date' => trim($invoice_details['due_date']),
                                        'transaction_number' => trim($invoice_details['transaction_number']),
                                        'default_tax' => trim($invoice_details['default_tax']),
                                        'sub_total' => $invoice_details['sub_total'],
                                        'total_gst' => $invoice_details['total_gst'],
                                        'total_amount' => $invoice_details['total_amount'],
                                        'transaction_type' => 'invoice',
                                        'status' => $invoice_details['total_amount'] == 0 ? 'Paid' : $request->invoice_status,
                                    ]
                                );
                if($invoice_update){
                    foreach($particlars as $key=>$value){
                        $newParticulars = Transactions::create(
                            [
                                'user_id' => trim($userinfo[0]), 
                                'transaction_collection_id' => $request->invoice_id,
                                'parts_quantity' => trim($value['parts_quantity']),
                                'parts_description' => trim($value['parts_description']),
                                'parts_unit_price' => trim($value['parts_unit_price']),
                                'parts_amount' => trim($value['parts_amount']),
                                'parts_code' => (!empty($value['parts_code']) ? $value['parts_code'] : $value['parts_name']),
                                'parts_name' => trim($value['parts_name']),
                                // 'parts_tax_rate' => trim($value['invoice_parts_tax_rate']),
                                'parts_chart_accounts_id' => !empty(trim($value['parts_chart_accounts_id'])) ? trim($value['parts_chart_accounts_id']) : (!empty($sales_account) ? $sales_account['id'] : 0),
                                'parts_tax_rate_id' => $request->invoice_default_tax == 'no_tax' ? 0 : trim($value['parts_tax_rate_id']),
                                'parts_gst_amount' => 1,
                            ]);
                        
                        array_push($ids, $newParticulars->id);
                    }
                    if(!empty($ids)){
                        Transactions::whereNotIn('id', $ids)
                                        ->where('transaction_collection_id', $request->invoice_id)
                                        ->where('user_id', trim($userinfo[0]))
                                        ->delete();
                    }
                    $date = Carbon::now()->toDateString();
                    $time = Carbon::now()->toTimeString();

                    $invoice_history = array(
                        "invoice_id" => trim($request->invoice_id),
                        "invoice_number" => trim($invoice_details['transaction_number']),
                        "user_id" => trim($userinfo[0]),
                        "user_name" => trim($userinfo[1]),
                        "action" => "Edited",
                        "description" => "INV-".str_pad($invoice_details['transaction_number'], 6, '0', STR_PAD_LEFT).' to '.trim(ucfirst($request->client_name)).' for $'.NumberFormat::string_replace(trim(str_replace("$", "", $request->invoice_total_amount))),
                        "date" => $date,
                        "time" => $time
                    );
                    $this->createInvoiceHistory($invoice_history);
                    DB::commit();
                }
            }else{
                $invoice = TransactionCollections::create(
                    [
                        'user_id' => trim($userinfo[0]), 
                        'client_name' => trim($request->client_name),
                        'client_email' => trim($request->client_email),
                        'client_phone' => trim($request->client_phone),
                        'issue_date' => trim($invoice_details['issue_date']),
                        'due_date' => trim($invoice_details['due_date']),
                        'transaction_number' => trim($invoice_details['transaction_number']),
                        'default_tax' => trim($invoice_details['default_tax']),
                        'sub_total' => $invoice_details['sub_total'],
                        'total_gst' => $invoice_details['total_gst'],
                        'total_amount' => $invoice_details['total_amount'],
                        'transaction_type' => 'invoice',
                        'invoice_ref_number' => trim($request->invoice_ref_number) ? : 0,
                        'status' => $invoice_details['total_amount'] == 0 ? 'Paid' : 'Unpaid',
                    ]
                );
                if($invoice->id){
                    foreach($particlars as $key=>$value){
                        Transactions::create(
                        [
                            'user_id' => trim($userinfo[0]), 
                            'transaction_collection_id' => $invoice->id,
                            'parts_quantity' => trim($value['parts_quantity']),
                            'parts_description' => trim($value['parts_description']),
                            'parts_unit_price' => trim($value['parts_unit_price']),
                            'parts_amount' => trim($value['parts_amount']),
                            'parts_code' => (!empty($value['parts_code']) ? $value['parts_code'] : $value['parts_name']),
                            'parts_name' => trim($value['parts_name']),
                            // 'parts_tax_rate' => trim($value['invoice_parts_tax_rate']),
                            'parts_chart_accounts_id' => !empty(trim($value['parts_chart_accounts_id'])) ? trim($value['parts_chart_accounts_id']) : (!empty($sales_account) ? $sales_account['id'] : 0),
                            'parts_tax_rate_id' => $request->invoice_default_tax == 'no_tax' ? 0 : trim($value['parts_tax_rate_id']),
                            'parts_gst_amount' => 1,
                        ]);
                    }
                }
                
                $date = Carbon::now()->toDateString();
                $time = Carbon::now()->toTimeString();

                $invoice_history = array(
                    "invoice_id" => trim($invoice->id),
                    "invoice_number" => trim($invoice_details['transaction_number']),
                    "user_id" => trim($userinfo[0]),
                    "user_name" => trim($userinfo[1]),
                    "action" => !empty($request->invoice_ref_number) ? "Cloned" : "Created",
                    "description" => "INV-".str_pad($invoice_details['transaction_number'], 6, '0', STR_PAD_LEFT).' to '.trim(ucfirst($request->client_name)).' for $'.NumberFormat::string_replace(trim(str_replace("$", "", $request->invoice_total_amount))),
                    "date" => $date,
                    "time" => $time
                );

                DB::commit();
                $this->createInvoiceHistory($invoice_history);
            }
        }
        // return view('invoice.invoicecreate', $pagedata);
        return redirect()->route('basic/invoice');
        
    }

    public function sendInvoice(Request $request)
    {
        $userinfo = $request->get('userinfo');
        if($request->invoice_id){
            $randomNumber = random_int(100000, 999999);   

            $invoice_settings = SumbInvoiceSettings::where('user_id', $userinfo[0])->first();
            
            // $invoice_exists = SumbInvoiceDetails::find($request->invoice_id);
            $invoice_detail = TransactionCollections::with(['transactions', 'transactions.invoiceTaxRates'])
                                ->whereHas('transactions', function($query) use($userinfo) {
                                    $query->where('user_id', $userinfo[0]);
                                })
                                ->where('id', $request->invoice_id)
                                ->where('invoice_sent', 0)
                                ->where('user_id', $userinfo[0])->first();
            if (!empty($invoice_detail)) {
                $invoice_detail = $invoice_detail->toArray();
                

                $request->invoice_format = !empty($invoice_settings) && $invoice_settings['business_invoice_format'] ? $invoice_settings['business_invoice_format'] : 'format002';
                
                
                $invpdf['inv'] = [
                    'logo' => !empty($invoice_settings) ? $invoice_settings['business_logo'] : '',
                    'invoice_number' => $invoice_detail['transaction_number'],
                    'client_name' => $invoice_detail['client_name'],
                    'client_email' => $invoice_detail['client_email'],
                    'client_address' => 'test',
                    'client_phone' => $invoice_detail['client_phone'],
                    'invoice_sub_total' => $invoice_detail['sub_total'],
                    'invoice_total_gst' => $invoice_detail['total_gst'],
                    'invoice_total_amount' => $invoice_detail['total_amount'],
                    'invoice_name' => !empty($invoice_settings) ? $invoice_settings['business_name'] : $userinfo[1], 
                    'invoice_email' => !empty($invoice_settings) ? $invoice_settings['business_email'] : $userinfo[2],
                    'invoice_phone' => !empty($invoice_settings) ? $invoice_settings['business_phone'] : '',
                    'invoice_address' => !empty($invoice_settings) ? $invoice_settings['business_address'] : '',
                    'invoice_abn' => !empty($invoice_settings) ? $invoice_settings['business_abn'] : '',
                    'invoice_terms' => !empty($invoice_settings) ? $invoice_settings['business_terms_conditions'] : '',
                    'invoice_format' => $request->invoice_format,
                    'invoice_date' => $invoice_detail['issue_date'],
                    'invoice_due_date' => $invoice_detail['due_date'],
                    'inv_parts' => $invoice_detail['transactions'],
                    'logoimgdet' => '',
                    'logobase64' => '',
                    'image' =>''
                ];
                $logoimg = '';
                if(!empty($invoice_settings['business_logo']) && File::exists(public_path('uploads/'.$userinfo[0].'/'.$invoice_settings['business_logo']))){
                    $logoimg = base64_encode(file_get_contents('uploads/'.$userinfo[0].'/'.$invoice_settings['business_logo']));
                    $invpdf['inv']['logoimgdet'] = !empty($invoice_settings['business_logo']) ? getimagesize('uploads/'.$userinfo[0].'/'.$invoice_settings['business_logo']) : '';
                    $invpdf['inv']['logobase64'] = !empty($invpdf['inv']['logoimgdet']) ? 'data:'.$invpdf['inv']['logoimgdet']['mime'].';charset=utf-8;base64,' . $logoimg : '';
                    $invpdf['inv']['image'] = !empty($invoice_settings['business_logo']) ? 'uploads/'.$userinfo[0].'/'.$invoice_settings['business_logo'] : '' ;

                }

                $inv_filename = "INV-".str_pad($invoice_detail['transaction_number'], 6, '0', STR_PAD_LEFT).".pdf";
                
                $inv_email_filename = "inv".Carbon::now()->timestamp.'-'.$invoice_detail['transaction_number'].'-'.md5($randomNumber).".pdf";

               
                $path = public_path('pdf/'.$userinfo[0]);
                $pdf = Pdf::loadView('pdf.'.$request->invoice_format, $invpdf);

                if(!File::exists($path)) {
                    File::makeDirectory($path);
                }

                // Storage::put(env('APP_PDF_DIRECTORY').$userinfo[0].'/'.$inv_email_filename, $pdf->output());
                $pdf->save($path.'/'.$inv_email_filename);

                $transactiondata['invoice_pdf'] = $inv_filename;
                $invpdf['inv']['file_name'] = $inv_filename;
                $invpdf['inv']['email_file_name'] = $inv_email_filename;

                
                $emails = explode(",", $request->send_invoice_to_emails);
                $emails = array_unique($emails);
                
                $invpdf['inv']['from'] = $userinfo[1];
                $invpdf['inv']['subject'] = $request->send_invoice_subject;
                $invpdf['inv']['message'] = $request->send_invoice_message;
                $invpdf['inv']['path'] = $path;

                try {
                    // Mail::to($emails)->send(new InvoiceMail($pdf, $invpdf['inv']));

                    Notification::route('mail', $emails)->notify(new InvoiceNotification($invpdf['inv'] ,$emails));

                } catch (Exception $e) {
                    if (count(Mail::failures()) > 0) {
                        
                    }
                }
                InvoiceReports::create([
                    'user_id' =>  $userinfo[0],
                    'transaction_collection_id' => $invoice_detail['id'],
                    'invoice_report_file' => $inv_filename
                ]);

                if(!$invoice_detail['invoice_sent'] && $invoice_detail['status'] == 'Recalled'){
                    TransactionCollections::where('id', $invoice_detail['id'])
                    ->where('user_id', $userinfo[0])
                    ->where('invoice_sent', 0)
                    ->where('status', 'Recalled')
                    ->update(['invoice_sent' => 1, 'status' => 'Unpaid']);
                }else{
                    TransactionCollections::where('id', $invoice_detail['id'])
                    ->where('user_id', $userinfo[0])
                    ->update(['invoice_sent' => 1]);
                }

                $date = Carbon::now()->toDateString();
                $time = Carbon::now()->toTimeString();
    
                $invoice_history = array(
                    "invoice_id" => trim($request->invoice_id),
                    "invoice_number" => trim($invoice_detail['transaction_number']),
                    "user_id" => trim($userinfo[0]),
                    "user_name" => trim($userinfo[1]),
                    "action" => "Invoice sent",
                    "description" => "This invoice has been sent to ".$request->send_invoice_to_emails,
                    "date" => $date,
                    "time" => $time
                );
                $this->createInvoiceHistory($invoice_history);

                return redirect()->route('basic/invoice')->with('success', 'Invoice sent successfully');
            }
        }
        return redirect()->route('basic/invoice');
    }

    public function searchInvoiceItem(Request $request)
    {
        if ($request->ajax())
        {
            $userinfo = $request->get('userinfo');
            $invoice_item_name = trim($request->invoice_item_name);
                $invoice_items = SumbInvoiceItems::where('user_id', $userinfo[0])
                ->where('invoice_item_name', 'like', '%' . $request->invoice_item_name . '%')
                ->orderBy('invoice_item_name')
                ->get();

            echo json_encode($invoice_items);
        }
        else
        {
            $response = [
                'status' => 'error',
                'err' => 'Something went wrong',
                'data' => ''
            ];
            echo json_encode($response);
        }
    }

    public function invoiceItemForm(Request $request)
    {
        if ($request->ajax())
        {
            $userinfo = $request->get('userinfo');
            $invoice_item_exists = SumbInvoiceItems::where('user_id', $userinfo[0])
                                            ->where('invoice_item_code', $request->invoice_item_code)
                                            ->first();
            if(!empty($invoice_item_exists)){
                $response = [
                    'status' => 'error',
                    'err' => 'Item code already exists',
                    'data' => ''
                ];
                echo json_encode($response);
            }else{
                DB::beginTransaction();
                $sales_account = SumbChartAccountsTypeParticulars::where('chart_accounts_particulars_code', 200)->where('user_id', $userinfo[0])->first();
                if(empty($sales_account)){
                    $chart_account = SumbChartAccounts::where('chart_accounts_name', 'Assets')->first();
                    if($chart_account){
                        $chart_account_type = SumbChartAccountsType::where('chart_accounts_type', 'Current Asset')->first();
                        if($chart_account_type){
                            $tax_rates = SumbInvoiceTaxRates::first();
                            $sales_account = SumbChartAccountsTypeParticulars::create(
                                [
                                    'user_id' => $userinfo[0],
                                    'chart_accounts_id' => $chart_account['id'], 
                                    'chart_accounts_type_id' => $chart_account_type['id'], 
                                    'chart_accounts_particulars_code' => 200, 
                                    'chart_accounts_particulars_name' => 'Sales', 
                                    'chart_accounts_particulars_description' => 'Sales',
                                    'chart_accounts_particulars_tax' => trim($tax_rates['id']),
                                    'accounts_tax_rate_id' => trim($tax_rates['id'])
                                ]);
                        }
                    }
                }

                $item = SumbInvoiceItems::create(
                    [
                        'user_id' => trim($userinfo[0]), 
                        'invoice_item_code' => $request->invoice_item_code,
                        'invoice_item_name' => trim($request->invoice_item_name),
                        'invoice_item_unit_price' => trim($request->invoice_item_unit_price),
                        'invoice_item_tax_rate' => trim($request->invoice_item_tax_rate),
                        'invoice_item_tax_rate_id' => trim($request->invoice_item_tax_rate_id),
                        'invoice_item_description' => trim($request->invoice_item_description),
                        'invoice_item_chart_accounts_parts_id' => !empty(trim($request->invoice_item_chart_accounts_parts_id)) ? trim($request->invoice_item_chart_accounts_parts_id) : ($sales_account ? $sales_account['id'] : 0),
                    ]);
                if($item->id){
                    DB::commit();
                    $invoice_items = SumbInvoiceItems::with(['taxRates'])->where('user_id', $userinfo[0])->get();
                    if($invoice_items){
                        $response = [
                            'status' => 'success',
                            'err' => '',
                            'data' => $invoice_items
                        ];

                        echo json_encode($response);
                    }
                } 
            }
        }
    }

    public function invoiceItemFormList(Request $request)
    {
        if ($request->ajax())
        {
            $userinfo = $request->get('userinfo');
            $invoice_item_name = trim($request->invoice_item_name);
            $invoice_items = SumbInvoiceItems::where('user_id', $userinfo[0])
            ->orderBy('invoice_item_name')
            ->get();
            if($invoice_items)
            {
                $response = [
                    'status' => 'success',
                    'err' => '',
                    'data' => $invoice_items
                ];
                echo json_encode($response);
            }
            else
            {
                $response = [
                    'status' => 'error',
                    'err' => 'No items found',
                    'data' => ''
                ];
                echo json_encode($response);
            }
        }
        else
        {
            $response = [
                'status' => 'error',
                'err' => 'Something went wrong',
                'data' => ''
            ];
            echo json_encode($response);
        }
    }

    public function invoiceItemFormListById(Request $request, $id)
    {
        if ($request->ajax())
        {
            $userinfo = $request->get('userinfo');
            $invoice_item = SumbInvoiceItems::with(['taxRates'])->where('user_id', $userinfo[0])
                            ->where('id', $id)
                            ->first();
            if($invoice_item)
            {
                $response = [
                    'status' => 'success',
                    'err' => '',
                    'data' => $invoice_item
                ];
                echo json_encode($response);
            }
            else
            {
                $response = [
                    'status' => 'error',
                    'err' => 'No item found',
                    'data' => ''
                ];
                echo json_encode($response);
            }
        }
        else
        {
            $response = [
                'status' => 'error',
                'err' => 'Something went wrong',
                'data' => ''
            ];
            echo json_encode($response);
        }
    }

    public function statusUpdate(Request $request)
    {
        $userinfo = $request->get('userinfo');

        $request->validate([
            'status' => 'required',
            'invoice_id' => 'required|exists:transaction_collections,id,user_id,'.$userinfo[0].',is_active,1',
        ]);

        
        $total_due_amount = TransactionCollections::where('id', $request->invoice_id)->first();
        
        if($total_due_amount){
            $date = Carbon::now()->toDateString();
            $time = Carbon::now()->toTimeString();
            $invoice_history = array(
                "invoice_id" => trim($request->invoice_id),
                "invoice_number" => trim($total_due_amount->transaction_number),
                "user_id" => trim($userinfo[0]),
                "user_name" => trim($userinfo[1]),
                "date" => $date,
                "time" => $time
            );

            if($request->status == 'Paid' && $request->payment_date && $request->amount_paid){

                $request->payment_date = Carbon::createFromFormat('d/m/yy', $request->payment_date)->format('Y-m-d');

                $request->amount_paid = NumberFormat::string_replace(trim($request->amount_paid));
                
                $request->amount_paid == $total_due_amount->total_amount ? $request->status = 'Paid' : $request->status = 'PartlyPaid' ;
                
                //Subtract paid amount with due amount
                $due_amount_remain = $total_due_amount->total_amount - $request->amount_paid;
                $amount_paid = $request->amount_paid + $total_due_amount->amount_paid;

                $status_updated = TransactionCollections::where('id', $request->invoice_id)
                    ->where('user_id', $userinfo[0])
                    ->where('is_active', 1)
                    ->whereIn('status', ['Unpaid', 'Recalled', 'PartlyPaid'])
                    ->update(['status' => $request->status, 'payment_date' => $request->payment_date, 'amount_paid' => $amount_paid, 'total_amount' => $due_amount_remain ]);
                
                if($status_updated){
                    PaymentHistory::create(
                        [
                            'user_id'=> $userinfo[0], 
                            'transaction_collection_id'=> $request->invoice_id, 
                            'date'=> $request->payment_date, 
                            'amount_paid'=> $request->amount_paid 
                        ]
                    );
                    $invoice_history['description'] = 'Payment received on ' .$request->payment_date. ' for $'.trim($request->amount_paid);
                    $invoice_history['action'] = $request->status;

                    $this->createInvoiceHistory($invoice_history);
                }
                if(!$status_updated){
                    return redirect()->route('basic/invoice')->with('error', 'Something went wrong');
                }
            }else{
                if($total_due_amount->status == 'Paid' && ($request->status == 'Unpaid' || $request->status == 'Voided')){
                    $status_updated = TransactionCollections::where('id', $request->invoice_id)
                        ->where('user_id', $userinfo[0])
                        ->where('is_active', 1)
                        ->where('status', '!=', $request->status)
                        ->update(['status' => $request->status, 'total_amount' => ($total_due_amount->total_amount + $total_due_amount->amount_paid), 'amount_paid' => 0, 'payment_date' => NULL]);
                    if($status_updated){
                        PaymentHistory::where('transaction_collection_id', $request->invoice_id)
                            ->where('user_id', $userinfo[0])
                            ->delete();

                        $invoice_history['description'] = $request->status == 'Unpaid' ? "INV-".str_pad($total_due_amount->transaction_number, 6, '0', STR_PAD_LEFT).' to '.trim(ucfirst($total_due_amount->client_name)).' for $'.trim($total_due_amount->total_amount + $total_due_amount->amount_paid) : '';
                        $invoice_history['action'] = $request->status;

                        $this->createInvoiceHistory($invoice_history);
                    }
                }else{

                    $status_updated = TransactionCollections::where('id', $request->invoice_id)
                        ->where('user_id', $userinfo[0])
                        ->where('is_active', 1)
                        ->where('status', '!=', $request->status)
                        ->update(['status' => $request->status]);
                }
            }
            
            return redirect()->route('basic/invoice')->with('success', 'Invoice status updated successfully');
        }
        return redirect()->route('basic/invoice')->with('error', 'Something went wrong');

    }
    
    public function delete(Request $request)
    {
        $userinfo = $request->get('userinfo');
        $deleted = TransactionCollections::where('user_id', $userinfo[0])->where('id', $request->id)->where('is_active', 1)->whereIn('status', ['Unpaid', 'Voided'])->update(['is_active'=> 0]);
        if($deleted){
            $deleted_invoice = TransactionCollections::where('user_id', $userinfo[0])->where('id', $request->id)->first()->toArray();
            
            $date = Carbon::now()->toDateString();
            $time = Carbon::now()->toTimeString();

            $invoice_history = array(
                "invoice_id" => trim($request->id),
                "invoice_number" => trim($deleted_invoice['transaction_number']),
                "user_id" => trim($userinfo[0]),
                "user_name" => trim($userinfo[1]),
                "action" => 'Deleted',
                "description" => '',
                "date" => $date,
                "time" => $time
            );

            $this->createInvoiceHistory($invoice_history);

            return redirect()->route('basic/invoice')->with('success', 'Invoice deleted successfully');
        }
        return redirect()->route('basic/invoice')->with('error', 'Something went wrong');
    }

    public function invoiceTaxRates(Request $request)
    {
        if ($request->ajax())
        {
            $invoice_tax_rates = SumbInvoiceTaxRates::get();
            if($invoice_tax_rates)
            {
                $response = [
                    'status' => 'success',
                    'err' => '',
                    'data' => $invoice_tax_rates
                ];
                echo json_encode($response);
            }
            else
            {
                $response = [
                    'status' => 'error',
                    'err' => 'No item found',
                    'data' => ''
                ];
                echo json_encode($response);
            }
        }
        else
        {
            $response = [
                'status' => 'error',
                'err' => 'Something went wrong',
                'data' => ''
            ];
            echo json_encode($response);
        }
    }
    public function cloneInvoice(Request $request)
    {
        $userinfo = $request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Create Invoice'
        );
        $pagedata['invoice_details'] = $request->post();
        $pagedata['invoice_id'] = '';
        $pagedata['type'] = 'create';

        $transaction_number = TransactionCollections::select('transaction_number')->where('user_id', $userinfo[0])->orderBy('transaction_number', 'desc')->first();

        $invoice_details = TransactionCollections::with(['transactions', 'transactions.chartAccountsParticulars'])
                                ->whereHas('transactions', function($query) use($userinfo) {
                                    $query->where('user_id', $userinfo[0]);
                                })
                                ->where('id', $request->invoice_id)
                                ->where('status', 'Voided')
                                ->where('is_active', 1)
                                ->where('user_id', $userinfo[0])->first();
                
            if (!empty($invoice_details)) {
                $invoice_details = $invoice_details->toArray();
                $invoice_details['invoice_ref_number'] = $invoice_details['transaction_number'];
                $invoice_details['invoice_clone'] = true;
                $invoice_details['status'] = 'Unpaid';
                $invoice_details['transaction_number'] = 000001 + $transaction_number['transaction_number'];
                $invoice_details['parts'] = $invoice_details['transactions'];
                $invoice_details['invoice_part_total_count'] = "[]";
                unset($invoice_details['transactions']);
                
                $pagedata['invoice_details'] = $invoice_details;    
            
                $get_clients = SumbClients::where('user_id', $userinfo[0])->orderBy('client_name')->get();
                if (!empty($get_clients)) {
                    $pagedata['clients'] = $get_clients = $get_clients->toArray();
                }
                
                $get_items = SumbInvoiceItems::where('user_id', $userinfo[0])->orderBy('invoice_item_name')->get();
                if (!empty($get_items)) {
                    $pagedata['invoice_items'] = $get_items->toArray();
                }

                $chart_accounts_types = SumbChartAccounts::with(['chartAccountsTypes'])->get();
                if (!empty($chart_accounts_types)) {
                    $pagedata['chart_accounts_types'] = $chart_accounts_types->toArray();
                }

                $chart_account = SumbChartAccounts::with(['chartAccountsTypes',
                                'chartAccountsParticulars' => function($query) use($userinfo) {
                                    $query->where('user_id', $userinfo[0]);
                                }])->get();
                if (!empty($chart_account)) {
                    $pagedata['chart_account'] = $chart_account->toArray();
                }

                $tax_rates = SumbInvoiceTaxRates::get();
                if (!empty($tax_rates)) {
                    $pagedata['tax_rates'] = $tax_rates->toArray();
                }

                $invoice_settings = SumbInvoiceSettings::where('user_id', $userinfo[0])->first();
                if (!empty($invoice_settings)) {
                    $pagedata['invoice_settings'] = $invoice_settings->toArray();
                }

                $date = Carbon::now()->toDateString();
                $time = Carbon::now()->toTimeString();

                $invoice_history = array(
                    "invoice_number" => trim($invoice_details['transaction_number']),
                    "user_id" => trim($userinfo[0]),
                    "user_name" => trim($userinfo[1]),
                    "action" => "Cloned invoice from ". $invoice_details['invoice_ref_number'],
                    "date" => $date,
                    "time" => $time
                );
                $this->createInvoiceHistory($invoice_history);
            }
        return view('basic.invoicecreate', $pagedata);
    }

    public function createInvoiceHistory($invoice_history){

        InvoiceHistory::create($invoice_history);
    }

    public function recallInvoice(Request $request)
    {
        $userinfo = $request->get('userinfo');
        $invoice_detail = TransactionCollections::where('id', $request->id)
                                ->where('invoice_sent', 1)
                                ->where('status', 'Unpaid')
                                ->where('user_id', $userinfo[0])->first();
        if (!empty($invoice_detail)) {
            $invoice_detail = $invoice_detail->toArray();

            $invpdf['inv'] = [];
            $email = $invoice_detail['client_email'];

            $invpdf['inv']['subject'] = 'Invoice INV-'.str_pad($invoice_detail['transaction_number'], 6, '0', STR_PAD_LEFT).' has been recalled.';
            $invpdf['inv']['message'] = 'The invoice INV-'.str_pad($invoice_detail['transaction_number'], 6, '0', STR_PAD_LEFT).' sent to you on '.$invoice_detail['issue_date']. ' has been recalled. 
                                        
                                        A new invoice will be sent to you. 
                                        
                                        If you have paid the invoice, please reply to this email: '. $userinfo[2];

            Mail::to($email)->send(new RecallInvoiceMail($invpdf['inv']));

            $updated = TransactionCollections::where('id', $request->id)
                        ->where('invoice_sent', 1)
                        ->where('status', 'Unpaid')
                        ->where('user_id', $userinfo[0])
                        ->update(['invoice_sent' => 0, 'status' => 'Recalled']);
            if($updated)
            {
                return redirect("/basic/invoice/$request->id/edit");
            }
        }
    }

    public function updateInvoiceSentStatus(Request $request)
    {
        $userinfo = $request->get('userinfo');
        $invoice_sent = TransactionCollections::where('id', $request->id)
            ->where('user_id', $userinfo[0])
            ->update(['invoice_sent' => 1]);
        if($invoice_sent){
            return redirect()->route('basic/invoice')->with('success', 'Invoice marked as sent');
        }else{
            return redirect()->route('basic/invoice')->with('error', 'Something went wrong');
        }
    }
}
