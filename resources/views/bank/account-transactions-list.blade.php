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
                    <h3 class="sumb--title m-b-20">Account Transactions</h3>
                </section>

                <section>
                @if(empty($bank_accounts))
                    <div class="row">
                        <div class="col-xl-7 col-lg-8">
                            <div class="sumb--backAccountsBox sumb--dashstatbox sumb--putShadowbox">
                                Start connecting your bank account to [B]izmate to automatically see your transactions.
                                <div class="bank--cards">
                                     <a href="/bank/accounts/add"><i class="zmdi zmdi-plus-circle-o"></i></a>
                                </div>

                            </div>
                        </div>
                    </div>

                @else

                    <form action="/account/transactions"  method="GET" enctype="multipart/form-data" id="transaction_search_form">


                        <div class="row d-flex align-items-end">

                            <div class="bankreconHeader col-xl-5 col-lg-5 col-md-6 col-sm-12 col-12">
                                <h5>Bank Accounts</h5>
                                <div class="form-input--wrap" style="margin-bottom: 0px;">
                                    <select class="form-input--dropdown" name="bank_account_id" id="bank_account_id">

                                        @if(!empty($bank_accounts))
                                            @foreach ($bank_accounts as $account)
                                                <option {{ ( $account['account_id'] == $bank_account_id ) ? 'selected' : '' }} value="{{ $account['account_id'] }}">
                                                {{ $account['account_name'] }} -
                                                @if ($account['class']['type'] == 'credit-card')
                                                    {{ Str::mask($account['account_number'], '*', 0, strlen($account['account_number'])-4 ) }}
                                                @else
                                                    {{ $account['account_number'] }}
                                                @endif
                                            </option>
                                            @endforeach
                                        @else
                                            <option value="">Select Bank Account</option>
                                        @endif

                                    </select>
                                </div>
                            </div>

                            <div class="col-xl-7 col-lg-7 col-md-6">
                                <a href="/bank/transaction/reconcile?bank_account_id={{$bank_account_id}}" id="" class="acctTransactions--btn float-right"><i class="fa-solid fa-code-merge"></i>Reconcile Bank Transactions</a>
                            </div>

                        </div>

                        <hr class="hr--separator">

                        <div class="row">
                            <div class="col-xl-3">
                                <div class="form-input--wrap">
                                    <label class="form-input--question" for="">Keyword Search</label>
                                    <div class="form--inputbox row">
                                        <div class="col-12">
                                            <input type="text" id="search_desc_class" name="search_desc_class" placeholder="Class or Description"  value="{{!empty($search_desc_class) ? $search_desc_class : ''}}">
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="col-xl-2">
                                <div class="form-input--wrap">
                                    <label class="form-input--question" for="">Minimum Amount</label>
                                    <div class="form--inputbox row">
                                        <div class="col-12">
                                            <input type="number" id="min_amt" name="min_amt" value="{{!empty($min_amt) ? $min_amt : ''}}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-2">
                                <div class="form-input--wrap">
                                    <label class="form-input--question" for="">Maximum Amount</label>
                                    <div class="form--inputbox row">
                                        <div class="col-12">
                                            <input type="number" id="max_amt" name="max_amt" value="{{!empty($max_amt) ? $max_amt : ''}}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-5">
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="form-input--wrap">
                                            <label class="form-input--question" for="">Start Date</label>
                                            <div class="date--picker row">
                                                <div class="col-12">
                                                    <input type="text" id="start_date" name="start_date" placeholder="Date('DD/MM/YYYY')" readonly value="{{!empty($start_date) ? $start_date : ''}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="form-input--wrap">
                                            <label class="form-input--question" for="">End Date</label>
                                            <div class="date--picker row">
                                                <div class="col-12">
                                                    <input type="text" id="end_date" name="end_date" placeholder="Date('DD/MM/YYYY')" readonly value="{{!empty($end_date) ? $end_date : ''}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3">

                                <div class="btn-group sumb--dashboardDropdown transaction--filter" role="group">
                                    <button id="btnGroupDrop_type" type="button" data-toggle="dropdown" aria-expanded="false">

                                            Filter My Transactions
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop_type">
                                        <!-- <a class="dropdown-item" href="javascript:void(0)" type="button" onclick="directionItems(null, null, 'credit')">Credit</a>
                                        <a class="dropdown-item" href="javascript:void(0)" type="button" onclick="directionItems(null, null, 'debit')">Debit</a> -->
                                        <a class="dropdown-item" href="javascript:void(0)" type="button" onclick="searchItems(null, null, '1')">Reconciled</a>
                                        <a class="dropdown-item" href="javascript:void(0)" type="button" onclick="searchItems(null, null, '0')">Unreconciled</a>
                                        <a class="dropdown-item" href="/bank/account/transactions?bank_account_id={{ $bank_account_id }}">View All</a>
                                    </div>
                                </div>

                                <input id="filter_by" type="hidden" name="filterBy" value='{{ !empty($filterBy) ? $filterBy : "" }}'>
                                <input id="reconcile_status" type="hidden" name="isReconciled" value='{{ !empty($isReconciled) ? $isReconciled : "" }}'>
                            </div>

                            <div class="bankTransactions-list--btns col-xl-9">
                                <div class="form-input--wrap" style="text-align: right;">
                                    <button type="submit" name="search_transaction" class="btn sumb--btn" value="Search"><i class="fa-solid fa-magnifying-glass"></i>Search</button>
                                    <button type="button" class="btn sumb--btn sumb-clear-btn" onclick="clearSearchItems()"><i class="fa-solid fa-circle-xmark"></i>Clear Search</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="sumb--recentlogdements sumb--putShadowbox">

                        <div class="table-responsive">
                            <table class="AccountTransaction_list">
                                <thead>
                                    <tr>
                                        <th style="border-top-left-radius: 7px;">Date</th>
                                        <th>Transaction ID</th>
                                        <th>Description</th>
                                        <th style="min-width: 150px">Spent</th>
                                        <th style="min-width: 150px">Received</th>
                                        <th class="bankTransaction_list_actions" style="border-top-right-radius: 7px;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @if(empty($bank_transactions['data']))
                                <tr>
                                    <td style="padding: 30px 15px; text-align: center;" colspan="5">No Transactions Found </td>
                                </tr>
                                @else
                                    @foreach($bank_transactions['data'] as $transaction)

                                        <tr class='clickable-row' data-href='/accounts/{{$bank_account_id}}/transactions/{{$transaction['payment_transaction']['id']}}'>
                                            <td>
                                                <?php echo ($transaction['payment_transaction']['issue_date']); ?>
                                            </td>
                                            <td>

                                            <td>
                                                @if($transaction['payment_transaction']['transaction_type'] == 'payment')
                                                    {{ 'Payment: '. $transaction['payment_transaction']['client_name'] }}
                                                @elseif($transaction['payment_transaction']['transaction_type'] == 'minor_adjustment')
                                                    {{ 'Reconciliation adjustment' }}
                                                @elseif($transaction['payment_transaction']['transaction_type'] == 'arprepayment' || $transaction['payment_transaction']['transaction_type'] == 'apprepayment')
                                                    {{ "Prepayment: ".$transaction['payment_transaction']['client_name'] }}
                                                @elseif($transaction['payment_transaction']['transaction_type'] == 'aroverpayment' || $transaction['payment_transaction']['transaction_type'] == 'apoverpayment')
                                                    {{ "Overpayment: ".$transaction['payment_transaction']['client_name'] }}
                                                @else
                                                    {{ $transaction['payment_transaction']['client_name'] }}
                                                @endif

