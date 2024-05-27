@include('includes.head')
@include('includes.user-header')

<div class="page-container">

    @include('includes.user-top')

    <!-------Unreconcile transaction alert pop-up start--------------->
    <div id="unreconcile_transaction_modal" class="modal fade modal-reskin" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title unreconcile--header" id="exampleModalLabel">Unreconcile</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
                    </button>
                </div>
                <div class="modal-body">
                    This removes the link between the imported transaction and the account transaction in Bizmate.

                    Note: both the bank transaction and the account transaction will remain.

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close--btn" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary " id="delete_invoice" value="" onclick="unReconcileTransaction('{{$account_transaction['account_id']}}', '{{$account_transaction['reconcileTransaction']['bank_transaction_id']}}')">OK</button>
                </div>
            </div>
        </div>
    </div>
    <!-------Unreconcile transaction alert pop-up end--------------->

    {{--    @include('includes.user-top')--}}

    <!-- MAIN CONTENT-->
    <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid" id="my-div-to-mask">
                <section>
                    <h3 class="sumb--title">Transaction Summary</h3>
                    <div class="transactionSummary">
                        <div class="row d-flex align-items-center">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12 {{ !empty($account_transaction['reconcileTransaction']) && $account_transaction['reconcileTransaction']['is_reconciled'] ? '' : 'order-xl-2 order-lg-2 order-md-2 order-sm-2 order-2 unreconciled--type' }}">
                                Transaction Type
                                <span>
                                    @if(count($account_transaction['transactionCollection'])>1)
                                        {{ "Payment: Multiple" }}
                                    @elseif($account_transaction['transactionCollection'][0]['transaction_type'] == 'invoice' || $account_transaction['transactionCollection'][0]['transaction_type'] == 'expense')
                                        {{ "Payment" }}
                                    @elseif($account_transaction['transactionCollection'][0]['transaction_type'] == 'arprepayment'
                                            || $account_transaction['transactionCollection'][0]['transaction_type'] == 'apprepayment')
                                        {{ "Prepayment" }}
                                    @elseif($account_transaction['transactionCollection'][0]['transaction_type'] == 'aroverpayment'
                                            || $account_transaction['transactionCollection'][0]['transaction_type'] == 'apoverpayment')
                                        {{ "Overpayment" }}
                                    @else
                                        {{ ucwords(Str::replace("_", " ", $account_transaction['transactionCollection'][0]['transaction_type'])) }}
                                    @endif

                                </span>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12 {{ !empty($account_transaction['reconcileTransaction']) && $account_transaction['reconcileTransaction']['is_reconciled'] ? '' : 'order-xl-1 order-lg-1 order-md-1 order-sm-1 order-1 unreconciled' }}">
                                {{ !empty($account_transaction['reconcileTransaction']) && $account_transaction['reconcileTransaction']['is_reconciled'] ? "Reconciled Date" : "Unreconciled" }}
                                <span>{{ !empty($account_transaction['reconcileTransaction']) && $account_transaction['reconcileTransaction']['is_reconciled'] ? date('d/m/Y', strtotime($account_transaction['reconcileTransaction']['reconciled_at'])) : '' }}</span>
                            </div>

                            @if(!empty($account_transaction['reconcileTransaction']) && $account_transaction['reconcileTransaction']['is_reconciled'] || $account_transaction['transactionCollection'][0]['transaction_type'] == 'spend_money' || $account_transaction['transactionCollection'][0]['transaction_type'] == 'receive_money')
                                <div class="accountTransaction--settings col-xl-4 col-sm-12 col-12 col-lg-4 col-md-4  order-xl-3 order-lg-3 order-md-3 order-sm-3 order-3 ">
                                    <div class="accountTransaction--dropdown dropdown">
                                        <a class="fileSharebtn" href="#" role="button" id="mainlinkadd" data-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-gear"></i>Settings</a>
                                        <div class="dropdown-menu dropdown-menu-left" aria-labelledby="mainlinkadd">
                                            @if($account_transaction['reconcileTransaction']['is_reconciled'])
                                                <a class="dropdown-item" href="#" id="unreconcile_transaction"><i class="zmdi zmdi-code-setting"></i>Unreconcile</a>
                                            @endif
                                            @if($account_transaction['transactionCollection'][0]['transaction_type'] == 'spend_money' || $account_transaction['transactionCollection'][0]['transaction_type'] == 'receive_money')
                                                <a class="dropdown-item" href="/bank/cash-receipt?id={{$account_transaction['transactionCollection'][0]['id']}}&transaction_type={{$account_transaction['transactionCollection'][0]['transaction_type']}}">
                                                    Edit Transaction
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if( $account_transaction['transactionCollection'][0]['transaction_type'] == 'invoice' || $account_transaction['transactionCollection'][0]['transaction_type'] == 'expense' )

                        <div class="transactionSummary--deets">
                            <div class="row minorAdjustment">
                                <div class="col-xl-4 col-lg-4 col-md-4 order-xl-5 order-lg-5 order-md-5 order-sm-4 order-4">
                                    Payment Made
                                    <span>{{ !empty($account_transaction) ? $account_transaction['payment_date'] : ''}}</span>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="partstable" class="AccountTransaction_table">
                                <thead>
                                <tr>
                                    <th scope="col" style="min-width: 200px;">Contact</th>
                                    <th scope="col">Invoice Number</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Due Date</th>
                                    <th scope="col" style="min-width: 100px;">Total</th>
                                    <th scope="col" style="min-width: 100px;">Payment Amount</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($account_transaction['transactionCollection'] as $transaction)
                                    <tr id="" class="">
                                        <td>
                                            {{ $transaction['client_name'] }}
                                        </td>
                                        <td>
                                            @if($transaction['transaction_type'] == 'invoice')
                                                <a href="/invoice/{{$transaction['id']}}/edit">
                                                <i class="fa-solid fa-eye"></i> {{ 'INV-'.str_pad($transaction['transaction_number'], 6, '0', STR_PAD_LEFT) }}</a>
                                            @elseif($transaction['transaction_type'] == 'expense')
                                                <a href="/expense/{{$transaction['id']}}/view"> <i class="fa-solid fa-eye"></i>{{ 'EXP-'.str_pad($transaction['transaction_number'], 6, '0', STR_PAD_LEFT) }}</a>
                                            @endif
                                        </td>
                                        <td>{{ $transaction['issue_date'] }}</td>
                                        <td>{{ $transaction['due_date'] }}</td>
                                        <td>{{ number_format(abs($transaction['amount_paid'] + $account_transaction['total_amount']), 2) }}</td>
                                        <td>{{ number_format(abs($transaction['pivot']['payment']), 2) }}</td>
                                    </tr>
                                @endforeach

                                <tr class="invoice-separator">
                                    <td colspan="7">
                                        &nbsp;
                                    </td>
                                </tr>

                                <tr class="invoice-total--subamount">
                                    <td colspan="2" rowspan="6">
                                        &nbsp;
                                    </td>
                                </tr>
                                <tr class="invoice-total--paid">
                                    <td colspan="2">
                                        Total amount
                                    </td>
                                    <td colspan="3">
                                        <input type="float" id="" name="" readonly value="{{ number_format( abs($account_transaction['total_amount']), 2) }}">
                                    </td>
                                </tr>

                                </tbody>
                            </table>
                        </div>


                    @elseif( !empty($account_transaction) && ($account_transaction['transactionCollection'][0]['transaction_type'] == 'arprepayment'
                                || $account_transaction['transactionCollection'][0]['transaction_type'] == 'apprepayment'
                                || $account_transaction['transactionCollection'][0]['transaction_type'] == 'aroverpayment'
                                || $account_transaction['transactionCollection'][0]['transaction_type'] == 'apoverpayment'
                            ))

                        <div class="transactionSummary--deets">
                            <div class="row minorAdjustment">
                                <div class="col-xl-4 col-lg-4 col-md-4">
                                    Pre-Payment Made
                                    <span>{{ $account_transaction['payment_date'] }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="partstable" class="AccountTransaction_table">
                                <thead>
                                <tr>
                                    <th scope="col" style="width:135px; min-width:300px;">Contact</th>
                                    <th scope="col" style="width:200px; min-width:200px;">Invoice Number</th>
                                    <th scope="col" style="width:200px; min-width:200px;">Date</th>
                                    <th scope="col" style="width:170px; min-width:170px;">Credit Total</th>
                                    <th scope="col" style="width:150px; min-width:200px;">Payment Amount</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($account_transaction['transactionCollection'] as $transaction)
                                    <tr id="" class="" >
                                        <td>{{ $transaction['client_name'] }}</td>
                                        <td>
                                            <a class="dropdown-item" href="/bank/cash-receipt?id={{$account_transaction['transactionCollection'][0]['id']}}&transaction_type={{$account_transaction['transactionCollection'][0]['transaction_type']}}">
                                                {{  !empty($transaction['transaction_number']) ? 'INV-'.str_pad($transaction['transaction_number'], 6, '0', STR_PAD_LEFT) : 'No invoice number' }}
                                            </a>
                                        </td>
                                        <td>{{ $transaction['issue_date'] }}</td>
                                        <td>{{ number_format(abs($transaction['total_amount']), 2) }}</td>
                                        <td>{{ number_format(abs($transaction['total_amount']), 2) }}</td>
                                    </tr>
                                @endforeach
                                <tr class="invoice-separator">
                                    <td colspan="4">
                                        &nbsp;
                                    </td>
                                </tr>
                                <tr class="invoice-total--subamount">
                                    <td colspan="2" rowspan="2">
                                        &nbsp;
                                    </td>
                                </tr>
                                <tr class="invoice-total--paid">
                                    <td colspan="1">
                                        Total amount
                                    </td>
                                    <td colspan="2">
                                        <input type="float" id="" name="" readonly value="{{ number_format( abs($account_transaction['total_amount']), 2) }}">
                                    </td>
                                </tr>

                                </tbody>
                            </table>
                        </div>

                    @elseif(!empty($account_transaction) && ($account_transaction['transactionCollection'][0]['transaction_type'] == 'spend_money' || $account_transaction['transactionCollection'][0]['transaction_type'] == 'receive_money'))

                        <div class="transactionSummary--deets">
                            <div class="row">
                                <div class="accountTransaction--from col-xl-4 col-lg-4 col-md-4">
                                    {{ $account_transaction['transactionCollection'][0]['transaction_type'] == 'spend_money' ? "To" : "From" }}
                                    <span>
                                        {{ !empty($account_transaction) ? $account_transaction['transactionCollection'][0]['client_name'] : ''}}
                                    </span>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4">
                                    Date
                                    <span>{{ !empty($account_transaction) ? $account_transaction['payment_date'] : '' }}</span>
                                </div>
                            </div>
                        </div>


                        <div class="table-responsive">
                            <table id="partstable" class="AccountTransaction_table">
                                <thead>
                                <tr>
                                    <th scope="col" style="width:135px; min-width:135px;">Item</th>
                                    <th scope="col" style="width:70px; min-width:70px;">QTY</th>
                                    <th scope="col" style="width:200px; min-width:200px;">Description</th>
                                    <th scope="col" style="width:80px; min-width:80px;">Unit Price</th>
                                    <th scope="col" style="width:170px; min-width:170px;">Account</th>
                                    <th scope="col" style="width:150px; min-width:150px;">Tax Rate</th>
                                    <th scope="col" style="width:70px; min-width:70px;">Amount</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(!empty($account_transaction['transactionCollection']))
                                    @foreach(($account_transaction['transactionCollection']) as $collection)
                                        @foreach(($collection['transactions']) as $parts)

                                            <tr id="" class="invoice_parts_form_cls" >
                                                <td>
                                                        <?php $itemNameCode = !empty($parts['parts_name'] && $parts['parts_code'] ) ? $parts['parts_code']. " : " .$parts['parts_name'] : '' ?>
                                                    {{ $itemNameCode }}
                                                </td>
                                                <td>
                                                    {{!empty($parts['parts_quantity']) ? $parts['parts_quantity'] : 0 }}
                                                </td>
                                                <td>
                                                    <textarea class="autoresizing" readOnly>{{!empty($parts['parts_description']) ? $parts['parts_description'] : ''}}</textarea>
                                                </td>
                                                <td>
                                                    {{ !empty($parts['parts_unit_price']) ? number_format($parts['parts_unit_price'], 2)  : 0.00 }}
                                                </td>
                                                <td>
                                                    {{ !empty($parts['chartAccountsParticulars']) ? $parts['chartAccountsParticulars']['chart_accounts_particulars_code'] .' - '. $parts['chartAccountsParticulars']['chart_accounts_particulars_name'] : '' }}
                                                </td>
                                                <td>
                                                    {{ !empty($parts['invoiceTaxRates']) ? $parts['invoiceTaxRates']['tax_rates_name'] : '' }}
                                                </td>
                                                <td>
                                                    {{ !empty($parts['parts_amount']) ? number_format($parts['parts_amount'], 2) : 0.00 }}
                                                </td>
                                            </tr>
                                        @endforeach

                                        <tr class="invoice-separator">
                                            <td colspan="8">
                                                &nbsp;
                                            </td>
                                        </tr>

                                        <tr class="invoice-total--subamount">
                                            <td colspan="4" rowspan="5">
                                            </td>
                                            <td colspan="1">Subtotal (excl GST)</td>
                                            <td colspan="2">
                                                <input type="float" readonly value="{{!empty($collection) ? number_format($collection['sub_total'], 2) : 0 }}">
                                            </td>
                                        </tr>
                                        <tr class="invoice-total--gst">
                                            <td colspan="1" >Total GST {{!empty($collection) && $collection['total_gst'] > 0 ? '10%' : '0%'}}</td>
                                            <td colspan="2">
                                                <input type="float" readonly value="{{!empty($collection) ? number_format($collection['total_gst'], 2) : 0 }}">
                                            </td>
                                        </tr>
                                        <tr class="invoice-total--paid">
                                            <td colspan="1">
                                                Total amount
                                            </td>
                                            <td colspan="2">
                                                <input type="float" id="" name="" readonly value="{{!empty($collection) ? number_format($collection['total_amount'], 2) : 0 }}">
                                            </td>
                                        </tr>
                                        <tr class="invoice-total--taxRate">
                                            <td>&nbsp;</td>
                                            <td colspan="2">
                                                Amounts are {{ ucwords(Str::replace("_", " ", $collection['default_tax'])) }}
                                            </td>
                                        </tr>
                                    @endforeach

                                @endif


                                </tbody>
                            </table>
                        </div>



                    @elseif(!empty($account_transaction) && ($account_transaction['transactionCollection'][0]['transaction_type'] == 'minor_adjustment'))

                        <div class="transactionSummary--deets">
                            <div class="row minorAdjustment">
                                <div class="col-xl-4 col-lg-4 col-md-4">
                                    Adjustment Date
                                    <span>{{ !empty($account_transaction) ? $account_transaction['transactionCollection'][0]['payment_date'] : '---'}}</span>
                                </div>
                            </div>


                        </div>

                        <div class="table-responsive">
                            <table id="partstable" class="AccountTransaction_table">
                                <thead>
                                <tr>
                                    <th scope="col" style="width:135px; min-width:135px;">&nbsp;</th>
                                    <th scope="col" style="width:70px; min-width:70px;">Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        Reconciliation Adjustment
                                    </td>
                                    <td>
                                        {{ !empty($account_transaction) ? number_format(abs($account_transaction['transactionCollection'][0]['total_amount']), 2) : ''}}
                                    </td>
                                </tr>


                                </tbody>
                            </table>
                        </div>

                    @endif

                    <div class="row">
                        <div class="col-xl-4 col-lg-2 col-md-3 col-sm-12 col-xs-12 col-12">
                            <a id="history_btn" class="btn sumb--btn" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                                <i class="fa-solid fa-clock-rotate-left"></i><span id="history_toggle_text">Show</span> History
                            </a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-12">
                            <div class="collapse" id="collapseExample">
                                <div class="card card-body sumb--recentlogdements sumb--putShadowbox">
                                    <div class="table-responsive">
                                        <table id="invoice_list">
                                            <thead>
                                            <tr>
                                                <th scope="col" style="width:135px; min-width:135px;">Changes</th>
                                                <th scope="col" style="width:200px; min-width:200px;">Date</th>
                                                <th scope="col" style="width:135px; min-width:135px;">User</th>
                                                <th scope="col" style="width:400px; min-width:400px;">Details</th>
                                            </tr>
                                            </thead>
                                            <tbody id="history_discuss" class="accountTransactionHistory">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-navigation">
                        <div class="form-navigation--btns row">

                            <div class="col-xl-8 col-lg-10 col-md-9 col-sm-12 col-xs-12 col-12"></div>

                            <div class="col-xl-4 col-lg-2 col-md-3 col-sm-12 col-xs-12 col-12">
                                <a href="/account/transactions?bank_account_id={{$bank_account_id}}" class="btn sumb--btn"><i class="fa-solid fa-circle-left"></i>Back</a>
                            </div>
                        </div>
                    </div>
                    <!-- </form> -->
                </section>
            </div>
        </div>
    </div>
</div>

@include('includes.footer')

<script>

    function prtg() {
        $('#pqty').show();
        $('#puprice').show();
    }
    function prts() {
        $('#pqty').hide();
        $('#puprice').hide();
    }
    $(function() {

        $(document).on('click', '#unreconcile_transaction', function(event) {
            $('#unreconcile_transaction_modal').modal({
                backdrop: 'static',
                keyboard: true,
                show: true
            });
        });

        $('#history_btn').on('click', function () {

            if( $('#history_toggle_text').text() == "Show")
            {
                $("#history_discuss").empty();
                $.ajaxSetup({headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }});
                $.ajax ({
                    type    : "GET",
                    url     : '/transaction/'+<?php echo !empty($account_transaction) ? $account_transaction['id'] : '' ?>+'/history/?transaction_collection_id='+{{$account_transaction['reference_id']}},
                    enctype: 'multipart/form-data',
                    success : function(response) {
                        $body.find('#pre-loader').hide();
                        $.each(response.data, function (key, value) {
                            $("#history_discuss").append('<tr>\
                                    <td>'+value['action']+'</td>\
                                    <td>'+value['created_at']+'</td>\
                                    <td>'+value['user_name']+'</td>\
                                    <td>'+value['description']+'</td>\
                                </tr>'
                            );
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

                $('#history_toggle_text').text('Hide');
            }else{
                $('#history_toggle_text').text('Show');
            }
            // $('#history_toggle_text').text() == "Show" ? $('#history_toggle_text').text('Hide') : $('#history_toggle_text').text('Show');
        });
    });

    function unReconcileTransaction(accountId, bankTransactionId)
    {
        $body.find('#pre-loader').show();
        $.ajaxSetup({headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }});
        $.ajax ({
            type    : "PUT",
            url     : '/accounts/'+accountId+'/transactions/'+bankTransactionId+'/unreconcile',
            enctype: 'multipart/form-data',
            success : function(response) {
                $body.find('#pre-loader').hide();
                window.location.href = '/account/transactions?bank_account_id='+accountId;
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
    }
</script>
</body>

</html>
<!-- end document-->
