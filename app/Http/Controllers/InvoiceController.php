<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use URL;
use App\Models\SumbInvoiceSettings;
use Illuminate\Support\Facades\Validator;
use App\Models\InvoiceReports;
use App\Helper\NumberFormat;
use App\Traits\InvoiceAndExpenseGraph;
use App\Models\PaymentHistory;
use File;
use App\Notifications\InvoiceNotification;
use Illuminate\Support\Facades\Notification;
use App\Interfaces\TransactionCollectionRepositoryInterface;
use App\Interfaces\ClientsRepositoryInterface;
use App\Interfaces\InvoiceItemRepositoryInterface;
use App\Interfaces\ChartAccountRepositoryInterface;
use App\Interfaces\TaxRateRepositoryInterface;
use App\Interfaces\InvoiceHistoryRepositoryInterface;


class InvoiceController extends Controller {

    private TransactionCollectionRepositoryInterface $transactionCollectionRepository;
    private ClientsRepositoryInterface $clientsRepository;
    private InvoiceItemRepositoryInterface $invoiceItemRepository;
    private ChartAccountRepositoryInterface $chartAccountRepository;
    private TaxRateRepositoryInterface $taxRateRepositoryInterface;
    private InvoiceHistoryRepositoryInterface $invoiceHistoryRepository;

    public function __construct(
        TransactionCollectionRepositoryInterface $transactionCollectionRepository,
        ClientsRepositoryInterface $clientsRepository,
        InvoiceItemRepositoryInterface $invoiceItemRepository,
        ChartAccountRepositoryInterface $chartAccountRepository,
        TaxRateRepositoryInterface $taxRateRepository,
        InvoiceHistoryRepositoryInterface $invoiceHistoryRepository,
        Request $request
    )
    {
        $this->middleware('invoice_seetings');
        $this->transactionCollectionRepository = $transactionCollectionRepository;
        $this->clientsRepository = $clientsRepository;
        $this->invoiceItemRepository = $invoiceItemRepository;
        $this->chartAccountRepository = $chartAccountRepository;
        $this->taxRateRepository = $taxRateRepository;
        $this->invoiceHistoryRepository = $invoiceHistoryRepository;
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
            'userinfo'=> $userinfo,
            'pagetitle' => 'Invoice & Expenses'
        );

        $weekly_transaction = []; $monthly_transaction = [];

        $transactions = $this->getInvoiceAndExpenseGraphs($request, 'invoice');

