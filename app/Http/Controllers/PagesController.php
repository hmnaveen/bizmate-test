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

class PagesController extends Controller
{
    public $userinfo;
    use InvoiceAndExpenseGraph;
    public function __construct() {
        //$this->userinfo = ;
        $this->middleware('invoice_seetings');
    }
    
    public function membershippage(Request $request) {
        $userinfo = $request->get('userinfo');
        $pagedata = array('userinfo'=> $userinfo);
        
        return view('pages.buynow', $pagedata); 
    }
}
