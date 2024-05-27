@include('includes.head')
@include('includes.user-header')

<link href="/css/accordion-styles.css?v={{ config('app.version') }}" rel="stylesheet" media="all">

<div id="delete_invoice_modal" class="modal fade modal-reskin modal-deleteItem" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title deleteicon--header" id="exampleModalLabel">Delete Invoice</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
        </button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this invoice <span id="delete_invoice_number"></span>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary close--btn" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary delete--btn" id="delete_invoice" value="">Delete</button>
      </div>
    </div>
  </div>
</div>
<!-------Delete invoice alert pop-up end--------------->


<!-------Invoice due amount and date alert pop-up--------------->
<form action="/basic/status-change"  method="GET" enctype="multipart/form-data">
    @csrf
    <div id="invoice_payment_date_modal" class="modal fade modal-reskin modal-deleteItem" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                        <input type="float" id="invoice_amount_paid" name="amount_paid" placeholder=""  value="" required onfocusin="basicInvoiceRemoveComma('invoice_amount_paid')" onfocusout="basicInvoiceAddComma('invoice_amount_paid')">
                                    </div>

                                    <input type="hidden" id="invoice_due_amount" value="">
                                </div>
                                <div class="" role="alert" id="invoice_amount_paid_error"></div>

                            </div>
                        </div>

                        <div class="col-xl-6 col-lg-6 order-xl-6">
                            <div class="form-input--wrap">
                                <label class="form-input--question" for="">Date Paid</label>
                                <div class="date--picker row">
                                    <div class="col-12">
                                        <input type="text" id="invoice_payment_date" name="payment_date" placeholder="Date('DD/MM/YYYY')"  readonly value="" required>
                                    </div>
                                </div>
                                <div class="" role="alert" id="invoice_payment_date_error"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="invoice_status" name="status" placeholder="Date('DD/MM/YYYY')"  readonly value="">
                    <input type="hidden" id="invoice_id" name="invoice_id" readonly value="">

                    <button type="button" class="btn btn-secondary close--btn" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary payment--btn" id="submit_payment_date" value="">Submit</button>
                    <button hidden="hidden" type="submit" class="btn btn-primary delete--btn" id="payment_date_form" value="">Submit</button>
                </div>
            </div>
        </div>
    </div>
</form>
<!-------Invoice due amount and date alert pop-up end--------------->

<?php $current_year_total_sum = 0;?>
<!-------Recall invoice alert pop-up--------------->
    <div class="modal fade modal-reskin modal-recallEmail" id="recall_invoice_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title recallicon--header" id="exampleModalLabel">Recall Invoice</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to recall this invoice?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close--btn" data-dismiss="modal">Close</button>
                    <button type="button" name="recall_invoice" class="btn btn-primary recall--btn" id="recall_invoice" value="">Recall Invoice</button>
                </div>
            </div>
        </div>
    </div>
<!-------Recall invoice alert pop-up end--------------->




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