        if(!empty($transactions))
        {
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
        $pagedata['error'] = $errors;
        if (!empty($request->input('err'))) { $pagedata['err'] = $request->input('err'); }

        $itemsperpage = 10;
        if (!empty($request->input('ipp'))) { $itemsperpage = $request->input('ipp'); }
        $pagedata['ipp'] = $itemsperpage;

        //==== preparing error message
        $pagedata['error'] = $errors;
        if (!empty($request->input('err'))) { $pagedata['err'] = $request->input('err'); }

        $purl = $oriform = $request->all();
        unset($purl['ipp']);
        $pagedata['myurl'] = route('invoice');

        $pagedata['npurl'] = http_build_query(['ipp'=>$itemsperpage]);

        //================== get all tranasactions
        $ptype = 'all';
        request()->merge(['transaction_type' => ['invoice', 'receive_money'], 'userinfo'=> $userinfo, 'itemsperpage'=> $itemsperpage]);

        if($request->search_number_email_amount || $request->start_date || $request->end_date || $request->orderBy || $request->filterBy)
        {
            if($request->start_date)
            {
                $start_date = Carbon::createFromFormat('d/m/yy', $request->start_date)->format('Y-m-d');
            }
            if($request->end_date)
            {
                $end_date = Carbon::createFromFormat('d/m/yy', $request->end_date)->format('Y-m-d');
            }

            if($request->search_number_email_amount)
            {
                request()->merge([
                    'total_amount' => $request->search_number_email_amount,
                    'invoice_number' => $request->search_number_email_amount,
                    'amount_paid'=> $request->search_number_email_amount
                ]);

                if(is_numeric(trim($request->search_number_email_amount)))
                {
                    request()->merge([
                        'total_amount' => ltrim($request->total_amount, '0'),
                    ]);

                    request()->merge([
                        'invoice_number' => $request->total_amount,
                        'amount_paid'=> $request->total_amount
                    ]);
                }
                else if(is_string(trim($request->search_number_email_amount)))
                {
                    request()->merge([
                        'invoice_number' => str_replace('inv-00000', '', trim(strtolower($request->search_number_email_amount)))
                    ]);
                }
            }

            $filters = $request->only([
                'total_amount','amount_paid','invoice_number','userinfo','search_number_email_amount','start_date',
                'end_date','orderBy','direction','filterBy','transaction_type','itemsperpage'
            ]);

            $invoicedata = $this->transactionCollectionRepository->getTransactionsByFilter($filters, true);

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

        }else{

            $filters = $request->only([
                'userinfo','transaction_type','itemsperpage'
            ]);

            $pagedata['orderBy'] = 'issue_date';
            $pagedata['direction'] = 'ASC';
            $pagedata['filterBy'] = '';

            $invoicedata = $this->transactionCollectionRepository->getTransactionsByFilter($filters, false);
        }

        $total_invoice_counts = $this->transactionCollectionRepository->getTransactionCount($userinfo[0]);

        $pagedata['total_invoice_counts'] = !empty($total_invoice_counts) ?  $total_invoice_counts->toArray() : '';
        $pagedata['invoicedata'] = $invoicedata;

        $pagedata['bar_chart_data'] = $weekly_transaction;
        $pagedata['line_chart_data'] = $monthly_transaction;

        $allrequest = $request->all();
        // $pfirst = $allrequest;
         $pfirst['page'] = 1;
        // $pprev = $allrequest;
         $pprev['page'] = $invoicedata['current_page']-1;
        // $pnext = $allrequest;
        $pnext['page'] = $invoicedata['current_page']+1;
        // $plast = $allrequest;
        $plast['page'] = $invoicedata['last_page'];
        $pagedata['paging'] = [
            'current' => url()->current().'?'.http_build_query($allrequest),
            'starpage' => url()->current().'?'.http_build_query($pfirst),
            'first' => ($invoicedata['current_page'] == 1) ? '' : url()->current().'?'.http_build_query($pfirst),
            'prev' => ($invoicedata['current_page'] == 1) ? '' : url()->current().'?'.http_build_query($pprev),
            'now' => 'Page '.$invoicedata['current_page']." of ".$invoicedata['last_page'],
            'next' => ($invoicedata['current_page'] >= $invoicedata['last_page']) ? '' : url()->current().'?'.http_build_query($pnext),
            'last' => ($invoicedata['current_page'] >= $invoicedata['last_page']) ? '' : url()->current().'?'.http_build_query($plast),
        ];
        return view('invoice.invoicelist', $pagedata);
    }

    public function store(Request $request)
    {
        $request->invoice_id = '';
        $request->type = 'create';

        $pagedata = $this->invoiceForm($request);
        return view('invoice.invoicecreate', $pagedata);
    }

    public function update(Request $request)
    {
        request()->merge([ 'id' => $request->id ? $request->id : '', 'type' => 'edit', 'transaction_type' => 'invoice']);

        $pagedata = $this->invoiceForm($request);

        if(!$pagedata)
        {
            return redirect('/invoice')->withError('Invalid data supplied');
        }

        return view('invoice.invoicecreate', $pagedata);
    }

