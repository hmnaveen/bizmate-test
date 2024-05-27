<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\SignupMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use DB;
use URL;

use App\Models\SumbUsers;
use App\Models\SumbExpensesClients;
use App\Models\SumbExpenseDetails;
use App\Models\SumbExpenseParticulars;
use App\Models\SumbExpenseSettings;

class ExpenseController extends Controller {

    public function __construct() {
        //$this->userinfo = ;
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
            'pagetitle' => 'Expenses'
        );
        $errors = array(
            1 => ['A new expense has been saved.', 'primary'],
            2 => ['The expense was deleted.', 'danger'],
            3 => ['The expense is now paid.', 'primary'],
            4=> ['The expense is now voided.', 'primary'],
            5 => ['The expense is now unpaid.', 'primary'],
            6 => ['The expense was updated.', 'primary'],
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
        $pagedata['myurl'] = route('expense');
        $pagedata['ourl'] = route('expense', $purl);
        $pagedata['npurl'] = http_build_query(['ipp'=>$itemsperpage]);

        $pagedata['search_number_email_amount'] = '';
        $pagedata['start_date'] = '';
        $pagedata['end_date'] = '';
        $pagedata['orderBy'] = '';
        $pagedata['direction'] = '';
        
        //==== get all tranasactions
        $ptype = 'all';
        if (!empty($request->input('type'))) {
            $expensedata = SumbExpenseDetails::where('user_id', $userinfo[0])->where('inactive_status', 0)->paginate($itemsperpage);
            if(!empty($expensedata)){
                $expensedata = $expensedata->toArray();
            }
            $ptype = $request->input('type');
        } else {
            if($request->search_number_name_amount || $request->start_date || $request->end_date || $request->orderBy){
                
                if($request->start_date){
                    $start_date = Carbon::createFromFormat('m/d/Y', $request->start_date)->format('Y-m-d');
                }
                if($request->end_date){
                    $end_date = Carbon::createFromFormat('m/d/Y', $request->end_date)->format('Y-m-d');
                }
                // var_dump($request->start_date);die();
                $total_amount = $request->search_number_name_amount;
                $expense_number = $request->search_number_name_amount;

                if($request->search_number_name_amount){
                    if(is_numeric(trim($request->search_number_name_amount))){
                        $total_amount = trim($request->search_number_name_amount);
                        $expense_number = $total_amount;
                    } 
                    else if(is_string(trim($request->search_number_name_amount))){
                        $expense_number = trim(strtolower($request->search_number_name_amount));                        
                    }
                }
                $userinfo = $request->get('userinfo');
                $expensedata = SumbExpenseDetails::
                                where('user_id', $userinfo[0])
                                ->where('inactive_status', 0);
                                if($request->search_number_name_amount && $request->start_date && $request->end_date){
                                    // $expensedata->where('expense_date','>=',$start_date);
                                    // $expensedata->where('expense_date','<=',$end_date);
                                    // $expensedata->where('expense_number', 'LIKE', "%{$expense_number}%");
                                    // $expensedata->orWhere('client_name', 'LIKE', "%{$request->search_number_name_amount}%");
                                    // $expensedata->orWhere('expense_total_amount', 'LIKE', "{$total_amount}");
                                    
                                    $expensedata->where('expense_date','>=',$start_date)
                                    ->where('expense_date','<=',$end_date)
                                    ->where(function($query) use ($expense_number, $total_amount, $request) {
                                        $query->where('expense_number', 'LIKE', "%{$expense_number}%")
                                              ->orWhere('client_name', 'LIKE', "%{$request->search_number_name_amount}%")
                                              ->orWhere('expense_total_amount', 'LIKE', "{$total_amount}");
                                    });
                                }
                                if($request->search_number_name_amount && !$request->start_date && !$request->end_date){
                                    $expensedata->where('expense_number', 'LIKE', "%{$expense_number}%");
                                    $expensedata->orWhere('client_name', 'LIKE', "%{$request->search_number_name_amount}%");
                                    $expensedata->orWhere('expense_total_amount', 'LIKE', "{$total_amount}");
                                }
                                if($request->start_date && $request->end_date && !($request->search_number_name_amount)){
                                    $expensedata->whereBetween('expense_date',array($start_date, $end_date));
                                }
                                if($request->start_date && !$request->search_number_name_amount && !$request->end_date){
                                    $expensedata->where('expense_date','>=',$start_date);
                                }
                                if($request->end_date && !$request->search_number_name_amount && !$request->start_date){
                                    $expensedata->where('expense_date','<=',$end_date);
                                }
                                if(!$request->start_date && $request->search_number_name_amount && $request->end_date){
                                    $expensedata->where('expense_date','<=',$end_date)
                                    ->where(function($query) use ($expense_number, $total_amount, $request) {
                                        $query->where('expense_number', 'LIKE', "%{$expense_number}%")
                                              ->orWhere('client_name', 'LIKE', "%{$request->search_number_name_amount}%")
                                              ->orWhere('expense_total_amount', 'LIKE', "{$total_amount}");
                                    });
                                }
                                if(!$request->end_date && $request->search_number_name_amount && $request->start_date){
                                    $expensedata->where('expense_date','>=',$start_date)
                                    ->where(function($query) use ($expense_number, $total_amount, $request) {
                                        $query->where('expense_number', 'LIKE', "%{$expense_number}%")
                                              ->orWhere('client_name', 'LIKE', "%{$request->search_number_name_amount}%")
                                              ->orWhere('expense_total_amount', 'LIKE', "{$total_amount}");
                                    });
                                }
                                if($request->orderBy){
                                    $expensedata->orderBy($request->orderBy, $request->direction);
                                }
                $expensedata = $expensedata->paginate($itemsperpage)->toArray();

                $pagedata['search_number_name_amount'] = $request->search_number_name_amount;
                $pagedata['start_date'] = $request->start_date;
                $pagedata['end_date'] = $request->end_date;
                $pagedata['orderBy'] = $request->orderBy;
                if($request->direction == 'ASC')
                {
                    $pagedata['direction'] = 'DESC';
                }
                if($request->direction == 'DESC')
                {
                    $pagedata['direction'] = 'ASC';
                }
                
            }else{
                $pagedata['orderBy'] = 'expense_date';
                $pagedata['direction'] = 'ASC';

                $expensedata = SumbExpenseDetails::
                // with(['particulars'])
                // ->whereHas('particulars', function($query) use($userinfo) {
                //     $query->where('user_id', $userinfo[0]);
                // })
                // ->
                where('user_id', $userinfo[0])
                ->where('inactive_status', 0)
                ->orderBy('expense_date', 'DESC')
                ->paginate($itemsperpage)->toArray();
            }
        }
        //   echo "<pre>"; print_r($expensedata); echo "</pre>"; die();
        $pagedata['expensedata'] = $expensedata;
        
        //echo '<pre>';
        //print_r($expensedata);
        //paginghandler
        $allrequest = $request->all();
        $pfirst = $allrequest; $pfirst['page'] = 1;
        $pprev = $allrequest; $pprev['page'] = $expensedata['current_page']-1;
        $pnext = $allrequest; $pnext['page'] = $expensedata['current_page']+1;
        $plast = $allrequest; $plast['page'] = $expensedata['last_page'];
        $pagedata['paging'] = [
            'current' => url()->current().'?'.http_build_query($allrequest),
            'starpage' => url()->current().'?'.http_build_query($pfirst),
            'first' => ($expensedata['current_page'] == 1) ? '' : url()->current().'?'.http_build_query($pfirst),
            'prev' => ($expensedata['current_page'] == 1) ? '' : url()->current().'?'.http_build_query($pprev),
            'now' => 'Page '.$expensedata['current_page']." of ".$expensedata['last_page'],
            'next' => ($expensedata['current_page'] >= $expensedata['last_page']) ? '' : url()->current().'?'.http_build_query($pnext),
            'last' => ($expensedata['current_page'] >= $expensedata['last_page']) ? '' : url()->current().'?'.http_build_query($plast),
        ];
        //print_r($pagedata['paging']);
        //die();
        //echo "<pre>"; print_r($expensedata); die();
        
        
        //echo "<pre>"; print_r(empty($expensedata)); echo "</pre>"; die();
        
        //echo "<pre>loggedin!";
        //$value = $request->session()->get('keysumb');
        //print_r($request->get('userinfo'));
        return view('invoice.expenselist', $pagedata); 
    }
    
