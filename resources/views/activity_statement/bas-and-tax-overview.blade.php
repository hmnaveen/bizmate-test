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
                    <h3 class="sumb--title m-b-20">Activity Statements</h3>
                </section>

                <section>

                    
                        <div class="row">

                            <div class="col-xl-6 col-lg-6">
                                <div class="activity--statement sumb--putShadowbox">
                                    <label>How to create your Activity Statement</label>
                                    <ul class="instructions">
                                        <li>Set the parameters of your activity statement settings on the Settings page <span>(click Settings button)</span></li>
                                        <li>Select the date when your activity statement would start from</li>
                                        <li>Select Create Statement</li>
                                        <li>Prepare your activity statement by filling in necessary data</li>
                                        <li>Finalise and submit your filled in activity statement. Make sure all data is accurate</li>
                                        <li>Your activity statement will appear below <span>(Needs Attention)</span></li>
                                    </ul>
                                    
                                </div>
                            </div>
                            
                            <div class="col-xl-6 col-lg-6">
                                <div class="activity--statement sumb--putShadowbox" style="height: 92%">
                                    @php
                                        $collection = collect($activity_statements)->map(function (array $item) {
                                            return $item['activity_statement_status'];
                                        });
                                        $collected = $collection->countBy()->all();
                                    @endphp
                                    <div class="activity--statement_status">
                                        <div class="blocks needs--attention">
                                            <div>
                                                <span>
                                                    {{ !empty($collected) && isset($collected['draft']) ? $collected['draft'] : 0 }}
                                                </span>
                                                Needs Attention
                                            </div>
                                        </div>
                                        <div class="blocks completed--statements">
                                            <div>
                                                <span>
                                                {{ !empty($collected) && isset($collected['finalise']) ? $collected['finalise'] : 0 }}
                                                </span>
                                                Completed Statements
                                            </div>
                                        </div>
                                    </div>

                                    
                                </div>
                            </div>
                        </div>
                </section>

                <section>
                    @error('date_filters')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    <form action="/bas/statement" method="get" enctype="multipart/form-data" class="form-horizontal" id="">
                        <div class="row create-new--statement_wrap">
                            <div class="col-xl-6 col-lg-6 col-md-8 col-sm-12">
                                <div class="create-new--statement block-deets">
                                    <div class="financialyr--wrap">
                                        <div class="financialyr--dropdown" id="selected_date_dropdown">
                                            Select Tax Year
                                        </div>
                                        <div id="financialyr--options">
                                            <div id="default--selection">
                                                <a class="prev--years-btn">Previous </a>
                                                @if(!empty($financial_year))
                                                    <ul>
                                                        @foreach(array_values($financial_year)[0] as $current_year)
                                                            <li onclick="getData('{{$current_year['display_date']}}', '{{$current_year['start_date']}}', '{{$current_year['end_date']}}', '{{$current_year['type']}}')">
                                                                {{ !empty($current_year['display_date']) ? $current_year['display_date'] : '' }}
                                                                <span class="dates--inclusive">{{$current_year['start_date'].' - '.$current_year['end_date']}}</span>
                                                                <span class="selection--type">{{$current_year['type']}}</span>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </div>
                                            
                                            <div id="previous--years">
                                                <a class="current--year-btn">Current Year</a>
                                                <ul>
                                                    @if(!empty($years))
                                                        @foreach($years as $year)
                                                            <li>
                                                                <a class="prevyr-link" data-year="{{$year}}">{{$year}}</a>
                                                            </li>
                                                        @endforeach
                                                    @endif
                                                </ul>
                                            </div>

                                            
                                            <div id="selected--year">
                                                <a class="current--year-btn">Current Year</a>
                                               
                                                @if(!empty($financial_year))
                                                    <ul>
                                                        <li id="selected--year-value"></li>
                                                        @foreach(array_values($financial_year) as $previous_date)
                                                            @foreach($previous_date as $date)
                                                                <li id="{{$date['year']}}" data-value="{{$date['year']}}" style="display:none" onclick="getData('{{$date['display_date']}}', '{{$date['start_date']}}', '{{$date['end_date']}}', '{{$date['type']}}')">
                                                                    {{ !empty($date['display_date']) ? $date['display_date'] : '' }}
                                                                    <span class="dates--inclusive">{{$date['start_date'].' - '.$date['end_date']}}</span>
                                                                    <span class="selection--type">{{$date['type']}}</span>
                                                                </li>
                                                            @endforeach
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </div>
                                        </div>

                                    </div>

                                    <input type="hidden" id="date_filters" name="date_filters">
                                    <button type="submit" id="create_bas_and_tax_statement" name="create_bas_and_tax_statement" class="add--btn" value="Create Statement">Create Statement</button>
                                </div>

                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-4 col-sm-12 activity--statement__settings">
                                <a href="/bas/settings" class="activity--statement_btn"><i class="fa fa-cog"></i>Settings</a>
                            </div>
                        </div>
                    </form>

                </section>

                <hr class="form--separator">

                <section>
                    <h3 class="sumb--title m-b-20">Needs Attention</h3>
                </section>

                <section>
                    <div class="row">
                        <div class="col-xl-12">

                            <div class="activity--statement sumb--putShadowbox">

                                <div class="table-responsive">
                                    <table style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th style="border-top-left-radius: 7px;" colspan="5">Tax Year</th>
                                                <th style="border-top-right-radius: 7px; text-align: center;">Action</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                        @if(!empty($activity_statements))
                                            <?php array_filter($activity_statements, function($v,$k){
                                                    if($v['activity_statement_status'] == 'draft'){ ?>
                                                        <tr>
                                                            <td style="width: 20px; text-align: right; padding-right: 0px;" onclick="window.location='/bas/statement/edit?activity_id={{$v['id']}}&start_date={{$v['start_date']}}&end_date={{$v['end_date']}}'">
                                                                <i class="fa-solid fa-expand"></i>
                                                            </td>
                                                            
                                                            <td style="width: 120px;" onclick="window.location='/bas/statement/edit?activity_id={{$v['id']}}&start_date={{$v['start_date']}}&end_date={{$v['end_date']}}'">
                                                                <?php
                                                                    
                                                                    $start_date = explode(" ", date('M Y', strtotime($v['start_date'])));
                                                                    $end_date = explode(" ", date('M Y', strtotime($v['end_date'])));
                                                                    
                                                                    if( ($start_date[0] == $end_date[0]) && ($start_date[1] == $end_date[1]) )
                                                                    {
                                                                        echo $end_date[0]." ".$end_date[1];
                                                                    }else if ( ($start_date[0] != $end_date[0]) && ($start_date[1] == $end_date[1]) )
                                                                    {
                                                                        echo $start_date[0]." ".$end_date[0]." - ".$start_date[1]."".$end_date[1];
                                                                    }else if( $start_date[1] != $end_date[1] )
                                                                    {
                                                                        echo $start_date[0]." ".$start_date[1]."-".$end_date[0]." ".$end_date[1];
                                                                    }
                                                                ?>
                                                            </td>

                                                            <td style="width: 150px;" onclick="window.location='/bas/statement/edit?activity_id={{$v['id']}}&start_date={{$v['start_date']}}&end_date={{$v['end_date']}}'">
                                                                {{!empty($v['gst_calculation_period']) ? 'GST' : ''}},{{!empty($v['payg_withhold_period']) && $v['payg_withhold_period'] != 'none'  ? 'PAYG W' : ''}},{{!empty($v['payg_income_tax_method']) && $v['payg_income_tax_method'] != 'none' ? 'PAYG I' : ''}}
                                                            </td>
                                                            <td class="activity--statement__status" onclick="window.location='/bas/statement/edit?activity_id={{$v['id']}}&start_date={{$v['start_date']}}&end_date={{$v['end_date']}}'">
                                                                <span class="{{$v['activity_statement_status']}}">{{!empty($v['activity_statement_status']) ? ucFirst($v['activity_statement_status']) : ''}}</span>
                                                            </td>
                                                            <td style="text-align: right;">
                                                                <span class="bastax--resume">resume</span>
                                                            </td>

                                                            <td class="activity--statement__actions" style="width: 7%;">
                                                                <a title="Delete" onclick="deleteDraftedActivities({{$v['id']}}, {{json_encode($v['start_date'])}}, {{json_encode($v['end_date'])}})"><i class="fa-solid fa-trash-can"></i></a>
                                                            </td>
                                                        </tr>
                                                    <?php }
                                                }, ARRAY_FILTER_USE_BOTH); ?>
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            

                            
                        </div>
                    </div>

                </section>

                <section>
                    <h3 class="sumb--title m-b-20">Completed Statements</h3>
                </section>

                <section>
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="activity--statement sumb--putShadowbox">

                                <div class="table-responsive">
                                    <table style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th style="border-top-left-radius: 7px;" colspan="4">Tax Year</th>
                                                <th colspan="2">Payment Type</th>
                                                <th style="border-top-right-radius: 7px; text-align: center;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(!empty($activity_statements))
                                                <?php array_filter($activity_statements, function($v,$k){
                                                        if($v['activity_statement_status'] == 'finalise'){?>
                                                            <tr>

                                                                <td style="width: 20px; text-align: right; padding-right: 0px;" onclick="window.location='/bas/statement/edit?activity_id={{$v['id']}}&start_date={{$v['start_date']}}&end_date={{$v['end_date']}}'">
                                                                    <i class="fa-solid fa-circle-check"></i>
                                                                </td>
                                                            
                                                                <td style="width: 120px;" onclick="window.location='/bas/statement/edit?activity_id={{$v['id']}}&start_date={{$v['start_date']}}&end_date={{$v['end_date']}}'">
                                                                    <?php
                                                                        
                                                                        $start_date = explode(" ", date('M Y', strtotime($v['start_date'])));
                                                                        $end_date = explode(" ", date('M Y', strtotime($v['end_date'])));
                                                                        
                                                                        if(($start_date[0] == $end_date[0]) && ($start_date[1] == $end_date[1]))
                                                                        {
                                                                            echo $end_date[0]." ".$end_date[1];
                                                                        }
                                                                        else if(($start_date[0] != $end_date[0]) && ($start_date[1] == $end_date[1]))
                                                                        {
                                                                            echo $start_date[0]." ".$end_date[0]." - ". $start_date[1]."".$end_date[1];
                                                                        }
                                                                        else if($start_date[1] != $end_date[1])
                                                                        {
                                                                            echo $start_date[0]." ".$start_date[1]."-". $end_date[0]." ".$end_date[1];
                                                                        }
                                                                    //     echo ($start_date[0] == $end_date[0]) && ($start_date[1] == $end_date[1]) ? $end_date[0]." ".$end_date[1] : 
                                                                    //    (($start_date[0] != $end_date[0]) && ($start_date[1] == $end_date[1]) ?  $start_date[0]." ".$end_date[0]." - ". $start_date[1]."".$end_date[1]  :
                                                                    //     ($start_date[1] != $end_date[1]) ? $start_date[0]." ".$start_date[1]."-". $end_date[0]." ".$end_date[1] : '');
                                                                    ?>
                                                                </td>
                                                                
                                                                <td style="width: 150px;" onclick="window.location='/bas/statement/edit?activity_id={{$v['id']}}&start_date={{$v['start_date']}}&end_date={{$v['end_date']}}'">
                                                                    {{!empty($v['gst_calculation_period']) ? 'GST' : ''}},{{!empty($v['payg_withhold_period']) && $v['payg_withhold_period'] != 'none'  ? 'PAYG W' : ''}},{{!empty($v['payg_income_tax_method']) && $v['payg_income_tax_method'] != 'none' ? 'PAYG I' : ''}}
                                                                </td>

                                                                <td class="activity--statement__status" style="width: 120px;" onclick="window.location='/bas/statement/edit?activity_id={{$v['id']}}&start_date={{$v['start_date']}}&end_date={{$v['end_date']}}'">
                                                                    <span class="{{$v['activity_statement_status']}}">{{!empty($v['activity_statement_status']) ? ucFirst($v['activity_statement_status']) : ''}}</span>
                                                                </td>

                                                                <td style="width: 150px;" onclick="window.location='/bas/statement/edit?activity_id={{$v['id']}}&start_date={{$v['start_date']}}&end_date={{$v['end_date']}}'">
                                                                    {{ !empty($v['payment_type']) && $v['payment_type'] == 1 ? 'Amount Refund' : 'Amount Due' }}
                                                                </td>

                                                                <td class="activity--statement__payment" onclick="window.location='/bas/statement/edit?activity_id={{$v['id']}}&start_date={{$v['start_date']}}&end_date={{$v['end_date']}}'">
                                                                    <b class="{{ !empty($v['payment_type']) && $v['payment_type'] == 1 ? 'for--refund' : 'for--payment' }}">{{ !empty($v['payment_type']) && $v['payment_type'] == 1 ? $v['total_owed_by_ato'] : $v['total_owed_to_ato'] }}</b>
                                                                </td>

                                                                <td class="activity--statement__actions" style="width: 7%;" onclick="window.location='/bas/statement/edit?activity_id={{$v['id']}}&start_date={{$v['start_date']}}&end_date={{$v['end_date']}}'">
                                                                    <a title="Review"><i class="fa-solid fa-eye"></i></a>
                                                                </td>
                                                            </tr>
                                                        <?php }
                                                }, ARRAY_FILTER_USE_BOTH)?>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                
            </div>
        </div>
    </div>
