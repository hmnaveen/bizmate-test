<?php

namespace App\Http\Controllers\Basic;

use App\Http\Controllers\Controller;

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
use Illuminate\Support\Facades\Validator;
use App\Models\SumbInvoiceSettings;
use File;


class InvoiceSettingsController extends Controller {

    public function __construct() {
        // $this->middleware('basic_detail');
    }
    
    //***********************************************
    //*
    //* Invoice Settings View Page
    //*
    //***********************************************
    
    public function invoiceSettingsForm(Request $request) {
        $userinfo = $request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Invoice Settings'
        );
        $pagedata['type'] = 'add';
        $pagedata['invoice_settings'] = '';
        $pagedata['error_message'] = '';
        $pagedata['first_login'] = '';

        $invoice_settings = SumbInvoiceSettings::where('user_id', $userinfo[0])->orderBy('id')->first();
        
        if(!empty($invoice_settings) && $invoice_settings['business_abn'] && $invoice_settings['business_email'] && $invoice_settings['business_name'] && $invoice_settings['business_address']){
            $pagedata['invoice_settings'] = $invoice_settings->toArray();
            $pagedata['type'] = 'edit';
            $pagedata['settings_id'] = $pagedata['invoice_settings']['id'];
            $pagedata['first_login'] = 0;
        }
        else{
            $pagedata['error_message'] = 'Before proceeding, fill in your business information to create invoice templates and save time on repetitive input. You may view samples below.';
            $pagedata['first_login'] = 1;
        }
        return view('basic.settings', $pagedata);
    }

    public function store(Request $request)
    {
        $userinfo = $request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Create Invoice'
        );

        $redirect_user = 0;

        $validated = $request->validate([
            'business_name' => 'required|max:255',
            'business_email' => 'required|max:255',
            'business_phone' => 'required|max:14',
            'business_address' => 'required|max:255',
            'business_abn' => 'required|digits:11',
            'business_terms_conditions' => 'max:350'
        ]);
        $invoice_settings = [
            'user_id' => $userinfo[0],
            'business_name' => $request->business_name,
            'business_email' => $request->business_email,
            'business_phone' => $request->business_phone,
            'business_address' => $request->business_address,
            'business_logo' => $request->logo_path ? $request->logo_path : '', 
            'business_abn' => $request->business_abn,
            'business_terms_conditions' => $request->business_terms_conditions,
            'business_invoice_format' => $request->business_invoice_format
        ];

        DB::beginTransaction();
        $settingsExists = SumbInvoiceSettings::where('user_id', $userinfo[0])->first();
        if($settingsExists)
        {
            $update_setting = SumbInvoiceSettings::where('id', $settingsExists->id)->where('user_id', $userinfo[0])->update($invoice_settings);
            
            if($update_setting){
                DB::commit();

                $redirect_user = 1;
                return redirect()->route('basic/invoice/settings')->with('success', $redirect_user);
            }
        }else{
            $setting = SumbInvoiceSettings::create($invoice_settings);
            if($setting->id){
                DB::commit();
                $redirect_user = 1;
                return redirect()->route('basic/invoice/settings')->with('success', $redirect_user);
            }
        }
        return redirect()->route('basic/invoice/settings')->with('error', 'Something went wrong');
    }

    public function update(Request $request)
    {
        $userinfo = $request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Create Invoice'
        );

        $redirect_user = 0;
        $validated = $request->validate([
            'business_name' => 'required|max:255',
            'business_email' => 'required|max:255',
            'business_phone' => 'required|max:14',
            'business_address' => 'required|max:255',
            'business_abn' => 'required|max:11'
        ]);

        $invoice_settings = [
            'user_id' => $userinfo[0],
            'business_name' => $request->business_name,
            'business_email' => $request->business_email,
            'business_phone' => $request->business_phone,
            'business_address' => $request->business_address,
            'business_logo' => $request->logo_path ? $request->logo_path : '', 
            'business_abn' => $request->business_abn,
            'business_terms_conditions' => $request->business_terms_conditions,
            'business_invoice_format' => $request->business_invoice_format
        ];
        if($request->invoice_settings_id)
        {
            DB::beginTransaction();
            $setting = SumbInvoiceSettings::where('id', $request->invoice_settings_id)->where('user_id', $userinfo[0])->update($invoice_settings);
            if($setting){
                DB::commit();
                
                $redirect_user = 1;
            }
        }
        return redirect()->route('basic/invoice/settings')->with('success', $redirect_user);
    }

    public function logoUpload(Request $request)
    {
        if ($request->ajax())
        {
            $userinfo = $request->get('userinfo');
            $request->validate([
                'fileInput' => 'required|mimes:jpg,jpeg,png',
            ]);

            $setting = SumbInvoiceSettings::where('user_id', $userinfo[0])->first();
            if($setting && $setting->business_logo)
            {
                $existing_path = public_path('uploads/'.$userinfo[0]);
                if(file_exists($existing_path)){
                    File::deleteDirectory($existing_path);
                }
            }

            $file = $request->file('fileInput');

            $destinationPath = 'uploads/'.$userinfo[0];
            $ofile = $file->getClientOriginalName();
            $nfile = md5($ofile) . "." . $file->getClientOriginalExtension();
            $file->move($destinationPath, $nfile);

            if($file)
            {
                return response()->json([
            
                    'logo' => $nfile
                
                ], 200);
            }

            return response()->json([
            
                'message' => 'Error in file upload'
            
            ], 500);
        }
        else
        {
            return response()->json([
                
                'message' => 'Something went wrong'
            
            ], 500);
        }
    }
}
