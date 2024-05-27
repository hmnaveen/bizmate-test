<?php

namespace App\Http\Controllers;

use DB;
use App\Models\SumbInvoiceSettings;
use App\Models\Transactions;
use App\Models\TransactionCollections;
use App\Models\SumbClients;
use App\Models\SumbInvoiceItems;
use App\Models\SumbInvoiceReports;
use App\Models\SumbChartAccounts;
use App\Models\SumbChartAccountsType;
use App\Models\SumbChartAccountsTypeParticulars;
use Illuminate\Support\Facades\Validator;
use App\Models\SumbInvoiceTaxRates;
use App\Models\InvoiceHistory;
use App\Helper\NumberFormat;
use App\Models\PaymentHistory;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Interfaces\TransactionCollectionRepositoryInterface;
use Illuminate\Validation\Rule;

class CashReceiptController extends Controller
{
    private TransactionCollectionRepositoryInterface $transactionCollectionRepository;

    public function __construct(TransactionCollectionRepositoryInterface $transactionCollectionRepository)
    {
        $this->transactionCollectionRepository = $transactionCollectionRepository;
    }

    public function showCashReceipt(Request $request)
    {
        $userinfo = $request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Cash Receipt'
        );

        $pagedata['invoice_details'] = $request->post();
        request()->merge(['type' => 'edit']);
        // $invoice_details = [];
        $pagedata['invoice_id'] = $request->id ? $request->id : '';
        $pagedata['type'] = $request->type;
        $pagedata['transaction_type'] = $request->transaction_type;
        $pagedata['payment_option'] = $request->payment_option;

        $filters = [
            'id' => $request->id,
            'transaction_type' => [$request->transaction_type],
            'payment_option' => $request->payment_option,
            'type' => $request->type,
        ];
        $invoice_details = $this->transactionCollectionRepository->getTransaction($filters, $userinfo[0]);
        if(empty($invoice_details))
        {
            if($request->transaction_type == 'receive_money')
            {
                return redirect()->route('invoice')->withError('Invalid data supplied');
            }else if($request->transaction_type == 'spend_money')
            {
                return redirect()->route('expense')->withError('Invalid data supplied');
            }
        }

        // $pagedata['reconcile_details'] = [];

        // !empty($invoice_details && $invoice_details->load('reconcileTransaction')) ? $pagedata['reconcile_details'] = $invoice_details->load('reconcileTransaction')['reconcileTransaction'] : [];
        $invoice_details = $invoice_details->toArray();
        $invoice_details['parts'] = $invoice_details['transactions'];
        $invoice_details['invoice_part_total_count'] = "[]";
        unset($invoice_details['transactions']);
        $pagedata['invoice_details'] = $invoice_details;

        $invoice_history = InvoiceHistory::where('user_id', $userinfo[0])->where('invoice_id', $request->id)->get();
        if (!empty($invoice_history)) {
            $pagedata['invoice_history'] = $invoice_history->toArray();
        }

        $payment_history = PaymentHistory::where('user_id', $userinfo[0])->where('transaction_collection_id', $request->id)->get();
        if (!empty($payment_history)) {
            $pagedata['payment_history'] = $payment_history->toArray();
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
        return view('invoice.bankcashreceipt', $pagedata);
    }