</div>





<!-- END PAGE CONTAINER-->

@include('includes.footer')
</body>

</html>


<script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>


<script>

$(function() {
    let $body = $(this);
   
});

function deleteDraftedActivities(activity_id, start_date, end_date){
    if (activity_id && start_date && end_date)  {
        $body.find('#pre-loader').show();
        data = {activity_id: activity_id, start_date: start_date, end_date: end_date};

        $.ajaxSetup({headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }});
        $.ajax({
            method: 'delete',
            url: "/bas/statement/delete",
            data: data,
            success: function(data){
                $body.find('#pre-loader').hide();
                Swal.fire(

                'Success',
                data.message,
                'success'

                ).then((res) => {
                    window.location = window.location.href;
                });
            },
            error: function(e){
	        	$body.find('#pre-loader').hide();
	        	Swal.fire({
				  icon: 'error',
				  title: 'Oops...',
				  text: e.responseJSON.message
				})

	        }
        });
    } else {
        console.log("error!");
    }
}

$(document).ready(function () {
    $(".tab1").click(function () {
        
        $(".tab1").removeClass("btn sumb--btn active");
        $(this).addClass("btn sumb--btn active");
        
        const gst_calculation = $(this).find("[name=gst_calculation_period]").attr('id');
        $("#"+gst_calculation).attr('checked',true);
    });


    $(".tab2").click(function () {
        
        $(".tab2").removeClass("btn sumb--btn active");
        $(this).addClass("btn sumb--btn active");
        
        const gst_accounting = $(this).find("[name=gst_accounting_method]").attr('id');
        $("#"+gst_accounting).attr('checked',true);
    });


    $(".tab3").click(function () {
        
        $(".tab3").removeClass("btn sumb--btn active");
        $(this).addClass("btn sumb--btn active");
        
        const payg_withhold_period = $(this).find("[name=payg_withhold_period]").attr('id');
        $("#"+payg_withhold_period).attr('checked',true);
    });


    $(".tab4").click(function () {
        
        $(".tab4").removeClass("btn sumb--btn active");
        $(this).addClass("btn sumb--btn active");
        
        const payg_income_tax = $(this).find("[name=payg_income_tax_method]").attr('id');
        $("#"+payg_income_tax).attr('checked',true);
    });


    // Financial Year Dropdown
    $(".financialyr--dropdown").click(function(e){
        e.stopPropagation();
        $("#financialyr--options").toggleClass("show");
    });

    //prevent dropdown from closing
    $("#financialyr--options").click(function(e){
        e.stopPropagation();
    });

    //previous years
    $(".prev--years-btn").click(function(){
        $("#default--selection").hide();
        $("#previous--years").toggleClass("show");
    });

    //return to current year
    $(".current--year-btn").click(function(){
        $("#default--selection").show();
        $("#previous--years").removeClass("show");
        $("#selected--year").removeClass("show");
    });

    //get previous year
    $(".prevyr-link").click(function(){
        $ShowYear = $(this).data("year");
        
        $("#previous--years").removeClass("show");
        $("#selected--year").toggleClass("show")
        $("#selected--year-value").text("Viewing "+$ShowYear);

        var v = $(this).data("year");
        
        $('ul li').each(function(){
            var l = this.getAttribute('data-value');
            if(l)
            {
                return $(this).toggle(l.indexOf(v) !== -1);
            }
        });
    });


    //hide/reset dropdown when click outside
    $(document).click(function(){
        $("#financialyr--options").removeClass("show");
        $("#default--selection").show();
        $("#previous--years").removeClass("show");
        $("#selected--year").removeClass("show")
    });
});

function getData(display_date, start_date, end_date, type)
{
    $("#selected_date_dropdown").empty();
    $("#selected_date_dropdown").text(display_date);
    $("#financialyr--options").removeClass("show");

    $("#date_filters").val(start_date+","+end_date+","+encodeURI(type));
}
</script>
<!-- end document-->