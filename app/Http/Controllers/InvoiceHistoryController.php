<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use DB;
use URL;

use App\Models\SumbUsers;
use App\Models\Transactions;
use App\Models\TransactionCollections;
use Illuminate\Support\Facades\Validator;
use App\Models\InvoiceHistory;

class InvoiceHistoryController extends Controller {

    public function __construct() {
        $this->middleware('invoice_seetings');
    }
    
    //***********************************************
    //*
    //* Invoice Page
    //*
    //***********************************************
    
    public function index(Request $request) {
        $userinfo =$request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Invoice & Expenses'
        );
        
        $itemsperpage = 10;
        if (!empty($request->input('ipp'))) { $itemsperpage = $request->input('ipp'); }
        $pagedata['ipp'] = $itemsperpage;
        
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
            $invoicedata = SumbInvoiceDetails::where('user_id', $userinfo[0])->where('is_active', 1)->paginate($itemsperpage)->toArray();  
            $ptype = $request->input('type');
        } else {
            
            if($request->search_number_email_amount || $request->start_date || $request->end_date || $request->orderBy || $request->filterBy){
                if($request->start_date){
                    $start_date = Carbon::createFromFormat('m/d/Y', $request->start_date)->format('Y-m-d');
                }
                if($request->end_date){
                    $end_date = Carbon::createFromFormat('m/d/Y', $request->end_date)->format('Y-m-d');
                }
                // var_dump($request->start_date);die();
                $total_amount = $request->search_number_email_amount;
                $invoice_number = $request->search_number_email_amount;

                if($request->search_number_email_amount){
                    if(is_numeric(trim($request->search_number_email_amount))){
                        $total_amount = ltrim($request->search_number_email_amount, '0');
                        $invoice_number = $total_amount;
                    }
                    else if(is_string(trim($request->search_number_email_amount))){
                        $invoice_number = str_replace('inv-00000', '', trim(strtolower($request->search_number_email_amount)));                        
                    }
                }
                $userinfo = $request->get('userinfo');
                $invoicedata = TransactionCollections::where('user_id', $userinfo[0])->where('is_active', 1);
                                if($request->search_number_email_amount){
                                    $invoicedata->where(function($query) use($invoice_number, $request, $total_amount){
                                        $query->where('transaction_number', 'LIKE', "%{$invoice_number}%")
                                       ->orWhere('client_email', 'LIKE', "%{$request->search_number_email_amount}%")
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
                $invoice_history = InvoiceHistory::where('user_id', $userinfo[0])
                        ->orderBy('date', 'DESC')
                        ->paginate($itemsperpage)->toArray();
            }
        }
        $pagedata['invoice_history'] = $invoice_history;
       
        $allrequest = $request->all();
        $pfirst = $allrequest; $pfirst['page'] = 1;
        $pprev = $allrequest; $pprev['page'] = $invoice_history['current_page']-1;
        $pnext = $allrequest; $pnext['page'] = $invoice_history['current_page']+1;
        $plast = $allrequest; $plast['page'] = $invoice_history['last_page'];
        $pagedata['paging'] = [
            'current' => url()->current().'?'.http_build_query($allrequest),
            'starpage' => url()->current().'?'.http_build_query($pfirst),
            'first' => ($invoice_history['current_page'] == 1) ? '' : url()->current().'?'.http_build_query($pfirst),
            'prev' => ($invoice_history['current_page'] == 1) ? '' : url()->current().'?'.http_build_query($pprev),
            'now' => 'Page '.$invoice_history['current_page']." of ".$invoice_history['last_page'],
            'next' => ($invoice_history['current_page'] >= $invoice_history['last_page']) ? '' : url()->current().'?'.http_build_query($pnext),
            'last' => ($invoice_history['current_page'] >= $invoice_history['last_page']) ? '' : url()->current().'?'.http_build_query($plast),
        ];
        return view('invoice.invoicehistory', $pagedata); 
    }
}