    public function updateCashReceipt(Request $request) {
        $userinfo = $request->get('userinfo');
            $pagedata = array(
                'userinfo'=>$userinfo,
                'pagetitle' => 'Edit Cash Receipt'
            );
        if($request->save_invoice == 'Save Invoice'){

            request()->merge(['invoice_total_amount' => NumberFormat::string_replace(trim(str_replace("$", "", $request->invoice_total_amount))),
                'user_total' => NumberFormat::string_replace(trim(str_replace("$", "", $request->user_total)))
            ]);

            $validator = Validator::make($request->all(),[
                'client_name' => 'bail|required|max:255',
                // 'client_email' =>'required_if:transaction_type,invoice|max:255',
                'invoice_issue_date' => 'bail|required',
                'transaction_type' => 'bail|required',
                // 'invoice_id' => 'bail|required|exists:reconciled_transactions,transaction_collection_id,user_id,'.$userinfo[0].',is_active,1,is_active,1',
                // 'user_total' => "bail|required|same:invoice_total_amount"
                'user_total' => [
                    'required',
                    Rule::in($request->invoice_total_amount),
                ],
            ],['user_total' => "The totals do not match."],);

            $pagedata['invoice_id'] = $request->invoice_id;
            $request->type = 'edit';
            $pagedata['type'] = $request->type;
            $pagedata['transaction_type'] = $request->transaction_type;
            $pagedata['payment_option'] = $request->payment_option;


            $invoice_details = [];
            $parts = [];
            $invoice_details = [
                "user_id" => $userinfo[0],
                "client_name" => $request->client_name,
                "client_email" => !empty($request->client_email) ? $request->client_email : '',
                "client_phone" => !empty($request->client_phone) ? $request->client_phone : '',
                "due_date" => !empty($request->invoice_due_date) ? $request->invoice_due_date : NULL,
                "issue_date" => $request->invoice_issue_date,
                "transaction_number" => !empty($request->invoice_number) ? $request->invoice_number : 0,
                "default_tax" => !empty($request->invoice_default_tax) ? $request->invoice_default_tax : 'no_tax',
                "sub_total" =>  NumberFormat::string_replace(trim($request->invoice_sub_total)),
                "total_gst" => NumberFormat::string_replace(trim($request->invoice_total_gst)),
                "total_amount" =>  NumberFormat::string_replace(trim(str_replace("$", "", $request->invoice_total_amount))),
                "amount_paid" =>  NumberFormat::string_replace($request->amount_paid ? trim($request->amount_paid) : 0 ),
                "payment_date" => $request->amount_paid ? trim($request->amount_paid) : '',
                "transaction_type" => $request->transaction_type,
                "invoice_ref_number" => trim($request->invoice_ref_number) ? trim($request->invoice_ref_number) : 0,
                "payment_option" => trim($request->payment_option) ? trim($request->payment_option) : '',
                "user_total" => trim($request->user_total) ? trim($request->user_total) : '',
                "reconcile_status" => trim($request->reconcile_status) ? trim($request->reconcile_status) : '',
            ];


            $transactions = [];
            if(count(json_decode(trim($request->invoice_part_total_count), true)) >= 0){
                $removeKeys = ['id', 'invoice_parts_id', 'parts_tax_rate_name', 'parts_chart_accounts', 'parts_tax_rate'];
                $ids = json_decode(trim($request->invoice_part_total_count), true);

                foreach($ids as $k=>$id){

                    $parts[] = [
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
                        'invoice_parts_id' => $id,
                        'user_id' => $userinfo[0],
                        'transaction_collection_id'=> $request->invoice_id
                    ];

                    $invoice_details['parts'] = $parts;
                    $transactions[] = collect($parts[$k])->forget(['id', 'invoice_parts_id', 'parts_tax_rate_name', 'parts_chart_accounts', 'parts_tax_rate', 'parts_name_code'])->all();
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
                    $pagedata['clients'] = $get_clients->toArray();
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
                return view('invoice.bankcashreceipt')->withErrors($validator)->with($pagedata);
            }

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
            $invoice_details['due_date'] =  !empty($request->invoice_due_date) ? Carbon::createFromFormat('d/m/Y', $request->invoice_due_date)->format('Y-m-d') : NULL;

            unset($invoice_details['parts']);
            unset($invoice_details['invoice_part_total_count']);

            $ids = [];
            if($request->invoice_id && $request->type == 'edit'){

                unset($invoice_details['invoice_ref_number']);
                unset($invoice_details['amount_paid']);
                unset($invoice_details['payment_date']);
                unset($invoice_details['user_total']);
                unset($invoice_details['reconcile_status']);

                $data = $this->transactionCollectionRepository->createOrUpdateTransaction($invoice_details, $transactions, $userinfo, $request->invoice_id, $request->type);
            }
        }

        if($request->transaction_type == 'arprepayment' || $request->transaction_type == 'aroverpayment' || $request->transaction_type == 'receive_money')
        {
            return redirect()->route('invoice');
        }else if($request->transaction_type == 'apprepayment' || $request->transaction_type == 'apoverpayment' || $request->transaction_type == 'spend_money')
        {
            return redirect()->route('expense');
        }
    }


    public function createInvoiceHistory($invoice_history){
        InvoiceHistory::create($invoice_history);
    }
}
