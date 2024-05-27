@include('includes.head')
@include('includes.user-header')

<link href="/css/accordion-styles.css?v={{ config('app.version') }}" rel="stylesheet" media="all">


<!-------Invoice due amount and date alert pop-up--------------->
<form action="/expense-status-change"  method="GET" enctype="multipart/form-data">
    @csrf
    <div id="expense_payment_date_modal" class="modal fade modal-reskin modal-deleteItem" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title paymenticon--header" id="exampleModalLabel">Payment Date</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 order-xl-6">
                            <div class="form-input--wrap">
                                <label class="form-input--question" for="">Amount Paid</label>
                                <div class="form--inputbox row">
                                    <div class="col-12">
                                        <input type="float" id="expense_amount_paid" name="amount_paid" placeholder=""  value="" required onfocusin="expenseRemoveComma('expense_amount_paid')" onfocusout="expenseAddComma('expense_amount_paid')">
                                    </div>

                                    <input type="hidden" id="expense_due_amount" value="">
                                </div>
                                <div class="" role="alert" id="expense_amount_paid_error"></div>

                            </div>
                        </div>

                        <div class="col-xl-6 col-lg-6 order-xl-6">
                            <div class="form-input--wrap">
                                <label class="form-input--question" for="">Date Paid</label>
                                <div class="date--picker row">
                                    <div class="col-12">
                                        <input type="text" id="expense_payment_date" name="payment_date" placeholder="Date('DD/MM/YYYY')"  readonly value="" required>
                                    </div>
                                </div>
                                <div class="" role="alert" id="expense_payment_date_error"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="expense_status" name="status" placeholder="Date('DD/MM/YYYY')"  readonly value="">
                    <input type="hidden" id="expense_id" name="expense_id" readonly value="">

                    <button type="button" class="btn btn-secondary close--btn" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary payment--btn" id="submit_expense_payment_date" value="">Submit</button>
                    <button hidden="hidden" type="submit" class="btn btn-primary delete--btn" id="payment_date_form" value="">Submit</button>
                </div>
            </div>
        </div>
    </div>
</form>
<!-------Invoice due amount and date alert pop-up end--------------->


<!-- PAGE CONTAINER-->
<div class="page-container">