    //***********************************************
    //*
    //* Create Expenses Page
    //*
    //***********************************************
    public function create_expense(Request $request) {
        $userinfo =$request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Create Expenses'
        );
        $dtnow = Carbon::now();
        // $dtnow = Carbon::now();
        // if(!SumbExpenseSettings::where('user_id', $userinfo[0])->first()){
        //     SumbExpenseSettings::insert(['user_id'=>$userinfo[0], 'created_at'=>$dtnow, 'updated_at'=>$dtnow]);
        // }
       

        // $errors = array(
        //     1 => ['Values are required to process invoice or expenses, please fill in non-optional fields.', 'danger'],
        //     2 => ['Your amount is incorrect, it should be numeric only and no negative value. Please try again', 'danger'],
        //     3 => ['A new expenses has been saved.', 'primary'],
        // );
        // $pagedata['errors'] = $errors;
        // if (!empty($request->input('err'))) { $pagedata['err'] = $request->input('err'); }
        $get_settings = SumbExpenseSettings::where('user_id', $userinfo[0])->first();
        if(!empty($get_settings)){
            $pagedata['data'] = $get_settings->toArray();
        }
        else{
            $user = SumbUsers::where('id', $userinfo[0])->first();
            if(!empty($user)){
               // echo $user['id'];die();
                SumbExpenseSettings::insert(['user_id'=>$user['id'], 'created_at'=>$dtnow, 'updated_at'=>$dtnow]);
                $get_settings = SumbExpenseSettings::where('user_id', $userinfo[0])->first();
                if(!empty($get_settings)){
                    $pagedata['data'] = $get_settings->toArray();
                }
            }
        }
        
