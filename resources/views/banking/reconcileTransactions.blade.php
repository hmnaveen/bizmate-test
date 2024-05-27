@include('includes.head')
@include('includes.user-header')

<form action="/"  method="GET" enctype="multipart/form-data"> 
    @csrf
    <div id="invoice_payment_date_modal" class="modal fade modal-reskin" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title splittransaction--header">Split Transaction</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-input--wrap">
                                <label class="form-input--question" style="margin-top: 0px;">Balance</label>
                                <div class="form--inputbox row">
                                    <div class="col-12">
                                        <input type="float" class="form-control form-control-sm" id="balance" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="form-input--wrap">
                                <label class="form-input--question">Part Payment</label>
                                <div class="form--inputbox row">
                                    <div class="col-12">
                                        <input type="float" min="0" class="form-control" id="part_payment" >
                                    </div>
                                </div>
                            </div>

                            <div class="form-input--wrap" style="margin-bottom: 0px;">
                                <label class="form-input--question">Remaining Amount</label>
                                <div class="form--inputbox row" style="margin-bottom: 0px;">
                                    <div class="col-12">
                                        <input type="float" class="form-control" id="remaining_balance" disabled >
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
                  
                <div class="modal-footer">

                    <input type="hidden" id="transaction_index" name="transaction_index"   readonly value="" >
                    <input type="hidden" id="split_transaction_details" name="split_transaction_details"  readonly value="" >

                    <button type="button" class="btn btn-secondary close--btn" data-dismiss="modal" >Close</button>
                    <button type="button" class="btn btn-primary split--btn" id="submit_payment_date" value="" onclick="splitTransaction()" >Split</button>
                    <button hidden="hidden" type="submit" class="btn btn-primary delete--btn" id="payment_date_form" value="" >Submit</button>

                </div>
            </div>
        </div>
    </div>
</form>



<!--  Add new account pop-up modal starts -->
<div class="modal fade modal-reskin modal-invoice--addaccount" id="newAddAccountModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title document--header" id="exampleModalLongTitle">New Add Account</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xl-6">
                        <div class="form-input--wrap">
                            <label class="form-input--question" for="">Account Type</label>
                            @if(!empty($chart_accounts_types))
                                <select class="form-input--dropdown" id="invoice_chart_accounts_type_id">
                                    <option value="">Select Account Type</option>
                                    @foreach($chart_accounts_types as $chart_accounts)
                                        @if(!empty($chart_accounts))
                                            <optgroup label="{{$chart_accounts['chart_accounts_name']}}">
                                                @foreach($chart_accounts['chart_accounts_types'] as $types)
                                                    <option id="invoice_chart_accounts_id_{{$types['id']}}" value="{{!empty($chart_accounts) ? $chart_accounts['id'] : ''}}"  hidden></option>
                                                    <option value="{{$types['id']}}">{{!empty($types['chart_accounts_type']) ? $types['chart_accounts_type'] : ''}}</option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                    @endforeach
                                </select>
                            @endif
                            <div class="" role="alert" id="invoice_chart_accounts_type_error"></div>
                        </div>
                        
                    </div>

                    <div class="col-xl-6">
                        <div class="form-input--wrap">
                            <label class="form-input--question" for="">Tax Rate</label>
                            <div class="row">
                                <div class="col-12">
                                    @if(!empty($tax_rates))
                                        <select class="form-input--dropdown" id="invoice_chart_accounts_tax_rate" name="invoice_chart_accounts_tax_rate" value="" required>
                                            <option selected value="0#|#0">Tax Rate Option</option>
                                            @foreach($tax_rates as $tax_rate)
                                                <option value="{{$tax_rate['id'].'#|#'.$tax_rate['tax_rates']}}">{{$tax_rate['tax_rates_name']}}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                            </div>
                            <div class="" role="alert" id="invoice_chart_accounts_tax_rate_error"></div>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-xl-6">
                        <div class="form-input--wrap">
                            <label class="form-input--question" for=""> Code </label>
                            <div class="form--inputbox row">
                                <div class="col-12">
                                    <input type="text" required  class="form-control" id="invoice_chart_accounts_code" name="invoice_chart_accounts_code" placeholder=""  value="">
                                </div>
                            </div>
                            <div class="" role="alert" id="invoice_chart_accounts_code_error"></div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="form-input--wrap">
                            <label class="form-input--question" for="">Name</label>
                            <div class="form--inputbox row">
                                <div class="col-12">
                                    <input type="text" required  class="form-control" id="invoice_chart_accounts_name" name="invoice_chart_accounts_name" placeholder=""  value="">
                                </div>
                            </div>
                            <div class="" role="alert" id="invoice_chart_accounts_name_error"></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="form-input--wrap">
                            <label class="form-input--question" for="" >Description</label>
                            <textarea class="form-control" id="invoice_chart_accounts_description" name="invoice_chart_accounts_description"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <input type="hidden" id="invoice_account_part_row_id" value="">
                <input type="hidden" id="invoice_account_transaction_row_id" value="">
                <input type="hidden" id="add_account_from" value="">
                <button type="button" class="btn btn-secondary close--btn" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary save--btn" onclick="addNewAccountReconcile('invoice_account_transaction_row_id','invoice_account_part_row_id')">Save</button>
            </div>
        </div>
    </div>
</div>
<!-- Add new account modal ends -->