<!-- PAGE CONTAINER-->
<div class="page-container">

    @include('includes.user-top')

    <!-- MAIN CONTENT-->
    <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid">

                <section>
                    <h3 class="sumb--title">Invoice</h3>
                </section>

                <section>

                    <div class="accordion" id="SummaryGraph">
                        <div class="accordion-item">
                            <h3 class="accordion-header" id="headingOne">
                                <button class="accordion-button" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    Summary Graph
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-parent="#SummaryGraph">
                                <div class="accordion-body">

                                    <div class="sumb--graphs row">
                                        <div class="col-xl-5 col-lg-5 col-md-12">
                                            <div class="sumb--graphbox sumb--dashstatbox sumb--putShadowbox invoices-block">
                                                <h5>Invoices
                                                    <span>Invoices owed to you</span>
                                                </h5>

                                                <div class="Invoices-wrap">
                                                    <canvas id="InvoicesChart"></canvas>
                                                </div>

                                                <div class="block-deets">
                                                    <ul>
                                                    <?php
                                                        if(!empty($total_invoice_counts)){
                                                            $invoice_status = array();
                                                            foreach ($total_invoice_counts as $isummary) {
                                                                $invoice_status[$isummary['status']] = array('status'=>$isummary['status'],'total'=>$isummary['total'], 'status_count'=>$isummary['status_count']);
                                                            }

                                                            if (!empty($invoice_status['Paid']['status_count'])) { ?>
                                                                <li class="Paid">
                                                                    <span><?php echo $invoice_status['Paid']['status_count']?></span> Paid Invoice <u>$<?php echo number_format($invoice_status['Paid']['total'], 2) ?></u>
                                                                </li>
                                                            <?php }

                                                            if (!empty($invoice_status['PartlyPaid']['status_count'])) { ?>
                                                                <li class="PartlyPaid">
                                                                    <span><?php echo $invoice_status['PartlyPaid']['status_count']?></span> Partly Paid Invoice <u>$<?php echo number_format($invoice_status['PartlyPaid']['total'], 2) ?></u>
                                                                </li>
                                                            <?php }

                                                            if (!empty($invoice_status['Unpaid']['status_count'])) { ?>
                                                                <li class="Unpaid">
                                                                    <span><?php echo $invoice_status['Unpaid']['status_count']?></span> Unpaid Invoice <u>$<?php echo number_format($invoice_status['Unpaid']['total'], 2) ?></u>
                                                                </li>
                                                            <?php }

                                                            if (!empty($invoice_status['Recalled']['status_count'])) { ?>
                                                                <li class="Recalled">
                                                                    <span><?php echo $invoice_status['Recalled']['status_count']?></span> Recalled Invoice <u>$<?php echo number_format($invoice_status['Recalled']['total'], 2) ?></u>
                                                                </li>
                                                            <?php }

                                                            if (!empty($invoice_status['Voided']['status_count'])) { ?>
                                                                <li class="Voided">
                                                                    <span><?php echo $invoice_status['Voided']['status_count']?></span> Voided Invoice <u>$<?php echo number_format($invoice_status['Voided']['total'], 2) ?></u>
                                                                </li>
                                                            <?php }
                                                        }
                                                    ?>
                                                    </ul>

                                                    <a href="/basic/invoice/create" class="add--btn">Add New Invoice</a>
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

                                                    <span>Invoice Previous Year
                                                        <u>
                                                            <?php echo '$'.number_format($previous_year_sum, 2); ?>
                                                        </u>
                                                    </span>
                                                    <span>Invoice Current Year
                                                        <u>
                                                            <?php echo '$'.number_format($current_year_sum, 2); ?>
                                                        </u>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!--

                    <div class="sumb--statistics row nondashboard">

                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="sumb--dashstatbox sumb--putShadowbox statistic__item--blue">
                                <div class="sumb-statistic__item invoce-expenses__stats">
                                    <h2>
                                        @if(!empty($total_invoice_amount))
                                            ${{number_format($total_invoice_amount, 2)}}
                                        @endif
                                    </h2>
                                    <span>Total Invoice Amount</span>
                                    @if(!empty($total_invoice_counts))
                                        @foreach($total_invoice_counts as $invoice_count)
                                            <span>{{$invoice_count['status_count']}} {{$invoice_count['status']}} Invoice</span>
                                        @endforeach
                                    @endif
                                    <div class="icon">
                                        <i class="fa-solid fa-file-invoice"></i>
                                    </div>
                                </div>
                            </div>
                        </div>


                         <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="sumb--dashstatbox sumb--putShadowbox statbox__item--rejected">
                                <div class="sumb-statistic__item invoce-expenses__stats">
                                    <h2>
                                        $1
                                    </h2>
                                    <span>Total Expenses Amount </span>
                                    <span>1 Paid Expenses</span>
                                    <span>2 Unpaid Expenses</span>
                                    <span>3 Void Expenses</span>
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

                            @if (\Session::has('success'))
                                <div class="alert alert-success">
                                    {!! \Session::get('success') !!}
                                </div>
                            @endif

                            @if (\Session::has('error'))
                                <div class="alert alert-danger">
                                    {!! \Session::get('error') !!}
                                </div>
                            @endif

                            @if (\Session::has('email-sent') && Session('email-sent'))

                            @endif

                            <form action="/basic/invoice"  method="GET" enctype="multipart/form-data" id="search_form">
                                <div class="row" style="margin-top: 20px">
                                    <div class="col-xl-4 col-lg-4 order-xl-1">
                                        <div class="form-input--wrap">
                                            <label class="form-input--question" for="">Invoice No.</label>
                                            <div class="form--inputbox row">
                                                <div class="col-12">
                                                    <input type="text" id="search_number_email_amount" name="search_number_email_amount" placeholder="Invoice No., Amount"  value="{{!empty($search_number_email_amount) ? $search_number_email_amount : ''}}">
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

                                    <div class="col-xl-4 col-lg-12 order-xl-4">

                                        <div class="btn-group sumb--dashboardDropdown transaction--filter" role="group">
                                            <button id="btnGroupDrop_type" type="button" data-toggle="dropdown" aria-expanded="false">
                                                @if ($filterBy)
                                                    {{$filterBy}}
                                                @else
                                                    Filter My Transactions
                                                @endif
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop_type">
                                                <a class="dropdown-item" href="javascript:void(0)" type="button" onclick="searchItems(null, null, 'Paid')">Paid</a>
                                                <a class="dropdown-item" href="javascript:void(0)" type="button" onclick="searchItems(null, null, 'Unpaid')">Unpaid</a>
                                                <a class="dropdown-item" href="javascript:void(0)" type="button" onclick="searchItems(null, null, 'Voided')">Void</a>
                                                <a class="dropdown-item" href="javascript:void(0)" type="button" onclick="searchItems(null, null, 'Recalled')">Recalled</a>
                                                <a class="dropdown-item" href="javascript:void(0)" type="button" onclick="searchItems(null, null, 'PartlyPaid')">Partly Paid</a>
                                                <a class="dropdown-item" href="/basic/invoice">View All</a>
                                            </div>
                                        </div>
                                    </div>
                                    <input id="filter_by" type="hidden" name="filterBy" value='{{!empty($filterBy) ? $filterBy : "" }}'>

                                    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 order-xl-5 order-lg-1 order-md-1 order-sm-2 order-2">

                                    </div>

                                    <div class="invoice-list--btns col-xl-4 col-lg-6 col-md-6 col-sm-12 order-xl-6 order-lg-2 order-md-2 order-sm-1 order-1" style="text-align: right;">
                                        <button type="button" id="search_invoice" name="search_invoice" class="btn sumb--btn " value="Search" onclick="searchItems(null, null, '{{$filterBy}}')"><i class="fa-solid fa-magnifying-glass"></i>Search</button>
                                        <button type="button" class="btn sumb--btn sumb-clear-btn" onclick="clearSearchItems()"><i class="fa-solid fa-circle-xmark"></i>Clear Search</button>
                                    </div>
                                </div>
                            </form>

                            <div class="sumb--recentlogdements sumb--putShadowbox">
                                <div class="table-responsive">
                                    <table class="invoice_list">
                                        <thead>
                                            <tr>
                                                <th style="border-top-left-radius: 7px;" id="issue_date" onclick="searchItems('issue_date', '{{!empty($orderBy) && $orderBy == 'issue_date' ? $direction  : 'ASC'}}')"> Invoice date </th>
                                                <th id="transaction_number" onclick="searchItems('transaction_number', '{{!empty($orderBy) && $orderBy == 'transaction_number' ? $direction  : 'ASC'}}')">Number</th>
                                                <th id="client_name" onclick="searchItems('client_name', '{{!empty($orderBy) && $orderBy == 'client_name' ? $direction  : 'ASC'}}')">Client</th>
                                                <!-- <th id="client_email" onclick="searchItems('client_email', '{{!empty($orderBy) && $orderBy == 'client_email' ? $direction  : 'ASC'}}')">Client Email</th> -->
                                                <th id="payment_date" onclick="searchItems('payment_date', '{{!empty($orderBy) && $orderBy == 'payment_date' ? $direction  : 'ASC'}}')">Payment Date</th>
                                                <th id="status" onclick="searchItems('status', '{{!empty($orderBy) && $orderBy == 'status' ? $direction  : 'ASC'}}')">Status</th>
                                                <th id="amount_paid" onclick="searchItems('amount_paid', '{{!empty($orderBy) && $orderBy == 'amount_paid' ? $direction  : 'ASC'}}')">Paid</th>
                                                <th id="total_amount" onclick="searchItems('total_amount', '{{!empty($orderBy) && $orderBy == 'total_amount' ? $direction  : 'ASC'}}')">Due</th>
                                                <th id="invoice_sent" onclick="searchItems('invoice_sent', '{{!empty($orderBy) && $orderBy == 'invoice_sent' ? $direction  : 'ASC'}}')">Email</th>
                                                <!-- <th>Edit</th> -->
                                                <th class="sumb--recentlogdements__actions" style="border-top-right-radius: 7px;">options</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (empty($invoicedata['total']))
                                            <tr>
                                                <td colspan="9" style="padding: 20px 15px 25px; text-align:center;">
                                                   <div class="block-deets">
                                                        <a href="/basic/invoice/create" class="add--btn">Add New Invoice</a>
                                                    </div>
                                                </td>
                                            </tr>
                                            @else
                                                @foreach ($invoicedata['data'] as $invoice)

                                                    <tr>
                                                        <td onclick="window.location='/basic/invoice/{{$invoice['id']}}/edit'">{{ $invoice['issue_date'] }}</td>
                                                        <td onclick="window.location='/basic/invoice/{{$invoice['id']}}/edit'">{{ 'INV-'. str_pad($invoice['transaction_number'], 6, '0', STR_PAD_LEFT) }}</td>
                                                        <td onclick="window.location='/basic/invoice/{{$invoice['id']}}/edit'">{{ $invoice['client_name'] }}</td>
                                                        <!-- <td onclick="window.location='/basic/invoice/{{$invoice['id']}}/edit'">{{ $invoice['client_email'] }}</td> -->
                                                        <td onclick="window.location='/basic/invoice/{{$invoice['id']}}/edit'">{{ $invoice['payment_date'] }}</td>
                                                        <td onclick="window.location='/basic/invoice/{{$invoice['id']}}/edit'"><span class="payment--status-{{$invoice['status']}}">{{ $invoice['status'] == 'PartlyPaid' ? 'Partial Paid' : $invoice['status'] }}</span></td>
                                                        <td onclick="window.location='/basic/invoice/{{$invoice['id']}}/edit'">${{ number_format((float)$invoice['amount_paid'], 2, '.', ',') }}</td>
                                                        <td onclick="window.location='/basic/invoice/{{$invoice['id']}}/edit'">${{ number_format((float)$invoice['total_amount'], 2, '.', ',') }}</td>
                                                        <td onclick="window.location='/basic/invoice/{{$invoice['id']}}/edit'"><span class="{{ $invoice['invoice_sent'] ? 'email2client_sent' : 'email2client_pending' }}"></span></td>
                                                        <!-- <td><a class="btn" href="/invoice/{{$invoice['id']}}/edit"><i class='far fa-edit'></i></a> <a class="btn" href="/invoice/{{$invoice['id']}}/edit"><i class='far fa-edit'></i></a></td> -->
                                                        <td class="sumb--recentlogdements__actions">
                                                            <div class="sumb--fileSharebtn dropdown">
                                                                <a class="fileSharebtn" href="#" role="button" id="mainlinkadd" data-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-square-caret-down"></i></a>
                                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="mainlinkadd">
                                                                    @if($invoice['status'] == 'Paid')
                                                                        <a class="dropdown-item" href="/basic/status-change/?invoice_id={{ $invoice['id'] }}&status=Unpaid">Flag as UNPAID</a>
                                                                        <a class="dropdown-item" href="/basic/status-change/?invoice_id={{ $invoice['id'] }}&status=Voided">Flag as VOID</a>
                                                                    @elseif($invoice['status'] == 'Voided')
                                                                        <a class="dropdown-item" href="/basic/clone-invoice/?invoice_id={{ $invoice['id'] }}">Clone</a>
                                                                    @elseif($invoice['status'] == 'Unpaid' || $invoice['status'] == 'Recalled' || $invoice['status'] == 'PartlyPaid')
                                                                        <a class="dropdown-item" href="/basic/status-change/?invoice_id={{ $invoice['id'] }}&status=Voided">Flag as VOID</a>
                                                                        <!-- <a class="dropdown-item" href="/basic/status-change/?invoice_id={{ $invoice['id'] }}&status=Paid">Flag as PAID</a> -->
                                                                        <a class="dropdown-item" onclick="confirmPaymentDatePop('Paid', {{$invoice['id']}}, {{$invoice['total_amount']}});">Add Payment</a>
                                                                        @if(!$invoice['invoice_sent'] && $invoice['status'] != 'PartlyPaid')
                                                                            <a class="dropdown-item" onclick="deleteInvoice({{$invoice['transaction_number']}}, {{$invoice['id']}});">Delete</a>
                                                                        @endif
                                                                        @if($invoice['status'] == 'Unpaid' && $invoice['invoice_sent'])
                                                                            <a class="dropdown-item" onclick="recallInvoice({{$invoice['id']}})">Recall invoice with edit</a>
                                                                        @endif
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
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
                                                        Display: {{$invoicedata['per_page']}} Items
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





