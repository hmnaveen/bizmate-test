<?php

namespace App\Http\Controllers\ActivityStatement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BasAndTaxSettings;
use App\Models\ActivityStatements;


class BasAndTaxSettingsController extends Controller
{
    public function index(Request $request)
    {
        $userinfo = $request->get('userinfo');

        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Bas and Tax Overview'
        );

        $bas_and_tax_settings = BasAndTaxSettings::where('user_id', $userinfo[0])->first();

        $pagedata['bas_and_tax_settings'] = !empty($bas_and_tax_settings) ? $bas_and_tax_settings->toArray() : '';

        return view('activity_statement.bas-and-tax-settings', $pagedata);
    }

    public function create(Request $request)
    {
        $userinfo = $request->get('userinfo');
        
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Bas and Tax Overview'
        );

        $request->validate([
            'gst_calculation_period' => 'bail|required',
            'gst_accounting_method' => 'bail|required',
            'payg_withhold_period' => 'bail|required',
            'payg_income_tax_method' => 'bail|required',
        ]);

        try{
            \DB::beginTransaction();
           
            $bas_and_tax = BasAndTaxSettings::updateOrCreate([
                    'user_id' => $userinfo[0]
                ],
                [
                    'gst_calculation_period' => $request->gst_calculation_period, 
                    'gst_accounting_method' => $request->gst_accounting_method, 
                    'payg_withhold_period' => $request->payg_withhold_period, 
                    'payg_income_tax_method' => $request->payg_income_tax_method
                ]);
           
            \DB::commit();

        if($bas_and_tax->wasChanged())
        {
            ActivityStatements::where('user_id', $userinfo[0])->where('activity_statement_status', 'draft')->delete();
        }
        
        return redirect()->route('bas/overview'); 
        
        }catch(\Exceptions $e){

            \DB::rollback();

        }
    }

    public function verify(Request $request)
    {
        $userinfo = $request->get('userinfo');
        
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Bas and Tax Overview'
        );

        try{
            if(ActivityStatements::where('user_id', $userinfo[0])->first())
            {
                $exists = BasAndTaxSettings::where('user_id', $userinfo[0])->where('gst_calculation_period', $request->gst_calculation_period)
                                ->where('gst_accounting_method', $request->gst_accounting_method)
                                ->where('payg_withhold_period', $request->payg_withhold_period)
                                ->where('payg_income_tax_method', $request->payg_income_tax_method)
                                ->first();
                if(!$exists)
                {
                    return response()->json([
                        'message' => 'Draft statements will be deleted. Finalised statements will not be affected.',
                    ],422);
                }
            }
            return response()->json([], 200);
        }catch(\Exceptions $e){

            \Log::error($e);

        }
    }
}
