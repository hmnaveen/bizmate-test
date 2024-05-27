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
                            <a href="/account/transactions?bank_account_id={{$bank_account_id}}" id="" class="acctTransactions--btn float-right"><i class="fas fa-list-alt"></i>Account Transactions</a>
                        </div>
                    </div>


                    <div class="reconcileTransactions--logs sumb--putShadowbox">

                        @if(empty($bank_transactions))
                            <div class="reconcileTransactions--notransactions">No Transactions Found</div>
                        @else
                            
                            @php($k = 0)
                            @php($match = 0)
                            @php($otherMatches = [])
                            @foreach($bank_transactions['data'] as $transaction)
                                
                                @foreach($appTransactions as $appTransaction)
                                    @if(str_replace(',', '', $appTransaction['total_amount']) == str_replace(',', '', $transaction['amount']) && (($appTransaction['transaction_type'] == 'invoice' && $transaction['direction'] == 'credit') || ($appTransaction['transaction_type'] == 'expense' && $transaction['direction'] == 'debit'))) 
                                        @php($match = 1)
                                        @break;
                                    @else
                                        @php($match = 0)
                                    @endif
                                @endforeach

                                <div class="reconcileTransactions {{ ($match == 1) ? 'match':'' }}">
                                
                                    <div class="row">
                                        <div class="col-xl-4 col-lg-4 order-xl-1 order-lg-1 order-md-1 order-sm-1 order-1">
                                            <div class="RT--bankTransaction">

                                                <span><i class="fa-solid fa-building-columns"></i>Back Transaction</span>

                                                <div class="transactionItem-box">
                                                    <div class="row d-flex align-items-center">
                                                        <div class="col-xl-8 col-lg-7 col-md-8 col-sm-8 col-8">
                                                            <div class="desc">{{ $transaction['description'] }}</div>
                                                            <div class="date"><?php echo substr($transaction['post_date'],0,10); ?></div>
                                                        </div>
                                                        <div class="col-xl-4 col-lg-5 col-md-4 col-sm-4 col-4">
                                                            <div class="deets {{ ucfirst($transaction['direction']) }}">
                                                                <div>
                                                                    {{ ($transaction['direction'] == 'debit' ? '('.($transaction['amount']).')' : ($transaction['amount']) ) }}
                                                                    <span>{{ ucfirst($transaction['direction']) }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>

                                            </div>
                                        </div>

                                        <div class="col-xl-2 col-lg-2 order-xl-2 order-lg-2 order-md-4 order-sm-4 order-4 button--style {{( ($match == 1) ? '' : 'hide' )}}" id="reconcil_transaction_ok_btn_{{ $k }}">
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

                                        
                                        <div class="col-xl-6 col-lg-6 order-xl-3 order-lg-2 order-md-2 order-2">
                                            <div class="RT--sumbTransaction">
                                                <span><i class="fa-solid fa-arrow-right-arrow-left"></i>Transactions in SUM[B]</span>

                                                <div class="transactions">
                                                    @php($i = 0)
                                                    @php($j = 0)
                                                    @foreach($appTransactions as $appTransaction)
                                                        @if(str_replace(',', '', $appTransaction['total_amount']) == str_replace(',', '',($transaction['amount'])) && (($appTransaction['transaction_type'] == 'invoice' && $transaction['direction'] == 'credit') || ($appTransaction['transaction_type'] == 'expense' && $transaction['direction'] == 'debit'))) 
                                                        
                                                            @php($i++)
                                                            @php(array_push($otherMatches, $appTransaction))
                                                        @endif
                                                    @endforeach


                                                    <div class="find--andmatch_mobile">
                                                        <a onclick="findAndMatchTransactions('{{ $k }}','{{$transaction['direction']}}'); hideTabs('{{ $k }}');"><i class="fa-solid fa-magnifying-glass"></i>Find & Match</a>
                                                    </div>

                                                    <ul class="nav nav-tabs" id="tabs_{{ $k }}">
                                                        <li class="nav-item">
                                                            <a id="match_tab_{{ $k }}" class="nav-link tablinks_{{ $k }} active" onclick="openTab(event, 'Match_{{ $k }}','{{ $k }}','{{ $i }}')" aria-current="page"><i class="zmdi zmdi-flip"></i>Matched</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a id="create_tab_{{ $k }}" class="nav-link tablinks_{{ $k }}" onclick="openTab(event, 'Create_{{ $k }}','{{ $k }}','{{ $i }}')"><i class="fa-regular fa-file-lines"></i>New</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a id="discuss_tab_{{ $k }}" class="nav-link tablinks_{{ $k }}" onclick="openTab(event, 'discuss_{{ $k }}','{{ $k }}','{{ $i }}')"><i class="fa-solid fa-comments"></i>Review</a>
                                                        </li>
                                                        <li class="find--match fm--btn_{{ $k }}">
                                                            <a onclick="findAndMatchTransactions('{{ $k }}','{{$transaction['direction']}}'); hideTabs('{{ $k }}');"><i class="fa-solid fa-magnifying-glass"></i>Look & Match</a>
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
                                                                                <input type="text" onkeyup="getClient('{{$k}}',this, '.createNew')" id="transaction_client_name_{{ $k }}" name="client_name_{{ $k }}" required class="form-control" placeholder="Search Client Name" aria-label="Client Name" aria-describedby="button-addon2" autocomplete="off"  value="" >
                                                                            </div>
                                                                        </div>

                                                                        <div class="createNew form--recentsearch clientname_{{$k}} row">
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
                                                                        <input class="input--box" autocomplete="off" data-toggle="dropdown" type="text" id="chart_accounts_{{ $k }}" name="invoice_parts_chart_accounts_{{ $k }}_0"  value="" readonly required >

                                                                        <input type="hidden" id="chart_accounts_id_{{ $k }}" name="invoice_parts_chart_accounts_parts_id_{{ $k }}_0" value="">

                                                                        <ul class="dropdown-menu invoice-expenses--dropdown" id="invoice_chart_account_list_{{ $k }}_0">
                                                                            @if (!empty($chart_account))
                                                                            @php ($counter = 0)
                                                                            @foreach ($chart_account as $item)
                                                                                <li class="accounts-group--label">{{$item['chart_accounts_name']}}</li>
                                                                                @foreach ($item['chart_accounts_particulars'] as $particulars)
                                                                                    <li>
                                                                                        <button type="button" class="invoice_item" data-myid="{{ $counter }}" onclick="addReconcileChartAccount('{{ $particulars['id'] }}', '{{$k}}', 0, 'create')" >
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
                                                                                        <select class="form-input--dropdown" id="tax_rate_{{ $k }}" name="invoice_parts_tax_rate_{{ $k }}_0" onchange="transactionCalculation('{{ $k }}',0); getTaxRatePercentage('{{ $k }}',0);" required style="display : {{!empty($transaction['default_tax']) && $transaction['default_tax']=="no_tax" ? 'none' : 'block' }}; border-color: #28282a;">
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
                                                            <a class="tablinks_{{ $k }}" id="addTransactionDetails_{{ $k }}" onclick="openTransactionForm('{{ $k }}', '{{ $transaction['direction'] }}');hideTabs('{{ $k }}')"><i class="zmdi zmdi-plus-circle-o"></i>Add Details</a>
                                                        </div>
                                                    </div>

                                                    <div id="Match_{{ $k }}" class="matchedTransaction tabcontent_{{ $k }}">
                                                        @if($i > 0)
                                                            @foreach($appTransactions as $appTransaction)
                                                                @if(str_replace(',', '', $appTransaction['total_amount']) == str_replace(',', '', $transaction['amount']) && (($appTransaction['transaction_type'] == 'invoice' && $transaction['direction'] == 'credit') || ($appTransaction['transaction_type'] == 'expense' && $transaction['direction'] == 'debit'))) 
                                                                    <div class="row d-flex align-items-center">
                                                                        <div class="col-8">
                                                                            <div class="desc">{{ $appTransaction['client_name'] }}</div>
                                                                            <div class="date">{{ $appTransaction['issue_date'] }}</div>
                                                                        </div>
                                                                        <div class="col-4">
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
                                                                <a onclick="openOtherMatches('{{ ($transaction['amount']) }}','{{ $k }}','{{ json_encode($otherMatches) }}')" id="otherMatch_{{ $k }}"><i class="fa-solid fa-triangle-exclamation"></i>{{ $i-1 }} Other Matches Found</a>
                                                            </div>

                                                            <!-- Other Matching Transactions -->
                                                            <div class="match_{{ $k }} matchBox"> </div>
                                                        @endif
                                                        
                                                    </div>

                                                    <div id="discuss_{{ $k }}" class="discussTransaction tabcontent_{{ $k }}" name="discussTransaction">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="form-input--wrap textarea">
                                                                    <textarea class="col-12" id="transaction_discussion_{{ $k }}" name="transaction_discussion_{{ $k }}" onkeyup="discuss('transaction_discussion_{{ $k }}','existing_transaction_discussion_{{ $k }}','{{ $k }}','{{ $transaction['id'] }}')">{{!empty($transaction['discuss']) ? $transaction['discuss'][0]['discuss'] : '' }}</textarea>
                                                                    <input type="hidden" id="existing_transaction_discussion_{{ $k }}" value="{{!empty($transaction['discuss']) ? $transaction['discuss'][0]['discuss'] : '' }}">
                                                                </div>
                                                                <div id="discussTransBTN_{{ $k }}" class="add--deets">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                    </div>


                                                </div>

                                                
                                            </div>
                                        </div>
                                        <div class="col-12 order-xl-4 order-lg-3 order-md-3 order-sm-3 order-3">
                                            <div name="createTransactionForm" id="createTransactionForm_{{ $k }}" style="display: none;">
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
                                                                    <select class="form-input--dropdown" id="payment_option_{{ $k }}" name="payment_option_{{ $k }}" onchange="paymentOptions('{{ $k }}','{{ json_encode($account) }}','{{ !empty($transaction) ? json_encode(collect($transaction)->forget('discuss'))  : '' }}','{{ !empty($tax_rates) ? json_encode($tax_rates)  : '' }}','{{ !empty($chart_account) ? json_encode($chart_account) : '' }}','{{ !empty($invoice_items) ? json_encode($invoice_items) : '' }}')">
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
                                                                        <input type="text" onkeyup="getClient('{{$k}}',this,'.addDeets_client')" id="client_name_{{ $k }}" name="client_name_{{ $k }}" required class="form-control" placeholder="Search Client Name" aria-label="Client Name" aria-describedby="button-addon2" autocomplete="off"  value="{{ !empty($transaction['sub_class']) ? $transaction['sub_class']['title'] : '' }}" >
                                                                    </div>
                                                                </div>
                                                                <div class="addDeets_client form--recentsearch clientname_{{$k}} row">
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
                                                                                    <label for="savethisrep_deets">
                                                                                        <input type="checkbox" id="savethisrep_deets" name="savethisrep_deets" value="yes" class="form-check-input" {{ !empty($form['save_client']) ? 'checked' : '' }}>
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
                                                                                    <input placeholder="Search your item list"  autocomplete="off" data-toggle="dropdown" type="text" id="item_name_code_{{ $k }}_0" name="item_name_code_{{ $k }}_0" onkeyup="searchItems(this,'{{ $k }}', 0)" value="">
                                                                                    
                                                                                    <input type="hidden" id="item_part_code_{{ $k }}_0" name="item_part_code_{{ $k }}_0" value="">
                                                                                    <input type="hidden" id="item_part_name_{{ $k }}_0" name="item_part_name_{{ $k }}_0" value="">

                                                                                    <ul class="search_items_{{ $k }}_0 dropdown-menu invoice-expenses--dropdown" id="invoice_item_list_{{ $k }}_0">
                                                                                        
                                                                                        @if (!empty($invoice_items))
                                                                                            @php($counter = 0)
                                                                                            @foreach ($invoice_items as $item)
                                                                                                @php($counter ++)
                                                                                                <li>
                                                                                                    <button type="button" class="invoice_item" data-myid="{{ $counter }}" onclick="getItemsById('{{ $item['id'] }}', '{{ $k }}', 0)">
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
                                                                                    <input id="invoice_parts_unit_price_{{ $k }}_0" name="invoice_parts_unit_price_{{ $k }}_0" type="float" value="{{ !empty($transaction) ? $transaction['amount'] : '' }}" onchange="transactionCalculation('{{ $k }}',0);" onfocusin="removeComma('invoice_parts_unit_price_{{ $k }}_0');" onfocusout="addComma('invoice_parts_unit_price_{{ $k }}_0');">
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
                                                                                                    <button type="button" class="invoice_item" data-myid="{{ $counter }}" onclick="addReconcileChartAccount('{{ $particulars['id'] }}', '{{$k}}', 0)">
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
                                                                                                    <select class="form-input--dropdown" id="invoice_parts_tax_rate_{{ $k }}_0" name="invoice_parts_tax_rate_{{ $k }}_0" onchange="transactionCalculation('{{ $k }}',0); getTaxRatePercentage('{{ $k }}',0);" required style="display : {{!empty($transaction['default_tax']) && $transaction['default_tax']=="no_tax" ? 'none' : 'block' }}">
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
                                                                                    <input class="input--readonly" readonly id="invoice_parts_amount_{{ $k }}_0" name="invoice_parts_amount_{{ $k }}_0" type="float" value="{{ !empty($transaction) ? $transaction['amount'] : '' }}">
                                                                                </td>
                                                                                <td class="tableOptions">
                                                                                    <button class="btn sumb--btn delepart" type="button" onclick="deleteReconcileParts('{{ $k }}', 0)" ><i class="fas fa-trash-alt"></i></button>
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
                                                                                            <select name="invoice_default_tax_{{ $k }}" id="invoice_default_tax_{{ $k }}" class="form-input--dropdown" onchange="transactionCalculation('{{ $k }}',0)">
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
                                                                                    <input readonly required id="sub_total_amount_{{ $k }}" step="any" name="sub_total_{{ $k }}" type="float" value="{{ !empty($transaction) ? $transaction['amount'] : '' }}">
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
                                                                                    <input type="float" required readonly step="any" class="grandtotal" name="total_amount_{{ $k }}" id="total_amount_{{ $k }}" value="{{ !empty($transaction) ? $transaction['amount'] : '' }}">
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
                                                        <input type="hidden" id="bank_transaction_amount_{{ $k }}" name="bank_transaction_amount_{{ $k }}" value="{{ ($transaction['amount']) }}">
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 order-5" hidden="hidden" id="transactions_div_{{ $k }}">

                                            <!-----------Find Matching Transactions----------->
                                            <div class="findMatchingTra_wrap">

                                                <h5><i class="fa-solid fa-magnifying-glass"></i>Find & Select Matching Transactions</h5>
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
                                                                                    <input type="float" readonly id='transaction_subtotal_{{ $k }}' name="transaction_subtotal_{{ $k }}" value='0.00'>
                                                                                    Subtotal
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <div class="minoradjustment_wrap">
                                                                                <div class="wrap">
                                                                                    Minor adjustment <input type="float" id='transaction_minor_adjustment_{{ $k }}' onkeyup="minorAdjustment('{{ $k }}')" value='0'>

                                                                                    <div class="desc">
                                                                                        <!-- Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus vitae tellus porttitor, varius urna ac, fringilla magna. Fusce sed mauris facilisis, lacinia justo et, cursus felis. -->
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-12">
                                                                    <div class="currentadjustment_wrap">
                                                                        <div class="wrap">
                                                                            <input type="float" readonly id='spent_receive_money_{{ $k }}' name="spent_receive_money_{{ $k }}" value='0.00'>
                                                                            Current Adjustment
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-12">
                                                                    <div class="amountmatch_wrap">
                                                                        <div class="wrap">
                                                                            <i class="fa-regular fa-face-laugh-wink"></i>
                                                                            <input type="float" readonly id="total_matched_{{ $k }}" value='0.00'>
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
        if($(this).attr('name') == "discussTransaction"){
            $(this).hide();
        }
    });

    $('.form--recentsearch').hide();
    $('li.add--newactclnt').hide();

});

$('#part_payment').keyup(function() {
    const balance = Number($("#balance").val().replace(/\,/g,''));
    let remaining_balance = parseFloat(balance).toFixed(2) - parseFloat($("#part_payment").val()).toFixed(2);
    
    if(isNaN(remaining_balance)) remaining_balance = 0;

    if(parseFloat($("#part_payment").val()).toFixed(2) > balance)
    {
        $("#remaining_balance").val('More than original');
    }else{
        $("#remaining_balance").val(parseFloat(remaining_balance).toFixed(2));
    }
});

</script>