<!-- END PAGE CONTAINER-->

@include('includes.footer')
</body>

</html>


<script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>


<script>
    let $body = $(this);

    $(window).on('beforeunload', function(){

        $body.find('#pre-loader').show();

    });

    $(function () {

        $body.find('#pre-loader').hide();
    });

    $('#search_number_email_amount').bind("enterKey",function(e){
        searchItems(null, null, '{{$filterBy}}');
    });
    $('#search_number_email_amount').keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
    });


    function deleteInvoice(transaction_number, id){
        $("#delete_invoice_number").text('');
        $("#delete_invoice_number").text('INV-000000'+transaction_number);
        $("#delete_invoice").val('');
        $("#delete_invoice").val(id);

        $('#delete_invoice_modal').modal({
            backdrop: 'static',
            keyboard: true,
            show: true
        });
    }

    function recallInvoice(id){
        $("#recall_invoice").val('');
        $("#recall_invoice").val(id);

        $('#recall_invoice_modal').modal({
            backdrop: 'static',
            keyboard: true,
            show: true
        });
    }

    $(document).on('click', '#recall_invoice', function(event) {
        var invoice_id = $("#recall_invoice").val();

        var url = "{{URL::to('/basic/invoice/{id}/recall')}}";
        url = url.replace('{id}', invoice_id);
        location.href = url;
    });

    $(document).on('click', '#delete_invoice', function(event) {
        var invoice_id = $("#delete_invoice").val();

        var url = "{{URL::to('/basic/invoice/{id}/delete')}}";
        url = url.replace('{id}', invoice_id);
        location.href = url;
    });

    function confirmPaymentDatePop(status, id, due_amount){
        $("#invoice_payment_date_error").removeClass('alert alert-danger');
        $("#invoice_payment_date_error").html('');

        $("#invoice_amount_paid_error").removeClass('alert alert-danger');
        $("#invoice_amount_paid_error").html('');

        $("#invoice_payment_date").val('');
        $("#invoice_id").val('');
        $("#invoice_id").val(id);
        $("#invoice_status").val('Paid');
        $("#invoice_amount_paid").val('');

        $("#invoice_amount_paid").val(Number(due_amount).toFixed(2).replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));

        $("#invoice_due_amount").val('');
        $("#invoice_due_amount").val(Number(due_amount).toFixed(2).replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));


        $('#invoice_payment_date_modal').modal({
            backdrop: 'static',
            keyboard: true,
            show: true
        });
    }


    $(document).on('click', '#submit_payment_date', function(event) {
        var invoice_payment_date = $("#invoice_payment_date").val();
        var invoice_amount_paid = Number($("#invoice_amount_paid").val().replace(/\,/g,''));
        var invoice_due_amount = Number($("#invoice_due_amount").val().replace(/\,/g,''));

        if(invoice_payment_date && (invoice_due_amount >= invoice_amount_paid) && !(invoice_amount_paid <= 0) ){
            $("#payment_date_form").click();
        }else{
            if(!invoice_payment_date){
                $("#invoice_payment_date_error").addClass('alert alert-danger');
                $("#invoice_payment_date_error").html('Payment date is required');
            }
            if(!invoice_amount_paid){
                $("#invoice_amount_paid_error").addClass('alert alert-danger');
                $("#invoice_amount_paid_error").html('Amount paid is required');
            }
            else if(invoice_amount_paid > invoice_due_amount){
                $("#invoice_amount_paid_error").addClass('alert alert-danger');
                $("#invoice_amount_paid_error").html('Amount must be less than or equal to due amount');
            }
            else if(invoice_amount_paid <= 0){
                $("#invoice_amount_paid_error").addClass('alert alert-danger');
                $("#invoice_amount_paid_error").html('Amount cannot be less than or equal to zero');
            }
        }
    });

    function setDatepickerPos(input, inst) {
        var rect = input.getBoundingClientRect();
        // use 'setTimeout' to prevent effect overridden by other scripts
        setTimeout(function () {
            var scrollTop = $("body").scrollTop();
    	    inst.dpDiv.css({ top: rect.top + input.offsetHeight + scrollTop });
        }, 0);
    }

    $(function() {
        $( "#start_date" ).datepicker({ dateFormat: 'dd/mm/yy' });
        $( "#end_date" ).datepicker({ dateFormat: 'dd/mm/yy' });
        $( "#invoice_payment_date" ).datepicker({ dateFormat: 'dd/mm/yy', beforeShow: function (input, inst) { setDatepickerPos(input, inst) } });
    });

    <?php if(!empty($orderBy)){?>
        <?php if($direction == 'ASC'){?>
            $("#"+ '{{$orderBy}}').append('&nbsp;<i class="fas fa-sort-down"></i>');
        <?php } if($direction == 'DESC'){?>
            $("#"+ '{{$orderBy}}').append('&nbsp;<i class="fas fa-sort-up"></i>');
        <?php }?>
    <?php }?>

    function clearSearchItems(){
        if($("#search_number_email_amount").val() || $("#start_date").val() || $("#end_date").val()){
            var url = "{{URL::to('/basic/invoice')}}";
            location.href = url;
        }

        $("#search_number_email_amount").val('');
        $("#start_date").val('');
        $("#end_date").val('');
        return false;
    }

    function searchItems(orderBy, direction, filterBy){
        // $("#filter_by").val('');
        if(orderBy && direction){
            $("#search_form").append('<input id="orderBy" type="hidden" name="orderBy" value='+orderBy+' >');
            $("#search_form").append('<input id="direction" type="hidden" name="direction" value='+direction+' >');
        }else{
            $("#search_form").append('<input id="orderBy" type="hidden" name="orderBy" value="issue_date" >');
            $("#search_form").append('<input id="direction" type="hidden" name="direction" value="ASC">');
        }
        if(filterBy){
            $("#filter_by").val(filterBy);
        }
        $("#search_form").submit();
    }


    //get Dates of the week