       $get_expclients = SumbExpensesClients::where('user_id', $userinfo[0])->orderBy('client_name')->get();
        if (!empty($get_expclients)) {
            $pagedata['exp_clients'] = $get_expclients->toArray();
        }
        $pagedata['type'] = 'create';
        return view('invoice.expensescreate', $pagedata);
    }
    
    //***********************************************
    //*
    //* Create Expenses Process
    //*
    //***********************************************
    public function save_expense(Request $request) {
        $userinfo = $request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Create Expense'
        );

        //validation
        $validator = $request->validate([
            'expense_number' => 'bail|required',
            'expense_date' => 'bail|required|date',
            'expense_due_date' => 'bail|required|date',
            'client_name' => 'bail|required|max:100',
            'expense_description.*' => 'bail|required',
            'item_quantity.*' => 'bail|required|numeric',
            'item_unit_price.*' => 'bail|required|numeric',
            'expense_tax.*' => 'bail|required|gt:-1',
            'expense_amount.*' => 'bail|required',
            'tax_type' => 'bail|required',
            'expense_total_amount.*' => 'bail|required|numeric',
            'total_gst.*' => 'bail|required|numeric',
            'total_amount.*' => 'bail|required|numeric',
            'file_upload' =>  'mimes:jpg,jpeg,png,pdf',
            'item_account.*' => 'bail|required',
        ]
        // ,
        // [
        //    'expense_number' => 'Expense Number is Required',
        //    'expense_date.required' => 'Date is Required',
        //    'expense_date.date' => 'Enter proper date format',
        //    'expense_due_date.required' => 'Due Date is Required',
        //    'expense_due_date.date' => 'Enter proper Due date format',
        //    'client_name.required' => 'Recepient Name is Required',
        //    'client_name.max' => 'Recepient Name must to exceed 100 characters',
        //    'expense_description.*.required' => 'Item Description is Required',
        //    'item_quantity.*.required' => 'Item Quantity is Required',
        //    //'item_quantity.*.gt' => 'Item Quantity must be greater than 0',
        //    'item_quantity.*.numeric' => 'Item Quantity must be a numeric value',
        //    'item_unit_price.*.required' => 'Item Unit Price is Required',
        //    //'item_unit_price.*.gt' => 'Item Unit Price must be greater than 0',
        //    'item_unit_price.*.numeric' => 'Item Unit Price must be a numeric value',
        //    'expense_tax.*.required' => 'Tax rate is Required',
        //    'expense_tax.*.gt' => 'Tax rate must be selected',
        //    'file_upload' =>  'Please insert image/pdf only'
        // ]
    );

        $expense_details = [];
        $dtnow = Carbon::now();

        $expense_date_exploded = explode("/", $request->expense_date);
        $expense_due_date_exploded = explode("/", $request->expense_due_date);
        $carbon_expense_date = Carbon::createFromDate($expense_date_exploded[2], $expense_date_exploded[0], $expense_date_exploded[1]);
        $carbon_expense_due_date = Carbon::createFromDate($expense_due_date_exploded[2], $expense_due_date_exploded[0], $expense_due_date_exploded[1]);

        $get_exp_settings = SumbExpenseSettings::where('user_id', $userinfo[0])->first()->toArray();

        if ($request->hasFile('file_upload')) {
            // Get the file instance
            $file = $request->file('file_upload');

            // Store the file in the public directory
            $path = $file->store('public');

            // Get the file URL
            $url = Storage::url($path);
        }

        $expense_details = array(
            "user_id" => $userinfo[0],
            "transaction_id" => $get_exp_settings['expenses_count'],
            "expense_number" => $request->expense_number,
            "client_name" => $request->client_name,
            "expense_date" => $carbon_expense_date,
            "expense_due_date" => $carbon_expense_due_date,
            "tax_type" => $request->tax_type,
            "expense_total_amount" => $request->expense_total_amount,
            "total_gst" => $request->total_gst,
            "total_amount" => $request->total_amount,
            "file_upload" => (isset($url) ? $url : ''),
            "file_upload_format" => (isset($file) ? $file->extension() : ''),
            "created_at" => $dtnow,
            "updated_at" => $dtnow,
            "status_paid" => (($request->total_amount != 0) ? 'unpaid' : 'paid')
        );

        $pagedata['expense_details'] = $expense_details;
        // if ($validator->fails()) {
        //     // return response()->json([
        //     //     'status' => false,
        //     //     'message' => 'validation error',
        //     //     'errors' => $validator->errors()
        //     // ], 401);
        //     // echo "sds";
        //     // die();
        //    // return redirect()->route( 'expenses-create' )->withErrors($validator)->with('form_data',$pagedata);
        //    //return Redirect::back()->withErrors($validator);
        //    return view('invoice.expensescreate')->withErrors($validator)->with($pagedata);
        // }

        //echo "<pre>"; var_dump( $expense_details); echo "</pre>";
       // die();
      
        // echo "<pre>";
        // print_r($request->all());
        
        //check form data
        // if (empty($request->invoice_date) || empty($request->client_name) || empty($request->amount)) {
        //     $oriform['err'] = 1;
        //     return redirect()->route('expenses-create', $oriform); die();
        // }
        
        // $oriform = ['err'=>0, 'invoice_date'=>$request->invoice_date, 'client_name'=>$request->client_name, 'invoice_details'=>$request->invoice_details, 'amount'=>$request->amount];
        
        // if (!empty($request->savethisrep)) {
        //     $oriform['savethisrep'] = $request->savethisrep;
        // } else {
        //     $oriform['savethisrep'] = 0;
        // }
        
        // //print_r(is_numeric($request->amount));
        // if (!is_numeric($request->amount)) {
        //     $oriform['err'] = 2;
        //     return redirect()->route('expenses-create', $oriform); die();
        // }
        

        
        
        //if save reciepient is on
        if (!empty($request->savethisrep)) {
            $getexp_clients = SumbExpensesClients::where(DB::raw('UPPER(client_name)'), strtoupper($request->client_name))
                ->where('user_id',$userinfo[0])->first();
            print_r($getexp_clients);
            if (empty($getexp_clients)) {
                $dataprep_client = [
                    'user_id'               => $userinfo[0],
                    'client_name'           => $request->client_name,
                   // 'client_description'    => $request->invoice_details,
                    'created_at'            => $dtnow,
                    'updated_at'            => $dtnow,
                ];
                SumbExpensesClients::insert($dataprep_client);
            }
        }
        
        //saving data
        $transaction_id = SumbExpenseDetails::insertGetId($expense_details);
        
        for ($i = 0; $i < count($request->item_quantity); $i++) {

            $request->item_account[$i];
            $account_code_name = explode('-', $request->item_account[$i]);

            $expense_details = array(
                "user_id" => $userinfo[0],
                "expense_description" => $request->expense_description[$i],
                "item_quantity" => $request->item_quantity[$i],
                "item_unit_price" => $request->item_unit_price[$i],
                "expense_tax" => $request->expense_tax[$i],
                "expense_amount" => $request->expense_amount[$i],
                "expense_id" => $transaction_id,
                "expense_number" => $get_exp_settings['expenses_count'],
                'expense_account_code' => $account_code_name[0],
                'expense_account_name' => $account_code_name[1],
                'created_at'            => $dtnow,
                'updated_at'            => $dtnow
            );
            SumbExpenseParticulars::insert($expense_details);
        };
        $updatethis = SumbExpenseSettings::where('user_id', $userinfo[0])->first();
        $updatethis->increment('expenses_count');
        
        return redirect()->route('expense', ['err'=>1]); die();
    }

    //***********************************************
    //*
    //* Edit Expense Page
    //*
    //***********************************************
    public function edit_expense(Request $request)
    {
        $userinfo = $request->get('userinfo');
        $id = $request->id;
        $pagedata = array(
            'userinfo' => $userinfo,
            'pagetitle' => 'Edit Expense'
        );
        $expense_particulars = [];
       
        $pagedata['expense_details'] = SumbExpenseDetails::where('user_id', $userinfo[0])->where('id', $id)->where('status_paid','unpaid')->where('inactive_status',0)->first();
        $expense_particulars = SumbExpenseParticulars::where('user_id', $userinfo[0])->where('expense_id', $id)->orderBy('id')->get();
        $pagedata['data'] = $get_settings = SumbExpenseSettings::where('user_id', $userinfo[0])->first()->toArray();
       
        if(!empty($expense_particulars)){
            $pagedata['expense_particulars'] = $expense_particulars->toArray();
        }

        $get_expclients = SumbExpensesClients::where('user_id', $userinfo[0])->orderBy('client_name')->get();
        if (!empty($get_expclients)) {
            $pagedata['exp_clients'] = $get_expclients->toArray();
        }
        $pagedata['type'] = 'edit';
        return view('invoice.expensescreate', $pagedata);

        //  echo "<pre>"; var_dump($pagedata['expense_details']); echo "</pre>";
        //  die();
        
    }
    //***********************************************
    //*
    //* View Expense Page
    //*
    //***********************************************
    public function view_expense(Request $request)
    {
        $userinfo = $request->get('userinfo');
        $id = $request->id;
        $pagedata = array(
            'userinfo' => $userinfo,
            'pagetitle' => 'Edit Expense'
        );
        $expense_particulars = [];
       
        $pagedata['expense_details'] = SumbExpenseDetails::where('user_id', $userinfo[0])->where('id', $id)->where('status_paid','!=','unpaid')->where('inactive_status',0)->first();
        $expense_particulars = SumbExpenseParticulars::where('user_id', $userinfo[0])->where('expense_id', $id)->orderBy('id')->get();
        $pagedata['data'] = $get_settings = SumbExpenseSettings::where('user_id', $userinfo[0])->first()->toArray();
       
        if(!empty($expense_particulars)){
            $pagedata['expense_particulars'] = $expense_particulars->toArray();
        }

        $get_expclients = SumbExpensesClients::where('user_id', $userinfo[0])->orderBy('client_name')->get();
        if (!empty($get_expclients)) {
            $pagedata['exp_clients'] = $get_expclients->toArray();
        }
        $pagedata['type'] = 'view';
          return view('invoice.expensescreate', $pagedata);

        //  echo "<pre>"; var_dump($pagedata['expense_details']); echo "</pre>";
        //  die();
        
    }
