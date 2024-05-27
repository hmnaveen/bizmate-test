@include('includes.head')
@include('includes.user-header')

<!-- PAGE CONTAINER-->
<div class="page-container">

    @include('includes.user-top')

    <!-- MAIN CONTENT-->
    <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid">
                <section>
                    <h3 class="sumb--title m-b-20">Business Activity</h3>
                </section>

                <section>
                    <div class="row sumb--transactions">
                        <div class="col-xl-12">
                            @isset($err) 
                            <div class="sumb-alert alert alert-{{ $errors[$err][1] }}" role="alert">
                                {{ $errors[$err][0] }}
                            </div>
                            @endisset

                            @if (\Session::has('success'))
                                <div class="alert alert-success">
                                    <ul>
                                        <li>{!! \Session::get('success') !!}</li>
                                    </ul>
                                </div>
                            @endif
                            <form action="/reports"  method="GET" enctype="multipart/form-data" id="search_form">
                                <div class="row">
                                    <div class="col-xl-3 col-lg-3 col-md-6">
                                        <div class="form-input--wrap">
                                            <label class="form-input--question" for="">Start Date</label>
                                            <div class="date--picker row">
                                                <div class="col-12">
                                                    <input type="text" id="start_date" name="report_start_date" placeholder="date('m/d/Y')"  readonly value="{{!empty($report_start_date) ? $report_start_date : '' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-6">
                                        <div class="form-input--wrap">
                                            <label class="form-input--question" for="">End Date</label>
                                            <div class="date--picker row">
                                                <div class="col-12">
                                                    <input type="text" id="end_date" name="report_end_date" placeholder="date('m/d/Y')"  readonly value="{{!empty($report_end_date) ? $report_end_date : '' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                                        <div class="form-input--wrap">
                                            <label class="form-input--question" for="">Pre-made filters</label>
                                            <select class="form-input--dropdown" id="date_filters" name="date_filters" value="" onchange="getDateFilters('date_filters')" value="" >
                                                <option selected value="">None</option>  
                                                <optgroup label="---">
                                                    <option id="" {{ !empty($filters) && $filters['current_month'] == $date_filters ? "selected" : '' }} value="{{!empty($filters) ? $filters['current_month'] : '' }}">This Month ({{!empty($filters) ? $filters['current_month_display_date'] : ''}})</option>
                                                    <option id="" {{ !empty($filters) && $filters['current_quarter'] == $date_filters ? "selected" : '' }} value="{{ !empty($filters) ? $filters['current_quarter'] : '' }}">This Quarter ({{!empty($filters) ? $filters['current_quarter_display_date'] : ''}})</option>
                                                    <option id="" {{ !empty($filters) && $filters['current_year'] == $date_filters ? "selected" : '' }} value="{{ !empty($filters) ? $filters['current_year'] : '' }}">This Financial Year ({{!empty($filters) ? $filters['current_year_display_date'] : ''}})</option>
                                                </optgroup>
                                                <optgroup label="---">  
                                                    <option id="" {{ !empty($filters) && $filters['last_month'] == $date_filters ? "selected" : '' }} value="{{ !empty($filters) ? $filters['last_month'] : '' }}">Last Month ({{!empty($filters) ? $filters['last_month_display_date'] : ''}})</option>
                                                    <option id="" {{ !empty($filters) && $filters['last_quarter'] == $date_filters ? "selected" : '' }} value="{{ !empty($filters) ? $filters['last_quarter'] : '' }}">Last Quarter ({{!empty($filters) ? $filters['last_quarter_display_date'] : ''}})</option>
                                                    <option id="" {{ !empty($filters) && $filters['last_year'] == $date_filters ? "selected" : '' }} value="{{ !empty($filters) ? $filters['last_year'] : '' }}">Last Financial Year ({{!empty($filters) ? $filters['last_year_display_date'] : ''}})</option>
                                                </optgroup>
                                                <optgroup label="---">  
                                                    <option id="" {{ !empty($filters) && $filters['month_to_date'] == $date_filters ? "selected" : '' }} value="{{ !empty($filters) ? $filters['month_to_date'] : '' }}">Month to Date ({{!empty($filters) ? $filters['month_to_date_display_date'] : ''}})</option>
                                                    <option id="" {{ !empty($filters) && $filters['quarter_to_date'] == $date_filters ? "selected" : '' }} value="{{ !empty($filters) ? $filters['quarter_to_date'] : '' }}">Quarter to Date ({{!empty($filters) ? $filters['quarter_to_date_display_date'] : ''}})</option>
                                                    <option id="" {{ !empty($filters) && $filters['year_to_date'] == $date_filters ? "selected" : '' }} value="{{ !empty($filters) ? $filters['year_to_date'] : '' }}">Year to Date ({{!empty($filters) ? $filters['year_to_date_display_date'] : ''}})</option>
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                                        <div class="form-input--wrap">
                                            <label class="form-input--question" for="">Accounts</label>
                                            <select class="form-input--dropdown" id="testSelect1" name="report_chart_accounts_ids[]" multiple>
                                                @foreach($account_parts as $particulars)
                                                    <option <?php if(in_array($particulars['id'], $account_parts_code)) echo 'selected' ?> value="{{ $particulars['id'] }}"> {{ $particulars['chart_accounts_particulars_code']}} - {{$particulars['chart_accounts_particulars_name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="dropdown col-xl-6 col-lg-6 col-md-6 col-sm-12 order-xl-4 order-lg-4 order-md-4 order-sm-5 order-5">
                                        <button type="button" name="" class="btn sumb--btn export--btn dropdown-toggle" value="Search" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Export Report</button>
                                    
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item" href="#" onclick="exportTransaction('xlsx')">Excel</a>
                                            <a class="dropdown-item" href="#" onclick="exportTransaction('csv')">CSV</a>
                                        </div>

                                    </div>
                                    <div class="reporting--list-btns col-xl-6 col-lg-6 col-md-6 col-sm-12 order-xl-5 order-lg-5 order-md-5 order-sm-4 order-4">
                                        <button type="submit" name="search_transactions" class="btn sumb--btn search--btn" value="Search">Search</button>
                                        <button onClick="clearSearchItems()" class="btn sumb--btn clear--btn sumb-clear-btn">Clear Search</button>
                                    </div>
                                </div>
                            </form>

                            <form action="/business-activity/export-report"  method="GET" enctype="multipart/form-data" id="" hidden>
                                <div class="col-sm-2">
                                    <div class="form-input--wrap" style="margin-top:35px">
                                        <input type="hidden" id="export_start_date" name="export_start_date" value="">
                                        <input type="hidden" id="export_end_date" name="export_end_date" value="">
                                        <input type="hidden" id="export_accounts" name="export_accounts[]" value="" >
                                        <input type="hidden" id="export_file_type" name="export_file_type" value="">

                                        <!-- <div class="dropdown">
                                            <button class="btn sumb--btn dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Export
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <a class="dropdown-item" href="#" onclick="exportTransaction('xlsx')">Excel</a>
                                                <a class="dropdown-item" href="#" onclick="exportTransaction('csv')">CSV</a>
                                            </div>
                                        </div> -->
                                        <!-- <button type="button" name="" id="" class="btn sumb--btn" value="ExportCsv" onclick="exportsTransaction()">Export to Csv</button> -->
                                        <button hidden type="submit" name="" id="export_transaction_to_csv" class="btn sumb--btn" value="ExportCsv" >Export to Csv</button>
                                    </div>
                                </div>
                            </form>
                                
                            <?php if(!empty($transaction_details) && count($transaction_details) > 1){?>
                                <h3 class="m-t-20">Accounts Transactions</h3>
                            <?php } else if(!empty($transaction_details) && $transaction_details[0]['chart_accounts_particulars_name']){ ?>
                                <h3 class="m-t-20">{{ucfirst($transaction_details[0]['chart_accounts_particulars_name']). ' Transactions'}}</h3>
                            <?php }?>
                            
                            <div class="sumb--transactionsTable sumb--putShadowbox">
                                <div class="table-responsive">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th id="invoice_issue_date" >Date </th>
                                                <th id="invoice_number" >Description</th>
                                                <th id="client_name" >Source</th>
                                                <th id="client_email" >Debt</th>
                                                <th id="invoice_status" >Credit</th>
                                                <th id="invoice_total_amount" >Running Balance</th>
                                                <th>Gross</th>
                                                <th>Tax</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="view--separator">
                                                <td colspan="8"></td>
                                            </tr>
                                            @if(!empty($transaction_details))
                                                @foreach($transaction_details as $transaction)
                                                    <tr>
                                                        <td class="table--alignLeft">
                                                            <h5>{{!empty($transaction['chart_accounts_particulars_name']) ? $transaction['chart_accounts_particulars_name'] .' Transaction' : ''}}</h5>
                                                        </td>
                                                    </tr>
                                                        @if(!empty($transaction['particulars']))
                                                            @foreach($transaction['particulars'] as $invoice_items)
                                                            <tr class="transactions--withLink">
                                                                <td onclick="redirectUrl('{{$invoice_items['type']}}', '{{$invoice_items['invoice']['primary_invoice_id']}}', '{{$invoice_items['status']}}')" >
                                                                    {{!empty($invoice_items['invoice']['invoice_issue_date']) ? $invoice_items['invoice']['invoice_issue_date'] : ''}}
                                                                </td>
                                                                <td onclick="redirectUrl('{{$invoice_items['type']}}', '{{$invoice_items['invoice']['primary_invoice_id']}}', '{{$invoice_items['status']}}')">
                                                                    {{!empty($invoice_items['invoice_parts_description']) ? $invoice_items['invoice_parts_description'] : ''}}
                                                                </td>
                                                                <td onclick="redirectUrl('{{$invoice_items['type']}}', '{{$invoice_items['invoice']['primary_invoice_id']}}', '{{$invoice_items['status']}}')">
                                                                    {{!empty($invoice_items['type']) && $invoice_items['type'] =='invoice' || $invoice_items['type'] =='receive_money' ? 'Receivable Invoice' : 'Payable'}}  
                                                                </td>
                                                                <td onclick="redirectUrl('{{$invoice_items['type']}}', '{{$invoice_items['invoice']['primary_invoice_id']}}', '{{$invoice_items['status']}}')">
                                                                    {{!empty($invoice_items['invoice_parts_expense_credit_amount']) && $invoice_items['type'] =='expense' ? number_format($invoice_items['invoice_parts_expense_credit_amount'], 2)  : ($invoice_items['type'] =='minor_adjustment' ? number_format($invoice_items['invoice_parts_amount'], 2) : ($invoice_items['type'] =='spend_money' ? number_format($invoice_items['invoice_parts_amount'], 2) : '-'))}}
                                                                </td>
                                                                <td onclick="redirectUrl('{{$invoice_items['type']}}', '{{$invoice_items['invoice']['primary_invoice_id']}}', '{{$invoice_items['status']}}')">
                                                                    {{!empty($invoice_items['invoice_parts_credit_amount']) && $invoice_items['type'] =='invoice' ? number_format($invoice_items['invoice_parts_credit_amount'], 2)  : ($invoice_items['type'] =='receive_money' ? number_format($invoice_items['invoice_parts_amount'], 2) : '-')}}
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
                                                        <td class="table--alignLeft"><b>Total {{!empty($transaction['chart_accounts_particulars_name']) ? $transaction['chart_accounts_particulars_name'] : ''}}</b></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td><b> {{!empty($transaction['total_expense_credits_amount'])  ? number_format($transaction['total_expense_credits_amount'], 2) : ''}}</b></td>
                                                        <td><b> {{!empty($transaction['total_credits_amount'])  ? number_format($transaction['total_credits_amount'], 2) : ''}}</b></td>
                                                        <td></td>
                                                        <td><b> {{!empty($transaction['total_parts_amount']) ? number_format($transaction['total_parts_amount'], 2) : ''}}</b></td>
                                                        <td><b> {{!empty($transaction['total_tax_amount']) ? number_format($transaction['total_tax_amount'], 2) : ''}}</b></td>
                                                    </tr>
                                                    <tr class="view--separator">
                                                        <td colspan="8"></td>
                                                    </tr>
                                                    
                                                @endforeach

                                                @else

                                                <tr>
                                                    <td colspan="8" style="padding: 30px 15px; text-align:center; font-size: 13px; color: #666">No data at this time.</td>
                                                </tr>


                                            @endif
                                        </tbody>
                                        <tbody class="overallTotal">
                                            <tr>
                                                <td rowspan="2">Total</td>
                                                <td></td>
                                                <td></td>
                                                <td><b> {{!empty($final_tansaction_details['final_expense_amount'])  ? number_format($final_tansaction_details['final_expense_amount'], 2) : ''}}</b></td>
                                                <td><b> {{!empty($final_tansaction_details['final_invoice_amount'])  ? number_format($final_tansaction_details['final_invoice_amount'], 2) : ''}}</b></td>
                                                <td></td>
                                                <td><b> {{!empty($final_tansaction_details['final_gross_amount'])  ? number_format($final_tansaction_details['final_gross_amount'], 2) : ''}}</b></td>
                                                <td><b> {{!empty($final_tansaction_details['final_tax_amount'])  ? number_format($final_tansaction_details['final_tax_amount'], 2) : ''}}</b></td>
                                            </tr>

                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td>Debt</td>
                                                <td>Credit</td>
                                                <td></td>
                                                <td>Gross</td>
                                                <td>Tax</td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section>
                    &nbsp;
                </section>
                
            </div>
        </div>
    </div>
</div>

<!-- END PAGE CONTAINER-->


@include('includes.footer')


<script>

let $body = $(this);

$(window).on('beforeunload', function(){

    $body.find('#pre-loader').show();

});

$(function () {

    $body.find('#pre-loader').hide();
});


$(document).ready(function(){

    $('#testSelect1').multiselect({
        nonSelectedText: 'Select Framework',
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true
    });
});

$(function() {
    $( "#start_date" ).datepicker({ dateFormat: 'dd/mm/yy' });
    $( "#end_date" ).datepicker({ dateFormat: 'dd/mm/yy' });
});

function redirectUrl(transaction_type, id, status){
    var uri_type = '';
    if(transaction_type == "expense" || transaction_type == "invoice"){
        transaction_type == 'expense' && (status == 'Paid' || status == 'PartlyPaid') ? uri_type= 'view' : uri_type='edit';
        var url = "{{URL::to('/endpoint/{id}/uri_type?from=reports')}}";
        url = url.replace('endpoint', transaction_type);    
        url = url.replace('{id}', id);
        url = url.replace('uri_type', uri_type);
        location.href = url;
    }
}

function exportTransaction(type)
{
    $("#export_file_type").val(' ');
    $("#export_file_type").val(type);

    $("#export_start_date").val($("#start_date").val());
    $("#export_end_date").val($("#end_date").val());
    $('#export_accounts').val(JSON.stringify($('#testSelect1').val()));
    $("#export_transaction_to_csv").click();
}
function clearSearchItems(){
    $("#start_date").val('');
    $("#end_date").val('');
    $("#testSelect1").empty('');
    return false;
}

function getDateFilters(id)
{
    var dates = JSON.parse($("#"+id).val());
   
    $("#start_date").datepicker('setDate', dates.start_date);
    $("#end_date").datepicker('setDate', dates.end_date);
}

function getPeriod(){
    if($("#compare_with_type").val() == 'months'){
        $("#compare_period").empty();
        $("#compare_period").append('<option id="" value="" >None</option><option id="" value="1">1 month</option>\
            <option id="" value="2">2 months</option>\
            <option id="" value="3" >3 months</option>\
            <option id="" value="4" >4 months</option>'
        );
    }
    else if($("#compare_with_type").val() == 'quarters'){
        $("#compare_period").empty();
        $("#compare_period").append('<option id="" value="" >None</option><option id="" value="1" >1 quarter</option>\
            <option id="" value="2" >2 quarters</option>\
            <option id="" value="3" >3 quarters</option>\
            <option id="" value="4" >4 quarters</option>'
        );
    }
    else if($("#compare_with_type").val() == 'years'){
        $("#compare_period").empty();
        $("#compare_period").append('<option id="" value="" >None</option><option id="" value="1" >1 year</option>\
            <option id="" value="2" >2 years</option>\
            <option id="" value="3" >3 years</option>\
            <option id="" value="4" >4 years</option>'
        );
    }
}
</script>
<!-- end document-->