const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun","Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
const NextWeek = new Date();
const PrevWeek = new Date();

const currentYear = new Date().getFullYear();
const previousYear = new Date().getFullYear();

// Next Week
const firstDayNextWeek = new Date(NextWeek.setDate(NextWeek.getDate() - NextWeek.getDay() + 8));
const lastDayNextWeek = new Date(NextWeek.setDate(NextWeek.getDate() - NextWeek.getDay() + 7));
const NextWeekDates = firstDayNextWeek.getDate()+' '+ monthNames[firstDayNextWeek.getMonth()]+' - '+lastDayNextWeek.getDate()+' '+ monthNames[lastDayNextWeek.getMonth()];

//Previous Week
const firstDayPrevWeek = new Date(PrevWeek.setDate(PrevWeek.getDate() - PrevWeek.getDay() - 6));
const lastDayPrevWeek = new Date(PrevWeek.setDate(PrevWeek.getDate() - PrevWeek.getDay() + 7));
const PrevWeekDates = firstDayPrevWeek.getDate()+' '+ monthNames[firstDayPrevWeek.getMonth()]+' - '+lastDayPrevWeek.getDate()+' '+ monthNames[lastDayPrevWeek.getMonth()];



Chart.defaults.font.family = "Montserrat";

//Invoices Chart
const InvoicesChart = document.getElementById("InvoicesChart");