<!-- PAGE CONTAINER-->
<div class="page-container">

    @include('includes.user-top')
    
    <!-- MAIN CONTENT-->
    <div class="main-content">
        <div class="section__content section__content--p30 p-b-30">
            <div class="container-fluid">
                <section>
                    <h3 class="sumb--title m-b-20">Reconcile Bank Transactions</h3>
                </section>

                @if(empty($bank_accounts))
                    <div class="row">
                        <div class="col-xl-7 col-lg-8">
                            <div class="sumb--backAccountsBox sumb--dashstatbox sumb--putShadowbox">
                                Start connecting your bank account to [B]izmate to reconcile your transactions.
                                <div class="bank--cards">
                                    <a href="/bank/accounts/add"><i class="zmdi zmdi-plus-circle-o"></i></a>
                                </div>

                            </div>
                        </div>
                    </div>
                @else

                    <div class="row">

                        <div class="bankreconHeader col-xl-4 col-lg-5 col-md-6 col-sm-12 col-12">
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
                    </div>



                    <div class="reconcileTransactions--logs sumb--putShadowbox">

                        @if(empty($bank_transactions['data']))
                            <div class="reconcileTransactions--notransactions">No Transactions Found</div>
                        @else
                            
                                
                            @php($k = 0)
                            @php($match = 0)
                            @php($otherMatches = [])
                            @foreach($bank_transactions['data'] as $transaction)

                                @foreach($appTransactions as $appTransaction)
                                    @if($appTransaction['total_amount'] == abs($transaction['amount']) && (($appTransaction['transaction_type'] == 'invoice' && $transaction['direction'] == 'credit') || ($appTransaction['transaction_type'] == 'expense' && $transaction['direction'] == 'debit'))) 
                                        @php($match = 1)
                                        @break;
                                    @else
                                        @php($match = 0)
                                    @endif
                                @endforeach

                                <div class="reconcileTransactions {{ ($match == 1) ? 'match':'' }}">
                                
                                    <div class="row">
                                        <div class="col-xl-4 col-lg-4 col-md-12 order-xl-1 order-lg-1 order-md-1 order-sm-1 order-1">
                                            <div class="RT--bankTransaction">

                                                <span><i class="fa-solid fa-building-columns"></i>Back Transaction</span>

                                                <div class="row">
                                                    <div class="col-9">
                                                        <div class="desc">{{ $transaction['description'] }}</div>
                                                        <div class="date"><?php echo substr($transaction['post_date'],0,10); ?></div>
                                                    </div>
                                                    <div class="col-3">
                                                        <div class="deets {{ ucfirst($transaction['direction']) }}">
                                                            <div>
                                                                {{ ($transaction['amount'] < 0 ? '('.abs($transaction['amount']).')' : abs($transaction['amount']) ) }}
                                                                <span>{{ ucfirst($transaction['direction']) }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="col-xl-2 col-lg-2 col-md-12 order-xl-2 order-lg-2 order-md-4 order-sm-4 order-4 button--style {{( ($match == 1) ? '' : 'hide' )}}" id="reconcil_transaction_ok_btn_{{ $k }}">
                                            <div>
                                                <div>
                                                    @if ($match == 1)
                                                        <a id="transactionReconcile_{{ $k }}" onclick="reconcileTransactions('{{ $k }}', '{{$transaction['id']}}', '{{$appTransaction['id']}}', 1, '{{substr($transaction['post_date'],0,10)}}', '{{$appTransaction['total_amount']}}')"><i class="fa-solid fa-code-merge" style="margin-right: 5px"></i>Reconcile</a>
                                                    @else
                                                        &nbsp;
                                                    @endif       
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-xl-6 col-lg-6 col-md-12 order-xl-3 order-lg-3 order-md-2 order-2">
                                            <div class="RT--sumbTransaction">
                                                <span><i class="fa-solid fa-arrow-right-arrow-left"></i>Transactions in SUM[B]</span>

                                                <div class="transactions">
                                                    @php($i = 0)
                                                    @php($j = 0)
                                                    @foreach($appTransactions as $appTransaction)
                                                        @if($appTransaction['total_amount'] == abs($transaction['amount']) && (($appTransaction['transaction_type'] == 'invoice' && $transaction['direction'] == 'credit') || ($appTransaction['transaction_type'] == 'expense' && $transaction['direction'] == 'debit'))) 
                                                        
                                                            @php($i++)
                                                            @php(array_push($otherMatches, $appTransaction))
                                                        @endif
                                                    @endforeach

                                                    <ul class="nav nav-tabs" id="tabs_{{ $k }}">
                                                        <li class="nav-item">
                                                            <a id="match_tab_{{ $k }}" class="nav-link tablinks_{{ $k }} active" onclick="openTab(event, 'Match_{{ $k }}','{{ $k }}','{{ $i }}')" aria-current="page"><i class="zmdi zmdi-flip"></i>Match</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a id="create_tab_{{ $k }}" class="nav-link tablinks_{{ $k }}" onclick="openTab(event, 'Create_{{ $k }}','{{ $k }}','{{ $i }}')"><i class="fa-regular fa-file-lines"></i>Create</a>
                                                        </li>
                                                        <li class="find--match fm--btn_{{ $k }}">
                                                            <a onclick="findAndMatchTransactions('{{ $k }}','{{$transaction['id']}}','{{$transaction['direction']}}')"><i class="fa-solid fa-magnifying-glass"></i>Find & Match</a>
                                                        </li>
                                                    </ul>
                                                    

                                                    <div id="Create_{{ $k }}" class="createTransaction tabcontent_{{ $k }}" name="createtransaction">
                                                        <input hidden type="float" id="bank_transaction_id_{{$k}}" value="{{ $transaction['id'] }}">
                                                        <form id="create_transaction_reconcile_{{ $k }}" action="/" method="post" enctype="multipart/form-data">

                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="form-input--wrap">
                                                                        <label for="client_name" class="form-input--question">
                                                                            Client Name
                                                                        </label>
                                                                        <div class="form--inputbox recentsearch--input row">
                                                                            <div class="searchRecords col-12">
                                                                                <input type="text" onkeyup="getClient('{{$k}}',this)" id="transaction_client_name_{{ $k }}" name="client_name_{{ $k }}" required class="form-control" placeholder="Search Client Name" aria-label="Client Name" aria-describedby="button-addon2" autocomplete="off"  value="" >
                                                                            </div>
                                                                        </div>

                                                                        <div class="form--recentsearch clientname row">
                                                                            <div class="col-12">
                                                                                <div class="form--recentsearch__result"><p>
                                                                                    <ul>
                                                                                        @if (empty($exp_clients))
                                                                                            <li>You dont have any clients at this time</li>
                                                                                        @else
                                                                                            @php($counter = 0)
                                                                                            @foreach ($exp_clients as $ec)
                                                                                                @php($counter ++)
                                                                                                <li>
                                                                                                    <button type="button" class="dcc_click" data-myid="{{ $counter }}" onclick="appendClient('{{$k}}', this, 'transaction_client_name_{{ $k }}')">
                                                                                                        <span id="data_name_{{ $counter }}">{{ $ec['client_name'] }}</span>
                                                                                                    </button>
                                                                                                </li>
                                                                                            @endforeach
                                                                                        @endif

                                                                                        <li class="add--newactclnt">
                                                                                            <label for="savethisrep">
                                                                                                <input type="checkbox" id="savethisrep" name="savethisrep" value="yes" class="form-check-input" {{ !empty($form['save_client']) ? 'checked' : '' }}>
                                                                                                <div class="option--title">
                                                                                                    Add as a new active client?
                                                                                                    <span>Note: When the name is existing it will overide the old one.</span>
                                                                                                </div>
                                                                                            </label>
                                                                                        </li>
                                                                                    </ul>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                                                                    <div class="form-input--wrap account--dropdown">
                                                                        <label class="form-input--question">Account</label>
                                                                        <input class="input--box" autocomplete="off" data-toggle="dropdown" type="text" id="chart_accounts_{{ $k }}" name="invoice_parts_chart_accounts_{{ $k }}_0"  value="" readonly required onkeyup="removeReconcileOkBtn('chart_accounts_{{ $k }}')">

                                                                        <input type="hidden" id="chart_accounts_id_{{ $k }}" name="invoice_parts_chart_accounts_parts_id_{{ $k }}_0" value="">

                                                                        <ul class="dropdown-menu invoice-expenses--dropdown" id="invoice_chart_account_list_{{ $k }}_0">
                                                                            @if (!empty($chart_account))
                                                                            @php ($counter = 0)
                                                                            @foreach ($chart_account as $item)
                                                                                <li class="accounts-group--label">{{$item['chart_accounts_name']}}</li>
                                                                                @foreach ($item['chart_accounts_particulars'] as $particulars)
                                                                                    <li>
                                                                                        <button type="button" class="invoice_item" data-myid="{{ $counter }}" onclick="addInvoiceChartAccount('{{ $particulars['id'] }}', '{{$k}}', 0, 'create')" >
                                                                                            <span id="data_name_{{ $counter }}">{{ $particulars['chart_accounts_particulars_code'] }} - {{ $particulars['chart_accounts_particulars_name'] }} </span>
                                                                                        </button>
                                                                                    </li>
                                                                                @endforeach
                                                                            @endforeach
                                                                        @endif
                                                                        </ul>
                                                                    </div>
                                                                </div>

                                                                <div class="col-xl-6  col-lg-6 col-md-6 col-sm-12 col-12">
                                                                    <div class="form-input--wrap">
                                                                        <label class="form-input--question">Tax Rates</label>
                                                                        @if(!empty($tax_rates))
                                                                            <input type="hidden" name="invoice_parts_tax_rate_id_{{ $k }}_0" id="tax_rate_id_{{ $k }}" value="">
                                                                            <div class="form-input--wrap">
                                                                                <div class="row">
                                                                                    <div class="col-12 for--tables">
                                                                                        <select class="form-input--dropdown" id="tax_rate_{{ $k }}" name="invoice_parts_tax_rate_{{ $k }}_0" onchange="transactionCalculation('{{ $k }}',0); getTaxRates('{{ $k }}',0);" required style="display : {{!empty($transaction['default_tax']) && $transaction['default_tax']=="no_tax" ? 'none' : 'block' }}; border-color: #28282a;">
                                                                                            <option selected value="0#|#0">Tax Rate Option</option>    
                                                                                            @foreach($tax_rates as $tax_rate)
                                                                                                <option id="{{$tax_rate['id'].'_'.$k.'_'.'0'}}" value="{{$tax_rate['id'].'#|#'.$tax_rate['tax_rates']}}" >{{$tax_rate['tax_rates_name']}}</option>
                                                                                            @endforeach
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>

                                                                <div class="col-12">
                                                                    <div class="form-input--wrap textarea">
                                                                        <label class="form-input--question">Reason</label>
                                                                        <textarea class="col-12" id="transaction_description_{{ $k }}" name="invoice_parts_description_{{ $k }}_0"></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>


                                                            <input hidden type="float" id="bank_transaction_id_{{ $k }}"  name="bank_transaction_id_{{ $k }}" value="{{$transaction['id']}}">
                                                            <input hidden type="float" id="transaction_date_{{$k}}" name="issue_date_{{$k}}" value="<?php echo substr($transaction['post_date'],0,10); ?>">
                                                            <input hidden type="float" id="transaction_type_{{$k}}" name="transaction_type_{{$k}}" value="{{ $transaction['direction'] }}">

                                                        </form>

                                                        <div id="add--deets_{{ $k }}" class="add--deets">
                                                            <a class="tablinks_{{ $k }}" id="addTransactionDetails_{{ $k }}" onclick="openTransactionForm('{{ $k }}', '{{ $transaction['direction'] }}')"><i class="zmdi zmdi-plus-circle-o"></i>Add Details</a>
                                                        </div>
                                                    </div>

                                                    <div id="Match_{{ $k }}" class="matchedTransaction tabcontent_{{ $k }}">
                                                        @if($i > 0)
                                                            @foreach($appTransactions as $appTransaction)
                                                                @if($appTransaction['total_amount'] == abs($transaction['amount']) && (($appTransaction['transaction_type'] == 'invoice' && $transaction['direction'] == 'credit') || ($appTransaction['transaction_type'] == 'expense' && $transaction['direction'] == 'debit'))) 
                                                                    <div class="row">
                                                                        <div class="col-9">
                                                                            <div class="desc">{{ $appTransaction['client_name'] }}</div>
                                                                            <div class="date">{{ $appTransaction['issue_date'] }}</div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="deets {{ ucfirst($transaction['direction']) }}" style="padding-top: 5px;">
                                                                                <div>
                                                                                    {{ $appTransaction['total_amount'] }}
                                                                                    <span>{{ ucfirst($appTransaction['transaction_type']) }}</span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    @break
                                                                @endif
                                                            @endforeach
                                                        @else
                                                            <div class="no--matching"><i class="fa-solid fa-circle-exclamation"></i>No matching transactions</div>
                                                        @endif

                                                        @if($i > 1)
                                                            <div id="otherMatches_{{ $k }}" class="other--matches">
                                                                <a onclick="openOtherMatches('{{ abs($transaction['amount']) }}','{{ $k }}','{{ json_encode($otherMatches) }}')" id="otherMatch_{{ $k }}"><i class="fa-solid fa-triangle-exclamation"></i>{{ $i-1 }} Other Matches Found</a>
                                                            </div>

                                                            <!-- Other Matching Transactions -->
                                                            <div class="match_{{ $k }} matchBox"> </div>
                                                        @endif
                                                        
                                                    </div>
                                                </div>

                                                
                                            </div>
                                        </div>
                                        <div class="col-12 order-xl-4 order-lg-3 order-md-3 order-sm-3 order-3">
                                            <div name="createTransactionForm" id="createTransactionForm_{{ $k }}" >
                                                <div class="CTForm_wrap">
                                                    <form id="transaction-form-create_{{ $k }}" action="/transaction/create" method="post" enctype="multipart/form-data">
                                                        {{ method_field('POST') }}
                                                        {{ csrf_field() }}  

                                                        <p class="" role="alert" id="total_mismatch_error"></p>
                                                        <div class="alert alert-danger" id="validation_error_div" style="display: none; margin-top: 1rem">
                                                            <ul id="validation_error">
                                                            
                                                            </ul>
                                                        </div>
                                                        

                                                        <div class="addtl--deets row">

                                                            <?php 
                                                                $collection = collect($chart_account)->map(function ($item) {
                                                                    return $item['chart_accounts_particulars'];
                                                                })->collapse();
                                                                

                                                                $account = $transaction['direction'] == 'credit' ? $collection->where('chart_accounts_particulars_code', '610')->values()->first() : ($transaction['direction'] == 'debit' ? $collection->where('chart_accounts_particulars_code', '800')->values()->first() : '');
                                                                
                                                            ?>

                                                            <div class="col-xl-4 col-lg-4 col-md-4">
                                                                <div class="form-input--wrap">
                                                                    <label for="client_name" class="form-input--question" id="payment_option_label_{{ $k }}">
                                                                        {{ $transaction['direction'] == 'credit' ? 'Received as' : ($transaction['direction'] == 'debit' ? 'Spent as' : '') }}
                                                                    </label>
                                                                    <select class="form-input--dropdown" id="payment_option_{{ $k }}" name="payment_option_{{ $k }}" onchange="paymentOptions('{{ $k }}', '{{ json_encode($account) }}', '{{ !empty($transaction) ? json_encode($transaction)  : '' }}', '{{ !empty($tax_rates) ? json_encode($tax_rates)  : '' }}', '{{ !empty($chart_account) ? json_encode($chart_account) : '' }}', '{{ !empty($invoice_items) ? json_encode($invoice_items) : '' }}')">
                                                                        <option value="direct_payment">Direct Payment</option>
                                                                        <option value="pre_payment">Pre Payment</option>       
                                                                        <option value="over_payment">Over Payment</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="col-xl-4 col-lg-4 col-md-4">
                                                                <div class="form-input--wrap" style="margin-bottom: 0px;">
                                                                    <label for="client_name" class="form-input--question">
                                                                        Recipient's Name
                                                                    </label>
                                                                </div>
                                                                
                                                                <div class="form--inputbox recentsearch--input row">
                                                                    <div class="searchRecords col-12">
                                                                        <input type="text" onkeyup="getClient('{{$k}}',this)" id="client_name_{{ $k }}" name="client_name_{{ $k }}" required class="form-control" placeholder="Search Client Name" aria-label="Client Name" aria-describedby="button-addon2" autocomplete="off"  value="{{ !empty($transaction['sub_class']) ? $transaction['sub_class']['title'] : '' }}" >
                                                                    </div>
                                                                </div>
                                                                <div class="form--recentsearch clientname row">
                                                                    <div class="col-12">
                                                                        <div class="form--recentsearch__result"><p>
                                                                            <ul>
                                                                                @if (empty($exp_clients))
                                                                                    <li>You dont have any clients at this time</li>
                                                                                @else
                                                                                    @php($counter = 0)
                                                                                    @foreach ($exp_clients as $ec)
                                                                                        @php($counter ++)
                                                                                        <li>
                                                                                            <button type="button" class="dcc_click" data-myid="{{ $counter }}" onclick="appendClient('{{$k}}', this, 'client_name_{{ $k }}')">
                                                                                                <span id="data_name_{{ $counter }}">{{ $ec['client_name'] }}</span>
                                                                                            </button>
                                                                                        </li>
                                                                                    @endforeach
                                                                                @endif

                                                                                <li class="add--newactclnt">
                                                                                    <label for="savethisrep">
                                                                                        <input type="checkbox" id="savethisrep" name="savethisrep" value="yes" class="form-check-input" {{ !empty($form['save_client']) ? 'checked' : '' }}>
                                                                                        <div class="option--title">
                                                                                            Add as a new active client?
                                                                                            <span>Note: When the name is existing it will overide the old one.</span>
                                                                                        </div>
                                                                                    </label>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-xl-4 col-lg-4 col-md-4">
                                                                <div class="form-input--wrap">
                                                                    <label class="form-input--question" for="issue_date_{{ $k }}">Date <span>DD/MM/YYYY</span></label>
                                                                    <div class="date--picker row">
                                                                        <div class="col-12">
                                                                            <input type="text" id="issue_date_{{ $k }}" name="issue_date_{{ $k }}" required class="form-control" value="{{!empty($transaction) ?  date('d/m/Y', strtotime($transaction['post_date']))  : ''}}" onclick="getDate('{{$k}}')">
                                                                            @error('issue_date_{{ $k }}')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="row">
                                                            <div class="col-xl-12">
                                                                <div class="table-responsive" id="payment_option_table_{{ $k }}">
                                                                    <table class="CT_table" id="partstable_{{ $k }}">
                                                                        <thead id="head_{{ $k }}_directpayment">
                                                                            <tr>
                                                                                <th scope="col" id="item_header_{{ $k }}">Item</th>
                                                                                <th scope="col" id="qty_header_{{ $k }}">QTY</th>
                                                                                <th scope="col">Description</th>
                                                                                <th scope="col" style="min-width: 150px">Unit Price</th>
                                                                                <th scope="col">Account</th>
                                                                                <th scope="col" style="min-width: 200px">Tax Rate</th>
                                                                                <th scope="col">Amount</th>
                                                                                <th scope="col">&nbsp;</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody id="body_{{ $k }}_directpayment">
                                                                            <tr id="invoice_parts_row_id_{{ $k }}_0" class="invoice_parts_form_cls_{{ $k }}_0">
                                                                                <td id="row_item_{{ $k }}_0">
                                                                                    <input autocomplete="off" data-toggle="dropdown" type="text" id="invoice_parts_name_code_{{ $k }}_0" name="invoice_parts_name_code_{{ $k }}_0" onkeyup="searchInvoiceparts(this)" value="">
                                                                                    <input type="hidden" id="invoice_parts_code_{{ $k }}_0" name="invoice_parts_code_{{ $k }}_0" value="">
                                                                                    <input type="hidden" id="invoice_parts_name_{{ $k }}_0" name="invoice_parts_name_{{ $k }}_0" value="">

                                                                                    <ul class="dropdown-menu invoice-expenses--dropdown" id="invoice_item_list_{{ $k }}_0">
                                                                                        
                                                                                        @if (!empty($invoice_items))
                                                                                            @php($counter = 0)
                                                                                            @foreach ($invoice_items as $item)
                                                                                                @php($counter ++)
                                                                                                <li>
                                                                                                    <button type="button" class="invoice_item" data-myid="{{ $counter }}" onclick="getInvoiceItemsById('{{ $item['id'] }}', '{{ $k }}', 0)">
                                                                                                        <span id="data_name_{{ $counter }}">{{ $item['invoice_item_code'] }} : {{ $item['invoice_item_name'] }}</span>
                                                                                                        <input type="hidden" id="invoice_item_id_{{ $counter }}" name="invoice_item_id" value="{{ $item['id'] }}">
                                                                                                    </button>
                                                                                                </li>
                                                                                            @endforeach
                                                                                        @endif
                                                                                    </ul>
                                                                                </td>
                                                                                
                                                                                <td>
                                                                                    <input id="invoice_parts_quantity_{{ $k }}_0" name="invoice_parts_quantity_{{ $k }}_0" type="number" onchange="transactionCalculation('{{ $k }}',0)" value="1" required>
                                                                                </td>
                                                                                <td>
                                                                                    <textarea id="invoice_parts_description_{{ $k }}_0" name="invoice_parts_description_{{ $k }}_0" class="autoresizing" >{{ !empty($transaction) ? $transaction['description'] : '' }}</textarea>
                                                                                </td>
                                                                                <td>
                                                                                    <input id="invoice_parts_unit_price_{{ $k }}_0" name="invoice_parts_unit_price_{{ $k }}_0" type="float" value="{{ !empty($transaction) ? number_format($transaction['amount'], 2) : '' }}" onchange="transactionCalculation('{{ $k }}',0);" onfocusin="removeComma('invoice_parts_unit_price_{{ $k }}_0');" onfocusout="addComma('invoice_parts_unit_price_{{ $k }}_0');">
                                                                                    <input type="hidden" id="invoice_parts_gst_{{ $k }}_0" name="invoice_parts_gst_{{ $k }}_0" value="">
                                                                                </td>
                                                                                <td>
                                                                                    <input autocomplete="off" data-toggle="dropdown" type="text" id="invoice_parts_chart_accounts_{{ $k }}_0" name="invoice_parts_chart_accounts_{{ $k }}_0"  value="" required>
                                                                                    <input type="hidden" id="invoice_parts_chart_accounts_code_{{ $k }}_0" name="invoice_parts_chart_accounts_code_{{ $k }}_0" value="">
                                                                                    <input type="hidden" id="invoice_parts_chart_accounts_name_{{ $k }}_0" name="invoice_parts_chart_accounts_name_{{ $k }}_0" value="">

                                                                                    <input type="hidden" id="invoice_parts_chart_accounts_parts_id_{{ $k }}_0" name="invoice_parts_chart_accounts_parts_id_{{ $k }}_0" value="">

                                                                                    <ul class="dropdown-menu invoice-expenses--dropdown" id="invoice_chart_account_list_{{ $k }}_0">
                                                                                        <li id="add_new_invoice_chart_account_{{ $k }}_0" class="add-new--btn">
                                                                                            <a href="#" class="pop-model" data-toggle="modal" data-target="#newAddAccountModal" onclick="openNewAddAccountPopUpModelReconcile('{{ $k }}',0)"><i class="fa-solid fa-circle-plus"></i>New Account</a>
                                                                                        </li>
                                                                                        @if (!empty($chart_account))
                                                                                        @php ($counter = 0)
                                                                                        @foreach ($chart_account as $item)
                                                                                            <li class="accounts-group--label">{{$item['chart_accounts_name']}}</li>
                                                                                                
                                                                                            @foreach ($item['chart_accounts_particulars'] as $particulars)
                                                                                                <?php
                                                                                                    $user = array_search($particulars['chart_accounts_type_id'], array_column($item['chart_accounts_types'], 'id'));
                                                                                                ?>
                                                                                                <li>
                                                                                                    <button type="button" class="invoice_item" data-myid="{{ $counter }}" onclick="addInvoiceChartAccount('{{ $particulars['id'] }}', '{{$k}}', 0)">
                                                                                                        <span id="data_name_{{ $counter }}">{{ $particulars['chart_accounts_particulars_code'] }} - {{ $particulars['chart_accounts_particulars_name'] }} </span>
                                                                                                        <input type="hidden" id="invoice_parts_chart_accounts_type_id_{{ $k }}_0" name="invoice_parts_chart_accounts_type_id_{{ $k }}_0" value="{{$item['chart_accounts_types'][$user]['chart_accounts_type']}}">
                                                                                                        <input type="hidden" id="invoice_item_id_{{ $counter }}" name="invoice_item_id" value="{{ $particulars['id'] }}">
                                                                                                    </button>
                                                                                                </li>
                                                                                            @endforeach
                                                                                        @endforeach
                                                                                    @endif
                                                                                    </ul>
                                                                                </td>
                                                                                <td id="invoice_parts_tax_rate_td_{{ $k }}">

                                                                                    @if(!empty($tax_rates))
                                                                                        <input type="hidden" name="invoice_parts_tax_rate_id_{{ $k }}_0" id="invoice_parts_tax_rate_id_{{ $k }}_0" value="">
                                                                                        <input type="hidden" name="invoice_parts_tax_rate_name_{{ $k }}_0" id="invoice_parts_tax_rate_name_{{ $k }}_0" value="">
                                                                                        <div class="form-input--wrap">
                                                                                            <div class="row">
                                                                                                <div class="col-12 for--tables">
                                                                                                    <select class="form-input--dropdown" id="invoice_parts_tax_rate_{{ $k }}_0" name="invoice_parts_tax_rate_{{ $k }}_0" onchange="transactionCalculation('{{ $k }}',0); getTaxRates('{{ $k }}',0);" required style="display : {{!empty($transaction['default_tax']) && $transaction['default_tax']=="no_tax" ? 'none' : 'block' }}">
                                                                                                        <option selected value="0#|#0">Tax Rate Option</option>    
                                                                                                        @foreach($tax_rates as $tax_rate)
                                                                                                            <option id="{{$tax_rate['id'].'_'.$k.'_'.'0'}}" value="{{$tax_rate['id'].'#|#'.$tax_rate['tax_rates']}}" >{{$tax_rate['tax_rates_name']}}</option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    @endif
                                                                                </td>
                                                                                <td>
                                                                                    <input class="input--readonly" readonly id="invoice_parts_amount_{{ $k }}_0" name="invoice_parts_amount_{{ $k }}_0" type="float" value="{{ !empty($transaction) ? number_format($transaction['amount'], 2) : '' }}">
                                                                                </td>
                                                                                <td class="tableOptions">
                                                                                    <button class="btn sumb--btn delepart" type="button" onclick="deleteInvoiceParts('{{ $k }}', 0)" ><i class="fas fa-trash-alt"></i></button>
                                                                                </td>
                                                                            </tr>
                                                                            <tr class="add--new-line add--new-line_{{ $k }}" id="new_line_{{ $k }}">
                                                                                <td colspan="8">
                                                                                    <button class="btn sumb--btn" type="button" onclick="addNewLine('{{ $k }}')" id="addnewline_{{ $k }}_0"><i class="fa-solid fa-circle-plus"></i>Add New Line</button> 
                                                                                </td>
                                                                            </tr>
                                                                        
                                                                            <tr class="invoice-separator">
                                                                                <td colspan="8">&nbsp;</td>
                                                                            </tr>

                                                                            <tr class="expenses-tax--option" id="tax_options_{{ $k }}">
                                                                                <td colspan="4">&nbsp;</td>
                                                                                <td>Tax Option</td>
                                                                                <td colspan="3">
                                                                                    <div class="form-input--wrap">
                                                                                        <div class="col-12 for--tables">
                                                                                            <select name="invoice_default_tax_{{ $k }}" id="invoice_default_tax_{{ $k }}" class="form-input--dropdown" onchange="transactionCalculation('{{ $k }}', 0)">
                                                                                                <option value="tax_inclusive" >Tax Inclusive</option>
                                                                                                <option value="tax_exclusive">Tax Exclusive</option>
                                                                                                <option value="no_tax">No Tax</option>
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>

                                                                            <tr class="invoice-total--subamount">
                                                                                <td colspan="4" rowspan="4">
                                                                                    &nbsp;
                                                                                </td>
                                                                                <td>Subtotal</td>
                                                                                <td colspan="3">
                                                                                    <input readonly required id="sub_total_amount_{{ $k }}" step="any" name="sub_total_{{ $k }}" type="float" value="{{ !empty($transaction) ? number_format($transaction['amount'], 2) : '' }}">
                                                                                </td>
                                                                            </tr>

                                                                            <tr class="invoice-total--gst" id="total_gst_row_{{$k}}">
                                                                                <td>Total GST</td>
                                                                                <td colspan="3">
                                                                                    <input type="float" required readonly step="any" name="total_gst_{{ $k }}" id="total_gst_{{ $k }}" value="">
                                                                                </td>
                                                                            </tr>

                                                                            <tr class="invoice-total--amountdue">
                                                                                <td><strong>Total</strong></td>
                                                                                <td colspan="3">
                                                                                    <strong id="grandtotal"></strong>
                                                                                    <input type="float" required readonly step="any" class="grandtotal" name="total_amount_{{ $k }}" id="total_amount_{{ $k }}" value="{{ !empty($transaction) ? number_format($transaction['amount'], 2) : '' }}">
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-navigation">
                                                            <div class="form-navigation--btns row">
                                                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12 col-12">
                                                                    <button type="button" id="" onclick="transactionFormCancel('{{ $k }}')" class="btn sumb--btn cancel--btn"><i class="fa-solid fa-xmark"></i>Cancel</button>
                                                                </div> 
                                                                <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 col-xs-12 col-12">
                                                                    <input type="hidden" name="transaction_index" value="{{ $k }}">
                                                                    <button value="save_transaction_{{ $k }}" name="save_transaction_{{ $k }}" style="float: right;" type="button" class="btn sumb--btn" onclick="saveTransaction('{{ $k }}', this)"><i class="fa-solid fa-floppy-disk"></i> Save</button>
                                                                    <button id="createTransactionFormClear_{{ $k }}" style="float: right;" type="reset" class="btn sumb--btn reset--btn"><i class="fa fa-ban"></i> Clear Expense</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" id="transaction_type_{{ $k }}" name="transaction_type_{{ $k }}" value="{{ $transaction['direction'] }}">
                                                        <input type="hidden" id="reconcile_transaction_part_ids_{{ $k }}" name="reconcile_transaction_part_total_count_{{ $k }}" value="[0]" />
                                                        <input type="hidden" id="bank_transaction_amount_{{ $k }}" name="bank_transaction_amount_{{ $k }}" value="{{ abs($transaction['amount']) }}">
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 order-5" hidden="hidden" id="transactions_div_{{ $k }}">

                                            <!-----------Find Matching Transactions----------->
                                            <div class="findMatchingTra_wrap">

                                                <h5><i class="fa-solid fa-magnifying-glass"></i>Find & Select Matching Transactions </h5>
                                                <div class="scrollable_wrap" id="transactions_{{ $k }}" ></div>

                                                <form id="reconcile_transactions_{{ $k }}" action="/" method="post" enctype="multipart/form-data">   
                                                    
                                                    <div class="fmselectedtransac_wrap">
                                                        <h5 class="m-b-20"><i class="fa-solid fa-check-to-slot"></i>Selected transaction/s. You can select multiple transactions, as needed. </h5>
                                                        <div id="selected_transactions_table_{{ $k }}"></div>
                                                    </div>

                                                    <div class="fmcalculation_wrap">
                                                        <h5><i class="fa-solid fa-chart-simple"></i>Your selected transaction/s must match the money <span id="spent_receive_money_header_text_{{ $k }}"></span>. Make adjustments, as needed.</h5>
                                                       
                                                        <div class="cal_transactions_table_{{ $k }} calc_matching">
                                                            <div class="row row-flex">
                                                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                                                                    <div class="row row-flex">
                                                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                                                            <div class="transactiontype_wrap">
                                                                                <div class="wrap">
                                                                                    <div class="amount" id="spent_receive_money_text_{{ $k }}"></div>
                                                                                    Money <span id="sub_money_text_{{ $k }}"></span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                                                            <div class="subtotal_wrap">
                                                                                <div class="wrap">
                                                                                    <input type="float" readonly id='transaction_subtotal_{{ $k }}' name="transaction_subtotal_{{ $k }}" value='0'>
                                                                                    Subtotal
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <div class="minoradjustment_wrap">
                                                                                <div class="wrap">
                                                                                    Minor adjustment <input type="float" id='transaction_minor_adjustment_{{ $k }}' onkeyup="minorAdjustment('{{ $k }}')" value='0'>

                                                                                    <div class="desc">
                                                                                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus vitae tellus porttitor, varius urna ac, fringilla magna. Fusce sed mauris facilisis, lacinia justo et, cursus felis.
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-12">
                                                                    <div class="currentadjustment_wrap">
                                                                        <div class="wrap">
                                                                            <input type="float" readonly id='spent_receive_money_{{ $k }}' name="spent_receive_money_{{ $k }}" value='0'>
                                                                            Current Adjustment
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-12">
                                                                    <div class="amountmatch_wrap">
                                                                        <div class="wrap">
                                                                            <i class="fa-regular fa-face-laugh-wink"></i>
                                                                            <input type="float" readonly id="total_matched_{{ $k }}" value='0'>
                                                                            <span id="total_matched--text_{{ $k }}"></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                
                                                            </div>
                                                            
                                                        </div>
                                                        
                                                    </div>

                                                    
                                                </form>


                                                <div class="form-navigation">
                                                    <div class="form-navigation--btns row">
                                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 col-12">
                                                            <button type="button" id="" onclick="reconcileTransactionFormCancel('{{ $k }}')" class="btn sumb--btn cancel--btn"><i class="fa-solid fa-xmark"></i>Cancel</button>
                                                        </div> 
                                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 col-12">
                                                            <button id="reconcile_matching_transactions_{{ $k }}" onclick="reconcileTransactions('{{ $k }}', '{{$transaction['id']}}', 0, 1, '{{substr($transaction['post_date'],0,10)}}')" class="btn sumb--btn"><i class="fa-solid fa-code-merge" style="margin-right: 5px"></i>Reconcile</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                            <!-----Find Matching Transactions---------->
                                            
                                        </div>


                                    </div>
                                    
                                </div>
                            @php($k++) 
                            @endforeach

                        @endif
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
                                                Display: {{$bank_transactions['per_page']}} Items
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
            </div>
        </div>
    </div>
</div>


@include('includes.footer')

<script>
    var transaction_ids = [];
    var payment_option = '';
   $(document).ready(function () {
    
        let $body = $(this);
        $("#bank_account_id").on("change", function(){
            id = $("#bank_account_id").val();
            if ('URLSearchParams' in window) {
                var searchParams = new URLSearchParams(window.location.search);
                searchParams.forEach((value, key) => {
                    if(key == 'bank_account_id'){
                        searchParams.delete(key);
                    }
                });
                searchParams.set('bank_account_id', id);
                searchParams.set('page', 1);
                window.location.search = searchParams.toString();
            }
        });
    });

    $(document).ready(function () {

        $("#date").datepicker();
        $("#due_date").datepicker();

        $(".sumb--recentlogdements.matchBox").hide();

        $('.reconcileTransactions').children().each(function () {
            if($(this).attr('name') == "createTransactionForm"){
                $(this).hide();
            }
        });

        $('.transactions').children().each(function () {
            if($(this).attr('name') == "createtransaction"){
                $(this).hide();
            }
        });
    });
    
    function openNewAddAccountPopUpModelReconcile(id, rowIndex)
    {
        $("#invoice_chart_accounts_type_id").val('');
        // $("#invoice_account_part_row_id").val('');

        $("#invoice_account_transaction_row_id").val('');
        $("#invoice_account_transaction_row_id").val(id);

        $("#invoice_account_part_row_id").val('');
        $("#invoice_account_part_row_id").val(rowIndex);

        $("#invoice_chart_accounts_code").val('');
        $("#invoice_chart_accounts_name").val('');
        $("#invoice_chart_accounts_description").val('');
        $("#invoice_chart_accounts_tax_rate").val('');
    
        $("#add_account_from").val('');
        $("#invoice_item_chart_accounts_parts").val('');
        $("#invoice_item_chart_accounts_parts_id").val('');

        $('#newAddAccountModal').modal({
            backdrop: 'static',
            keyboard: true, 
            show: true
        });
    }


    function addNewAccountReconcile(id, rowIndex)
    {
        var id = $("#"+id).val();
        var rowIndex = $("#"+rowIndex).val();

        const invoice_chart_accounts_tax_id_val = $("#invoice_chart_accounts_tax_rate").val().split("#|#");
        
        var post_data = {
            chart_accounts_type_id: $("#invoice_chart_accounts_type_id").val(),
            // invoice_chart_accounts_type_id: $("#invoice_chart_accounts_type_id").val(),
            chart_accounts_parts_code: $("#invoice_chart_accounts_code").val(),
            chart_accounts_parts_name: $("#invoice_chart_accounts_name").val(),
            chart_accounts_description: $("#invoice_chart_accounts_description").val(),
            // invoice_chart_accounts_tax_rate: invoice_chart_accounts_tax_id_val[0],
            chart_accounts_tax_rate: invoice_chart_accounts_tax_id_val[0],
            chart_accounts_id: $("#invoice_chart_accounts_id_"+$("#invoice_chart_accounts_type_id").val()).val(),
        };
        if(post_data.chart_accounts_tax_rate && post_data.chart_accounts_type_id && post_data.chart_accounts_parts_code && post_data.chart_accounts_parts_name && post_data.chart_accounts_description && post_data.chart_accounts_tax_rate ){
            $("#invoice_chart_accounts_code_error").removeClass('alert alert-danger');
            $("#invoice_chart_accounts_code_error").html('');

            $("#invoice_chart_accounts_name_error").removeClass('alert alert-danger');
            $("#invoice_chart_accounts_name_error").html('');

            $("#invoice_chart_accounts_type_error").removeClass('alert alert-danger');
            $("#invoice_chart_accounts_type_error").html('');

            $("#invoice_chart_accounts_tax_rate_error").removeClass('alert alert-danger');
            $("#invoice_chart_accounts_tax_rate_error").html('');

            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });
            $.ajax({
                method: "POST",
                url: "/chart-account/create",
                data: post_data,
                success : function(response){
                        $("#invoice_chart_account_list_"+id+'_'+rowIndex).empty();
                        var counter = 0;
                        $("#invoice_chart_account_list_"+id+'_'+rowIndex).append('<li id="add_new_invoice_chart_account_'+id+'_'+rowIndex+'" class="add-new--btn"><a href="#" data-toggle="modal" data-target="#newAddAccountModal" onclick=openNewAddAccountPopUpModelReconcile('+id+','+rowIndex+')><i class="fa-solid fa-circle-plus"></i>New Account</a></li')

                        $.each(response.data,function(key,value){
                            $("#invoice_chart_account_list_"+id+'_'+rowIndex).append(
                                '<li class="accounts-group--label">'+value['chart_accounts_name']+'</li>'
                            );
                            $.each(value['chart_accounts_particulars'],function(k,val){
                                $("#invoice_chart_account_list_"+id).append('\n\<li><div style="padding: 10px;border-bottom: 1px solid lightgrey">\n\
                                <button type="button" class="invoice_item" data-myid="'+counter+'" onclick=addInvoiceChartAccount("'+encodeURI(val['id'])+'","'+id+'","'+rowIndex+'");>\n\
                                <span id="data_name_'+counter+'">'+val['chart_accounts_particulars_code']+' - '+val['chart_accounts_particulars_name']+'</span>\n\
                                                            </button></div></li>');
                            });
                        });
                        
                        if($("#invoice_parts_tax_rate_"+id+'_'+rowIndex+" option:selected").attr('id')){
                            const selected_option = $("#invoice_parts_tax_rate_"+id+'_'+rowIndex+" option:selected").attr('id');
                            $("#"+selected_option).removeAttr("selected");
                        }

                        $("#invoice_parts_chart_accounts_parts_id_"+id+'_'+rowIndex).val('');
                        $("#invoice_parts_chart_accounts_"+id+'_'+rowIndex).val('');
                        $("#invoice_parts_chart_accounts_code_"+id+'_'+rowIndex).val('');
                        $("#invoice_parts_chart_accounts_name_"+id+'_'+rowIndex).val('');
                        $("#invoice_parts_tax_rate_"+id+'_'+rowIndex).val('');
                        $("#invoice_parts_tax_rate_id_"+id+'_'+rowIndex).val('');

                        $("#invoice_parts_chart_accounts_parts_id_"+id+'_'+rowIndex).val(response.id);

                        $("#invoice_parts_chart_accounts_"+id+'_'+rowIndex).val(post_data.chart_accounts_parts_code+' - '+post_data.chart_accounts_parts_name);
                        $("#invoice_parts_chart_accounts_name_"+id+'_'+rowIndex).val(post_data.chart_accounts_parts_name);
                        $("#invoice_parts_chart_accounts_code_"+id+'_'+rowIndex).val(post_data.chart_accounts_parts_code);

                        // var tax_rate_id = $("#invoice_chart_accounts_tax_rate").val();
                        
                        $("#invoice_parts_tax_rate_id_"+id+'_'+rowIndex).val(invoice_chart_accounts_tax_id_val[0]);
                        
                        // $("#invoice_parts_tax_rate_"+id+ " option[id='"+tax_rate_id+"_"+id+"']").attr("selected", "selected");
                        $("#invoice_parts_tax_rate_"+id+'_'+rowIndex).val($("#invoice_chart_accounts_tax_rate").val()).change();
                        
                        $(".close").click();
                        
                },
                error : function(error){ 
                    $("#invoice_chart_accounts_code_error").addClass('alert alert-danger');
                    $("#invoice_chart_accounts_code_error").html(error.responseJSON.message);
                }
            });
        }else{
            $("#invoice_chart_accounts_code_error").addClass('alert alert-danger');
            $("#invoice_chart_accounts_code_error").html('Code field is required');

            $("#invoice_chart_accounts_name_error").addClass('alert alert-danger');
            $("#invoice_chart_accounts_name_error").html('Name field is required');

            $("#invoice_chart_accounts_type_error").addClass('alert alert-danger');
            $("#invoice_chart_accounts_type_error").html('Account Type field is required');

            $("#invoice_chart_accounts_tax_rate_error").addClass('alert alert-danger');
            $("#invoice_chart_accounts_tax_rate_error").html('Tax rate field is required');
        }
    }


    function paymentOptions(rowId, filtered_account, transaction, tax_rates, chart_account, invoice_items)
    {
        var filtered_account = JSON.parse(filtered_account);
        var tax_rates = JSON.parse(tax_rates);
        var transaction = JSON.parse(transaction);
        var invoice_items = JSON.parse(invoice_items);
        var chart_account = JSON.parse(chart_account);
        payment_option = $("#payment_option_"+rowId).val();
        if(($("#payment_option_"+rowId).val()) == 'over_payment')
        {
            const rowIndex = [0];
            $("#head_"+rowId+"_directpayment").remove();
            $("#body_"+rowId+"_directpayment").remove();

            $("#partstable_"+rowId).append(
                '<thead id="head_'+rowId+'_overpayment"><tr>\
                        <th scope="col" style="width:200px; min-width:200px;">Description</th>\
                        <th scope="col" style="width:80px; min-width:80px;">Amount</th>\
                        <th scope="col" style="width:170px; min-width:205px;">Account</th>\
                        <th scope="col" style="width:205px; min-width:170px;">Tax Rate</th>\
                        <th scope="col" style="width:70px; min-width:70px;">Amount</th>\
                    </tr>\
                </thead>\
                <tbody id="body_'+rowId+'_overpayment">\
                    <tr id="invoice_parts_row_id_'+rowId+'_0" class="invoice_parts_form_cls_'+rowId+'_0">\
                        <td><textarea id="invoice_parts_description_'+rowId+'_0" name="invoice_parts_description_'+rowId+'_0" class="autoresizing" style="width: 100%;">'+transaction['description']+'</textarea> </td>\
                        <td><input readonly id="invoice_parts_unit_price_'+rowId+'_0" name="invoice_parts_unit_price_'+rowId+'_0" type="float" value='+transaction['amount']+'>\
                        </td>\
                        <td><input readonly type="text" id="invoice_parts_chart_accounts_'+rowId+'_0" name="invoice_parts_chart_accounts_'+rowId+'_0"  value="'+filtered_account['chart_accounts_particulars_name']+ " - " +filtered_account['chart_accounts_particulars_code']+'" required>\
                            <input type="hidden" id="invoice_parts_chart_accounts_parts_id_'+rowId+'_0" name="invoice_parts_chart_accounts_parts_id_'+rowId+'_0" value="'+filtered_account['id']+'">\
                        </td>\
                        <td id="invoice_parts_tax_rate_td_'+rowId+'">\
                            <input id="invoice_parts_tax_rate_'+rowId+'_0" name="invoice_parts_tax_rate_'+rowId+'_0" type="text" value="'+tax_rates[0]['tax_rates_name']+'">\
                            <input type="hidden" name="invoice_parts_tax_rate_id_'+rowId+'_0" id="invoice_parts_tax_rate_id_'+rowId+'_0" value='+tax_rates[0]['id']+'>\
                        </td>\
                        <td>\
                            <input class="input--readonly" readonly id="invoice_parts_amount_'+rowId+'_0" name="invoice_parts_amount_'+rowId+'_0" type="float" value='+transaction['amount']+'>\
                        </td>\
                    </tr>\
                    <tr class="invoice-separator">\
                        <td colspan="7">&nbsp;</td>\
                    </tr>\
                    <tr class="expenses-tax--option" id="tax_options_'+rowId+'" style="display:none">\
                        <td colspan="4">&nbsp;</td>\
                        <td>Tax Option</td>\
                        <td colspan="2">\
                            <div class="form-input--wrap">\
                                <div class="col-12 for--tables">\
                                    <select name="invoice_default_tax_'+rowId+'" id="invoice_default_tax_'+rowId+'" class="form-input--dropdown" onchange=transactionCalculation("'+rowId+'", 0)>\
                                        <option value="no_tax" selected>No Tax</option>\
                                    </select>\
                                </div>\
                            </div>\
                        </td>\
                    </tr>\
                    <tr class="invoice-total--subamount">\
                        <td colspan="2" rowspan="2">\
                            &nbsp;\
                        </td>\
                        <td>Subtotal</td>\
                        <td colspan="3">\
                            <input readonly required id="sub_total_amount_'+rowId+'" step="any" name="sub_total_'+rowId+'" type="float" value='+transaction['amount']+'>\
                        </td>\
                    </tr>\
                    <tr class="invoice-total--amountdue">\
                        <td><strong>Total</strong></td>\
                        <td colspan="3">\
                            <strong id="grandtotal"></strong>\
                            <input type="float" required readonly step="any" class="grandtotal" name="total_amount_'+rowId+'" id="total_amount_'+rowId+'" value='+transaction['amount']+'>\
                        </td>\
                    </tr>\
                </tbody>'
            );
            $('#reconcile_transaction_part_ids_'+rowId).val(JSON.stringify(rowIndex));
        }

        if($("#payment_option_"+rowId).val() == 'direct_payment' || $("#payment_option_"+rowId).val() == 'pre_payment')
        {
            $("#head_"+rowId+"_overpayment").remove();
            $("#body_"+rowId+"_overpayment").remove();
           
            $("#head_"+rowId+"_directpayment").remove();
            $("#body_"+rowId+"_directpayment").remove();

            var counter = 0;
            const rowIndex = 0;
            const index = [0];
            var th = '';var td = '';
            var colcount = '';

            if($("#payment_option_"+rowId).val() == 'direct_payment')
            {
                th += ('<th scope="col" style="width:135px; min-width:135px;">Item</th>');
                td += ('<td id="row_item_'+rowId+'_0">\
                                <input autocomplete="off" data-toggle="dropdown" type="text" id="invoice_parts_name_code_'+rowId+'_0" name="invoice_parts_name_code_'+rowId+'_0" onkeyup="searchInvoiceparts(this)" value="">\
                                <input type="hidden" id="invoice_parts_code_'+rowId+'_0" name="invoice_parts_code_'+rowId+'_0" value="">\
                                <input type="hidden" id="invoice_parts_name_'+rowId+'_0" name="invoice_parts_name_'+rowId+'_0" value="">\
                                <ul class="dropdown-menu invoice-expenses--dropdown" id="invoice_item_list_'+rowId+'_0"></ul>\
                        </td>');
                colcount = 'direct_payment';
            } else {
                colcount = 'pre_payment';
            }

            $("#partstable_"+rowId).append('<thead id="head_'+rowId+'_directpayment">\
                        <tr>'+th+'\
                            <th scope="col">QTY</th>\
                            <th scope="col">Description</th>\
                            <th scope="col">Unit Price</th>\
                            <th scope="col">Account</th>\
                            <th scope="col">Tax Rate</th>\
                            <th scope="col">Amount</th>\
                            <th scope="col">&nbsp;</th>\
                        </tr>\
                    </thead>\
                    <tbody id="body_'+rowId+'_directpayment"></tbody>')

            getInvoiceTaxRates(rowId,0);
           
           if(payment_option == 'direct_payment')
           {
               getInvoiceItemList(rowId,0);
           }

           appendBody(rowId, rowIndex, td, transaction, chart_account);
           $('#reconcile_transaction_part_ids_'+rowId).val(JSON.stringify(index));
           
        }
    }


    function appendBody(rowId, rowIndex, td, transaction, chart_account)
    {

        var test = '';
        var counter=0;
        test+= '<ul class="dropdown-menu invoice-expenses--dropdown" id="invoice_chart_account_list_'+rowId+'_'+rowIndex+'">'
                +'<li id="add_new_invoice_chart_account_'+rowId+"_"+rowIndex+'" class="add-new--btn"><a href="#" data-toggle="modal" data-target="#newAddAccountModal" onclick=openNewAddAccountPopUpModelReconcile('+rowId+','+rowIndex+')><i class="fa-solid fa-circle-plus"></i>New Account</a></li>';     
                $.each(chart_account,function(key,value){
                    test+='<li class="accounts-group--label">'+value['chart_accounts_name']+'</li>'
                    $.each(value['chart_accounts_particulars'],function(k,val){
                        test+='<li><button type="button" class="invoice_item" data-myid="'+counter+'" onclick=addInvoiceChartAccount("'+encodeURI(val['id'])+'","'+rowId+'","'+rowIndex+'");>'
                            +'<span id="data_name_'+counter+'">'+val['chart_accounts_particulars_code']+' - '+val['chart_accounts_particulars_name']+'</span></button></li>'
                    });
                });
        test+='</ul>';

        $('#partstable_'+rowId).append('<tr id="invoice_parts_row_id_'+rowId+'_0" class="invoice_parts_form_cls_'+rowId+'_0">\
                        '+td+'\
                        <td>\
                            <input id="invoice_parts_quantity_'+rowId+'_0" name="invoice_parts_quantity_'+rowId+'_0" type="number" onchange="transactionCalculation('+rowId+',0)" value="1" required>\
                        </td>\
                        <td>\
                            <textarea id="invoice_parts_description_'+rowId+'_0" name="invoice_parts_description_'+rowId+'_0" class="autoresizing" >'+transaction['description']+'</textarea>\
                        </td>\
                        <td>\
                            <input id="invoice_parts_unit_price_'+rowId+'_0" name="invoice_parts_unit_price_'+rowId+'_0" type="float" value="'+transaction['amount']+'" onchange="transactionCalculation('+rowId+',0);" onfocusin=removeComma("invoice_parts_unit_price_'+rowId+'_0"); onfocusout=addComma("invoice_parts_unit_price_'+rowId+'_0");>\
                        </td>\
                        <td>\
                            <input autocomplete="off" data-toggle="dropdown" type="text" id="invoice_parts_chart_accounts_'+rowId+'_0" name="invoice_parts_chart_accounts_'+rowId+'_0"  value="" required>\
                            <input type="hidden" id="invoice_parts_chart_accounts_code_'+rowId+'_0" name="invoice_parts_chart_accounts_code_'+rowId+'_0" value="">\
                            <input type="hidden" id="invoice_parts_chart_accounts_name_'+rowId+'_0" name="invoice_parts_chart_accounts_name_'+rowId+'_0" value="">\
                            <input type="hidden" id="invoice_parts_chart_accounts_parts_id_'+rowId+'_0" name="invoice_parts_chart_accounts_parts_id_'+rowId+'_0" value="">\
                            '+test+'\
                        </td>\
                        <td id="invoice_parts_tax_rate_td_'+rowId+'">\
                            <input type="hidden" name="invoice_parts_tax_rate_id_'+rowId+'_0" id="invoice_parts_tax_rate_id_'+rowId+'_0" value="">\
                            <input type="hidden" name="invoice_parts_tax_rate_name_'+rowId+'_0" id="invoice_parts_tax_rate_name_'+rowId+'_0" value="">\
                            <div class="form-input--wrap">\
                                <div class="row">\
                                    <div class="col-12 for--tables">\
                                        <select class="form-input--dropdown" id="invoice_parts_tax_rate_'+rowId+'_0" name="invoice_parts_tax_rate_'+rowId+'_0" onchange="transactionCalculation('+rowId+',0); getTaxRates('+rowId+',0);" required style="display : '+(transaction['default_tax'] == "no_tax"  ? 'none' : 'block' )+' ">\
                                        </select>\
                                    </div>\
                                </div>\
                            </div>\
                        </td>\
                        <td>\
                            <input class="input--readonly" readonly id="invoice_parts_amount_'+rowId+'_0" name="invoice_parts_amount_'+rowId+'_0" type="float" value="'+transaction['amount']+'">\
                        </td>\
                        <td class="tableOptions">\
                            <button class="btn sumb--btn delepart" type="button" onclick=deleteInvoiceParts("'+rowId+'", 0) ><i class="fas fa-trash-alt"></i></button>\
                        </td>\
                    </tr>\
                    <tr class="add--new-line add--new-line_'+rowId+'" id="new_line_'+rowId+'">\
                        <td colspan="8">\
                            <button class="btn sumb--btn" type="button" onclick=addNewLine("'+rowId+'") id="addnewline_'+rowId+'_0"><i class="fa-solid fa-circle-plus"></i>Add New Line</button>\
                        </td>\
                    </tr>\
                    <tr class="invoice-separator">\
                        <td colspan="8">&nbsp;</td>\
                    </tr>\
                    <tr class="expenses-tax--option" id="tax_options_'+rowId+'">\
                        <td colspan="4">&nbsp;</td>\
                        <td>Tax Option</td>\
                        <td colspan="3">\
                            <div class="form-input--wrap">\
                                <div class="col-12 for--tables">\
                                    <select name="invoice_default_tax_'+rowId+'" id="invoice_default_tax_'+rowId+'" class="form-input--dropdown" onchange=transactionCalculation("'+rowId+'", 0)>\
                                        <option value="tax_inclusive" >Tax Inclusive</option>\
                                        <option value="tax_exclusive">Tax Exclusive</option>\
                                        <option value="no_tax">No Tax</option>\
                                    </select>\
                                </div>\
                            </div>\
                        </td>\
                    </tr>\
                    <tr class="invoice-total--subamount">\
                        <td colspan="4" rowspan="3">\
                            &nbsp;\
                        </td>\
                        <td>Subtotal</td>\
                        <td colspan="3">\
                            <input readonly required id="sub_total_amount_'+rowId+'" step="any" name="sub_total_'+rowId+'" type="float" value="'+transaction['amount']+'">\
                        </td>\
                    </tr>\
                    <tr class="invoice-total--gst" id="total_gst_row_'+rowId+'">\
                        <td>Total GST</td>\
                        <td colspan="2">\
                            <input type="float" required readonly step="any" name="total_gst_'+rowId+'" id="total_gst_'+rowId+'" value="">\
                        </td>\
                    </tr>\
                    <tr class="invoice-total--amountdue">\
                        <td><strong>Total</strong></td>\
                        <td colspan="3">\
                            <strong id="grandtotal"></strong>\
                            <input type="float" required readonly step="any" class="grandtotal" name="total_amount_'+rowId+'" id="total_amount_'+rowId+'" value="'+transaction['amount']+'">\
                        </td>\
                    </tr>');

    }


    function openOtherMatches(matchAmount,id, otherMathes){
        var otherMathes = JSON.parse(otherMathes);

        var matchListID = "match_list_"+id;
                    $(".match_"+id).show();
                    $(".match_"+id).append(
                    '<div class="matchList_wrap '+matchListID+'">'+
                    '<div class="header"><i class="zmdi zmdi-format-list-bulleted"></i>Other Matching Transactions</div>');

            otherMathes.forEach(element => {
            var expenseAmount = "";
            var invoiceAmount = "";
            if(element['transaction_type'] == "expense"){
                expenseAmount =  element["total_amount"];
            }else{
                invoiceAmount =  element["total_amount"];
            }
            $(".match_list_"+id).append(
                '<div class="otherMatchItem"><input class="otherMatchItemCheck" type="checkbox" id="otherMatchItem_'+element["transaction_number"]+'" value="'+element["transaction_number"]+'">'+
                                '<label class="otherMatchItemLabel" for="otherMatchItem_'+element["transaction_number"]+'">'+
                                    '<div class="row"><div class="descr col-9">'+
                                        '<div>'+element["client_name"]+'</div>'+
                                        '<div>'+element["issue_date"]+'</div>'+
                                    '</div><div class="deetsom col-3">'+
                                        '<div>'+expenseAmount+invoiceAmount+'<span>'+element['transaction_type']+'</span></div>'+
                                    '</div></div>'+
                                '</label></div>');
        });

        $(".match_list_"+id).append('</div>');

        var cancelMatchDiv = "cancelMatchDiv_" + id;

        $(".match_list_"+id).append(
            '<div id="'+cancelMatchDiv+'" class="cancelMatch_wrap">\n'+
                '<button onclick="cancelMatchDiv('+id+')"><i class="fa-solid fa-xmark"></i>Cancel</button>\n'+
            '</div>');
           
        $("#otherMatches_"+id).hide();

    }

    function cancelMatchDiv(id){
        $(".match_"+id).empty();
        $(".match_"+id).hide();
        // $(".match_"+id+ ".sumb--recentlogdements").empty();
        // $(".match_"+id+ ".sumb--recentlogdements").hide();
        $("#otherMatches_"+id).show();
    }

    function transactionFormCancel(id){

        $("#createTransactionForm_"+id).hide();

        const transactionReconcileBtn = document.getElementById('transactionReconcile_'+id);
        const createTransactionAndReconcile = document.getElementById('create_transaction_and_reconcile_'+id);
        
        var activeTab = $("#tabs_"+id).find(".active");
        var activeTabId = activeTab.attr('id');
        
        if(transactionReconcileBtn && activeTabId == 'match_tab_'+id)
        {
            const display = window.getComputedStyle(transactionReconcileBtn).visibility;
            if(display == 'hidden')
            {
                $("#transactionReconcile_"+id).attr("style", "visibility: visible");
            }
        }else if(createTransactionAndReconcile && activeTabId == 'create_tab_'+id)
        {
            const display = window.getComputedStyle(createTransactionAndReconcile).visibility;
            if(display == 'hidden')
            {
                $("#create_transaction_and_reconcile_"+id).attr("style", "visibility: visible");
            }
        }
    }

    function reconcileTransactionFormCancel(id)
    {
        transaction_ids = [];
        // $("#transactions_div_"+id).hide();
        $("#transactions_div_"+id).attr("hidden", true);

        const transactionReconcileBtn = document.getElementById('transactionReconcile_'+id);
        const createTransactionAndReconcile = document.getElementById('create_transaction_and_reconcile_'+id);
        var activeTab = $("#tabs_"+id).find(".active");
        var activeTabId = activeTab.attr('id');

        if(transactionReconcileBtn && activeTabId == 'match_tab_'+id)
        {
            const display = window.getComputedStyle(transactionReconcileBtn).visibility;
            
            if(display == 'hidden')
            {
                $("#transactionReconcile_"+id).attr("style", "visibility: visible");
            }
        }else if(createTransactionAndReconcile && activeTabId == 'create_tab_'+id)
        {
            const display = window.getComputedStyle(createTransactionAndReconcile).visibility;
            if(display == 'hidden')
            {
                $("#create_transaction_and_reconcile_"+id).attr("style", "visibility: visible");
            }
        }
    }

    function openTab(evt, tabName, id, matchCount) {
        if(tabName == ("Create_"+id)){
            $("#Match_"+id).hide();
            const transactionReconcileBtn = document.getElementById('transactionReconcile_'+id);
            const createTransactionAndReconcile = document.getElementById('create_transaction_and_reconcile_'+id);
            if(transactionReconcileBtn)
            {
                const display = window.getComputedStyle(transactionReconcileBtn).visibility;
                
                if(display == 'visible' || display == 'inline-block')
                {
                    $("#transactionReconcile_"+id).attr("style", "visibility: hidden");
                    if(createTransactionAndReconcile)
                    {
                        const display = window.getComputedStyle(createTransactionAndReconcile).visibility;
                        $("#create_transaction_and_reconcile_"+id).attr("style", "visibility: visible");
                    }
                    
                }
            }
            
        }else{
            const btn = document.getElementById('transactionReconcile_'+id);
            $("#create_transaction_and_reconcile_"+id).attr("style", "visibility: hidden");
            
            if(btn)
            {
                const display = window.getComputedStyle(btn).visibility;
                if(display == 'hidden')
                {
                    $("#transactionReconcile_"+id).attr("style", "visibility: visible");
                }
            }
            
            $("#Create_"+id).hide();
            $(".match_"+id+ ".sumb--recentlogdements").empty();
            $("#otherMatches_"+id).show();
            $("#createTransactionForm_"+id).hide();
        }

        // Declare all variables
        var i, tabcontent, tablinks;

        // Get all elements with class="tabcontent" and hide them
        tabcontent = document.getElementsByClassName("tabcontent_"+id);
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }

        // Get all elements with class="tablinks" and remove the class "active"
        tablinks = document.getElementsByClassName("tablinks_"+id);
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }

        // Show the current tab, and add an "active" class to the button that opened the tab
        document.getElementById(tabName).style.display = "block";
        evt.currentTarget.className += " active";

        $(".match_"+id+ ".sumb--recentlogdements").hide();
    }

    $(function() {
        $('.form--recentsearch').hide();
        $('li.add--newactclnt').hide();
    });

    function getDate(index)
    {
        $("#issue_date_"+index).datepicker({ dateFormat: 'dd/mm/yy' });
    }
    function getClient(index, obj)
    {
        $("#"+obj.id).on('keyup', function() {
            $("#create_transaction_and_reconcile_"+index).remove();
            $('.form--recentsearch.clientname').show();
            var value = $("#"+obj.id).val().toLowerCase();
           
            var clientList = $(".clientname .form--recentsearch__result li button");
            var matchedItems = $(".clientname .form--recentsearch__result li button").filter(function() {
                return $(this).text().toLowerCase().indexOf(value) > -1;              
            });
            
            if(value == ''){
                $('.form--recentsearch.clientname').hide();
                $('.clientname li.add--newactclnt input').prop('checked',false);
                $("#"+obj.id).removeClass('saveNewRecord');

            } else if($("#"+obj.id).hasClass('saveNewRecord')) {

                if(matchedItems.length !=0) {
                    $('.clientname li.add--newactclnt').hide();
                    $('.clientname li.add--newactclnt input').prop('checked',false);
                    $("#"+obj.id).removeClass('saveNewRecord');
                    matchedItems.toggle(true);
                } else {
                    $('.form--recentsearch.clientname').hide();
                }

            } else {

                clientList.toggle(false);
                matchedItems.toggle(true);

                if (matchedItems.length == 0) {
                    $('.clientname li.add--newactclnt').show();
                } else {
                    $('.clientname li.add--newactclnt input').prop('checked',false);
                    $('.clientname li.add--newactclnt').hide();
                }
            }
        });

        $('li.add--newactclnt input').on('click', function () {
            if(this.id == 'savethisrep') {
                if($('#savethisrep').is(':checked')){
                    $("#"+obj.id).addClass('saveNewRecord');
                } else {
                    $("#"+obj.id).removeClass('saveNewRecord');
                }
                $('.form--recentsearch.clientname').hide();

            } else {
                if($('#save_invdet').is(':checked')){
                    $('#invoice_name').addClass('saveNewRecord');
                } else {
                    $('#invoice_name').removeClass('saveNewRecord');
                }
                $('.form--recentsearch.invoicedeets').hide();
            }
        });
    }

    function appendClient(index, obj, id)
    {
        var clientid = $(obj).data('myid');
        var clientname = $("#data_name_"+clientid).html();
        
        $('#'+id).val(clientname);
        $('.form--recentsearch').hide();

        if($('#'+id).val() && $("#chart_accounts_"+index).val())
        {
            $("#transactionReconcile_"+index).attr("style", "display: none");
            $("#reconcil_transaction_ok_btn_"+index).append(
                '<button id="create_transaction_and_reconcile_'+index+'" onclick=createTransactionAndReconcile('+encodeURI(index)+') class="btn sumb--btn reconcile--btn" >Ok</button>'
            );
        }else{
            $("#create_transaction_and_reconcile_"+index).attr("style", "display: none");
        }
    }
    function addNewLine(i){
        var rowIndex = [0];
        rowIndex = $('#reconcile_transaction_part_ids_'+i).val();
        rowIndex = JSON.parse(rowIndex);
        if(rowIndex.length>0){
            rowIndex = parseInt(Math.max(...rowIndex))+1;
        }else{
            rowIndex = 1;
        }
        var ulId = JSON.parse($('#reconcile_transaction_part_ids_'+i).val())[0];
            var items_td = '';
            
            if(!payment_option)
            {
                payment_option ='direct_payment';
            }
            
            if(payment_option == 'direct_payment')
            {
                items_td = '<td><input autocomplete="off" data-toggle="dropdown" type="text" id="invoice_parts_name_code_'+i+'_'+rowIndex+'" name="invoice_parts_name_code_'+i+'_'+rowIndex+'" onkeyup="searchInvoiceparts(this)" value="" required>\
                <input type="hidden" id="invoice_parts_code_'+i+'_'+rowIndex+'" name="invoice_parts_code_'+i+'_'+rowIndex+'" value="">\
                <input type="hidden" id="invoice_parts_name_'+i+'_'+rowIndex+'" name="invoice_parts_name_'+i+'_'+rowIndex+'" value="">\
                    <ul class="dropdown-menu invoice-expenses--dropdown" id="invoice_item_list_'+i+'_'+rowIndex+'">\
                    </ul>\
                </td>';
            }
            
            $('#partstable_'+i+' tr.add--new-line_'+i).before('<tr class="invoice_parts_form_cls_'+i+'_'+rowIndex+'" id="invoice_parts_row_id_'+i+'_'+rowIndex+'" >\
                '+items_td+'\
                <td><input type="number" id="invoice_parts_quantity_'+i+'_'+rowIndex+'" name="invoice_parts_quantity_'+i+'_'+rowIndex+'" value="" onchange=transactionCalculation('+i+','+rowIndex+') required></td>\
                <td><textarea class="autoresizing" id="invoice_parts_description_'+i+'_'+rowIndex+'" name="invoice_parts_description_'+i+'_'+rowIndex+'" value="" required></textarea></td>\
                <td>\
                <input type="float" id="invoice_parts_unit_price_'+i+'_'+rowIndex+'" name="invoice_parts_unit_price_'+i+'_'+rowIndex+'" value="" onchange=transactionCalculation('+i+','+rowIndex+'); onfocusin=removeComma("invoice_parts_unit_price_'+i+'_'+rowIndex+'"); onfocusout=addComma("invoice_parts_unit_price_'+i+'_'+rowIndex+'"); required>\
                    <input type="hidden" id="invoice_parts_gst_'+i+'_'+rowIndex+'" name="invoice_parts_gst_'+i+'_'+rowIndex+'" value="">\
                </td>\
                <td>\
                    <input data-toggle="dropdown" type="text" id="invoice_parts_chart_accounts_'+i+'_'+rowIndex+'" name="invoice_parts_chart_accounts_'+i+'_'+rowIndex+'"  value="" required>\
                    <input type="hidden" id="invoice_parts_chart_accounts_code_'+i+'_'+rowIndex+'" name="invoice_parts_chart_accounts_code_'+i+'_'+rowIndex+'" value="">\
                    <input type="hidden" id="invoice_parts_chart_accounts_name_'+i+'_'+rowIndex+'" name="invoice_parts_chart_accounts_name_'+i+'_'+rowIndex+'" value="">\
                    <input type="hidden" id="invoice_parts_chart_accounts_parts_id_'+i+'_'+rowIndex+'" name="invoice_parts_chart_accounts_parts_id_'+i+'_'+rowIndex+'" value="">\
                    <ul class="dropdown-menu invoice-expenses--dropdown" id="invoice_chart_account_list_'+i+'_'+rowIndex+'">\
                    </ul>\
                </td>\
                <td>\
                    <input type="hidden" name="invoice_parts_tax_rate_id_'+i+'_'+rowIndex+'" id="invoice_parts_tax_rate_id_'+i+'_'+rowIndex+'" value="">\
                    <input type="hidden" name="invoice_parts_tax_rate_name_'+i+'_'+rowIndex+'" id="invoice_parts_tax_rate_name_'+i+'_'+rowIndex+'" value="">\
                    <div class="form-input--wrap">\
                        <div class="row">\
                            <div class="col-12 for--tables">\
                                <select class="form-input--dropdown" id="invoice_parts_tax_rate_'+i+'_'+rowIndex+'" name="invoice_parts_tax_rate_'+i+'_'+rowIndex+'" onchange="transactionCalculation('+i+','+rowIndex+');getTaxRates('+i+','+rowIndex+');" required  >\
                                </select>\
                            </div>\
                        </div>\
                    </div>\
                </td>\
                <td><input class="input--readonly" type="float" readonly id="invoice_parts_amount_'+i+'_'+rowIndex+'" name="invoice_parts_amount_'+i+'_'+rowIndex+'" value="" required>\n\
                </td><td class="tableOptions"><button class="btn sumb--btn delepart" type="button" onclick=deleteInvoiceParts('+i+','+rowIndex+')><i class="fas fa-trash-alt"></i></button></td></tr>');
        
        if(payment_option == 'direct_payment')
        {
            getInvoiceItemList(i,rowIndex);
        }
        
        getChartAccountsParticularsList(i, rowIndex);
        getInvoiceTaxRates(i,rowIndex);
        addOrRemoveInvoicePartsIds('add', i, rowIndex);
    }


    function addOrRemoveInvoicePartsIds(action_type, i, id){
        var rowIndex = [0];
        rowIndex = $('#reconcile_transaction_part_ids_'+i).val();
        rowIndex = JSON.parse(rowIndex);
        
        if(action_type == "add"){
            if(rowIndex.indexOf(id)<0){
                rowIndex.push(id);
            }
        }else{
            var index = rowIndex.indexOf(id);
            if (index > -1) {
                rowIndex.splice(index, 1);
            }
            $('#invoice_parts_row_id_'+i+'_'+id).remove();
            // $.each($('.invoice_parts_form_cls_'+i),function(k,v){
            //     var key=k+1;
            //     var rowIds=v.id.split('_');
            //     rowIds = parseInt(rowIds[4]);
                
            // })
        }
        
        $('#reconcile_transaction_part_ids_'+i).val(JSON.stringify(rowIndex));
    }

    function getInvoiceItemList(i,id){
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });
        $.ajax({
            method: "POST",
            url: "/invoice-items",
            // data: post_data,
            success:function(response){
                $("#invoice_item_list_"+id).empty();
                var counter = 0;

                $.each(response.data,function(key,value){
                    counter++;
                    $("#invoice_item_list_"+i+"_"+id).append('\n\<li>\n\
                        <button type="button" class="invoice_item" data-myid="'+counter+'" onclick=getInvoiceItemsById("'+encodeURI(value['id'])+'","'+i+'","'+id+'");>\n\
                        <span id="data_name_'+counter+'">'+value['invoice_item_code']+' : '+value['invoice_item_name']+'</span>\n\
                        <input type="hidden" id="invoice_item_id_'+counter+'" name="invoice_item_id" value="'+value['id']+'">\n\
                        </button></li>');
                });
            },
            error:function(error){ 
                alert(error.responseJSON.message);
            }
        });
    }

    function getInvoiceTaxRates(i,rowId){
        if( rowId >= 0 ){
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });
            $.ajax({
                method: "GET",
                url: "/invoice-tax-rates",
                success:function(response){                    
                    $("#invoice_parts_tax_rate_"+i+'_'+rowId).val('');
                    $("#invoice_parts_tax_rate_"+i+'_'+rowId).append('<option selected value="0#|#0">Tax Rate Option</option>');

                    $.each(response.data, function (key, value) {
                        $("#invoice_parts_tax_rate_"+i+'_'+rowId).append(
                            '<option id='+value['id']+"_"+i+"_"+rowId+' value='+value['id']+'#|#'+value['tax_rates']+'>'+value['tax_rates_name']+'</option>'
                        );
                    });
                },
                error:function(error){ 
                    alertBottom(null, error.responseJSON.message);
                }
            });
        }
    }

    function getTaxRates(i,rowId){
        if($("#invoice_parts_tax_rate_"+i+"_"+rowId+" option:selected").attr('id'))
        {
            const selected_option = $("#invoice_parts_tax_rate_"+i+"_"+rowId+" option:selected").attr('id');
            let selected_invoice_parts_tax_rate = $("#invoice_parts_tax_rate_"+i+"_"+rowId).val().split('#|#');
            $("#invoice_parts_tax_rate_id_"+i+"_"+rowId).val('');
            $("#invoice_parts_tax_rate_id_"+i+"_"+rowId).val(selected_invoice_parts_tax_rate[0]);
        }
    }

    function getInvoiceItemsById(itemId, i, rowId){
        if(itemId && rowId>=0){
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });
            $.ajax({
                method: "GET",
                url: "/invoice-items/" + itemId,
                success : function(response){
                    if($("#invoice_parts_tax_rate_"+i+"_"+rowId+" option:selected").attr('id')){
                        const selected_option = $("#invoice_parts_tax_rate_"+i+"_"+rowId+" option:selected").attr('id');
                        $("#"+selected_option).removeAttr("selected");
                    }
                        
                    $("#invoice_parts_name_code_"+i+"_"+rowId).val('');
                    $("#invoice_parts_name_"+i+"_"+rowId).val('');
                    $("#invoice_parts_code_"+i+"_"+rowId).val('');
                    $("#invoice_parts_quantity_"+i+"_"+rowId).val('');
                    $("#invoice_parts_description_"+i+"_"+rowId).val('');
                    $("#invoice_parts_unit_price_"+i+"_"+rowId).val('');
                    // $("#invoice_parts_amount_"+rowId).val('');
                    $("#invoice_parts_tax_rate_"+i+"_"+rowId).val('');
                    $("#invoice_parts_tax_rate_id_"+i+"_"+rowId).val('');
                    $("#invoice_parts_chart_accounts_"+i+"_"+rowId).val('');
                    $("#invoice_parts_chart_accounts_parts_id_"+i+"_"+rowId).val('');

                    $("#invoice_parts_name_code_"+i+"_"+rowId).val(response['data']['invoice_item_code']+' : '+response['data']['invoice_item_name']);
                    $("#invoice_parts_name_"+i+"_"+rowId).val(response['data']['invoice_item_name']);
                    $("#invoice_parts_code_"+i+"_"+rowId).val(response['data']['invoice_item_code']);
                    $("#invoice_parts_quantity_"+i+"_"+rowId).val(response['data']['invoice_item_quantity']);
                    $("#invoice_parts_description_"+i+"_"+rowId).val(response['data']['invoice_item_description']);
                    $("#invoice_parts_unit_price_"+i+"_"+rowId).val(parseFloat(response['data']['invoice_item_unit_price']).toFixed(2).replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));
                    // $("#invoice_parts_tax_rate_"+rowId).val(response['data']['invoice_item_tax_rate']);

                    const chart_account = response['data']['chart_accounts_parts'] ? response['data']['chart_accounts_parts']['chart_accounts_particulars_code']+' - '+response['data']['chart_accounts_parts']['chart_accounts_particulars_name'] : '';
                    $("#invoice_parts_chart_accounts_"+i+"_"+rowId).val(chart_account);
                    $("#invoice_parts_chart_accounts_parts_id_"+i+"_"+rowId).val(response['data']['chart_accounts_parts']['id']);
                    
                    const tax_rate_id = response['data']['tax_rates']['id'] ? response['data']['tax_rates']['id'] : '';
                                        
                    $("#invoice_parts_tax_rate_"+i+"_"+rowId).val(response['data']['tax_rates']['id']+"#|#"+response['data']['tax_rates']['tax_rates']).change();

                    $("#invoice_parts_tax_rate_id_"+i+"_"+rowId).val(tax_rate_id);
                    
                    transactionCalculation(i,rowId)

                },
                error:function(error){ 
                    alert(error.responseJSON.message);
                }
            });
        }
    }

    function transactionCalculation(i,id){
        var rowIndex = $('#reconcile_transaction_part_ids_'+i).val();
        rowIndex = JSON.parse(rowIndex);
        var sub_total=0;
        var total_gst=0;
        var gst_percentage = 0;
        var total_amount_due = 0;
        $.each(rowIndex, function (key, rowId) {
            var selected_invoice_parts_tax_rate = $("#invoice_parts_tax_rate_"+i+"_"+rowId).val().split("#|#");

            var quantity = $("#invoice_parts_quantity_"+i+"_"+rowId).val();
            var unit_price = Number($("#invoice_parts_unit_price_"+i+"_"+rowId).val().replace(/\,/g,'')).toFixed(2);
            if(quantity || unit_price){
                var totalPrice = (parseFloat((quantity ? quantity : 0 )*( unit_price ? unit_price : 0 )).toFixed(2));
                sub_total = (parseFloat(sub_total) + parseFloat(totalPrice)).toFixed(2);

                $("#invoice_parts_amount_"+i+"_"+rowId).val(totalPrice.replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));       
                $("#sub_total_amount_"+i).val(sub_total.replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));

                if($("#invoice_default_tax_"+i).val() == 'tax_exclusive'){
                    $("#total_gst_row_"+i).show();
                    total_amount_due = (parseFloat(sub_total) + parseFloat(total_gst)).toFixed(2);
                    $("#total_amount_"+i).val(total_amount_due.replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));
                    $("#invoice_parts_tax_rate_"+i+"_"+rowId).css("display", "block");
                }
                else if($("#invoice_default_tax_"+i).val() == 'no_tax'){
                    $("#total_gst_"+i).val(0);
                    $("#total_gst_row_"+i).hide();
                    $("#invoice_parts_tax_rate_"+i+"_"+rowId).css("display", "none");
                    $("#total_amount_"+i).val(sub_total.replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));
                    $("#invoice_total_gst_text").html("Total Tax 0%");
                }
                else{
                    $("#total_gst_row_"+i).show();
                    $("#invoice_parts_tax_rate_"+i+"_"+rowId).css("display", "block");
                    $("#total_amount_"+i).val(sub_total.replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));
                }
               
                if(parseFloat(selected_invoice_parts_tax_rate[1])>=0 && totalPrice>=0){
                    if($("#invoice_default_tax_"+i).val() == 'tax_exclusive'){
                        
                        var gst = (totalPrice * selected_invoice_parts_tax_rate[1]/100);
                        total_gst = (parseFloat(total_gst) + gst).toFixed(2);
                        if(selected_invoice_parts_tax_rate[1] > 0)
                            gst_percentage = selected_invoice_parts_tax_rate[1];

                        $("#total_gst_"+i).val(total_gst);
                        total_amount_due = (parseFloat(sub_total) + parseFloat(total_gst)).toFixed(2);
                        $("#total_amount_"+i).val(total_amount_due.replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));
                        $("#total_gst_text_"+i).html("Total Tax "+ gst_percentage +' %');
                        
                    }
                    else if($("#invoice_default_tax_"+i).val() == 'tax_inclusive'){
                        var inclusive_gst = (totalPrice - totalPrice / (1 + selected_invoice_parts_tax_rate[1]/100));
                        total_gst = (total_gst + inclusive_gst);

                        if(selected_invoice_parts_tax_rate[1] > 0)
                            gst_percentage = selected_invoice_parts_tax_rate[1];
                        
                        $("#total_gst_text"+i).html("Includes Tax "+ gst_percentage +' %');
                        $("#total_gst_"+i).val((parseFloat(total_gst)).toFixed(2));
                    }
                }

                else if(parseFloat(selected_invoice_parts_tax_rate[1]) == 0 && totalPrice>=0){
                    if($("#invoice_default_tax_"+i).val() == 'tax_exclusive'){

                        var gst = (totalPrice * selected_invoice_parts_tax_rate[1]/100);
                        if(parseFloat(total_gst)>0){
                            $("#total_gst_text"+i).html("Total Tax "+ gst_percentage +' %');
                        }else{
                            $("#total_gst_text"+i).html("Total Tax "+ selected_invoice_parts_tax_rate[1]+' %');
                        }
                        $("#total_gst_"+i).val(total_gst);

                        total_amount_due = (parseFloat(sub_total) + parseFloat(total_gst)).toFixed(2);
                        $("#total_amount_"+i).val(total_amount_due.replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));
                    }
                    else if($("#invoice_default_tax_"+i).val() == 'tax_inclusive'){
                            var inclusive_gst = (totalPrice - totalPrice / (1 + selected_invoice_parts_tax_rate[1]/100));

                            if(parseFloat(total_gst)>0){
                                $("#total_gst_text"+i).html("Includes Tax "+ gst_percentage +' %');
                            }else{
                                $("#total_gst_text"+i).html("Includes Tax "+ selected_invoice_parts_tax_rate[1] +' %');
                            }

                            $("#total_gst_"+i).val((parseFloat(total_gst)).toFixed(2));
                    }
                }
            }
        });
    }

    function getChartAccountsParticularsList(i,id){
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });
        $.ajax({
            method: "GET",
            url: "/chart-accounts-parts",
            success:function(response){
                $("#invoice_chart_account_list_"+i+"_"+id).empty();
                var counter = 0;
                $("#invoice_chart_account_list_"+i+"_"+id).append('<li id="add_new_invoice_chart_account_'+i+"_"+id+'" class="add-new--btn"><a href="#" data-toggle="modal" data-target="#newAddAccountModal" onclick=openNewAddAccountPopUpModelReconcile('+i+','+id+')><i class="fa-solid fa-circle-plus"></i>New Account</a></li>')
                
                $.each(response.data,function(key,value){
                    $("#invoice_chart_account_list_"+i+"_"+id).append(
                        '<li class="accounts-group--label">'+value['chart_accounts_name']+'</li>'
                    );
                    $.each(value['chart_accounts_particulars'],function(k,val){
                        $("#invoice_chart_account_list_"+i+"_"+id).append('\n\<li>\n\
                        <button type="button" class="invoice_item" data-myid="'+counter+'" onclick=addInvoiceChartAccount("'+encodeURI(val['id'])+'","'+i+'","'+id+'");>\n\
                            <span id="data_name_'+counter+'">'+val['chart_accounts_particulars_code']+' - '+val['chart_accounts_particulars_name']+'</span>\n\
                        </button></li>');
                    });
                });
            },
            error:function(error){ 
                alert(error.responseJSON.message);
            }
        });
    }

    function addInvoiceChartAccount(chart_accounts_parts_id, i,rowId, from){
        if(chart_accounts_parts_id && rowId>=0){
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });
            $.ajax({
            method: "GET",
            url: "/chart-accounts-parts/" + chart_accounts_parts_id,
            success : function(response){
                if(from == 'create'){
                    if($("#tax_rate_"+i+" option:selected").attr('id')){
                        const selected_option = $("#tax_rate_"+i+" option:selected").attr('id');
                        $("#"+selected_option).removeAttr("selected");
                    }

                    $("#tax_rate_id_"+i).val('');
                    $("#chart_accounts_id_"+i).val('');
                    $("#chart_accounts_"+i).val('');

                    $("#chart_accounts_"+i).val(response['data']['chart_accounts_particulars_code']+' - '+response['data']['chart_accounts_particulars_name']);
                    $("#chart_accounts_id_"+i).val(response['data']['id']);

                    const tax_rate_id = response['data']['invoice_tax_rates']['id'] ? response['data']['invoice_tax_rates']['id'] : '';

                    $("#tax_rate_"+i).val(response['data']['invoice_tax_rates']['id']+"#|#"+response['data']['invoice_tax_rates']['tax_rates']).change();
                    $("#tax_rate_id_"+i).val(tax_rate_id);


                    if($('#transaction_client_name_'+i).val() && $("#chart_accounts_"+i).val())
                    {
                        $("#transactionReconcile_"+i).attr("style", "visibility: hidden");
                        $("#create_transaction_and_reconcile_"+i).remove();
                        
                        // $("#reconcil_transaction_ok_btn_"+i).append(
                        //     '<button id="create_transaction_and_reconcile_'+i+'" onclick=createTransactionAndReconcile('+encodeURI(i)+') class="btn sumb--btn reconcile--btn" >Ok</button>'
                        // );

                        $("#add--deets_"+i).append( 
                            '<button id="create_transaction_and_reconcile_'+i+'" onclick=createTransactionAndReconcile('+encodeURI(i)+') class="createT--btn"><i class="fa-solid fa-code-merge" style="margin-right: 5px"></i>Reconcile</button>'
                        );
                    }else{
                        $("#create_transaction_and_reconcile_"+i).remove();
                    }

                }else{
                    if($("#invoice_parts_tax_rate_"+i+"_"+rowId+" option:selected").attr('id')){
                        const selected_option = $("#invoice_parts_tax_rate_"+i+"_"+rowId+" option:selected").attr('id');
                        $("#"+selected_option).removeAttr("selected");
                    }

                    $("#invoice_parts_chart_accounts_parts_id_"+i+"_"+rowId).val('');
                    $("#invoice_parts_chart_accounts_"+i+"_"+rowId).val('');
                    $("#invoice_parts_tax_rate_id_"+i+"_"+rowId).val('');

                    $("#invoice_parts_chart_accounts_"+i+"_"+rowId).val(response['data']['chart_accounts_particulars_code']+' - '+response['data']['chart_accounts_particulars_name']);
                    $("#invoice_parts_chart_accounts_parts_id_"+i+"_"+rowId).val(response['data']['id']);

                    const tax_rate_id = response['data']['invoice_tax_rates']['id'] ? response['data']['invoice_tax_rates']['id'] : '';
                
                    $("#invoice_parts_tax_rate_"+i+"_"+rowId).val(response['data']['invoice_tax_rates']['id']+"#|#"+response['data']['invoice_tax_rates']['tax_rates']).change();
                    $("#invoice_parts_tax_rate_id_"+i+"_"+rowId).val(tax_rate_id);
                }
            },
            error : function(error){
                alert(error.responseJSON.message);
            }
            });
        }
    }

    function createTransactionAndReconcile(rowId)
    {
        // var transaction_amount = $("#bank_transaction_amount_"+rowId).val();
        var total_gst = 0;
        var sub_total = $("#bank_transaction_amount_"+rowId).val();
        var tax_rate = $("#tax_rate_"+rowId).val().split("#|#");
        if(tax_rate[1]>0)
        {
            total_gst = sub_total * tax_rate[1]/100;
            sub_total = sub_total - total_gst;
        }
        var row_index_count = $('#reconcile_transaction_part_ids_'+rowId).val();
        row_index_count = JSON.parse(row_index_count);
        if(row_index_count.length >1)
        {
            $('#reconcile_transaction_part_ids_'+rowId).val(JSON.stringify([0]));

        }
        let data = {
            bank_transaction_id : $("#bank_transaction_id_"+rowId).val(),
            account_id : $("#bank_account_id").val(),
            transaction_type : $("#transaction_type_"+rowId).val(),
            issue_date : $("#transaction_date_"+rowId).val(),
            client_name : $("#transaction_client_name_"+rowId).val(),
            default_tax : 'tax_exclusive',
            description : $("#transaction_description_"+rowId).val(),
            tax_rate_id : $("#tax_rate_id_"+rowId).val(),
            unit_price : sub_total,
            sub_total : sub_total,
            total_gst : total_gst,
            total_amount : $("#bank_transaction_amount_"+rowId).val(),
            chart_accounts_parts_id : $("#chart_accounts_id_"+rowId).val(),
            payment_option : 'direct_payment',
            is_reconciled : 1
        };
        $.ajaxSetup({headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }});
        $.ajax ({
            type    : "post",
            url     : '/transaction/create-reconcile',
            data  : data,
            
            success : function(data) {

                location.reload();
                // $body.find('#pre-loader').hide();
                // Swal.fire(
               
                // ).then((res) => {

                //     window.location = window.location.href;

                // });

            },
            error: function(e){
                // $body.find('#pre-loader').hide();
                // Swal.fire({
                // icon: 'error',
                // title: 'Oops...',
                // text: e.responseJSON.message
                // })
            }
        });
	}

    function removeReconcileOkBtn(chart_account)
    {
       
        if($("#"+chart_account).val())
        {
          
        }
    }

    function deleteInvoiceParts(i,rowId){
        var rowIndex = [0];
        rowIndex = $('#reconcile_transaction_part_ids_'+i).val();
        rowIndex = JSON.parse(rowIndex);
        if(rowIndex.length>1){
            addOrRemoveInvoicePartsIds("delete", i, rowId);
            transactionCalculation(i, rowId);
        }
    }

    function saveTransaction(index, obj)
    {
        let bank_transaction_amount = parseFloat($('#bank_transaction_amount_'+index).val()).toFixed(2);
        let total_amount = parseFloat($('#total_amount_'+index).val().replace(/\,/g,'')).toFixed(2);
        
        if( bank_transaction_amount != total_amount )
        {
            $("#total_mismatch_error").addClass('alert alert-danger');
            $("#total_mismatch_error").text("The totals do not match.");
            alert("The totals do not match.");
            return false;
            
        }
        var rowIndex = [0];
        rowIndex = $('#reconcile_transaction_part_ids_'+index).val();
        rowIndex = JSON.parse(rowIndex);
        var transactions = [];
        post_data = 
        {
            row : index,
            total_items : rowIndex 
        }
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });
        $.ajax({
            method: "POST",
            url: "/transaction/create",
            data: $('#transaction-form-create_'+index).serialize(),
            success: function(response){
                transaction_ids.push(response.data['id']);

                let transaction_subtotal = parseFloat($("#transaction_subtotal_"+index).val());
                let spent_receive_money = parseFloat($("#spent_receive_money_"+index).val());

                $("#createTransactionForm_"+index).hide();
                $("#transactions_div_"+index).removeAttr('hidden');
                let spend_money = response.data['transaction_type'] == 'spend_money' ? response.data['total_amount'] : "";
                let receive_money = response.data['transaction_type'] == 'receive_money' ? response.data['total_amount'] : "";
                let amount = spend_money > 0 ? spend_money : receive_money;
                
                let spend_money_input = (response.data['transaction_type'] == 'spend_money' || response.data['transaction_type'] == 'expense') ? '<input type="float" readonly id=spend_money_'+encodeURI(response.data['id'])+' name=transaction_money_'+encodeURI(response.data['id'])+' value='+encodeURI(spend_money)+'>' : "";
                let receive_money_input = (response.data['transaction_type'] == 'receive_money' || response.data['transaction_type'] == 'invoice') ? '<input type="float" readonly id=receive_money_'+encodeURI(response.data['id'])+' name=transaction_money_'+encodeURI(response.data['id'])+' value='+encodeURI(receive_money)+'>' : "";

                var transType="";
                    
                if(response.data['transaction_type'] == 'spend_money' || response.data['transaction_type'] == 'expense'){
                    transType = "Received";
                }
                if(response.data['transaction_type'] == 'receive_money' || response.data['transaction_type'] == 'invoice'   ){
                    transType = "Spend";
                }

                $('#transactions_'+index).append('\
                    <div class="fmtransac_wrap create--new" id="matching_transactions_'+encodeURI(response.data['id'])+'">\
                        <input type="checkbox" checked id="matching_transactions_checkbox_'+encodeURI(response.data['id'])+'" value='+encodeURI(response.data['id'])+' onclick="addOrRemoveTransactions('+encodeURI(index)+','+encodeURI(response.data['id'])+','+encodeURI(response.data['transaction_type'])+')">\
                        <label for="matching_transactions_checkbox_'+encodeURI(response.data['id'])+'">\
                            <div class="row align-items-center">\
                                <div class="deets col-xl-5 col-lg-5 col-md-4 col-sm-12">\
                                    <div class="name">'+response.data['client_name']+'</div>\
                                    '+response.data['issue_date']+'\
                                </div>\
                                <div class="reference col-xl-3 col-lg-2 col-md-3 col-sm-12">\
                                    Ref#:\
                                    '+('00000000000' + response.data['transaction_number']).slice(-12)+'\
                                </div>\
                                <div class="amount col-xl-3 col-lg-3 col-md-4 col-sm-12">\
                                    <div class="viewableAmount" id="viewableAmount_'+response.data['id']+'">\
                                    '+spend_money+'\
                                    '+receive_money+'\
                                    </div>\
                                    '+transType+'\
                                </div>\
                            </div>\
                        </label>\
                    </div>');
                            
                $('#selected_transactions_table_'+index).append('\
                    <div class="fmselectedtransac--item" id=selected_transactions_'+encodeURI(response.data['id'])+'>\
                        <div class="row align-items-center">\
                            <div class="deets col-xl-5 col-lg-5 col-md-5 col-sm-12">\
                                <div class="name">'+response.data['client_name']+'</div>\
                                '+response.data['issue_date']+'\
                            </div>\
                            <div class="reference col-xl-4 col-lg-4 col-md-4 col-sm-12">\
                                Ref#: '+('00000000000' + response.data['transaction_number']).slice(-12)+'\
                            </div>\
                            <div class="amount col-xl-3 col-lg-3 col-md-3 col-sm-12">\
                                '+spend_money_input+'\
                                '+receive_money_input+'\
                                '+transType+'\
                            </div>\
                        </div>\
                    </div>');

                let sum = parseFloat(transaction_subtotal) + (parseFloat(spend_money) > 0 ? parseFloat(spend_money) : parseFloat(receive_money));

                $("#transaction_subtotal_"+index).val(sum.toFixed(2));
                $("#spent_receive_money_"+index).val((spent_receive_money + (parseFloat(spend_money) > 0 ? parseFloat(spend_money) : parseFloat(receive_money))).toFixed(2));
                
                spend_money >0 ? $("#spent_receive_money_text_"+index).text("spent $"+spend_money) : $("#spent_receive_money_text_"+index).text("received $"+receive_money);
                spend_money >0 ? $("#spent_receive_money_header_text_"+index).text("spent") : $("#spent_receive_money_header_text_"+index).text("received");
                
                let total_matched = (parseFloat($("#bank_transaction_amount_"+index).val()) - parseFloat($("#transaction_subtotal_"+index).val())).toFixed(2);
                $("#total_matched_"+index).val(total_matched);

                $("#transaction_minor_adjustment_"+index).val('0');

                parseFloat($("#spent_receive_money_"+index).val()) != parseFloat($("#bank_transaction_amount_"+index).val()) ? $("#transactionReconcile_"+index).prop('disabled', true) : $("#transactionReconcile_"+index).prop('disabled', false);
                parseFloat($("#spent_receive_money_"+index).val()) != parseFloat($("#bank_transaction_amount_"+index).val()) ? $("#reconcile_matching_transactions_"+index).prop('disabled', true) : $("#reconcile_matching_transactions_"+index).prop('disabled', false);
            
            },
            error:function(errors){ 
                if(errors.responseJSON)
                {
                    $("#validation_error_div").attr("style", "display:block");
                    $.each(errors.responseJSON.message, function (key, val) {
                        $("#validation_error").append('<li>'+val+'</li>');
                    });
                }
            }
        });
    }

    function findAndMatchTransactions(index, bank_transaction_id, type)
    {
        let transaction_type = [];
        type == 'debit' ? transaction_type.push('spend_money','expense') : transaction_type.push('invoice','receive_money');
        
        let data = {
            transaction_type : transaction_type
        }
        $body.find('#pre-loader').show();
        $.ajaxSetup({headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }});
        $.ajax({
            method: 'get',
            url: "/transaction/match",
            data: data,
            success: function(data){
                $body.find('#pre-loader').hide();

                $("#createTransactionForm_"+index).hide();
                $("#transactions_div_"+index).removeAttr('hidden');

                transaction_ids = [];

                $.each(data.transactions,function(key,value){

                    let split = "";
                    var transType="";

                    if(value['transaction_type'] == 'invoice'){
                        split+= ('<a class="splitBTN" onclick=splitTransactionPopUp("receive_money_'+value['id']+'","'+index+'","'+encodeURI(JSON.stringify(value))+'") return false><i class="zmdi zmdi-arrow-split"></i>Split</a>');
                        transType = "Received";
                    }
                    if(value['transaction_type'] == 'expense'){
                        split+= ('<a class="splitBTN" onclick=splitTransactionPopUp("spend_money_'+value['id']+'","'+index+'","'+encodeURI(JSON.stringify(value))+'") return false><i class="zmdi zmdi-arrow-split"></i>Split</a>');
                        transType = "Spend";
                    }
                    
                    let spend_money = value['transaction_type'] == 'spend_money' || value['transaction_type'] == 'expense' ? parseFloat(value['total_amount']).toFixed(2) : "";
                    let receive_money = value['transaction_type'] == 'receive_money' || value['transaction_type'] == 'invoice' ? parseFloat(value['total_amount']).toFixed(2) : "";
                    let amount = spend_money > 0 ? spend_money : receive_money;
                    let spend_money_input = (value['transaction_type'] == 'spend_money' || value['transaction_type'] == 'expense') ? '<input type="float" readonly id="spend_money_'+value['id']+'" name="transaction_money_'+value['id']+'" value='+encodeURI(spend_money)+'>' : "";
                    let receive_money_input = (value['transaction_type'] == 'receive_money' || value['transaction_type'] == 'invoice') ? '<input type="float" readonly id="receive_money_'+value['id']+'" name="transaction_money_'+value['id']+'" value='+encodeURI(receive_money)+'>' : "";

                    $('#transactions_'+index).append('\
                        <div class="fmtransac_wrap" id="matching_transactions_'+value['id']+'">\
                            <input type="checkbox" id="matching_transactions_checkbox_'+value['id']+'" value="'+value['id']+'" onclick=addOrRemoveTransactions("'+encodeURI(index)+'","'+encodeURI(value['id'])+'","'+encodeURI(value['transaction_type'])+'")>\
                            <label for="matching_transactions_checkbox_'+value['id']+'">\
                                <div class="row align-items-center">\
                                    <div class="deets col-xl-5 col-lg-5 col-md-4 col-sm-12">\
                                        <div class="name">'+value['client_name']+'</div>\
                                        '+value['issue_date']+'\
                                    </div>\
                                    <div class="reference col-xl-3 col-lg-2 col-md-3 col-sm-12">\
                                        Ref#:\
                                        '+('00000000000' + value['transaction_number']).slice(-12)+'\
                                    </div>\
                                    <div class="amount col-xl-3 col-lg-3 col-md-4 col-sm-12">\
                                        <input type="hidden" readonly id="match_transaction_spend_money_'+value['id']+'" value='+encodeURI(spend_money)+' >\
                                        <input type="hidden" readonly id="match_transaction_receive_money_'+value['id']+'" value='+encodeURI(receive_money)+' >\
                                        <div class="viewableAmount" id="viewableAmount_'+value['id']+'">\
                                        '+encodeURI(spend_money)+'\
                                        '+encodeURI(receive_money)+'\
                                        </div>\
                                        '+transType+'\
                                    </div>\
                                </div>\
                            </label>\
                            <div class="spltBTN_wrap" id="split_transaction_btn_'+value['id']+'" style="display: none;">'+split+'</div>\
                        </div>');

                    $('#selected_transactions_table_'+index).append('\
                        <div class="fmselectedtransac--item" id=selected_transactions_'+encodeURI(value['id'])+' style="display:none">\
                            <div class="row align-items-center">\
                                <div class="deets col-xl-5 col-lg-5 col-md-5 col-sm-12">\
                                    <div class="name">'+value['client_name']+'</div>\
                                    '+value['issue_date']+'\
                                </div>\
                                <div class="reference col-xl-4 col-lg-4 col-md-4 col-sm-12">\
                                    Ref#: '+('00000000000' + value['transaction_number']).slice(-12)+'\
                                </div>\
                                <div class="amount col-xl-3 col-lg-3 col-md-3 col-sm-12">\
                                    '+spend_money_input+'\
                                    '+receive_money_input+'\
                                    '+transType+'\
                                </div>\
                            </div>\
                        </div>');
                 
                if (spend_money > 0) {
                    $("#spent_receive_money_text_"+index).text("$"+spend_money); //spent
                    $("#sub_money_text_"+index).text("Spent"); //spent
                } else {
                    $("#spent_receive_money_text_"+index).text("$"+receive_money); //received
                    $("#sub_money_text_"+index).text("Received");
                }

                spend_money >0 ? $("#spent_receive_money_header_text_"+index).text("spent") : $("#spent_receive_money_header_text_"+index).text("received");

                let total_matched = (parseFloat($("#bank_transaction_amount_"+index).val()) - parseFloat($("#transaction_subtotal_"+index).val())).toFixed(2);
                $("#total_matched_"+index).val(total_matched);
                $("#total_matched--text_"+index).text('Needs Adjustment');
                   
                parseFloat($("#spent_receive_money_"+index).val()) != parseFloat($("#bank_transaction_amount_"+index).val()) ? $("#transactionReconcile_"+index).prop('disabled', true) : $("#transactionReconcile_"+index).prop('disabled', false);
                parseFloat($("#spent_receive_money_"+index).val()) != parseFloat($("#bank_transaction_amount_"+index).val()) ? $("#reconcile_matching_transactions_"+index).prop('disabled', true) : $("#reconcile_matching_transactions_"+index).prop('disabled', false);
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
    }
    
    function addOrRemoveTransactions(rowIndex, id, transaction_type)
    {
        let transaction_subtotal = $("#transaction_subtotal_"+rowIndex).val();
        let spent_receive_money = $("#spent_receive_money_"+rowIndex).val();
        let transaction_amount = 0;
        
        if(transaction_type == 'invoice' || transaction_type == 'receive_money')
        {
            transaction_amount = $("#receive_money_"+id).val();
        }
        if(transaction_type == 'expense' || transaction_type == 'spend_money')
        {
            transaction_amount = $("#spend_money_"+id).val();
        }
        let transaction_minor_adjustment = Number($("#transaction_minor_adjustment_"+rowIndex).val()).toFixed(2);
        var match_transaction_ele = document.getElementById("matching_transactions_checkbox_"+id);
        if(match_transaction_ele.checked)
        {
            
            $("#selected_transactions_checkbox_"+id).prop("checked", true);
            document.getElementById("selected_transactions_"+id).style.display = "block";
            
            $("#transaction_subtotal_"+rowIndex).val((parseFloat(transaction_subtotal) + parseFloat(transaction_amount)).toFixed(2));
            transaction_subtotal = $("#transaction_subtotal_"+rowIndex).val();
            
            $("#spent_receive_money_"+rowIndex).val((parseFloat(transaction_subtotal) + parseFloat(transaction_minor_adjustment)).toFixed(2));

            let total_matched = (parseFloat($("#bank_transaction_amount_"+rowIndex).val()) - parseFloat($("#spent_receive_money_"+rowIndex).val())).toFixed(2);
            $("#total_matched_"+rowIndex).val(parseFloat(total_matched).toFixed(2));
        

            if (total_matched >= 1) {
                $(".amountmatch_wrap > .wrap").addClass("nomatch");
                $(".amountmatch_wrap > .wrap").removeClass("match");
                $("#total_matched--text_"+rowIndex).text('Needs Adjustment');
            } else if (total_matched == 0) {
                $(".amountmatch_wrap > .wrap").addClass("match");
                $(".amountmatch_wrap > .wrap").removeClass("nomatch");
                $("#total_matched--text_"+rowIndex).text('Totals Matched');
            } else {
                $(".amountmatch_wrap > .wrap").addClass("nomatch");
                $(".amountmatch_wrap > .wrap").removeClass("match");
                $("#total_matched--text_"+rowIndex).text('Needs Adjustment');
            }

            transaction_ids.push(id);
        
            parseFloat($("#spent_receive_money_"+rowIndex).val()) != parseFloat($("#bank_transaction_amount_"+rowIndex).val()) ? $("#transactionReconcile_"+rowIndex).prop('disabled', true) : $("#transactionReconcile_"+rowIndex).prop('disabled', false);
            parseFloat($("#spent_receive_money_"+rowIndex).val()) != parseFloat($("#bank_transaction_amount_"+rowIndex).val()) ? $("#reconcile_matching_transactions_"+rowIndex).prop('disabled', true) : $("#reconcile_matching_transactions_"+rowIndex).prop('disabled', false);

            if(transaction_type == 'expense' || transaction_type == 'invoice')
            {
                $("#split_transaction_btn_"+id).attr("style", "display: block");
            }
           
        }else{

            $("#matching_transactions_checkbox_"+id).prop("checked", false);
            document.getElementById("selected_transactions_"+id).style.display = "none";

            $("#transaction_subtotal_"+rowIndex).val((parseFloat(transaction_subtotal) - parseFloat(transaction_amount)).toFixed(2));
            transaction_subtotal = $("#transaction_subtotal_"+rowIndex).val();
            $("#spent_receive_money_"+rowIndex).val((parseFloat(transaction_subtotal) + parseFloat(transaction_minor_adjustment)).toFixed(2));

            let total_matched = (parseFloat($("#bank_transaction_amount_"+rowIndex).val()) - parseFloat($("#spent_receive_money_"+rowIndex).val())).toFixed(2);
            $("#total_matched_"+rowIndex).val(parseFloat(total_matched).toFixed(2));

            if (total_matched >= 1) {
                $(".amountmatch_wrap > .wrap").addClass("nomatch");
                $(".amountmatch_wrap > .wrap").removeClass("match");
                $("#total_matched--text_"+rowIndex).text('Needs Adjustment');
            } else if (total_matched == 0) {
                $(".amountmatch_wrap > .wrap").addClass("match");
                $(".amountmatch_wrap > .wrap").removeClass("nomatch");
                $("#total_matched--text_"+rowIndex).text('Totals Matched');
            } else {
                $(".amountmatch_wrap > .wrap").addClass("nomatch");
                $(".amountmatch_wrap > .wrap").removeClass("match");
                $("#total_matched--text_"+rowIndex).text('Needs Adjustment');
            }

            const index = transaction_ids.indexOf(id);
            if (index > -1) { // only splice array when item is found
                transaction_ids.splice(index, 1); // 2nd parameter means remove one item only
            }
            parseFloat($("#spent_receive_money_"+rowIndex).val()) != parseFloat($("#bank_transaction_amount_"+rowIndex).val()) ? $("#transactionReconcile_"+rowIndex).prop('disabled', true) : $("#transactionReconcile_"+rowIndex).prop('disabled', false);
            parseFloat($("#spent_receive_money_"+rowIndex).val()) != parseFloat($("#bank_transaction_amount_"+rowIndex).val()) ? $("#reconcile_matching_transactions_"+rowIndex).prop('disabled', true) : $("#reconcile_matching_transactions_"+rowIndex).prop('disabled', false);

            if(transaction_type == 'expense' || transaction_type == 'invoice')
            {
                $("#split_transaction_btn_"+id).attr("style", "display: none");
            }
        }
    }

    function splitTransactionPopUp(id,row_index,transaction)
    {
        $("#balance").val(' ');
        $("#split_transaction_details").val(' ');
        $("#transaction_index").val(' ');
        $("#part_payment").val(' ');
        $("#remaining_balance").val(' ');

        var transaction = JSON.parse(decodeURI(transaction));
        let balance = $("#"+id).val();
        $("#balance").val(Number(balance).toFixed(2).replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));
        $("#split_transaction_details").val(JSON.stringify(transaction));
        $("#transaction_index").val(row_index);

        $('#invoice_payment_date_modal').modal({
            backdrop: 'static',
            keyboard: true, 
            show: true
        });
    }

    function splitTransaction()
    {
        const balance = Number($("#balance").val().replace(/\,/g,''));
        const remaining_balance = parseFloat(balance).toFixed(2) - parseFloat($("#part_payment").val()).toFixed(2);
        
        if(parseFloat($("#part_payment").val()).toFixed(2) >= balance)
        {
           $(".close").click();
        }else
        {
            const transaction = JSON.parse($('#split_transaction_details').val());
            const index = $('#transaction_index').val();
            let receive_money = '';
            let spend_money = '';
            
            if(transaction.transaction_type == "invoice")
            {
                $("#split_transaction_btn_"+transaction.id).empty();

                receive_money = remaining_balance;
                const transaction_id = transaction.id;
                
                $("#match_transaction_receive_money_"+transaction.id).val(parseFloat($("#part_payment").val()).toFixed(2));
                $("#viewableAmount_"+transaction.id).text(parseFloat($("#part_payment").val()).toFixed(2));
                $("#receive_money_"+transaction.id).val(parseFloat($("#part_payment").val()).toFixed(2));
                
                const unsplit='<a class="splitBTN" onclick=unSplitTransaction("receive_money_'+transaction_id+'","match_transaction_receive_money_'+transaction_id+'","'+index+'","'+encodeURI($('#split_transaction_details').val())+'","'+remaining_balance+'") return false><i class="zmdi zmdi-undo"></i>Undo Split</a>'
                $("#split_transaction_btn_"+transaction.id).append(unsplit);

            }else if(transaction.transaction_type == "expense")
            {
                $("#split_transaction_btn_"+transaction.id).empty();

                spend_money = remaining_balance;
                const transaction_id = transaction.id;
                
                $("#match_transaction_spend_money_"+transaction.id).val(parseFloat($("#part_payment").val()).toFixed(2));
                $("#viewableAmount_"+transaction.id).text(parseFloat($("#part_payment").val()).toFixed(2));
                $("#spend_money_"+transaction.id).val(parseFloat($("#part_payment").val()).toFixed(2));
                
                const unsplit= ('<a class="splitBTN" onclick=unSplitTransaction("spend_money_'+transaction_id+'","match_transaction_spend_money_'+transaction_id+'","'+index+'","'+encodeURI($('#split_transaction_details').val())+'","'+remaining_balance+'") return false><i class="zmdi zmdi-undo"></i>Undo Split</a>');
                $("#split_transaction_btn_"+transaction.id).append(unsplit);

            }
            let splitTransaction='\
                                <div class="splitTrans_wrap" id="split_transaction_tr_'+transaction.id+'">\
                                    <div class="row align-items-center">\
                                        <div class="deets col-xl-5 col-lg-5 col-md-4 col-sm-12">\
                                            <div class="name">'+transaction.client_name+'</div>\
                                            '+transaction.issue_date+'\
                                        </div>\
                                        <div class="reference col-xl-3 col-lg-2 col-md-3 col-sm-12">\
                                            Split Ref#: '+('00000000000' + transaction.transaction_number).slice(-12)+'\
                                        </div>\
                                        <div class="amount col-xl-3 col-lg-3 col-md-4 col-sm-12">\
                                            <div class="viewableAmount">'+spend_money+'\
                                            '+receive_money+'</div>\
                                            After Split\
                                        </div>\
                                    </div>\
                                </div>';
            
            $(splitTransaction).insertAfter("#matching_transactions_"+transaction.id);

            $("#transaction_subtotal_"+index).val((parseFloat($("#transaction_subtotal_"+index).val()) - remaining_balance).toFixed(2));
            $("#spent_receive_money_"+index).val((parseFloat($("#spent_receive_money_"+index).val()) - remaining_balance).toFixed(2));
            
            let total_matched = (parseFloat($("#bank_transaction_amount_"+index).val()) - parseFloat($("#spent_receive_money_"+index).val())).toFixed(2);
            $("#total_matched_"+index).val(total_matched);

            if (total_matched >= 1) {
                $(".amountmatch_wrap > .wrap").addClass("nomatch");
                $(".amountmatch_wrap > .wrap").removeClass("match");
                $("#total_matched--text_"+index).text('Needs Adjustment');
            } else if (total_matched == 0) {
                $(".amountmatch_wrap > .wrap").addClass("match");
                $(".amountmatch_wrap > .wrap").removeClass("nomatch");
                $("#total_matched--text_"+index).text('Totals Matched');
            } else {
                $(".amountmatch_wrap > .wrap").addClass("nomatch");
                $(".amountmatch_wrap > .wrap").removeClass("match");
                $("#total_matched--text_"+index).text('Needs Adjustment');
            }

            parseFloat($("#spent_receive_money_"+index).val()) != parseFloat($("#bank_transaction_amount_"+index).val()) ? $("#transactionReconcile_"+index).prop('disabled', true) : $("#transactionReconcile_"+index).prop('disabled', false);
            parseFloat($("#spent_receive_money_"+index).val()) != parseFloat($("#bank_transaction_amount_"+index).val()) ? $("#reconcile_matching_transactions_"+index).prop('disabled', true) : $("#reconcile_matching_transactions_"+index).prop('disabled', false);

            $(".close").click();
        }
    }
    
    function unSplitTransaction(selected_transaction_type_id, match_transaction_type_id, index, transaction, remaining_balance)
    {
        var transaction = JSON.parse(decodeURI(transaction));
        $("#"+selected_transaction_type_id).val(parseFloat(transaction.total_amount).toFixed(2));
        $("#"+match_transaction_type_id).val(parseFloat(transaction.total_amount).toFixed(2));
        
        $("#transaction_subtotal_"+index).val((parseFloat($("#transaction_subtotal_"+index).val()) + parseFloat(remaining_balance)).toFixed(2));
        $("#spent_receive_money_"+index).val((parseFloat($("#spent_receive_money_"+index).val()) + parseFloat(remaining_balance)).toFixed(2));
                
        let total_matched = (parseFloat($("#bank_transaction_amount_"+index).val()) - parseFloat($("#spent_receive_money_"+index).val())).toFixed(2);
        $("#total_matched_"+index).val(total_matched);

        if (total_matched >= 1) {
            $(".amountmatch_wrap > .wrap").addClass("nomatch");
            $(".amountmatch_wrap > .wrap").removeClass("match");
            $("#total_matched--text_"+index).text('Needs Adjustment');
        } else if (total_matched == 0) {
            $(".amountmatch_wrap > .wrap").addClass("match");
            $(".amountmatch_wrap > .wrap").removeClass("nomatch");
            $("#total_matched--text_"+index).text('Totals Matched');
        } else {
            $(".amountmatch_wrap > .wrap").addClass("nomatch");
            $(".amountmatch_wrap > .wrap").removeClass("match");
            $("#total_matched--text_"+index).text('Needs Adjustment');
        }

        parseFloat($("#spent_receive_money_"+index).val()) != parseFloat($("#bank_transaction_amount_"+index).val()) ? $("#transactionReconcile_"+index).prop('disabled', true) : $("#transactionReconcile_"+index).prop('disabled', false);
        parseFloat($("#spent_receive_money_"+index).val()) != parseFloat($("#bank_transaction_amount_"+index).val()) ? $("#reconcile_matching_transactions_"+index).prop('disabled', true) : $("#reconcile_matching_transactions_"+index).prop('disabled', false);
       
        $("#split_transaction_btn_"+transaction.id).empty();
        $("#split_transaction_tr_"+transaction.id).remove();
       
        $("#viewableAmount_"+transaction.id).text(parseFloat(transaction.total_amount).toFixed(2));
       
        let split = ('<a class="splitBTN" onclick=splitTransactionPopUp("'+selected_transaction_type_id+'","'+index+'","'+encodeURI(JSON.stringify(transaction))+'") return false><i class="zmdi zmdi-arrow-split"></i>Split</a>')
        $("#split_transaction_btn_"+transaction.id).append(split);
    }


    function confirmPaymentDatePop(status, id, due_amount)
    {
        
        $("#invoice_payment_date").val('');
        $("#invoice_id").val('');
        $("#invoice_id").val(id);
        $("#invoice_status").val('Paid');
        $("#invoice_amount_paid").val('');

        $("#invoice_amount_paid").val(Number(due_amount).toFixed(2).replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));

        $("#invoice_due_amount").val('');
        $("#invoice_due_amount").val(Number(due_amount).toFixed(2).replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));
    }

    function minorAdjustment(rowIndex)
    {
        let transaction_minor_adjustment = Number($("#transaction_minor_adjustment_"+rowIndex).val()).toFixed(2);
        let transaction_subtotal = Number($("#transaction_subtotal_"+rowIndex).val()).toFixed(2);
        if(transaction_minor_adjustment <= 0 || transaction_minor_adjustment >= 0)
        {
            transaction_subtotal = (parseFloat(transaction_minor_adjustment) + parseFloat(transaction_subtotal)).toFixed(2);

            $("#spent_receive_money_"+rowIndex).val(transaction_subtotal);

            let total_matched = (parseFloat($("#bank_transaction_amount_"+rowIndex).val()) - transaction_subtotal).toFixed(2);
            $("#total_matched_"+rowIndex).val(total_matched);

            if (total_matched >= 1) {
                $(".amountmatch_wrap > .wrap").addClass("nomatch");
                $(".amountmatch_wrap > .wrap").removeClass("match");
                $("#total_matched--text_"+rowIndex).text('Needs Adjustment');
            } else if (total_matched == 0) {
                $(".amountmatch_wrap > .wrap").addClass("match");
                $(".amountmatch_wrap > .wrap").removeClass("nomatch");
                $("#total_matched--text_"+rowIndex).text('Totals Matched');
            } else {
                $(".amountmatch_wrap > .wrap").addClass("nomatch");
                $(".amountmatch_wrap > .wrap").removeClass("match");
                $("#total_matched--text_"+rowIndex).text('Needs Adjustment');
            }

            parseFloat($("#spent_receive_money_"+rowIndex).val()) != parseFloat($("#bank_transaction_amount_"+rowIndex).val()) ? $("#transactionReconcile_"+rowIndex).prop('disabled', true) : $("#transactionReconcile_"+rowIndex).prop('disabled', false);
            parseFloat($("#spent_receive_money_"+rowIndex).val()) != parseFloat($("#bank_transaction_amount_"+rowIndex).val()) ? $("#reconcile_matching_transactions_"+rowIndex).prop('disabled', true) : $("#reconcile_matching_transactions_"+rowIndex).prop('disabled', false);
        }
    }

    function openTransactionForm(id, type)
    {

        $("#createTransactionForm_"+id).show();

        const transactionReconcileBtn = document.getElementById('transactionReconcile_'+id);
        const createTransactionAndReconcile = document.getElementById('create_transaction_and_reconcile_'+id);
        if(transactionReconcileBtn)
        {
            const display = window.getComputedStyle(transactionReconcileBtn).visibility;
            
            if(display == 'visible' || display == 'inline-block')
            {
                $("#transactionReconcile_"+id).attr("style", "visibility: hidden");
            }
        }
        else if(createTransactionAndReconcile){

            const display = window.getComputedStyle(createTransactionAndReconcile).visibility;
            if(display == 'visible' || display == 'inline-block')
            {
                $("#create_transaction_and_reconcile_"+id).attr("style", "visibility: hidden");
            }
        }
    }

    $('#part_payment').keyup(function() {
        const balance = Number($("#balance").val().replace(/\,/g,''));
        const remaining_balance = parseFloat(balance).toFixed(2) - parseFloat($("#part_payment").val()).toFixed(2);
        
        if(parseFloat($("#part_payment").val()).toFixed(2) > balance)
        {
            $("#remaining_balance").val('More than original');
        }else{
            $("#remaining_balance").val(remaining_balance);
        }
    });


    function reconcileTransactions(rowId, bank_transaction_id, transaction_collection_id, is_reconciled, issue_date, total_amount=0)
    {
        var account_id = $("#bank_account_id").val();
        var minor_adjustment = $("#transaction_minor_adjustment_"+rowId).val();
        let total_transaction_amount = $("#spent_receive_money_"+rowId).val();
        let direct_reconcile_action = '';

        if(transaction_collection_id>0)
        {
            transaction_ids.push(transaction_collection_id);
            total_transaction_amount = total_amount;
            direct_reconcile_action = 'transaction_money_'+transaction_collection_id+'='+total_amount;
            
        }
        
        let post_data = 
        {
            bank_transaction_id : bank_transaction_id,
            transaction_collection_id : transaction_ids,
            account_id : account_id,
            is_reconciled : is_reconciled,
            minor_adjustment : minor_adjustment,
            issue_date : issue_date,
        }
        
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });
        $.ajax({
            method: 'PUT',
            url: "/transaction/reconcile",
            data: $("#reconcile_transactions_"+rowId).serialize()+'&transaction_collection_id='+transaction_ids+
                    '&account_id='+account_id+'&bank_transaction_id='+bank_transaction_id+'&minor_adjustment='+minor_adjustment+
                    '&is_reconciled='+is_reconciled+'&total_transaction_amount='+total_transaction_amount+'&issue_date='+issue_date+'&'+direct_reconcile_action,

            success : function(data) {
	        	// $body.find('#pre-loader').hide();
	        	// Swal.fire(

			    //   'Success',
			    //    data.message,
			    //   'success'

			    // ).then((res) => {

			    	location.reload();

			    // });

	        },
	        error: function(e){
	        	$body.find('#pre-loader').hide();
	        	Swal.fire({
				  icon: 'error',
				  title: 'Oops...',
				  text: e.responseJSON.message
				})

	        }
            
            // success: function(response){
            //     try{
            //         response = JSON.parse(response);
            //         console.log(response);
            //         if(response && response.status == "success"){
                        
            //             location.reload();
            //         }else if(response.status == "error"){
            //             alert(esponse.err);
            //         }
            //     }
            //     catch(error){}
            // },
            // error:function(error){}
        });
    }

    function removeComma(id)
    {
        var element = document.getElementById(id);
        element.value = Number(element.value.replace(/\,/g,'')).toFixed(2);
    }

    function addComma(id)
    {
        var element = document.getElementById(id);
        element.value = Number(element.value.replace(/\,/g,'')).toLocaleString(undefined, {maximumFractionDigits: 2, minimumFractionDigits: 2});
    }

</script>