<?php $current_year = []; $current_year_sum = 0; $previous_year = []; $previous_year_sum = 0;
if(!empty($line_chart_data)){
    foreach($line_chart_data[0]['data'] as $current){
        $current_year_sum += $current['total'];
        $current_year[] = $current['total'];
    }

    foreach($line_chart_data[1]['data'] as $previous){
        $previous_year_sum += $previous['total'];
        $previous_year[] = $previous['total'];
    }
}?>


    @include('includes.user-top')

    <!-- MAIN CONTENT-->
    <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid">

                <section>
                    <h3 class="sumb--title">Expenses</h3>
                </section>

                <section>

                    <div class="accordion" id="accordionExample" style="margin-bottom: 20px;">
                        <div class="accordion-item">
                            <h3 class="accordion-header" id="headingOne">
                                <button class="accordion-button" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    Summary Graph
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                                <div class="accordion-body">

                                <div class="sumb--graphs row" >
                                    <div class="col-xl-5 col-lg-5 col-md-12">
                                        <div class="sumb--graphbox sumb--dashstatbox sumb--putShadowbox invoices-block">
                                            <h5>Bills you need to pay</h5>

                                            <div class="Invoices-wrap">
                                                <canvas id="ExpensesChart"></canvas>
                                            </div>

                                            <div class="block-deets">
                                                <ul>
                                                    <li>
                                                        <!-- <span>{{ count($total_expense_counts) }}</span> Created Invoices <u>@if(!empty($total_expense_amount)) ${{number_format($total_expense_amount, 2)}} @endif</u> -->
                                                    </li>
                                                    @if(!empty($total_expense_counts))
                                                        @foreach($total_expense_counts as $expense_count)
                                                            <li class="{{ $expense_count['status'] }}">
                                                                <span>{{$expense_count['status_count']}}</span> {{$expense_count['status']}} Expense <u>${{!empty($expense_count) ? number_format($expense_count['total'], 2) : ''}}</u>
                                                            </li>
                                                        @endforeach
                                                    @endif


                                                    <!--
                                                    <li class="pending">
                                                        <span>160</span> Awaiting Payment <u>20000</u>
                                                    </li>
                                                    <li class="overdue">
                                                        <span>134</span> Overdue <u>20000</u>
                                                    </li>
                                                    -->
                                                </ul>

                                                <a href="/expense-create" class="add--btn">Add New Expense</a>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-xl-7 col-lg-7 col-md-12 graph_inv-exp">
                                        <div class="sumb--graphbox sumb--dashstatbox sumb--putShadowbox">
                                            <h5>Monthly Summary</h5>

                                            <div class="SummaryChart-wrap inv-exp--page">
                                                <canvas id="SummaryChart"></canvas>
                                            </div>
                                            <div class="ytdlegend for-invoice--expense">
                                                <span>Previous Year <u><?php echo '$'.number_format($previous_year_sum, 2); ?></u></span> <span>Current Year <u><?php echo '$'.number_format($current_year_sum, 2); ?></u></span>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <!--
                    <div class="sumb--statistics row">
                        <div class="col-xl-7 col-lg-8 col-md-8 col-sm-12 col-12">
                            <div class="sumb--dashstatbox sumb--putShadowbox statbox__item--rejected">
                                <div class="sumb-statistic__item invoce-expenses__stats">
                                    <h2>
                                        @if(!empty($total_expense_amount))
                                            ${{number_format($total_expense_amount, 2)}}
                                        @endif
                                    </h2>
                                    <span>Total Invoice Amount</span>
                                    @if(!empty($total_expense_counts))
                                        @foreach($total_expense_counts as $expense_count)
                                            <span>{{$expense_count['status_count']}} {{$expense_count['status']}} Invoice</span>
                                        @endforeach
                                    @endif
                                    <div class="icon">
                                        <i class="fa-solid fa-check-to-slot"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    -->
                </section>



                <section>
                    <div class="row">

                        <div class="col-xl-12">

                            @isset($err)
                            <div class="sumb-alert alert alert-{{ $errors[$err][1] }}" role="alert">
                                {{ $errors[$err][0] }}
                            </div>
                            @endisset

                            <form action="/expense"  method="GET" enctype="multipart/form-data" id="search_form">
                                <div class="row">
                                    <div class="col-xl-4 col-lg-4 order-xl-1">
                                        <div class="form-input--wrap">
                                            <label class="form-input--question" for="">Expense No.</label>
                                            <div class="form--inputbox row">
                                                <div class="col-12">
                                                    <input type="text" class="form-control" id="search_number_name_amount" name="search_number_name_amount" placeholder="Expense No., Name, Amount"  value="{{!empty($search_number_name_amount) ? $search_number_name_amount : ''}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-4 col-lg-4 order-xl-2">
                                        <div class="form-input--wrap">
                                            <label class="form-input--question" for="">Start Date</label>
                                            <div class="date--picker row">
                                                <div class="col-12">
                                                    <input type="text" id="start_date" name="start_date" placeholder="Date('DD/MM/YYYY')"  readonly value="{{!empty($start_date) ? $start_date : ''}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-4 col-lg-4 order-xl-3">
                                        <div class="form-input--wrap">
                                            <label class="form-input--question" for="">End Date</label>
                                            <div class="date--picker row">
                                                <div class="col-12">
                                                    <input type="text" id="end_date" name="end_date" placeholder="Date('DD/MM/YYYY')"  readonly value="{{!empty($end_date) ? $end_date : ''}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 order-xl-4 order-md-4 order-sm-5 order-5">
                                        <!--<a class="transaction--Addbtn_expenses" href="/basic/expense-create"><i class="fa-solid fa-circle-plus"></i>Add New Expense</a>-->
                                    </div>

                                    <div class="invoice-list--btns_expenses col-xl-6 col-lg-6 col-md-6 order-xl-5 order-md-5 order-sm-4 order-4" style="text-align: right;">
                                        <button type="button" name="search_expense" class="btn sumb--btn " value="Search" onclick="searchItems(null, null)"><i class="fa-solid fa-magnifying-glass"></i>Search</button>
                                        <button type="button" class="btn sumb--btn sumb-clear-btn" onclick="clearSearchItems()"><i class="fa-solid fa-circle-xmark"></i>Clear Search</button>
                                    </div>
                                </div>
                            </form>

                            <div class="sumb--recentlogdements sumb--putShadowbox">

                                <div class="table-responsive">
                                    <table class="invoice_list">
                                        <thead>
                                            <tr>
                                                <th style="border-top-left-radius: 7px;" id="issue_date" onclick="searchItems('issue_date', '{{!empty($orderBy) && $orderBy == 'issue_date' ? $direction  : 'ASC'}}')"> Expense date </th>
                                                <th id="transaction_number" onclick="searchItems('transaction_number', '{{!empty($orderBy) && $orderBy == 'transaction_number' ? $direction  : 'ASC'}}')">Number</th>
                                                <th id="client_name" onclick="searchItems('client_name', '{{!empty($orderBy) && $orderBy == 'client_name' ? $direction  : 'ASC'}}')">Client</th>
                                                <th id="payment_date" onclick="searchItems('payment_date', '{{!empty($orderBy) && $orderBy == 'payment_date' ? $direction  : 'ASC'}}')">Payment Date</th>
                                                <th id="status" onclick="searchItems('status', '{{!empty($orderBy) && $orderBy == 'status' ? $direction  : 'ASC'}}')">Status</th>
                                                <th id="amount_paid" onclick="searchItems('amount_paid', '{{!empty($orderBy) && $orderBy == 'amount_paid' ? $direction  : 'ASC'}}')">Paid</th>
                                                <th id="total_amount" onclick="searchItems('total_amount', '{{!empty($orderBy) && $orderBy == 'total_amount' ? $direction  : 'ASC'}}')">Due</th>
                                                <th class="sumb--recentlogdements__actions" style="border-top-right-radius: 7px;">options</th>
                                            </tr>

                                        </thead>
                                        <tbody>
                                            @if (empty($expensedata['total']))
                                                <tr>
                                                    <td colspan="8" style="padding: 30px 15px; text-align:center;">No Data At This time.</td>
                                                </tr>
                                            @else
                                                @foreach ($expensedata['data'] as $idat)

                                                    @if($idat['is_active'])
                                                        <tr>
                                                            <td onclick="redirectUri('{{ json_encode($idat) }}')" >{{ $idat['issue_date'] }}</td>
                                                            <td onclick="redirectUri('{{ json_encode($idat) }}')" >{{ str_pad($idat['transaction_number'], 6, '0', STR_PAD_LEFT) }}</td>
                                                            <td onclick="redirectUri('{{ json_encode($idat) }}')" >{{ $idat['client_name'] }}</td>
                                                            <td onclick="redirectUri('{{ json_encode($idat) }}')" >{{ $idat['payment_date'] }}</td>
                                                            <!-- <td>@if (!empty($idat['client_email'])) <a href="mailto:{{ $idat['client_email'] }}">{{ $idat['client_email'] }}</a> @else &nbsp; @endif</td> -->

                                                            <td onclick="redirectUri('{{ json_encode($idat) }}')"
                                                                class="@if ($idat['status'] == 'Voided') sumb--recentlogdements__status_rej @elseif ($idat['status'] == 'PartlyPaid') sumb--recentlogdements__status_partly_paid @elseif ($idat['status'] == 'Paid') sumb--recentlogdements__status_acc @else sumb--recentlogdements__status_proc @endif">
                                                                {{ !empty($idat['status']) &&  $idat['status'] == 'PartlyPaid' ? 'Partial Paid' :  ucwords($idat['status']) }}
                                                            </td>
                                                            <td onclick="redirectUri('{{ json_encode($idat) }}')" >${{ number_format((float)$idat['amount_paid'], 2, '.', ',') }}</td>
                                                            <td onclick="redirectUri('{{ json_encode($idat) }}')" >${{ number_format((float)$idat['total_amount'], 2, '.', ',') }}</td>

                                                            <td class="sumb--recentlogdements__actions">
                                                                @if($idat['status'] == 'Paid')
                                                                    <div class="sumb--fileSharebtn dropdown">
                                                                        <!-- <a href="{{ url('/expense/'.$idat['id'].'/view') }}"><i class="fa-solid fa-eye"></i></a> -->
                                                                        <a class="fileSharebtn" href="#" role="button" id="mainlinkadd" data-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-square-caret-down"></i></a>

                                                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="mainlinkadd">
                                                                        <a class="dropdown-item" href="/expense-status-change/?expense_id={{ $idat['id'] }}&status=Unpaid">Flag as Unpaid</a>
                                                                        <a class="dropdown-item" href="/expense-status-change/?expense_id={{ $idat['id'] }}&status=Voided">Flag as Void</a>
                                                                        </div>
                                                                    </div>
                                                                @elseif($idat['status'] == 'Voided')
                                                                    <div class="sumb--fileSharebtn dropdown expenses--void">

                                                                        <!-- <a href="{{ url('/expense/'.$idat['id'].'/view') }}"><i class="fa-solid fa-eye"></i></a> -->
                                                                        <a class="fileSharebtn" href="#" role="button" id="mainlinkadd" data-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-square-caret-down"></i></a>

                                                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="mainlinkadd">
                                                                            <a class="dropdown-item">Some options</a>
                                                                        </div>
                                                                    </div>
                                                                @elseif($idat['status'] == 'Unpaid' || $idat['status'] == 'PartlyPaid')
                                                                    <div class="sumb--fileSharebtn dropdown">
                                                                        <!-- <a href="{{ url('/expense/'.$idat['id'].'/edit') }}"><i class="fa-solid fa-edit"></i></a> -->
                                                                        <a class="fileSharebtn" href="#" role="button" id="mainlinkadd" data-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-square-caret-down"></i></a>

                                                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="mainlinkadd">
                                                                            @if($userinfo && $userinfo[3] != 'user_pro')
                                                                                <a class="dropdown-item" onclick="confirmPaymentDatePop('Paid', {{$idat['id']}}, {{$idat['total_amount']}});">Add Payment</a>
                                                                            @endif
                                                                            <a class="dropdown-item" href="/expense-status-change/?expense_id={{ $idat['id'] }}&status=Voided">Flag as Void</a>
                                                                            @if($idat['status'] == 'Unpaid')
                                                                                <a class="dropdown-item" href="#" id="deleteExpense" style="cursor: pointer;" value="{{ $idat['id'] }}" >Delete</a>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>

                                <table>
                                    <tr class="sumb--recentlogdements__pagination">
                                        <td colspan="8">
                                            <!-- table pagination -->
                                            <div class="btn-group" role="group" aria-label="Basic example">

                                                <a href="{{ empty($paging['first']) ? 'javascript:void(0)' : $paging['first'] }}" type="button" class="btn btn-outline-secondary {{ empty($paging['first']) ? 'disabled' : '' }}"><i class="fas fa-angle-double-left"></i></a>
                                                <a href="{{ empty($paging['prev']) ? 'javascript:void(0)' : $paging['prev'] }}" type="button" class="btn btn-outline-secondary {{ empty($paging['prev']) ? 'disabled' : '' }}" ><i class="fas fa-angle-left"></i></a>
                                                <a href="javascript:void(0)" type="button" class="btn btn-outline-secondary" >{{$paging['now']}}</a>
                                                <a href="{{ empty($paging['next']) ? 'javascript:void(0)' : $paging['next'] }}" type="button" class="btn btn-outline-secondary {{ empty($paging['next']) ? 'disabled' : '' }}" ><i class="fas fa-angle-right"></i></a>
                                                <a href="{{ empty($paging['last']) ? 'javascript:void(0)' : $paging['last'] }}" type="button" class="btn btn-outline-secondary {{ empty($paging['last']) ? 'disabled' : '' }}"><i class="fas fa-angle-double-right"></i></a>
                                                <!--
                                                <div class="btn-group" role="group">
                                                    <button id="btnGroupDrop1" type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                        Sort: Newest to Oldest
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                                        <a class="dropdown-item" href="#">Oldest To Newest</a>
                                                        <a class="dropdown-item" href="#">First Name Ascending</a>
                                                        <a class="dropdown-item" href="#">First Name Decending</a>
                                                        <a class="dropdown-item" href="#">Last Name Ascending</a>
                                                        <a class="dropdown-item" href="#">Last Name Decending</a>
                                                    </div>
                                                </div>
                                                -->
                                                <div class="btn-group" role="group">
                                                    <button id="btnGroupDrop1" type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                        Display: {{$expensedata['per_page']}} Items
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                                        <a class="dropdown-item" href="{{ $paging['starpage'].'?page=1&ipp=1' }}">1 Item</a>
                                                        <a class="dropdown-item" href="{{ $paging['starpage'].'?page=1&ipp=5' }}">5 Items</a>
                                                        <a class="dropdown-item" href="{{ $paging['starpage'].'?page=1&ipp=10' }}">10 Items</a>
                                                        <a class="dropdown-item" href="{{ $paging['starpage'].'?page=1&ipp=25' }}">25 Items</a>
                                                        <a class="dropdown-item" href="{{ $paging['starpage'].'?page=1&ipp=50' }}">50 Items</a>
                                                        <a class="dropdown-item" href="{{ $paging['starpage'].'?page=1&ipp=100' }}">100 Items</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
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

