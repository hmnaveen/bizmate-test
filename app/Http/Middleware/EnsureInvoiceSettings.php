<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\SumbInvoiceSettings;
use Illuminate\Support\Facades\Redirect;

class EnsureInvoiceSettings
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public  function handle(Request $request, Closure $next)
    {
        
        if(!empty($request->session()->get('keysumb'))){
            $value = $request->session()->get('keysumb');
            $decrypted_value = decrypt($value);
            $userinfo = explode("|&",$decrypted_value);

            $invoice_settings = SumbInvoiceSettings::where('user_id', $userinfo[0])->first();
            
            if(!empty($invoice_settings) && $invoice_settings['business_abn'] && $invoice_settings['business_email'] && $invoice_settings['business_name'] && $invoice_settings['business_address']) {
                return $next($request);
            }
            else if($userinfo[3] == 'user'){
                return redirect('/basic/invoice/settings');
            }else{
                return redirect('/invoice/settings');
            }
            
        }
        return $next($request);
    }
}
