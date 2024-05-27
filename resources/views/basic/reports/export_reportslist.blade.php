            <div class="row">
                <div class="col-sm-6 col-md-6 col-xl-6">
                    <?php if(!empty($transaction_details) && count($transaction_details) > 1){?>
                        <h3 style="padding:10px">Accounts Transactions</h3>
                    <?php } else if(!empty($transaction_details) && $transaction_details[0]['chart_accounts_particulars_name']){ ?>
                        <h3 style="padding:10px">{{ucfirst($transaction_details[0]['chart_accounts_particulars_name']). ' Transactions'}}</h3>
                    <?php }?>
                </div>
            </div>
            <br>
        <table class="">
            <thead>
                <tr>
                    <th style="border-top-left-radius: 7px;font-weight:bold" id="invoice_issue_date" > Date </th>
                    <th style="font-weight:bold" id="invoice_number" >Description</th>
                    <th style="font-weight:bold" id="client_name" >Source</th>
                    <th style="font-weight:bold" id="client_email" >Debt</th>
                    <th style="font-weight:bold" id="invoice_status" >Credit</th>
                    <th style="font-weight:bold" id="invoice_total_amount" >Running Balance</th>
                    <th style="font-weight:bold">Gross</th>
                    <th style="font-weight:bold">Tax</th>
                </tr>
            </thead>
            <tbody>
                <tr></tr>      
                @if(!empty($transaction_details))
                    @foreach($transaction_details as $transaction)
                        <tr>
                            <td style="color: #000000; font-weight:1000px">
                                <h5>{{!empty($transaction['chart_accounts_particulars_name']) ? $transaction['chart_accounts_particulars_name'] : ''}}</h5>
                            </td>
                        </tr>
                            @if(!empty($transaction['particulars']))
                                @foreach($transaction['particulars'] as $invoice_items)
                                <tr>
                                    <td onclick="redirectUrl('{{$invoice_items['type']}}', '{{$invoice_items['invoice']['primary_invoice_id']}}', '{{$invoice_items['status']}}')" >
                                        {{!empty($invoice_items['invoice']['invoice_issue_date']) ? $invoice_items['invoice']['invoice_issue_date'] : ''}}
                                    </td>
                                    <td onclick="redirectUrl('{{$invoice_items['type']}}', '{{$invoice_items['invoice']['primary_invoice_id']}}', '{{$invoice_items['status']}}')">
                                        {{!empty($invoice_items['invoice_parts_description']) ? $invoice_items['invoice_parts_description'] : ''}}
                                    </td>
                                    <td onclick="redirectUrl('{{$invoice_items['type']}}', '{{$invoice_items['invoice']['primary_invoice_id']}}', '{{$invoice_items['status']}}')">
                                        {{!empty($invoice_items['type']) && $invoice_items['type'] =='invoice' ? 'Receivable Invoice' : 'Payable'}}  
                                    </td>
                                    <td onclick="redirectUrl('{{$invoice_items['type']}}', '{{$invoice_items['invoice']['primary_invoice_id']}}', '{{$invoice_items['status']}}')">
                                        {{!empty($invoice_items['invoice_parts_expense_credit_amount']) && $invoice_items['type'] =='expense' ? number_format($invoice_items['invoice_parts_expense_credit_amount'], 2)  : '-'}}
                                    </td>
                                    <td onclick="redirectUrl('{{$invoice_items['type']}}', '{{$invoice_items['invoice']['primary_invoice_id']}}', '{{$invoice_items['status']}}')">
                                        {{!empty($invoice_items['invoice_parts_credit_amount']) && $invoice_items['type'] =='invoice' ? number_format($invoice_items['invoice_parts_credit_amount'], 2)  : '-'}}
                                    </td>
                                    <td>
                                        -
                                    </td>
                                    <td onclick="redirectUrl('{{$invoice_items['type']}}', '{{$invoice_items['invoice']['primary_invoice_id']}}', '{{$invoice_items['status']}}')">
                                        {{!empty($invoice_items['invoice_parts_amount']) ? number_format($invoice_items['invoice_parts_amount'], 2) : '-'}}
                                    </td>
                                    <td onclick="redirectUrl('{{$invoice_items['type']}}', '{{$invoice_items['invoice']['primary_invoice_id']}}', '{{$invoice_items['status']}}')">
                                        <?php if($invoice_items['invoice_gst']){
                                        echo  number_format($invoice_items['invoice_gst'], 2);
                                        } else if($invoice_items['expense_gst']){
                                        echo number_format($invoice_items['expense_gst'], 2);
                                        }?>
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                        
                        <tr>
                            <td style="color: #000000"><b>Total {{!empty($transaction['chart_accounts_particulars_name']) ? $transaction['chart_accounts_particulars_name'] : ''}}</b></td>
                            <td></td>
                            <td></td>
                            <td style="color: #000000"><b> {{!empty($transaction['total_expense_credits_amount'])  ? number_format($transaction['total_expense_credits_amount'], 2) : ''}}</b></td>
                            <td style="color: #000000"><b> {{!empty($transaction['total_credits_amount'])  ? number_format($transaction['total_credits_amount'], 2) : ''}}</b></td>
                            <td></td>
                            <td style="color: #000000"><b> {{!empty($transaction['total_parts_amount']) ? number_format($transaction['total_parts_amount'], 2) : ''}}</b></td>
                            <td style="color: #000000"><b> {{!empty($transaction['total_tax_amount']) ? number_format($transaction['total_tax_amount'], 2) : ''}}</b></td>
                        </tr>
                        <tr></tr>
                    @endforeach
                    @endif
                    <tr>
                        <td style="color: #000000"><b>Total </b></td>
                        <td></td>
                        <td></td>
                        <td style="color: #000000"><b> {{!empty($final_tansaction_details['final_expense_amount'])  ? number_format($final_tansaction_details['final_expense_amount'], 2) : ''}}</b></td>
                        <td style="color: #000000"><b> {{!empty($final_tansaction_details['final_invoice_amount'])  ? number_format($final_tansaction_details['final_invoice_amount'], 2) : ''}}</b></td>
                        <td></td>
                        <td style="color: #000000"><b> {{!empty($final_tansaction_details['final_gross_amount'])  ? number_format($final_tansaction_details['final_gross_amount'], 2) : ''}}</b></td>
                        <td style="color: #000000"><b> {{!empty($final_tansaction_details['final_tax_amount'])  ? number_format($final_tansaction_details['final_tax_amount'], 2) : ''}}</b></td>
                    </tr>

            </tbody>
        </table>
                               

<!-- END PAGE CONTAINER-->


<!-- end document-->