{{--                                                ATN-{{ str_pad($transaction['payment_transaction']['id'], 6, '0', STR_PAD_LEFT) }}--}}

                                            </td>

                                            <td>
                                                @switch($transaction['payment_transaction']['transaction_type'])
                                                    @case('payment' && $transaction['payment_transaction']['transaction_sub_type'] == 'spent')
                                                        {{ number_format(abs($transaction['payment_transaction']['total_amount']), 2) }}
                                                        @break

                                                    @case('minor_adjustment' && $transaction['payment_transaction']['transaction_sub_type'] == 'spent' )
                                                        {{ number_format(abs($transaction['payment_transaction']['total_amount']), 2) }}
                                                        @break

                                                    @case('spend_money')
                                                        {{ number_format(abs($transaction['payment_transaction']['total_amount']), 2) }}
                                                        @break
                                                    @case('apprepayment')
                                                        {{ number_format(abs($transaction['payment_transaction']['total_amount']), 2) }}
                                                        @break
                                                    @case('apoverpayment')
                                                        {{ number_format(abs($transaction['payment_transaction']['total_amount']), 2) }}
                                                        @break
                                                    @default
                                                        @break
                                                @endswitch
                                            </td>
                                            <td>
                                                @switch($transaction['payment_transaction']['transaction_type'])

                                                    @case('payment' && $transaction['payment_transaction']['transaction_sub_type'] == 'received')
                                                        {{ number_format(abs($transaction['payment_transaction']['total_amount']), 2) }}
                                                        @break

                                                    @case('minor_adjustment' && $transaction['payment_transaction']['transaction_sub_type'] == 'received' )
                                                        {{ number_format(abs($transaction['payment_transaction']['total_amount']), 2) }}
                                                        @break

                                                    @case('receive_money')
                                                        {{ number_format(abs($transaction['payment_transaction']['total_amount']), 2) }}
                                                        @break
                                                    @case('arprepayment')
                                                        {{ number_format(abs($transaction['payment_transaction']['total_amount']), 2) }}
                                                    @break
                                                    @case('aroverpayment')
                                                        {{ number_format(abs($transaction['payment_transaction']['total_amount']), 2) }}
                                                    @break
                                                    @default
                                                        @break

                                                @endswitch
                                            </td>

                                            <td class="{{ !empty($transaction) && $transaction['reconcile_transaction']['is_reconciled'] ? 'recon'  : 'unrecon' }}" style="text-align: center;">
                                                <span>{{ !empty($transaction['reconcile_transaction']['is_reconciled']) ? 'Reconciled'  : 'Unreconciled' }}</span>
                                            </td>
                                        </tr>
                                    <!-- </a> -->
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
                                        <a href="javascript:void(0)" type="button" class="btn btn-outline-secondary" >Page {{$paging['now']}}</a>
                                        <a href="{{ empty($paging['next']) ? 'javascript:void(0)' : $paging['next'] }}" type="button" class="btn btn-outline-secondary {{ empty($paging['next']) ? 'disabled' : '' }}" ><i class="fas fa-angle-right"></i></a>
                                        <a href="{{ empty($paging['last']) ? 'javascript:void(0)' : $paging['last'] }}" type="button" class="btn btn-outline-secondary {{ empty($paging['last']) ? 'disabled' : '' }}"><i class="fas fa-angle-double-right"></i></a>

                                        <div class="btn-group" role="group">
                                            <button id="btnGroupDrop1" type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                Display: {{ !empty($bank_transactions) ? $bank_transactions['per_page'] : ''}} Items
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
                @endif

                </section>

                <section>
                    &nbsp;
                </section>
            </div>
        </div>
    </div>