    public function invoiceForm($request)
    {
        $userinfo = $request->get('userinfo');
        $pagedata = array(
            'userinfo' => $userinfo,
            'pagetitle' => 'Create Invoice'
        );

        $pagedata['invoice_details'] = $request->post();
        $pagedata['invoice_id'] = $request->id ? $request->id : '';
        $pagedata['type'] = $request->type;

        $filters = [
            'id' => $request->id ? $request->id : '',
            'transaction_type' => ['invoice','receive_money'],
            'type' => $request->type
        ];

        if($request->type == 'edit' && $request->id){

            $invoice_details = $this->transactionCollectionRepository->getTransaction($filters, $userinfo[0]);

            if(empty($invoice_details))
            {
                return false;
            }

            $invoice_details = $invoice_details->toArray();

            $invoice_details['parts'] = $invoice_details['transactions'];
            $invoice_details['invoice_part_total_count'] = "[]";
            unset($invoice_details['transactions']);
            $pagedata['invoice_details'] = $invoice_details;

            $pagedata['invoice_history'] = $this->invoiceHistoryRepository->index($userinfo[0], $request->id);

            $pagedata['payment_history'] = PaymentHistory::where('user_id', $userinfo[0])->where('transaction_collection_id', $request->id)->get();
        }else{
            $invoice_details = $this->transactionCollectionRepository->getTransaction($filters, $userinfo[0]);

            if (!empty($invoice_details)) {
                $pagedata['transaction_number'] = 000001 + $invoice_details;
            }else{
                $pagedata['transaction_number'] = 000001;
            }
        }
        $pagedata['clients'] = $this->clientsRepository->getClients($userinfo[0]);

        $pagedata['invoice_items'] = $this->invoiceItemRepository->index($userinfo[0]);

        $chart_accounts_types = $this->chartAccountRepository->getChartAccountAndTypes();
        if ($chart_accounts_types->isNotEmpty()) {
            $pagedata['chart_accounts_types'] = $chart_accounts_types->toArray();
        }

        $chart_account = $this->chartAccountRepository->getChartAccountTypesAndParts($userinfo[0]);
        if ($chart_account->isNotEmpty()) {
            $pagedata['chart_account'] = $chart_account->toArray();
        }

        $tax_rates = $this->taxRateRepository->index();
        if ($tax_rates->isNotEmpty()) {
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
                'client_email' =>'required|max:255',
                'invoice_issue_date' => 'bail|required',
                'invoice_due_date' => 'bail|required',
                'invoice_number' => 'bail|required|max:255',
            ]);

