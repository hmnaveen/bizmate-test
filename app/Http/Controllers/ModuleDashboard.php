<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use App\Mail\SignupMail;
use Carbon\Carbon;
use DB;

//Models
use App\Models\TransactionCollections;
use App\Models\SumbUsers;
use App\Traits\InvoiceAndExpenseGraph;

class ModuleDashboard extends Controller {

    public $userinfo;
    use InvoiceAndExpenseGraph;
    public function __construct() {
        //$this->userinfo = ;
        $this->middleware('invoice_seetings');
    }
    
    //***********************************************
    //*
    //*  Dashboard Page
    //*
    //***********************************************
    public function index(Request $request) {
        $userinfo = $request->get('userinfo');
        $pagedata = array('userinfo'=> $userinfo);


        $weekly_transaction = []; 
        // $invoice_monthly_transaction = []; $expense_monthly_transaction = [];

        $transactions = $this->getInvoiceAndExpenseGraphs($request, 'dashboard');

        if(!empty($transactions)){
            $weekly_transaction = $transactions['weekly_transaction'];
            $invoice_monthly_transaction = $transactions['invoice_monthly_transaction'];
            $expense_monthly_transaction = $transactions['expense_monthly_transaction'];
        }

        $total_invoice_counts = TransactionCollections::groupBy('status')->groupBy('transaction_type')
            ->select( DB::raw('transaction_type, status, COUNT(*) as status_count, sum(total_amount+amount_paid) as total') )
            ->where('is_active', 1)
            ->whereIn('transaction_type', ['invoice', 'expense'])
            ->where('user_id', $userinfo[0])
            ->orderBy('status')
            ->get();

        $total_invoice_amount = TransactionCollections::where('is_active', 1)
            ->where('transaction_type', 'invoice')
            ->where('user_id', $userinfo[0])
            ->sum('total_amount');

        $pagedata['total_invoice_amount'] = !empty($total_invoice_amount) ?  $total_invoice_amount : '';
        $pagedata['total_invoice_counts'] = !empty($total_invoice_counts) ?  $total_invoice_counts->toArray() : '';

        // echo "<pre>";  var_dump($invoice_monthly_transaction); echo "</pre>";die();
        $pagedata['bar_chart_data'] = $weekly_transaction;
        $pagedata['invoice_line_chart_data'] = $invoice_monthly_transaction;
        $pagedata['expense_line_chart_data'] = $expense_monthly_transaction;

        return view('dashboard', $pagedata); 
        
    }

}