</div>

@include('includes.footer')

<script>
    $(function() {
        $( "#start_date" ).datepicker({ dateFormat: 'dd/mm/yy' });
        $( "#end_date" ).datepicker({ dateFormat: 'dd/mm/yy' });

        // $( "#start_date" ).datepicker();
        // $( "#end_date" ).datepicker();
    });

    $(document).ready(function () {
        $("#bank_account_id").on("change", function(){
            id = $("#bank_account_id").val();
            if ('URLSearchParams' in window) {
                var searchParams = new URLSearchParams(window.location.search);
                searchParams.forEach((value, key) => {
                    if(key == 'bank_account_id'){
                        searchParams.delete(key);
                    }
                    // console.log(key);
                });
               // console.log(searchParams.keys())
                searchParams.set('bank_account_id', id);
                window.location.search = searchParams.toString();
            }
        });

        $(".clickable-row").click(function() {
            window.location = $(this).data("href");
        });
    });

    function clearSearchItems(){

        var url = "{{URL::to('/account/transactions?bank_account_id='.$bank_account_id)}}";
        location.href = url;

        $("#search_desc_class").val('');
        $("#start_date").val('');
        $("#end_date").val('');
        $("#min_amt").val('');
        $("#max_amt").val('');
        $("#reconcile_status").val('');
        return false;
    }

    function directionItems(orderBy, direction, filterBy){
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
        $("#transaction_search_form").submit();
    }
    function searchItems(filterByStatus){
        $("#reconcile_status").val(filterByStatus);
        $("#transaction_search_form").submit();
    }

</script>