            $pagedata['invoice_id'] = $request->invoice_id;
            $pagedata['type'] = $request->type;
            $invoice_details = [];
            $parts = [];
            $invoice_details = [
                "user_id" => $userinfo[0],
                "client_name" => $request->client_name,
                "client_email" => !empty($request->client_email) ? $request->client_email : '',
                "client_phone" => !empty($request->client_phone) ? $request->client_phone : '',
                "due_date" => !empty($request->invoice_due_date) ? $request->invoice_due_date : '',
                "issue_date" => $request->invoice_issue_date,
                "transaction_number" => !empty($request->invoice_number) ? $request->invoice_number : '',
                "default_tax" => !empty($request->invoice_default_tax) ? $request->invoice_default_tax : 'no_tax',
                "sub_total" =>  NumberFormat::string_replace(trim($request->invoice_sub_total)),
                "total_gst" => NumberFormat::string_replace(trim($request->invoice_total_gst)),
                "total_amount" =>  NumberFormat::string_replace(trim(str_replace("$", "", $request->invoice_total_amount))),
                "amount_paid" =>  NumberFormat::string_replace($request->amount_paid ? trim($request->amount_paid) : 0 ),
                "payment_date" => $request->amount_paid ? trim($request->amount_paid) : '',
                "transaction_type" => "invoice",
                "invoice_ref_number" => trim($request->invoice_ref_number) ? trim($request->invoice_ref_number) : 0,

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

            if($validator->fails()) {
                $get_items = $this->invoiceItemRepository->index($userinfo[0]);
                if (!empty($get_items)) {
                    $pagedata['invoice_items'] = $get_items->toArray();
                }

                $pagedata['clients'] = $this->clientsRepository->getClients($userinfo[0]);

                $chart_account = $this->chartAccountRepository->getChartAccountTypesAndParts($userinfo[0]);
                if($chart_account->isNotEmpty()) {
                    $pagedata['chart_account'] = $chart_account->toArray();
                }

                $chart_accounts_types = $this->chartAccountRepository->getChartAccountAndTypes();
                if ($chart_accounts_types->isNotEmpty()) {
                    $pagedata['chart_accounts_types'] = $chart_accounts_types->toArray();
                }

                $tax_rates = $this->taxRateRepository->index();
                if ($tax_rates->isNotEmpty()) {
                    $pagedata['tax_rates'] = $tax_rates->toArray();
                }
                return view('invoice.invoicecreate')->withErrors($validator)->with($pagedata);
            }

            $client = $request->only([
                'client_name',
                'client_email',
                'client_phone'
            ]);

            //---------Store or Edit Clients-------------//
            $this->clientsRepository->createOrUpdateClient($client, $userinfo[0]);


            $invoice_details['issue_date'] =  Carbon::createFromFormat('d/m/Y', $request->invoice_issue_date)->format('Y-m-d');
            $invoice_details['due_date'] =  !empty($request->invoice_due_date) ? Carbon::createFromFormat('d/m/Y', $request->invoice_due_date)->format('Y-m-d') : '';

            $particlars = $invoice_details['parts'];

            unset($invoice_details['parts']);
            unset($invoice_details['invoice_part_total_count']);
            $ids = [];

            if($request->invoice_id && $request->type == 'edit'){
                unset($invoice_details['invoice_ref_number']);
                unset($invoice_details['amount_paid']);
                unset($invoice_details['payment_date']);

                $this->transactionCollectionRepository->createOrUpdateTransaction($invoice_details, $transactions, $userinfo, $request->invoice_id, $request->type);

            }else{
                unset($invoice_details['amount_paid']);
                unset($invoice_details['payment_date']);
                $invoice_details['status'] =  $invoice_details['total_amount'] == 0 ? 'Paid' : 'Unpaid';

                $this->transactionCollectionRepository->createOrUpdateTransaction($invoice_details, $transactions, $userinfo, $request->invoice_id = 0, $request->type);

            }
        }

