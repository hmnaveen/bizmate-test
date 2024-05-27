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
                    <h3 class="sumb--title m-b-20">Profit and Loss</h3>
                </section>
                <section>
                    <div class="row">
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

                            <form action="/basic/profit-loss"  method="GET" enctype="multipart/form-data" id="search_form">
                                <div class="row sumb--reporting">
                                    
                                    <div class="col-xl-3 col-lg-6 col-md-6">
                                        <div class="form-input--wrap">
                                            <label class="form-input--question" for="">Start Date</label>
                                            <div class="date--picker row">
                                                <div class="col-12">
                                                    <input type="text" id="start_date" name="start_date" placeholder="date('d/m/Y')"  readonly value="{{!empty($start_date) ? $start_date : '' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-lg-6 col-md-6">
                                        <div class="form-input--wrap">
                                            <label class="form-input--question" for="">End Date</label>
                                            <div class="date--picker row">
                                                <div class="col-12">
                                                    <input type="text" id="end_date" name="end_date" placeholder="date('d/m/Y')"  readonly value="{{!empty($end_date) ? $end_date : '' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-2 col-lg-4 col-md-4 col-sm-4">
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

                                    <div class="col-xl-2 col-lg-4 col-md-4 col-sm-4">
                                        <div class="form-input--wrap">
                                            <label class="form-input--question" for="">Compare with</label>
                                           
                                            <select class="form-input--dropdown" id="compare_with_type" name="compare_with_type" value="" onchange="getPeriod();" value="" >
                                                <option selected value="">None</option>    
                                                <option id="" value="months" {{!empty($compare_with_type) && $compare_with_type == "months" ? "selected" : '' }} >Months</option>
                                                <option id="" value="quarters" {{!empty($compare_with_type) && $compare_with_type == "quarters" ? "selected" : '' }} >Quarters</option>
                                                <option id="" value="years" {{!empty($compare_with_type) && $compare_with_type == "years" ? "selected" : '' }} >Years</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-2 col-lg-4 col-md-4 col-sm-4">
                                        <div class="form-input--wrap">
                                            <label class="form-input--question" for="">Periods</label>
                                            <select class="form-input--dropdown" id="compare_period" name="compare_period" value="{{!empty($compare_period)  ? $compare_period : '' }}" >
                                                <option selected value="">None</option>
                                                <?php if(!empty($compare_with_type)){?>

                                                    <option id="" value="1" {{!empty($compare_period) && $compare_period == "1" ? "selected" : '' }} >1 {{$compare_with_type ? substr($compare_with_type, 0, -1) : '' }}</option>
                                                    <option id="" value="2" {{!empty($compare_period) && $compare_period == "2" ? "selected" : '' }} >2 {{$compare_with_type}}</option>
                                                    <option id="" value="3" {{!empty($compare_period) && $compare_period == "3" ? "selected" : '' }} >3 {{$compare_with_type}}</option>
                                                    <option id="" value="4" {{!empty($compare_period) && $compare_period == "4" ? "selected" : '' }} >4 {{$compare_with_type}}</option>

                                                <?php }?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                                       

                                    <div class="dropdown col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 order-xl-5 order-lg-5 order-md-5 order-sm-6 order-6">
                                        <button type="button" name="" class="btn sumb--btn export--btn dropdown-toggle" value="Search" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Export Report</button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item" href="#" onclick="exportsProfitAndLoss('xlsx')">Excel</a>
                                            <a class="dropdown-item" href="#" onclick="exportsProfitAndLoss('csv')">CSV</a>
                                        </div>
                                    </div>
                                    <div class="reporting--list-btns col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 order-xl-6 order-lg-5 order-md-6 order-sm-5 order-5">
                                        <button type="submit" name="search_profit_loss" class="btn sumb--btn search--btn" value="Search" >Search</button>
                                        <button class="btn sumb--btn clear--btn sumb-clear-btn" onClick="clearSearchItems()">Clear Search</button>
                                    </div>
                                </div>
                                </form>                    
                                <form action="/basic/profit-loss/export-report"  method="GET" enctype="multipart/form-data" id="" hidden>
                                    <div class="col-sm-2">
                                        <div class="form-input--wrap" style="margin-top:35px">
                                            <input type="hidden" id="export_start_date" name="export_start_date" value="">
                                            <input type="hidden" id="export_end_date" name="export_end_date" value="">
                                            <input type="hidden" id="export_compare_with_type" name="export_compare_with_type" value="">
                                            <input type="hidden" id="export_compare_period" name="export_compare_period" value="">
                                            <input type="hidden" id="export_file_type" name="export_file_type" value="">
                                            <!-- <div class="dropdown">
                                                <button class="btn sumb--btn dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Export
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <a class="dropdown-item" href="#" onclick="exportsProfitAndLoss('xlsx')">Excel</a>
                                                    <a class="dropdown-item" href="#" onclick="exportsProfitAndLoss('csv')">CSV</a>
                                                </div>
                                            </div> -->

                                            <!-- <button type="button" name="" id="" class="btn sumb--btn" value="ExportCsv" onclick="exportsProfitAndLossCsv()">Export to Csv</button> -->
                                            <button hidden="hidden" type="submit" class="btn btn-primary delete--btn" id="export_profit_and_loss_to_csv" value="">Export to Csv</button>

                                        </div>
                                    </div>  
                                </form>

                                <div class="sumb--reportingTable sumb--putShadowbox">
                                    <div class="table-responsive">

                                        <!-------------------Table start----------------------------------------->
                                        <table @if (Request::get('compare_with_type')) class="withFilter" @endif>
                                            <thead>
                                                <tr>
                                                    <th colspan='1'></th>
                                                    @foreach($items as $item)
                                                        <th>
                                                            <h5>{{$item['display_date']}}</h5>
                                                        </th>
                                                    @endforeach
                                                </tr>
                                            </thead>

                                            <tr class="view--separator"><td colspan="<?php echo sizeof($items) + 1 ?>"></td></tr>

                                            <!-------------------Invoice Starts ----------------------------------------->

                                            <tbody>

                                                <tr class="view--title">
                                                    <th colspan="<?php echo sizeof($items) + 1 ?>">Sales</th>
                                                </tr>
                                                @foreach($items_metadata['invoice'] as $particular_name)
                                                    <tr>
                                                        <td>
                                                            {{$particular_name}}
                                                        </td>

                                                        @foreach($items as $item)
                                                            <?php
                                                                $total_particular = 0;$account_id='';$start_date='';$end_date='';
                                                                $transaction_type_index = array_search('invoice', array_column($item['transactions'], 'transaction_type'));

                                                                if($transaction_type_index !== false){
                                                                    $accounts_index = array_search($particular_name, array_column($item['transactions'][$transaction_type_index]['accounts'], 'chart_accounts_particulars_name'));
                                                                    if($accounts_index !== false){
                                                                        $start_date = $item['transactions'][$transaction_type_index]['accounts'][$accounts_index]['start_date'];
                                                                        $end_date = $item['transactions'][$transaction_type_index]['accounts'][$accounts_index]['end_date'];
                                                                        
                                                                        $account_id = $item['transactions'][$transaction_type_index]['accounts'][$accounts_index]['account_id'];
                                                                        $particulars = $item['transactions'][$transaction_type_index]['accounts'][$accounts_index]['particulars'];

                                                                        if(!empty($particulars)){
                                                                            foreach($particulars as $particular){
                                                                                $total_particular += $particular['parts_amount'];
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            ?>

                                                            <td>
                                                                @if ($total_particular == 0)
                                                                    -
                                                                @else
                                                                    <a href="/basic/reports?account_id={{$account_id}}&report_start_date={{$start_date}}&report_end_date={{$end_date}}" title="View Transaction">{{number_format($total_particular, 2)}} </a>
                                                                @endif
                                                            </td>

                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                                

                                                <tr class="view--perItemtotal">
                                                    <td><b>Total Sales</b></td>
                                                    
                                                    @foreach($items as $item)
                                                        <td>
                                                            <b>{{ $item['total_invoice'] ? number_format($item['total_invoice'], 2) : '' }}</b>                                      
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            </tbody>
                                            <!-------------------Invoice ends -----------------------------------------> 

                                            <tr class="view--separator"><td colspan="<?php echo sizeof($items) + 1 ?>"></td></tr>

                                            <!-------------------Expense Starts ---------------------------------------->

                                            <tbody>

                                                <tr class="view--title">
                                                    <th colspan="<?php echo sizeof($items) + 1 ?>">Operating Expenses</th>
                                                </tr>
                                                @foreach($items_metadata['expense'] as $particular_name)
                                                    <tr>
                                                        <td>
                                                            {{$particular_name}}
                                                        </td>

                                                        @foreach($items as $item)
                                                            <?php
                                                                $total_particular = 0;
                                                                $transaction_type_index = array_search('expense', array_column($item['transactions'], 'transaction_type'));

                                                                if($transaction_type_index !== false){
                                                                    $accounts_index = array_search($particular_name, array_column($item['transactions'][$transaction_type_index]['accounts'], 'chart_accounts_particulars_name'));
                                                                    if($accounts_index !== false){
                                                                        $start_date = $item['transactions'][$transaction_type_index]['accounts'][$accounts_index]['start_date'];
                                                                        $end_date = $item['transactions'][$transaction_type_index]['accounts'][$accounts_index]['end_date'];
                                                                        
                                                                        $account_id = $item['transactions'][$transaction_type_index]['accounts'][$accounts_index]['account_id'];
                                                                        $particulars = $item['transactions'][$transaction_type_index]['accounts'][$accounts_index]['particulars'];

                                                                        if(!empty($particulars)){
                                                                            foreach($particulars as $particular){
                                                                                $total_particular += $particular['parts_amount'];
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            ?>

                                                            <td>

                                                                @if ($total_particular == 0)
                                                                    -
                                                                @else
                                                                    <a href="/basic/reports?account_id={{$account_id}}&report_start_date={{$start_date}}&report_end_date={{$end_date}}" title="View Transaction">{{number_format($total_particular, 2)}} </a>
                                                                @endif
                                                                
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach

                                                <tr class="view--perItemtotal">
                                                    <td><b>Total Operating Expenses</b></td>
                                                    
                                                    @foreach($items as $item)
                                                        <td>
                                                            <b>{{ $item['total_expense'] ? number_format($item['total_expense'], 2) : '' }}</b>                                      
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            </tbody>
                                            <!-------------------Expense ends -----------------------------------------> 

                                            <tr class="view--separator"><td colspan="<?php echo sizeof($items) + 1 ?>"></td></tr>

                                            <tbody  class="overallTotal">
                                                <tr>
                                                    <td>Total Net Profit</td>
                                                    
                                                    @foreach($items as $item)
                                                        <td>
                                                            <b>{{ $item['total_net_profit'] ? number_format($item['total_net_profit'], 2) : '' }}</b>                                      
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            </tbody>
                                        </table>

                                        <!-------------------Table end----------------------------------------->
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
</body>
</html>
<script>
    
    $(function() {
        $( "#start_date" ).datepicker({ dateFormat: 'dd/mm/yy' });
        $( "#end_date" ).datepicker({ dateFormat: 'dd/mm/yy' });
    });

    function clearSearchItems(){
        $("#start_date").val('');
        $("#end_date").val('');
        $("#compare_with_type").empty('');
        $("#compare_period").empty();
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

function exportsProfitAndLoss(type)
{
    $("#export_file_type").val(' ');
    $("#export_file_type").val(type);

    $("#export_start_date").val($("#start_date").val());
    $("#export_end_date").val($("#end_date").val());
    $("#export_compare_period").val($("#compare_period").val());
    $("#export_compare_with_type").val($("#compare_with_type").val());
    $("#export_profit_and_loss_to_csv").click();
}
</script>
<!-- end document-->