const dataInvoices = {
    label: "Amount",
    <?php if(!empty($bar_chart_data)){?>
    data: [
        <?php
            array_map(function ($item) {
                $totals = array_column($item['weekly_transactions'], 'total');
                    echo $totals ? array_sum($totals) ."," : 0 .",";
            }, $bar_chart_data);
        ?>
    ],
    <?php }?>
    lineTension: 0,
    fill: false,
    backgroundColor: ['#e5e5e5','#e5e5e5','#fdb917','#fee29f','#fee29f'],
    borderRadius: 5
};

const MonthlyInvoicesData = {
  labels: ["Older", PrevWeekDates,"This Week", NextWeekDates, "Future"],
  datasets: [dataInvoices]
};

const InvoicesBar = new Chart(InvoicesChart, {
  type: 'bar',
  data: MonthlyInvoicesData,
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
$allMonths = ['January','February','March', 'April','May','June','July','August','September','October','November','December'];

const dataCurrent = {
    label: "Current Year",
    data: [
       <?php echo implode(",", $current_year); ?>
    ],
    lineTension: 0.35,
    fill: false,
    borderColor: '#fdb917',
    backgroundColor: '#fdb917',
    radius: 4
};

const dataPrevious = {
    label: "Previous Year",
    data: [
        <?php echo implode(",", $previous_year); ?>
    ],
    lineTension: 0.35,
    borderColor: '#ccc',
    backgroundColor: '#ccc',
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
        localStorage.setItem("invoicesColl_" + this.id, true);
    });

    $(".collapse").on("hidden.bs.collapse", function () {
        localStorage.removeItem("invoicesColl_" + this.id);
    });

    $(".collapse").each(function () {
        if (localStorage.getItem("invoicesColl_" + this.id) === "true") {
            $(this).collapse("show");
        } else {
            $(this).collapse("hide");
        }
    });

});


</script>
<!-- end document-->