        return redirect()->route('invoice');

    }

    public function sendInvoice(Request $request)
    {
        $userinfo = $request->get('userinfo');
        if($request->invoice_id){
            $randomNumber = random_int(100000, 999999);

            $invoice_settings = SumbInvoiceSettings::where('user_id', $userinfo[0])->first();

            $filters = [
                'type' => 'edit',
                'id' => $request->invoice_id
            ];
            $invoice_detail = $this->transactionCollectionRepository->getTransaction($filters, $userinfo[0]);

            if (!empty($invoice_detail) && $invoice_detail->invoice_sent == 0) {

                $request->invoice_format = !empty($invoice_settings) && $invoice_settings['business_invoice_format'] ? $invoice_settings['business_invoice_format'] : 'format002';

                $invpdf['inv'] = [
                    'logo' => !empty($invoice_settings) ? $invoice_settings['business_logo'] : '',
                    'invoice_number' => $invoice_detail->transaction_number,
                    'client_name' => $invoice_detail->client_name,
                    'client_email' => $invoice_detail->client_email,
                    'client_address' => $invoice_detail->client_address,
                    'client_phone' => $invoice_detail->client_phone,
                    'invoice_sub_total' => $invoice_detail->sub_total,
                    'invoice_total_gst' => $invoice_detail->total_gst,
                    'invoice_total_amount' => $invoice_detail->total_amount,
                    'invoice_name' => !empty($invoice_settings) ? $invoice_settings['business_name'] : $userinfo[1],
                    'invoice_email' => !empty($invoice_settings) ? $invoice_settings['business_email'] : $userinfo[2],
                    'invoice_phone' => !empty($invoice_settings) ? $invoice_settings['business_phone'] : '',
                    'invoice_address' => !empty($invoice_settings) ? $invoice_settings['business_address'] : '',
                    'invoice_abn' => !empty($invoice_settings) ? $invoice_settings['business_abn'] : '',
                    'invoice_terms' => !empty($invoice_settings) ? $invoice_settings['business_terms_conditions'] : '',
                    'invoice_format' => $request->invoice_format,
                    'invoice_date' => $invoice_detail->issue_date,
                    'invoice_due_date' => $invoice_detail->due_date,
                    'inv_parts' => $invoice_detail->transactions->toArray(),
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

                $inv_filename = "INV-".str_pad($invoice_detail->transaction_number, 6, '0', STR_PAD_LEFT).".pdf";
                $inv_email_filename = "inv".Carbon::now()->timestamp.'-'.$invoice_detail->transaction_number.'-'.md5($randomNumber).".pdf";
                $path = public_path('pdf/'.$userinfo[0]);
                $pdf = Pdf::loadView('pdf.'.$request->invoice_format, $invpdf);

                if(!File::exists($path)) {
                    File::makeDirectory($path, 0775, true, true);
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
                    Notification::route('mail', $emails)->notify(new InvoiceNotification($invpdf['inv'] ,$emails));

                    // Mail::to($emails)->send(new InvoiceNotification($emails, $pdf, $invpdf['inv']));
                } catch (Exception $e) {
                    if (count(Mail::failures()) > 0) {

                    }
                }

                InvoiceReports::create([
                    'user_id' =>  $userinfo[0],
                    'transaction_collection_id' => $invoice_detail->id,
                    'invoice_report_file' => $inv_filename
                ]);

                $invoice_history = [
                    'email' => $request->send_invoice_to_emails,
                    'invoice_number' => $invoice_detail->transaction_number
                ];

                if($invoice_detail->status == 'Recalled'){

                    $this->transactionCollectionRepository->updateInvoiceStatus($invoice_detail->id, $userinfo, ['invoice_sent' => 1, 'status' => 'Unpaid'], $invoice_history);
                }else{

                    $this->transactionCollectionRepository->updateInvoiceStatus($invoice_detail->id, $userinfo, [ 'invoice_sent' => 1 ], $invoice_history);
                }

                return redirect()->route('invoice')->with('success', 'Invoice sent successfully');
            }
        }
        return redirect()->route('invoice');
    }

    public function invoiceItemForm(Request $request)
    {
        if ($request->ajax())
        {

            $userinfo = $request->get('userinfo');

            $item = [
                'user_id' => trim($userinfo[0]),
                'invoice_item_code' => $request->invoice_item_code,
                'invoice_item_name' => trim($request->invoice_item_name),
                'invoice_item_unit_price' => trim($request->invoice_item_unit_price),
                'invoice_item_tax_rate' => trim($request->invoice_item_tax_rate),
                'invoice_item_tax_rate_id' => trim($request->invoice_item_tax_rate_id),
                'invoice_item_description' => trim($request->invoice_item_description),
                'invoice_item_chart_accounts_parts_id' => trim($request->invoice_item_chart_accounts_parts_id)
            ];

            return $this->invoiceItemRepository->create($item);

        }
    }

    public function invoiceItemFormList(Request $request)
    {
        if ($request->ajax())
        {
            $userinfo = $request->get('userinfo');

            $invoice_items = $this->invoiceItemRepository->index($userinfo[0]);
            if($invoice_items->isNotEmpty())
            {
                return response()->json([

                    'data' => $invoice_items

                ], 200);
            }

            return response()->json([

                'message' => 'No item found'

            ], 404);

        }
    }

    public function invoiceItemFormListById(Request $request, $id)
    {
        if ($request->ajax())
        {
            $userinfo = $request->get('userinfo');

            $invoice_item = $this->invoiceItemRepository->show($userinfo[0], $id);
            if(!empty($invoice_item))
            {
                return response()->json([

                    'data' => $invoice_item

                ], 200);
            }

            return response()->json([

                'message' => 'No item found'

            ], 404);

        }
    }

    public function statusUpdate(Request $request)
    {
        $userinfo = $request->get('userinfo');

        $request->validate([
            'status' => 'required',
            'invoice_id' => 'required|exists:transaction_collections,id,user_id,'.$userinfo[0].',is_active,1',
        ]);

        $filters = [
            'type' => 'edit',
            'transaction_type' => ['invoice'],
            'id' => $request->invoice_id
        ];

        $date = Carbon::now()->toDateString();
        $time = Carbon::now()->toTimeString();

        $total_due_amount = $this->transactionCollectionRepository->getTransaction($filters, $userinfo[0]);
        if($total_due_amount){
            $invoice_history = array(
                "invoice_id" => trim($request->invoice_id),
                "invoice_number" => trim($total_due_amount->transaction_number),
                "user_id" => trim($userinfo[0]),
                "user_name" => trim($userinfo[1]),
                "action" => $request->status,
                "date" => $date,
                "time" => $time
            );

            if($userinfo[3] != 'user_pro' && $request->status == 'Paid' && $request->payment_date && $request->amount_paid)
            {
                $request->payment_date = Carbon::createFromFormat('d/m/yy', $request->payment_date)->format('Y-m-d');

                $request->amount_paid = NumberFormat::string_replace(trim($request->amount_paid));

                $request->amount_paid == $total_due_amount->total_amount ? $request->status = 'Paid' : $request->status = 'PartlyPaid' ;

                //Subtract paid amount with due amount
                $due_amount_remain = $total_due_amount->total_amount - $request->amount_paid;
                $amount_paid = $request->amount_paid + $total_due_amount->amount_paid;

                $invoice_history['description'] = 'Payment received on ' .$request->payment_date. ' for $'.trim($request->amount_paid);
                $invoice_history['action'] = $request->status;

                $invoice_status = [
                    'status' => $request->status,
                    'payment_date' => $request->payment_date,
                    'amount_paid' => $amount_paid,
                    'total_amount' => $due_amount_remain,
                ];

                $payment_history = [
                    'user_id'=> $userinfo[0],
                    'transaction_collection_id'=> $request->invoice_id,
                    'date'=> $request->payment_date,
                    'amount_paid'=> $request->amount_paid
                ];

                $status_updated = $this->transactionCollectionRepository->updateInvoicePaymentStatus($request->invoice_id, $userinfo, $invoice_status, $payment_history, $invoice_history, $is_payment_history_deleted=false);
                if(!$status_updated){
                    return redirect()->route('invoice')->with('error', 'Something went wrong');
                }
            }else{

                if($total_due_amount->status == 'Paid' && ($request->status == 'Unpaid' || $request->status == 'Voided')){
                    $invoice_status = [
                        'status' => $request->status,
                        'payment_date' => NULL,
                        'amount_paid' => 0,
                        'total_amount' => $total_due_amount->total_amount + $total_due_amount->amount_paid,
                        'transaction_number' => $total_due_amount->transaction_number
                    ];

                    $invoice_history['description'] = $request->status == 'Unpaid' ? "INV-".str_pad($total_due_amount->transaction_number, 6, '0', STR_PAD_LEFT).' to '.trim(ucfirst($total_due_amount->client_name)).' for $'.trim($total_due_amount->total_amount + $total_due_amount->amount_paid) : "";
                    $invoice_history['action'] = $request->status;

                    $status_updated = $this->transactionCollectionRepository->updateInvoicePaymentStatus($request->invoice_id, $userinfo, $invoice_status, $payment_history = [], $invoice_history, $is_payment_history_deleted=true);

                }else{
                    $invoice_history['description'] = " ";
                    $invoice_history['action'] = $request->status;

                    $status_updated = $this->transactionCollectionRepository->updateInvoicePaymentStatus($request->invoice_id, $userinfo, ['status' => $request->status], $payment_history = [], $invoice_history, $is_payment_history_deleted=false);
                }
            }

            if($status_updated)
            {
                return redirect()->route('invoice')->with('success', 'Invoice status updated successfully');
            }
            return redirect()->route('invoice')->with('error', 'Something went wrong');
        }
    }

    public function delete(Request $request)
    {
        $userinfo = $request->get('userinfo');

        $this->transactionCollectionRepository->destroy($userinfo, $request->id, ['Unpaid', 'Voided']);

        return redirect()->route('invoice')->with('success', 'Invoice deleted successfully');

    }

    public function invoiceTaxRates(Request $request)
    {
        if ($request->ajax())
        {
            $invoice_tax_rates = $this->taxRateRepository->index();
            if($invoice_tax_rates->isNotEmpty())
            {
                return response()->json([

                    'data' => $invoice_tax_rates

                ], 200);
            }

            return response()->json([

                'message' => 'No item found'

            ], 404);
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

        $transaction_number = $this->transactionCollectionRepository->getTransaction(['transaction_type' => ['invoice', 'receive_money']], $userinfo[0]);

        $invoice_details = $this->transactionCollectionRepository->getTransaction(['type' => 'edit', 'id' => $request->invoice_id ], $userinfo[0]);

        if ($invoice_details) {
            $invoice_details = $invoice_details->toArray();
            $invoice_details['invoice_ref_number'] = $invoice_details['transaction_number'];
            $invoice_details['invoice_clone'] = true;
            $invoice_details['status'] = 'Unpaid';
            $invoice_details['transaction_number'] = 000001 + $transaction_number;
            $invoice_details['parts'] = $invoice_details['transactions'];
            $invoice_details['invoice_part_total_count'] = "[]";
            unset($invoice_details['transactions']);

            $pagedata['invoice_details'] = $invoice_details;
            $pagedata['clients'] = $this->clientsRepository->getClients($userinfo[0]);
            $get_items = $this->invoiceItemRepository->index($userinfo[0]);
            if ($get_items->isNotEmpty()) {
                $pagedata['invoice_items'] = $get_items->toArray();
            }

            $chart_account = $this->chartAccountRepository->getChartAccountTypesAndParts($userinfo[0]);
            if ($chart_account->isNotEmpty()) {
                $pagedata['chart_account'] = $chart_account->toArray();
            }

            $chart_accounts_types = $this->chartAccountRepository->getChartAccountAndTypes();
            if ($chart_accounts_types->isNotEmpty()) {
                $pagedata['chart_accounts_types'] = $chart_accounts_types->toArray();
            }

            $tax_rates = $this->taxRateRepository->index();
            if ($tax_rates->isNotEmpty()) {
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

            $this->invoiceHistoryRepository->store($invoice_history);
        }
        return view('invoice.invoicecreate', $pagedata);
    }

    public function recallInvoice(Request $request)
    {
        $userinfo = $request->get('userinfo');

        $updated = $this->transactionCollectionRepository->reCallInvoice($userinfo, $request->id);

        if($updated)
        {
            return redirect("/invoice/$request->id/edit");
        }
    }

    public function updateInvoiceSentStatus(Request $request)
    {
        $userinfo = $request->get('userinfo');

        $invoice_sent = $this->transactionCollectionRepository->updateInvoiceStatus($request->id, $userinfo, ['invoice_sent' => 1], []);

        if($invoice_sent){
            return redirect()->route('invoice')->with('success', 'Invoice mark as sent');
        }else{
            return redirect()->route('invoice')->with('error', 'Something went wrong');
        }
    }
}