<div id="deleteExpenseModal" class="modal fade modal-reskin modal-deleteItem" tabindex="-1">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title deleteicon--header">Delete Expense</h5>
        <button type="button" class="close" data-dismiss="modal"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this expense <span id="">?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary close--btn" data-dismiss="modal">Cancel</button>
        <button id="deleteExpenseConfirm" type="button" class="btn btn-primary delete--btn" data-dismiss="modal">Delete</button>
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

    $('#search_number_name_amount').bind("enterKey",function(e){
        searchItems(null, null);
    });
    $('#search_number_name_amount').keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
    });

    function redirectUri(data)
    {
       var data = JSON.parse(data);
        if(data['transaction_type'] == 'expense')
        {
            console.log(data);
            const type = data['status'] == 'Voided' || data['status'] == 'Paid' ? 'view' : 'edit';
            location.href = "/expense/"+data['id']+"/"+type;
        }
        else if(data['transaction_type'] == 'spend_money')
        {
            location.href = "/bank/cash-receipt?id="+data['id']+"&transaction_type="+data['transaction_type']+"&payment_option="+data['payment_option']
        }
    }

    function expenseRemoveComma(id)
    {
        var element = document.getElementById(id);
        element.value = Number(element.value.replace(/\,/g,'')).toFixed(2);
    }

    function expenseAddComma(id)
    {
        var element = document.getElementById(id);
        element.value = Number(element.value.replace(/\,/g,'')).toLocaleString(undefined, {maximumFractionDigits: 2, minimumFractionDigits: 2});
    }

    //to delete selected expense
    var expenseID;
     $(document).on('click', '#deleteExpense', function(event) {
        // expenseID = $(event.target).val();
        expenseID = $(this).attr("value");
        $("#deleteExpenseModal").modal({
            backdrop: 'static',
            keyboard: true,
            show: true
        });
    });


    $(document).on('click', '#deleteExpenseConfirm', function(event) {
        if(expenseID){
            var url = "{{ route('delete-expense', ':id') }}";
            url = url.replace(':id', expenseID);
            location.href = url;
        }else{
            alert("Select an expense to be deleted")
        }
    });

    $(function() {
        $( "#start_date" ).datepicker({ dateFormat: 'dd/mm/yy' });
        $( "#end_date" ).datepicker({ dateFormat: 'dd/mm/yy' });
        $( "#expense_payment_date" ).datepicker({ dateFormat: 'dd/mm/yy', beforeShow: function (input, inst) { setDatepickerPos(input, inst) } });

    });

    function setDatepickerPos(input, inst) {
        var rect = input.getBoundingClientRect();
        // use 'setTimeout' to prevent effect overridden by other scripts
        setTimeout(function () {
            var scrollTop = $("body").scrollTop();
    	    inst.dpDiv.css({ top: rect.top + input.offsetHeight + scrollTop });
        }, 0);
    }

    <?php if(!empty($orderBy)){?>
        <?php if($direction == 'ASC'){?>
            $("#"+ '{{$orderBy}}').append('&nbsp;<i class="fas fa-sort-down"></i>');
        <?php } if($direction == 'DESC'){?>
            $("#"+ '{{$orderBy}}').append('&nbsp;<i class="fas fa-sort-up"></i>');
        <?php }?>
    <?php }?>

    function clearSearchItems(){

        if($("#search_number_name_amount").val() || $("#start_date").val() || $("#end_date").val()){
            var url = "{{URL::to('/expense')}}";
            location.href = url;
        }

        $("#search_number_name_amount").val('');
        $("#start_date").val('');
        $("#end_date").val('');
    }

    function searchItems(orderBy, direction){
        if(orderBy && direction){
            $("#search_form").append('<input id="orderBy" type="hidden" name="orderBy" value='+orderBy+' >');
            $("#search_form").append('<input id="direction" type="hidden" name="direction" value='+direction+' >');
        }else{
            $("#search_form").append('<input id="orderBy" type="hidden" name="orderBy" value="issue_date" >');
            $("#search_form").append('<input id="direction" type="hidden" name="direction" value="ASC">');
        }
        $("#search_form").submit();
    }


    function confirmPaymentDatePop(status, id, due_amount){
        $("#expense_payment_date_error").removeClass('alert alert-danger');
        $("#expense_payment_date_error").html('');

        $("#expense_amount_paid_error").removeClass('alert alert-danger');
        $("#expense_amount_paid_error").html('');

        $("#expense_payment_date").val('');
        $("#expense_id").val('');
        $("#expense_id").val(id);
        $("#expense_status").val('Paid');
        $("#expense_amount_paid").val('');

        $("#expense_amount_paid").val(Number(due_amount).toFixed(2).replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));

        $("#expense_due_amount").val('');
        $("#expense_due_amount").val(Number(due_amount).toFixed(2).replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));


        $('#expense_payment_date_modal').modal({
            backdrop: 'static',
            keyboard: true,
            show: true
        });
    }

    $(document).on('click', '#submit_expense_payment_date', function(event) {
        var expense_payment_date = $("#expense_payment_date").val();
        var expense_amount_paid = Number($("#expense_amount_paid").val().replace(/\,/g,''));
        var expense_due_amount = Number($("#expense_due_amount").val().replace(/\,/g,''));

        if(expense_payment_date && (expense_due_amount >= expense_amount_paid) && !(expense_amount_paid <= 0) ){
            $("#payment_date_form").click();
        }else{
            if(!expense_payment_date){
                $("#expense_payment_date_error").addClass('alert alert-danger');
                $("#expense_payment_date_error").html('Payment date is required');
            }
            if(!expense_amount_paid){
                $("#expense_amount_paid_error").addClass('alert alert-danger');
                $("#expense_amount_paid_error").html('Amount paid is required');
            }
            else if(expense_amount_paid > expense_due_amount){
                $("#expense_amount_paid_error").addClass('alert alert-danger');
                $("#expense_amount_paid_error").html('Amount must be less than or equal to due amount');
            }
            else if(expense_amount_paid <= 0){
                $("#expense_amount_paid_error").addClass('alert alert-danger');
                $("#expense_amount_paid_error").html('Amount cannot be less than or equal to zero');
            }
        }
    });

    //get Dates of the week

    const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun","Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    const NextWeek = new Date();
    const PrevWeek = new Date();

    // Next Week
    const firstDayNextWeek = new Date(NextWeek.setDate(NextWeek.getDate() - NextWeek.getDay() + 8));
    const lastDayNextWeek = new Date(NextWeek.setDate(NextWeek.getDate() - NextWeek.getDay() + 7));
    const NextWeekDates = firstDayNextWeek.getDate()+' '+ monthNames[firstDayNextWeek.getMonth()]+' - '+lastDayNextWeek.getDate()+' '+ monthNames[lastDayNextWeek.getMonth()];

    //Previous Week
    const firstDayPrevWeek = new Date(PrevWeek.setDate(PrevWeek.getDate() - PrevWeek.getDay() - 6));
    const lastDayPrevWeek = new Date(PrevWeek.setDate(PrevWeek.getDate() - PrevWeek.getDay() + 7));
    const PrevWeekDates = firstDayPrevWeek.getDate()+' '+ monthNames[firstDayPrevWeek.getMonth()]+' - '+lastDayPrevWeek.getDate()+' '+ monthNames[lastDayPrevWeek.getMonth()];



    Chart.defaults.font.family = "Montserrat";

    //Expenses Chart
    const ExpensesChart = document.getElementById("ExpensesChart");

    const dataExpenses = {
        label: "Amount",
        <?php if(!empty($bar_chart_data)){?>
        data: [
            <?php
                array_map(function ($item) {
                    $expense_amount = array_column($item['weekly_transactions'], 'total');
                        echo $expense_amount ? array_sum($expense_amount) ."," : 0 .",";
                }, $bar_chart_data);
            ?>
        ],
        <?php }?>
        lineTension: 0,
        fill: false,
        backgroundColor: ['#e5e5e5','#e5e5e5','#fdb917','#fee29f','#fee29f'],
        borderRadius: 5
    };

    const MonthlyExpensesData = {
    labels: ["Older", PrevWeekDates,"This Week", NextWeekDates, "Future"],
    datasets: [dataExpenses]
    };

    const ExpensesBar = new Chart(ExpensesChart, {
    type: 'bar',
    data: MonthlyExpensesData,
    options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: false },
            scales: {
                x: {
                    grid: {display: false},
                    ticks: { color: '#28282a',
                        font: {
                            size: 11
                        }
                    },
                    border: { display: false }
                },
                y: {
                    grid: { color: '#e5e5e5'},
                    border: { display: false }
                },
            }
        }
    });

    //YTD Summary Chart
    const SummaryChart = document.getElementById("SummaryChart");

    const dataCurrent = {
        label: "Current Year",
        data: [<?php echo implode(",", $current_year); ?>],
        lineTension: 0,
        fill: false,
        borderColor: '#fdb917',
        backgroundColor: '#fdb917',
        radius: 4
    };

    const dataPrevious = {
        label: "Previous Year",
        data: [<?php echo implode(",", $previous_year); ?>],
        lineTension: 0,
        borderColor: '#e5e5e5',
        backgroundColor: '#e5e5e5',
        radius: 4
    };

    const MonthlyData = {
    labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
    datasets: [dataCurrent,dataPrevious]
    };

    const YTDBar = new Chart(SummaryChart, {
    type: 'line',
    data: MonthlyData,
    options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: false },
            scales: {
                x: {
                    grid: {display: false},
                    ticks: { color: '#28282a',
                        font: {
                            size: 11
                        }
                    },
                    border: { display: false }
                },
                y: {
                    grid: { color: '#e5e5e5'},
                    border: { display: false }
                },
            }
        }
    });

    $(document).ready(function () {

        $(".collapse").on("shown.bs.collapse", function () {
            localStorage.setItem("expensesColl_" + this.id, true);
        });

        $(".collapse").on("hidden.bs.collapse", function () {
            localStorage.removeItem("expensesColl_" + this.id);
        });

        $(".collapse").each(function () {
            if (localStorage.getItem("expensesColl_" + this.id) === "true") {
                $(this).collapse("show");
            } else {
                $(this).collapse("hide");
            }
        });

    });
</script>

<!-- end document-->
