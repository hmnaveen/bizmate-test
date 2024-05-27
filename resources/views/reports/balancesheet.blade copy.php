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
                    <h3 class="sumb--title">Profit and Loss</h3>
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
                            <form action="/balance-sheet"  method="GET" enctype="multipart/form-data" id="search_form">
                                <div class="row">
                                    
                                    <div class="col-sm-2">
                                        <div class="form-input--wrap">
                                            <label class="form-input--question" for="">Start Date</label>
                                            <div class="date--picker row">
                                                <div class="col-12">
                                                    <input type="text" id="start_date" name="start_date" placeholder="date('m/d/Y')"  readonly value="{{!empty($start_date) ? $start_date : '' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-sm-2">
                                        <div class="form-input--wrap">
                                            <label class="form-input--question" for="">Compare with</label>
                                            <div class="col-12 for--tables">
                                                <select class="form-input--dropdown" id="compare_with_type" name="compare_with_type" value="" onchange="getPeriod();" value="" required>
                                                    <option selected value="">None</option>    
                                                    <option id="" value="months" {{!empty($compare_with_type) && $compare_with_type == "months" ? "selected" : '' }} >Months</option>
                                                    <option id="" value="quarters" {{!empty($compare_with_type) && $compare_with_type == "quarters" ? "selected" : '' }} >Quarters</option>
                                                    <option id="" value="years" {{!empty($compare_with_type) && $compare_with_type == "years" ? "selected" : '' }} >Years</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-input--wrap">
                                            <label class="form-input--question" for="">Periods</label>
                                            <div class="col-12 for--tables">
                                                <select class="form-input--dropdown" id="compare_period" name="compare_period" value="{{!empty($compare_period)  ? $compare_period : '' }}" required>
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
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-input--wrap" style="margin-top:35px">
                                            <button type="submit" name="search_profit_loss" class="btn sumb--btn" value="Search" >Search</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="sumb--recentlogdements sumb--putShadowbox">
                                    <div class="table-responsive">

                                    <!-------------------Table start----------------------------------------->
                                    <table class="invoice_list">
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
                                            <tbody>
                                            <tr><td colspan="<?php echo sizeof($items) + 1 ?>"></td></tr>
                                            <!-------------------Invoice Starts -----------------------------------------> 
                                                    <?php $net_assests = []; ?>
                                                    @foreach($items_metadata['accounts'] as $account)
                                                        <tr>
                                                            <td colspan="<?php echo sizeof($items) + 1 ?>">
                                                                <h4>{{$account['chart_accounts_name']}}</h4>
                                                            </td>
                                                        </tr>
                                                        <?php $account_types_total = []; ?>
                                                        @foreach($account['chart_accounts_types'] as $chart_accounts_type)
                                                            <tr>
                                                                <td colspan="<?php echo sizeof($items) + 1 ?>">
                                                                    <b> &nbsp;&nbsp;{{$chart_accounts_type['chart_accounts_type']}}</b>
                                                                </td>
                                                            </tr>
                                                            <?php $particulars_total = []; ?>
                                                            @foreach($chart_accounts_type['chart_account_particulars'] as $chart_account_particular)
                                                                <tr>
                                                                    <td>
                                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{$chart_account_particular['chart_accounts_particulars_name']}}
                                                                    </td>

                                                                    @foreach($items as $item_key=>$item)
                                                                        <?php
                                                                            $parts_amount = 0;
                                                                            $transaction_type_index = array_search($account['chart_accounts_id'], array_column($item['transactions'], 'chart_accounts_id'));

                                                                            if($transaction_type_index !== false){
                                                                                sort($item['transactions'][$transaction_type_index]['chart_accounts_types']);
                                                                                $accounts_index = array_search($chart_accounts_type['account_type_id'], array_column($item['transactions'][$transaction_type_index]['chart_accounts_types'], 'account_type_id'));
                                                                                
                                                                                if($accounts_index !== false){
                                                                                    sort($item['transactions'][$transaction_type_index]['chart_accounts_types'][$accounts_index]['chart_account_particulars']);
                                                                                    
                                                                                    $particulars_index = array_search($chart_account_particular['account_parts_id'], array_column($item['transactions'][$transaction_type_index]['chart_accounts_types'][$accounts_index]['chart_account_particulars'], 'account_parts_id'));
                                                                                    
                                                                                    if($particulars_index !== false){
                                                                                        $parts_amount = $item['transactions'][$transaction_type_index]['chart_accounts_types'][$accounts_index]['chart_account_particulars'][$particulars_index]['parts_amount'];
                                                                                    }
                                                                                }
                                                                            }
                                                                            if(!isset($particulars_total[$item_key])){
                                                                                $particulars_total[$item_key] = 0;
                                                                            }
                                                                            $particulars_total[$item_key] += $parts_amount;

                                                                            if(!isset($account_types_total[$item_key])){
                                                                                $account_types_total[$item_key] = 0;
                                                                            }
                                                                            $account_types_total[$item_key] += $parts_amount;

                                                                            if(!isset($net_assests[$item_key])){
                                                                                $net_assests[$item_key] = [];
                                                                            }

                                                                            if(!isset($net_assests[$item_key][$account['chart_accounts_name']])){
                                                                                $net_assests[$item_key][$account['chart_accounts_name']] = 0; 
                                                                            }
                                                                            $net_assests[$item_key][$account['chart_accounts_name']] += $parts_amount;
                                                                            
                                                                        ?>
                                                                        <td>
                                                                            <a href="" style="font-size: 13px;">{{$parts_amount}}</a>
                                                                        </td>
                                                                    @endforeach

                                                                </tr>
                                                            @endforeach

                                                            <tr>
                                                                <td>
                                                                    <h5> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Total {{$chart_accounts_type['chart_accounts_type']}}</h5>
                                                                </td>
                                                                @foreach($particulars_total as $particular_total)
                                                                    <td>
                                                                        <b> {{$particular_total}}</b>
                                                                    </td>
                                                                @endforeach
                                                            </tr>

                                                        @endforeach
                                                        
                                                            <tr>
                                                                <td>
                                                                    <h5> Total {{$account['chart_accounts_name']}}</h5>
                                                                </td>
                                                                @foreach($account_types_total as $account_type_total)
                                                                    <td>
                                                                        <b> {{$account_type_total}}</b>
                                                                    </td>
                                                                @endforeach
                                                            </tr>
                                                    @endforeach
                                                            <tr>
                                                                <td>
                                                                    <h5> Net Assets</h5>
                                                                </td>
                                                                
                                                                @foreach($net_assests as $net_asset)
                                                                    <?php $total_assets = 0; $total_liability = 0;
                                                                        
                                                                        foreach($net_asset as $net_asset_key=>$net_asset_val){
                                                                            if($net_asset_key == 'Assets' || $net_asset_key == 'Revenue'){
                                                                                $total_assets += $net_asset_val;
                                                                            }
                                                                            if($net_asset_key == 'Liabilities' || $net_asset_key == 'Expenses'){
                                                                                $total_liability += $net_asset_val;
                                                                            }
                                                                        }
                                                                    ?>
                                                                    <td>
                                                                        <b> {{$total_assets - $total_liability}}</b>
                                                                    </td>
                                                                @endforeach
                                                            </tr>
                                                           
                                                            <tr>
                                                                <td>
                                                                    <h5> Equity</h5>
                                                                </td>
                                                            </tr>
                                                            
                                                            <tr>
                                                                <td>
                                                                    <h5> Current Year Earnings </h5>
                                                                </td>
                                                                @foreach($net_assests as $net_asset)
                                                                    <?php $total_assets = 0; $total_liability = 0;
                                                                        
                                                                        foreach($net_asset as $net_asset_key=>$net_asset_val){
                                                                            if($net_asset_key == 'Assets' || $net_asset_key == 'Revenue'){
                                                                                $total_assets += $net_asset_val;
                                                                            }
                                                                            if($net_asset_key == 'Liabilities' || $net_asset_key == 'Expenses'){
                                                                                $total_liability += $net_asset_val;
                                                                            }
                                                                        }
                                                                    ?>
                                                                    <td>
                                                                        <b> {{$total_assets - $total_liability}}</b>
                                                                    </td>
                                                                @endforeach
                                                            </tr>
                                                            
                                                            <tr>
                                                                <td>
                                                                    <h5> Total Equity </h5>
                                                                </td>
                                                                @foreach($net_assests as $net_asset)
                                                                    <?php $total_assets = 0; $total_liability = 0;
                                                                        
                                                                        foreach($net_asset as $net_asset_key=>$net_asset_val){
                                                                            if($net_asset_key == 'Assets' || $net_asset_key == 'Revenue'){
                                                                                $total_assets += $net_asset_val;
                                                                            }
                                                                            if($net_asset_key == 'Liabilities' || $net_asset_key == 'Expenses'){
                                                                                $total_liability += $net_asset_val;
                                                                            }
                                                                        }
                                                                    ?>
                                                                    <td>
                                                                        <b> {{$total_assets - $total_liability}}</b>
                                                                    </td>
                                                                @endforeach
                                                            </tr>
                                                

                                        <!-------------------Invoice ends ----------------------------------------->      
                                            </tbody>
                                        </table>

                                    <!-------------------Table end----------------------------------------->




                                    </div>
                                </div>
                            </form>
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
        $( "#start_date" ).datepicker();
        $( "#end_date" ).datepicker();
    });

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