//***********************************************
    //*
    //* Delete Expense 
    //*
    //***********************************************
    public function delete_expense(Request $request)
    {
        $userinfo = $request->get('userinfo');
        $id = $request->id;
        $pagedata = array(
            'userinfo' => $userinfo,
            'pagetitle' => 'Delete Expense'
        );
        // $expense_particulars = [];
        // $deleteExpenseParticulars = [];

        // $expense_particulars = SumbExpenseParticulars::where('user_id', $userinfo[0])->where('expense_id', $id)->orderBy('id')->get();
        // if(!empty($expense_particulars)){
        //     $deleteExpenseParticulars = $expense_particulars->toArray();
        // }
        
        // SumbExpenseDetails::where('user_id', $userinfo[0])->where('transaction_id', $id)->first()->delete();
        
        // for($i = 0; $i < count($deleteExpenseParticulars); $i++){
        //     SumbExpenseParticulars::where('user_id', $userinfo[0])->where('id', $deleteExpenseParticulars[$i]['id'])->delete();
        // }
        $expense_details = array("inactive_status" => 1);

        $updateExpenseDetails = SumbExpenseDetails::where('user_id', $userinfo[0])->where('id', $id)->first();
        $updateExpenseDetails->update($expense_details);
        
        //die();
        return redirect()->route('expense', ['err'=>2]); die();
    
    }

    //***********************************************
    //*
    //* Update Expense Details
    //*
    //***********************************************
    public function update_expense(Request $request)
    {
        $userinfo = $request->get('userinfo');
        $id = $request->id;
        // $pagedata = array(
        //     'userinfo' => $userinfo,
        //     'pagetitle' => 'Update Expense'
        // );
        $expense_particulars = [];
        $expense_details = [];
        $updateExpenseParticulars = [];

        $validator = $request->validate([
            'expense_number' => 'bail|required',
            'expense_date' => 'bail|required|date',
            'expense_due_date' => 'bail|required|date',
            'client_name' => 'bail|required|max:100',
            'expense_description.*' => 'bail|required',
            'item_quantity.*' => 'bail|required|numeric|gt:0',
            'item_unit_price.*' => 'bail|required|numeric|gt:0',
            'expense_tax.*' => 'bail|required|gt:-1',
            'expense_amount.*' => 'bail|required|gt:0',
            'tax_type' => 'bail|required',
            'expense_total_amount.*' => 'bail|required|numeric|gt:0',
            'total_gst.*' => 'bail|required|numeric',
            'total_amount.*' => 'bail|required|numeric',
            'file_upload' =>  'mimes:jpg,jpeg,png,pdf'
        ]
        // ,
        // [
        //    'expense_number' => 'Expense Number is Required',
        //    'expense_date.required' => 'Date is Required',
        //    'expense_date.date' => 'Enter proper date format',
        //    'expense_due_date.required' => 'Due Date is Required',
        //    'expense_due_date.date' => 'Enter proper Due date format',
        //    'client_name.required' => 'Recepient Name is Required',
        //    'client_name.max' => 'Recepient Name must to exceed 100 characters',
        //    'expense_description.*.required' => 'Item Description is Required',
        //    'item_quantity.*.required' => 'Item Quantity is Required',
        //    'item_quantity.*.gt' => 'Item Quantity must be greater than 0',
        //    'item_quantity.*.numeric' => 'Item Quantity must be a numeric value',
        //    'item_unit_price.*.required' => 'Item Unit Price is Required',
        //    'item_unit_price.*.gt' => 'Item Unit Price must be greater than 0',
        //    'item_unit_price.*.numeric' => 'Item Unit Price must be a numeric value',
        //    'expense_tax.*.required' => 'Tax rate is Required',
        //    'expense_tax.*.gt' => 'Tax rate must be selected',
        //    'file_upload' =>  'Please insert image/pdf only'
        // ]
        );

        $dtnow = Carbon::now();
        
        $expense_date_exploded = explode("/", ($request->expense_date));
        $expense_due_date_exploded = explode("/", ($request->expense_due_date));
        
        $carbon_expense_date = Carbon::createFromDate($expense_date_exploded[2], $expense_date_exploded[0], $expense_date_exploded[1]);
        $carbon_expense_due_date = Carbon::createFromDate($expense_due_date_exploded[2], $expense_due_date_exploded[0], $expense_due_date_exploded[1]);

        // $get_exp_settings = SumbExpenseSettings::where('user_id', $userinfo[0])->first()->toArray();

        if ($request->hasFile('file_upload')) {
            // Get the file instance
            $file = $request->file('file_upload');

            // Store the file in the public directory
            $path = $file->store('public');

            // Get the file URL
            $url = Storage::url($path);
        }


        $expense_details = array(
            "user_id" => $userinfo[0],
            "expense_number" => $request->expense_number,
            "client_name" => $request->client_name,
            "expense_date" => $carbon_expense_date,
            "expense_due_date" => $carbon_expense_due_date,
            "tax_type" => $request->tax_type,
            "expense_total_amount" => $request->expense_total_amount,
            "total_gst" => $request->total_gst,
            "total_amount" => $request->total_amount,
            "file_upload" => (isset($url) ? $url : ''),
            "file_upload_format" => (isset($file) ? $file->extension() : ''),
            "updated_at" => $dtnow,
           // "status_paid" => 'paid'
        );
        // if ($validator->fails()) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'validation error',
        //         'errors' => $validator->errors()
        //     ], 401);
        //     // echo "sds";
        //     // die();
        //    // return redirect()->route( 'expenses-create' )->withErrors($validator)->with('form_data',$pagedata);
        // }

        $updateExpenseDetails = SumbExpenseDetails::where('user_id', $userinfo[0])->where('id', $id)->where('status_paid','unpaid')->where('inactive_status',0)->first();;
        $updateExpenseDetails->update($expense_details);

        $expense_particulars = SumbExpenseParticulars::where('user_id', $userinfo[0])->where('expense_id', $id)->orderBy('id')->get();
        if(!empty($expense_particulars)){
            $updateExpenseParticulars = $expense_particulars->toArray();
        }
        //echo count($request->item_quantity);
        //echo count($updateExpenseParticulars);

        if(count($request->item_quantity) == count($updateExpenseParticulars)){
            for ($i = 0; $i < count($request->item_quantity); $i++) {
            
                $account_code_name = explode('-', $request->item_account[$i]);

                $expense_particular_item = array(
                    "user_id" => $userinfo[0],
                    "expense_description" => $request->expense_description[$i],
                    "item_quantity" => $request->item_quantity[$i],
                    "item_unit_price" => $request->item_unit_price[$i],
                    "expense_tax" => $request->expense_tax[$i],
                    "expense_amount" => $request->expense_amount[$i],
                    "expense_account_code" => $account_code_name[0],
                    "expense_account_name" => $account_code_name[1],
                    "updated_at" => $dtnow,
                );
                $expense_item = SumbExpenseParticulars::where('user_id', $userinfo[0])->where('expense_id', $id)->where('id',$updateExpenseParticulars[$i]['id'])->first();
                $expense_item->update($expense_particular_item);
            };
        }else if(count($request->item_quantity) < count($updateExpenseParticulars)){
            for ($i = 0; $i < count($updateExpenseParticulars); $i++) {
                if($i < count($request->item_quantity)){

                    $account_code_name = explode('-', $request->item_account[$i]);

                    $expense_particular_item = array(
                        "user_id" => $userinfo[0],
                        "expense_description" => $request->expense_description[$i],
                        "item_quantity" => $request->item_quantity[$i],
                        "item_unit_price" => $request->item_unit_price[$i],
                        "expense_tax" => $request->expense_tax[$i],
                        "expense_amount" => $request->expense_amount[$i],
                        "expense_account_code" => $account_code_name[0],
                        "expense_account_name" => $account_code_name[1],
                        "updated_at" => $dtnow,
                    );
                    $expense_item = SumbExpenseParticulars::where('user_id', $userinfo[0])->where('expense_id', $id)->where('id',$updateExpenseParticulars[$i]['id'])->first();
                   $expense_item->update($expense_particular_item);
                //     echo "update";
                //   echo "<pre>"; var_dump($expense_particular_item); echo "</pre>";
                }else{
                    $expense_del_old_extra_item = SumbExpenseParticulars::where('user_id', $userinfo[0])->where('expense_id', $id)->where('id',$updateExpenseParticulars[$i]['id'])->first();
                   $expense_del_old_extra_item->delete();
                //   echo "delete";
                //   echo "<pre>"; var_dump($expense_del_old_extra_item); echo "</pre>";
                }
                
            };
        }else{
            for ($i = 0; $i < count($request->item_quantity); $i++) {
                if($i < count($updateExpenseParticulars)){

                    $account_code_name = explode('-', $request->item_account[$i]);
                    $expense_particular_item = array(
                        "user_id" => $userinfo[0],
                        "expense_description" => $request->expense_description[$i],
                        "item_quantity" => $request->item_quantity[$i],
                        "item_unit_price" => $request->item_unit_price[$i],
                        "expense_tax" => $request->expense_tax[$i],
                        "expense_amount" => $request->expense_amount[$i],
                        "expense_account_code" => $account_code_name[0],
                        "expense_account_name" => $account_code_name[1],
                        "updated_at" => $dtnow,
                    );
                    $expense_item = SumbExpenseParticulars::where('user_id', $userinfo[0])->where('expense_id', $id)->where('id',$updateExpenseParticulars[$i]['id'])->first();
                    $expense_item->update($expense_particular_item);
                //     echo "update";
                //   echo "<pre>"; var_dump($expense_particular_item); echo "</pre>";
                }else{
                    $expense_item = SumbExpenseParticulars::where('user_id', $userinfo[0])->where('expense_id', $id)->first();
                    
                    $account_code_name = explode('-', $request->item_account[$i]);
                    $expense_particular_new_item = array(
                        "user_id" => $userinfo[0],
                        "expense_description" => $request->expense_description[$i],
                        "item_quantity" => $request->item_quantity[$i],
                        "item_unit_price" => $request->item_unit_price[$i],
                        "expense_tax" => $request->expense_tax[$i],
                        "expense_amount" => $request->expense_amount[$i],
                        "expense_id" => $id,
                        "expense_number" => $expense_item['expense_number'],
                        "expense_account_code" => $account_code_name[0],
                        "expense_account_name" => $account_code_name[1],
                        'created_at'            => $dtnow,
                        'updated_at'            => $dtnow,
                    );
                    SumbExpenseParticulars::insert($expense_particular_new_item);
                //    echo "insert";
                //    echo "<pre>"; var_dump($expense_particular_new_item); echo "</pre>";
                }
            };
        }
        return redirect()->route('expense', ['err'=>6]); die();

        //  echo "<pre>"; var_dump($updateExpenseParticulars); echo "</pre>";
        //  die();
        
    }
   
    //***********************************************
    //*
    //* Invoice VOID PROCESS
    //*
    //***********************************************
    public function expense_void(Request $request) {
        $userinfo =$request->get('userinfo');
        $id = $request->id;
        $type = $request->type;
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Void Expense'
        );
        //echo "<pre>"; print_r($request->all()); //echo "</pre>";
        // $pagedata['oriform'] = $request->all();
        // echo "<pre>"; print_r($pagedata);
        // if (empty($pagedata['oriform']['invno'])) {
        //     return redirect()->route('expense', ['err'=>3]); die();
        // }

        $chk_inv = SumbExpenseDetails::where('user_id', $userinfo[0])->where('id', $id)->where('inactive_status',0)->first();
        if ($chk_inv->exists) {
            $chk_inv = $chk_inv->toArray();
        }
        //print_r($chk_inv);
        //die();
        SumbExpenseDetails::where('id',$chk_inv['id'])->update(['status_paid'=>$type]);
        //echo "<pre>"; print_r($chk_inv); //echo "</pre>";
        //die();
        return redirect()->route('expense', ['err'=>4]); die();
        //return redirect()->route('expense'); die();
    }
    //***********************************************
    //*
    //* Transaction status change PROCESS
    //*
    //***********************************************
    public function status_change(Request $request) {
        $userinfo =$request->get('userinfo');
        $id = $request->id;
        $type = $request->type;
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Status Change'
        );
      
        $chk_inv = SumbExpenseDetails::where('user_id', $userinfo[0])->where('id', $id)->where('status_paid','!=','void')->where('inactive_status',0)->first();
        if ($chk_inv->exists) {
            $chk_inv = $chk_inv->toArray();
        }
        //print_r($chk_inv);
        //die();
        SumbExpenseDetails::where('id',$chk_inv['id'])->update(['status_paid'=>$type]);
        //echo "<pre>"; print_r($chk_inv); //echo "</pre>";
        //die();
        if($type == 'paid'){
            return redirect()->route('expense', ['err'=>3]); die();
        }else{
            return redirect()->route('expense', ['err'=>5]); die();
        }
        
    }
}
