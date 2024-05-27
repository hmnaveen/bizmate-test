@include('includes.head')
@include('includes.user-header')


<!-------Invoice due amount and date alert pop-up--------------->
<form action="/status-change"  method="GET" enctype="multipart/form-data">
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
                                        <input type="float" id="invoice_amount_paid" name="amount_paid" placeholder=""  value="" required onfocusin="removeComma('invoice_amount_paid')" onfocusout="addComma('invoice_amount_paid')" >
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
                    <input type="hidden" id="invoice_status" name="status"   readonly value="">
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
                <input type="hidden" id="add_account_from" value="">
                <button type="button" class="btn btn-secondary close--btn" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary save--btn" onclick="addNewAccount('invoice_account_part_row_id')">Save</button>
            </div>
        </div>
    </div>
</div>
<!-- Add new account modal ends -->


<div class="page-container">

    @include('includes.user-top')
    <!-- MAIN CONTENT-->
    <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid" id="my-div-to-mask">
                <section>
                    @if(!empty($invoice_details && $invoice_details['status']) && $invoice_details['status'] == 'Voided' || !empty($invoice_details && $invoice_details['status']) && $invoice_details['status'] == 'Paid')
                        <h3 class="sumb--title">
                            @if( $invoice_details['transaction_type'] == 'arprepayment' || $invoice_details['transaction_type'] == 'apprepayment')
                                Pre Payment
                                <!-- {{ str_pad($invoice_details['transaction_number'], 6, '0', STR_PAD_LEFT) }} -->
                            @elseif($invoice_details['transaction_type'] == 'receive_money' || $invoice_details['transaction_type'] == 'spend_money')
                            <h3 class="sumb--title">Edit {{ucwords(Str::replace("_", " ", $invoice_details['transaction_type']))}}</h3>
                            @else
                                Over Payment
                            @endif
                            <span class="invoice--status-icon {{ $invoice_details['status'] }}">
                                @if(!empty($invoice_details) && $invoice_details['status'] == 'Voided')
                                    {{ $invoice_details['status'] }}
                                @elseif(!empty($invoice_details) && $invoice_details['status'] == 'Paid')
                                    {{ $invoice_details['status'] }}
                                @endif
                            </span>

                        </h3>
                        <div class="invoice--status-deets">Entries flagged as <u>{{!empty($invoice_details) ? $invoice_details['status'] : '' }}</u> cannot not be edited.</div>
                        @elseif( $invoice_details['transaction_type'] == 'arprepayment' || $invoice_details['transaction_type'] == 'apprepayment')
                            <h3 class="sumb--title">Edit Prepayment</h3>
                                <!-- {{ str_pad($invoice_details['transaction_number'], 6, '0', STR_PAD_LEFT) }} -->
                        @elseif($invoice_details['transaction_type'] == 'receive_money' || $invoice_details['transaction_type'] == 'spend_money')
                            <h3 class="sumb--title">Edit {{ucwords(Str::replace("_", " ", $invoice_details['transaction_type']))}}</h3>
                        @else
                            <h3 class="sumb--title">Edit Overpayment</h3>
                    @endif

                </section>

                <section id="testIngHide">
                    <form action="/bank/cash-receipt/edit?invoice_id={{$invoice_id}}&transaction_type={{$transaction_type}}" method="post" enctype="multipart/form-data" class="form-horizontal" id="invoice_form">
                        @csrf
                        <hr class="form-cutter">
                        <h4 class="form-header--title">Which client is this Invoice for?</h4>
                        <div class="row">
                            <div class="col-xl-4">
                                <div class="form-input--wrap">
                                    <label for="client_name" class="form-input--question">
                                        Client Name
                                    </label>
                                    <div class="form--inputbox recentsearch--input row">
                                        <div class="searchRecords col-12">
                                            <input type="text" id="client_name" name="client_name" class="form-control" placeholder="Search Client Name" aria-label="Client Name" aria-describedby="button-addon2" autocomplete="off" required value="{{!empty($invoice_details && $invoice_details['client_name']) ? $invoice_details['client_name'] : '' }}" >
                                        </div>
                                    </div>
                                    @error('client_name')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror

                                    <div class="form--recentsearch clientname row">
                                        <div class="col-12">

                                            <div class="form--recentsearch__result">
                                                <ul>
                                                    @if (empty($clients))
                                                        <li>You dont have any clients at this time</li>
                                                    @else
                                                        @php $counter = 0; @endphp
                                                        @foreach ($clients as $ec)
                                                            @php $counter ++; @endphp
                                                            <li>
                                                                <button type="button" class="dcc_click" data-myid="{{ $counter }}">
                                                                    <span id="data_name_{{ $counter }}">{{ $ec['client_name'] }}</span>
                                                                    <input type="hidden" id="data_email_{{ $counter }}" value="{{ $ec['client_email'] }}">
                                                                    <input type="hidden" id="data_phone_{{ $counter }}" value="{{ $ec['client_phone'] }}">
                                                                </button>
                                                            </li>
                                                        @endforeach
                                                    @endif

                                                    <li class="add--newactclnt">
                                                        <label for="save_client">
                                                            <input type="checkbox" id="save_client" name="save_client" value="yes" class="form-check-input" {{ !empty($form['save_client']) ? 'checked' : '' }}>
                                                            <div class="option--title">
                                                                    Add as new contact
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

                            <div class="col-xl-4">
                                <div class="form-input--wrap">
                                    <label for="client_email" class="form-input--question">
                                        Client Email
                                    </label>
                                    <div class="form--inputbox row">
                                        <div class="col-12">
                                            <input type="email" id="client_email" name="client_email" placeholder="Client Email Address" class="form-control" value="{{!empty($invoice_details) ? $invoice_details['client_email'] : '' }}">
                                        </div>
                                    </div>
                                    @error('client_email')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-xl-4">
                                <div class="form-input--wrap">
                                    <label for="client_phone" class="form-input--question">
                                        Client Contact Number
                                    </label>
                                    <div class="form--inputbox row">
                                        <div class="col-12">
                                            <input type="text" id="client_phone" name="client_phone" placeholder="Client Contact Number" class="form-control" value="{{!empty($invoice_details) ? $invoice_details['client_phone'] : ''}}">
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <hr class="form-cutter">

                        <h4 class="form-header--title">Your Invoice Details</h4>

                        <div class="row">

                            <div class="col-xl-4">
                                <div class="form-input--wrap">
                                    <label class="form-input--question" for="invoice_date">Date Issued <span>DD/MM/YYYY</span></label>
                                    <div class="date--picker row">
                                        <div class="col-12">
                                            <input type="text" id="invoice_issue_date" name="invoice_issue_date" placeholder="date(DD/MM/YYYY)"  readonly value="{{!empty($invoice_details) ? $invoice_details['issue_date'] : ''}}">
                                        </div>
                                    </div>
                                    @error('invoice_issue_date')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- <div class="col-xl-4">
                                <div class="form-input--wrap">
                                    <label class="form-input--question" for="invoice_date">Due Date <span>DD/MM/YYYY</span></label>
                                    <div class="date--picker row">
                                        <div class="col-12">
                                            <input type="text" id="invoice_duedate" name="invoice_due_date" placeholder="date(DD/MM/YYYY)" readonly value="{{!empty($invoice_details && $invoice_details['due_date']) ?  $invoice_details['due_date']  : '' }}">
                                        </div>
                                    </div>
                                    @error('invoice_due_date')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div> -->
                            @if($invoice_details['transaction_type'] == 'arprepayment' || $invoice_details['transaction_type'] == 'apprepayment' || $invoice_details['transaction_type'] == 'receive_money' || $invoice_details['transaction_type'] == 'spend_money')
                                <div class="col-xl-4">
                                    <div class="form-input--wrap">
                                        <label class="form-input--question">Invoice Number <span>Read-Only</span></label>
                                        <div class="form--inputbox readOnly row">
                                            <div class="col-12">
                                                <input type="text" readonly="" id="invoice_number" name="" value="{{!empty($invoice_details && $invoice_details['transaction_number'])  ? 'INV-'.str_pad($invoice_details['transaction_number'], 6, '0', STR_PAD_LEFT) : '' }}">
                                                <input type="hidden" readonly="" id="invoice_number_hidden" name="invoice_number" value="{{!empty($invoice_details) ? $invoice_details['transaction_number'] : $transaction_number }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <hr class="form-cutter">
                        <div class="row">
                            <div class="col-xl-8">
                                &nbsp;
                            </div>
                            <div class="col-xl-4">
                                <div class="form-input--wrap">
                                    <label class="form-input--question">Total</label>
                                    <div class="form--inputbox {{ (!empty($invoice_details['payment']) && $invoice_details['payment'][0]['reconcile_transaction']['is_reconciled']) || (isset($invoice_details['reconcile_status']) && $invoice_details['reconcile_status']) ?  "readOnly" : "" }}" >
                                        <div class="col-12">
                                            <input {{ (!empty($invoice_details['payment']) && $invoice_details['payment'][0]['reconcile_transaction']['is_reconciled']) || (isset($invoice_details['reconcile_status']) && $invoice_details['reconcile_status']) ?  "readOnly" : "" }} type="float" id="" name="user_total" value="{{ isset($invoice_details['user_total']) ? number_format($invoice_details['user_total'], 2) : number_format($invoice_details['total_amount'], 2) }}" >
                                            @error('user_total')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-xl-8">
                                &nbsp;
                            </div>
                            <div class="col-xl-4">
                                <div class="form-input--wrap">
                                    <label class="form-input--question">
                                        Amounts are
                                    </label>
                                    @if($invoice_details['transaction_type'] == 'arprepayment' || $invoice_details['transaction_type'] == 'apprepayment' || $invoice_details['transaction_type'] == 'receive_money' || $invoice_details['transaction_type'] == 'spend_money')
                                        <div class="row">
                                            <div class="col-12">
                                                <select class="form-input--dropdown" id="invoice_default_tax" name="invoice_default_tax" value="" onchange="invoicepartsQuantity('invoice_default_tax')">
                                                    <option value="tax_exclusive" {{!empty($invoice_details) && $invoice_details['default_tax']=="tax_exclusive" ? "selected" : ''}}>Tax Exclusive</option>
                                                    <option value="tax_inclusive" {{!empty($invoice_details) && $invoice_details['default_tax']=="tax_inclusive" ? "selected" : ''}}>Tax Inclusive</option>
                                                    <option value="no_tax" {{!empty($invoice_details) && $invoice_details['default_tax']=="no_tax" ? "selected" : ''}}>No tax</option>
                                                </select>
                                            </div>
                                        </div>
                                    @else
                                        do not include Tax
                                    @endif
                                </div>
                            </div>

                        </div>

                        <div class="table-responsive">
                            <table id="partstable">
                                <thead>
                                    <tr>
                                        @if($invoice_details['transaction_type'] == 'receive_money' || $invoice_details['transaction_type'] == 'spend_money')
                                            <th scope="col" style="width:135px; min-width:135px;">Item</th>
                                        @endif
                                        @if($invoice_details['transaction_type'] != 'aroverpayment' || $invoice_details['transaction_type'] != 'apoverpayment')
                                            <th scope="col" style="width:70px; min-width:70px;">QTY</th>
                                        @endif
                                            <th scope="col" style="width:200px; min-width:200px;">Description</th>
                                            <th scope="col" style="width:80px; min-width:80px;">Unit Price</th>
                                            <th scope="col" style="width:170px; min-width:170px;">Account</th>
                                            <th scope="col" style="width:150px; min-width:150px;">Tax Rate</th>
                                            <th scope="col" style="width:70px; min-width:70px;">Amount</th>
                                            <th scope="col" style="width:20px; min-width: {{ empty($invoice_details['status']) || $invoice_details['status'] === 'Unpaid' ? '20px' : '100px' }}">&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (!empty($invoice_details))
                                        <?php $invoice_part_total_count = json_decode($invoice_details['invoice_part_total_count'], true) ?>
                                        @php $row_index = 0; @endphp
                                        @foreach($invoice_details['parts'] as $parts)
                                            @php !empty($parts['invoice_parts_id']) ? ($row_index = $parts['invoice_parts_id']) : $row_index; @endphp

                                            @if(count($invoice_part_total_count) != count($invoice_details['parts']))
                                                @php array_push($invoice_part_total_count, $row_index); @endphp
                                                @php $invoice_details['invoice_part_total_count'] = json_encode($invoice_part_total_count); @endphp
                                            @endif
                                            <tr id="{{'invoice_parts_row_id_'.$row_index}}" class="invoice_parts_form_cls" >
                                                @if($invoice_details['transaction_type'] == 'receive_money' || $invoice_details['transaction_type'] == 'spend_money')
                                                    <td>
                                                        <?php $invoice_part_code_name = !empty($parts['parts_name'] && $parts['parts_code'] )
                                                                ? $parts['parts_code']. " : " .$parts['parts_name'] : '' ?>

                                                        <input type="hidden" id="{{'invoice_parts_code_'.$row_index}}" name="{{'invoice_parts_code_'.$row_index}}" value="{{!empty($parts['parts_code']) ? $parts['parts_code'] : ''}}">
                                                        <input type="hidden" id="{{'invoice_parts_name_'.$row_index}}" name="{{'invoice_parts_name_'.$row_index}}" value="{{!empty($parts['parts_name']) ? $parts['parts_name'] : ''}}">
                                                        <input type="hidden" id="{{'invoice_parts_id_'.$row_index}}" name="{{'invoice_parts_id_'.$row_index}}" value="{{!empty($parts['id']) ? $parts['id'] : ''}}">

                                                        <input placeholder="Search your item list" autocomplete="off" data-toggle="dropdown" id="{{'invoice_parts_name_code_'.$row_index}}" name="{{'invoice_parts_name_code_'.$row_index}}" type="text" onkeyup="searchInvoiceparts(this, '{{$row_index}}')" value="{{!empty($invoice_part_code_name) ? $invoice_part_code_name : ''}}" required>

                                                        <ul class="search_items_{{$row_index}} dropdown-menu invoice-expenses--dropdown" id="{{'invoice_item_list_'.$row_index}}" >
                                                            <li class="add-new--btn">
                                                                <a href="#" data-toggle="modal" data-target="#newItemModal" onclick="openPopUpModel('{{$row_index}}')"><i class="fa-solid fa-circle-plus"></i>New Item</a>
                                                            </li>
                                                            @if (!empty($invoice_items))
                                                                @php $counter = 0; @endphp
                                                                @foreach ($invoice_items as $item)
                                                                    @php $counter ++; @endphp
                                                                    <li>
                                                                        <button type="button" class="invoice_item" data-myid="{{ $counter }}" onclick="getInvoiceItemsById('{{ $item['id'] }}', '{{$row_index}}')">
                                                                            <span id="data_name_{{ $counter }}">{{ $item['invoice_item_code'] }} : {{ $item['invoice_item_name'] }}</span>
                                                                            <input type="hidden" id="invoice_item_id_{{ $counter }}" name="invoice_item_id" value="{{ $item['id'] }}">
                                                                        </button>
                                                                    </li>
                                                                @endforeach
                                                            @endif
                                                        </ul>
                                                        @error('parts_quantity_'.$row_index)
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                @endif
                                                @if($invoice_details['transaction_type'] != 'aroverpayment' || $invoice_details['transaction_type'] != 'apoverpayment' )
                                                    <td>
                                                        <input id="{{ 'invoice_parts_quantity_'.$row_index }}" name="{{'invoice_parts_quantity_'.$row_index}}" type="number" onchange="invoicepartsQuantity('{{$row_index}}')" value="{{!empty($parts['parts_quantity']) ? $parts['parts_quantity'] : 0 }}" required>
                                                        @error('parts_quantity_'.$row_index)
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                @endif
                                                <td>
                                                    <textarea id="{{'invoice_parts_description_'.$row_index}}" name="{{'invoice_parts_description_'.$row_index}}" class="autoresizing" required>{{!empty($parts['parts_description']) ? $parts['parts_description'] : ''}}</textarea>
                                                    @error('invoice_parts_description_'.$row_index)
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input {{$invoice_details['transaction_type'] == 'aroverpayment' || $invoice_details['transaction_type'] == 'apoverpayment' ? 'disabled' : '' }} id="{{'invoice_parts_unit_price_'.$row_index}}" name="{{'invoice_parts_unit_price_'.$row_index}}" type="float" value="{{!empty($parts['parts_unit_price']) ? number_format($parts['parts_unit_price'], 2)  : 0.00}}" onchange="invoicepartsQuantity('{{$row_index}}')" onfocusin='removeComma("invoice_parts_unit_price_{{$row_index}}")' onfocusout='addComma("invoice_parts_unit_price_{{$row_index}}")' step=".01" required >
                                                    @error('invoice_parts_unit_price_'.$row_index)
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                    <input type="hidden" id="{{'invoice_parts_gst_'.$row_index}}" name="{{'invoice_parts_gst_'.$row_index}}" value="">
                                                </td>
                                                <td>
                                                    <input {{$invoice_details['transaction_type'] == 'aroverpayment' || $invoice_details['transaction_type'] == 'apoverpayment' ? 'disabled' : '' }} data-toggle="dropdown" type="text" id="{{'invoice_parts_chart_accounts_'.$row_index}}" name="{{'invoice_parts_chart_accounts_'.$row_index}}"  value="{{!empty($parts['chart_accounts_particulars']) && $parts['chart_accounts_particulars']['id'] ? $parts['chart_accounts_particulars']['chart_accounts_particulars_code'] .' - '. $parts['chart_accounts_particulars']['chart_accounts_particulars_name'] : $parts['parts_chart_accounts'] }}">

                                                    <input type="hidden" id="{{'invoice_parts_chart_accounts_code_'.$row_index}}" name="{{'invoice_parts_chart_accounts_code_'.$row_index}}" value="">
                                                    <input type="hidden" id="{{'invoice_parts_chart_accounts_name_'.$row_index}}" name="{{'invoice_parts_chart_accounts_name_'.$row_index}}" value="">
                                                    <input type="hidden" id="{{'invoice_parts_chart_accounts_parts_id_'.$row_index}}" name="{{'invoice_parts_chart_accounts_parts_id_'.$row_index}}" value="{{!empty($parts['chart_accounts_particulars']) && $parts['chart_accounts_particulars']['id'] ? $parts['chart_accounts_particulars']['id'] : $parts['parts_chart_accounts_id']}}">

                                                    <ul class="dropdown-menu invoice-expenses--dropdown" id="{{'invoice_chart_account_list_'.$row_index}}" >
                                                        <li id="{{'add_new_invoice_chart_account_'.$row_index}}" class="add-new--btn">
                                                            <a href="" data-toggle="modal" data-target="#newAddAccountModal" onclick="openNewAddAccountPopUpModel(0)"><i class="fa-solid fa-circle-plus"></i>New Account</a>
                                                        </li>
                                                        @if (!empty($chart_account))
                                                            @php $counter = 0; @endphp
                                                            @foreach ($chart_account as $item)
                                                                <li class="accounts-group--label">{{$item['chart_accounts_name']}}</li>
                                                                @foreach ($item['chart_accounts_particulars'] as $particulars)
                                                                    <!-- <?php $user = array_search($particulars['chart_accounts_type_id'], array_column($item['chart_accounts_types'], 'id')); ?> -->
                                                                    <li>
                                                                        <button type="button" class="invoice_item" data-myid="{{ $counter }}" onclick="addInvoiceChartAccount('{{ $particulars['id'] }}', '{{$row_index}}')">
                                                                            <span id="data_name_{{ $counter }}">{{ $particulars['chart_accounts_particulars_code'] }} - {{ $particulars['chart_accounts_particulars_name'] }} </span>
                                                                            <!-- <input type="hidden" value="{{$item['chart_accounts_types'][$user]['chart_accounts_type']}}"> -->
                                                                            <input type="hidden" id="invoice_item_id_{{ $counter }}" name="invoice_item_id" value="{{ $particulars['id'] }}">
                                                                        </button>
                                                                    </li>
                                                                @endforeach
                                                            @endforeach
                                                        @endif
                                                    </ul>
                                                </td>
                                                <td>
                                                    @if(!empty($tax_rates))
                                                        <input type="hidden" name="{{'invoice_parts_tax_rate_id_'.$row_index}}" id="{{'invoice_parts_tax_rate_id_'.$row_index}}" value="{{!empty($parts['parts_tax_rate_id']) ? $parts['parts_tax_rate_id'] : ''}}">
                                                        <input type="hidden" name="{{'invoice_parts_tax_rate_name_'.$row_index}}" id="{{'invoice_parts_tax_rate_name_'.$row_index}}" value="">
                                                        <div class="form-input--wrap">
                                                            <div class="row">
                                                                <div class="col-12 for--tables">
                                                                    <select {{$invoice_details['transaction_type'] == 'aroverpayment' || $invoice_details['transaction_type'] == 'apoverpayment'  ? 'disabled' : '' }} class="form-input--dropdown" id="{{'invoice_parts_tax_rate_'.$row_index}}" name="{{'invoice_parts_tax_rate_'.$row_index}}" onchange="invoicepartsQuantity('{{$row_index}}'); getTaxRates('{{$row_index}}');" value="" required >
                                                                        <option selected value="0#|#0">Tax Rate Option</option>
                                                                        @foreach($tax_rates as $tax_rate)
                                                                            <option id="{{$tax_rate['id'].'_'.$row_index}}" value="{{$tax_rate['id'].'#|#'.$tax_rate['tax_rates']}}" {{ $parts['parts_tax_rate_id']==$tax_rate['id'] ? 'selected' : '' }}>{{$tax_rate['tax_rates_name']}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </td>

                                                <td>
                                                    <input class="input--readonly" readonly id="{{'invoice_parts_amount_'.$row_index}}" name="{{'invoice_parts_amount_'.$row_index}}" type="float" value="{{!empty($parts['parts_amount']) ? number_format($parts['parts_amount'], 2) : 0.00}}">
                                                    @error('invoice_parts_amount_'.$row_index)
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td class="tableOptions">
                                                    <button class="btn sumb--btn delepart" type="button" onclick="deleteInvoiceParts(<?php echo $row_index ?>)" ><i class="fas fa-trash-alt"></i></button>
                                                </td>
                                            </tr>
                                            @php $row_index++ @endphp
                                        @endforeach
                                    @endif

                                    @if($invoice_details['transaction_type'] == 'arprepayment' || $invoice_details['transaction_type'] == 'apprepayment' || $invoice_details['transaction_type'] == 'receive_money' || $invoice_details['transaction_type'] == 'spend_money')
                                        <tr class="add--new-line" style="display: {{ empty($invoice_details['status']) || $invoice_details['status'] === 'Unpaid' ? '' : 'none' }}">
                                            <td colspan="8">
                                                <button class="btn sumb--btn" type="button" id="addnewline" onclick="addInvoiceParts('{{$invoice_details['transaction_type']}}')" ><i class="fa-solid fa-circle-plus"></i>Add New Line</button>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr class="invoice-separator">
                                        <td colspan="8">
                                            hs
                                        </td>
                                    </tr>

                                    <tr class="invoice-total--subamount">
                                        <td colspan="4" rowspan="{{ empty($invoice_details['status']) || $invoice_details['status'] === 'Paid' || $invoice_details['status'] === 'Voided' ? '5' : '6' }}">
                                        </td>
                                        <td colspan="1">Subtotal (excl GST)</td>
                                        <td colspan="3">
                                            <input type="float" id="invoice_sub_total" name="invoice_sub_total" readonly value="{{!empty($invoice_details) ? number_format($invoice_details['sub_total'], 2) : 0 }}">
                                        </td>
                                    </tr>

                                    <tr class="invoice-total--gst">
                                        <td colspan="1" id="invoice_total_gst_text" >Total GST {{!empty($invoice_details) && $invoice_details['total_gst'] > 0 ? '10%' : '0%'}}</td>
                                        <td colspan="3">
                                            <input type="float" id="invoice_total_gst" name="invoice_total_gst" readonly value="{{!empty($invoice_details) ? number_format($invoice_details['total_gst'], 2) : 0 }}">
                                        </td>
                                    </tr>

                                    <?php if(!empty($invoice_details) && $invoice_details['amount_paid']>0){ ?>
                                        <tr class="invoice-total--paid">
                                            <td colspan="1">
                                                Total Credit
                                            </td>
                                            <td colspan="3">
                                                <input type="float" id="" name="" readonly value="{{!empty($invoice_details) ? number_format($invoice_details['total_amount'], 2) : 0 }}">
                                            </td>
                                        </tr>
                                        <tr class="invoice-partial--paid">
                                            <td colspan="4">
                                                <span><i class="fa-solid fa-list-check"></i>Payment History</span>

                                                <table class="history--list">
                                                    <?php if(!empty($payment_history)){
                                                            foreach($payment_history as $payment){?>
                                                                <tr>
                                                                    <td>Less Cash Refund <span><?php echo !empty($payment) ? $payment['date'] : ''; ?></span></td>
                                                                    <td>-{{ !empty($payment) ? number_format($payment['amount_paid'], 2) : 0 }}</td>
                                                                </tr>
                                                        <?php }}?>
                                                    <!-- Add TR here -->
                                                </table>
                                            </td>
                                        </tr>
                                    <?php }?>

                                    <tr class="invoice-total--amountdue">
                                        <td colspan="1"><strong>Remaining Credit</strong></td>
                                        <td colspan="3">
                                            <input class="grandtotal" type="float" id="invoice_total_amount" name="invoice_total_amount" readonly value="${{!empty($invoice_details) ? number_format($invoice_details['total_amount'], 2) : 0 }}">
                                        </td>
                                    </tr>
                                    <!-- @if($type=='edit' && ($invoice_details['status'] == 'Unpaid' ||  $invoice_details['status'] == 'Recalled' || $invoice_details['status'] == 'PartlyPaid'))
                                        <tr class="settlepayment--invoice">
                                            <td></td>
                                            <td colspan="3">
                                                <a  class="btn sumb--btn" data-toggle="collapse" href="#" role="button" onclick="confirmPaymentDatePop('Paid', {{$invoice_id}}, {{!empty($invoice_details) ? $invoice_details['total_amount'] : 0 }});">
                                                    <i class="fa-solid fa-floppy-disk"></i>Add Payment
                                                </a>
                                            </td>
                                        </tr>
                                    @endif -->

                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-xl-4 col-lg-2 col-md-3 col-sm-12 col-xs-12 col-12">
                                <!-- <div style="margin-top: 20px"> -->
                                    <a id="invoice_history_btn" class="btn sumb--btn" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                                        <i class="fa-solid fa-clock-rotate-left"></i><span id="invoice_history_toggle_text">Show</span> History ({{!empty($invoice_history) ? count($invoice_history) : 0 }} entries)
                                    </a>
                                <!-- </div> -->
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
                                                        <th scope="col" style="width:200px; min-width:250px;">Details</th>
                                                        <!-- <th></th> -->
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(!empty($invoice_history))
                                                        @foreach($invoice_history as $history)
                                                            <tr>
                                                                <td>{{!empty($history) ? $history['action']: ''}} </td>
                                                                <td>{{!empty($history) ? $history['date'].' '.$history['time']: ''}} </td>
                                                                <td>{{!empty($history) ? $history['user_name'] : ''}}</td>
                                                                <td>{{!empty($history) ? $history['description'] : ''}}</td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-navigation">
                            <div class="form-navigation--btns row">
                                <div class="col-xl-4 col-lg-2 col-md-3 col-sm-12 col-xs-12 col-12">
                                    @if($invoice_details['transaction_type'] == 'receive_money')
                                        <a href="/invoice" class="btn sumb--btn"><i class="fa-solid fa-circle-left"></i>Back</a>
                                    @elseif($invoice_details['transaction_type'] == 'spend_money')
                                        <a href="/expense" class="btn sumb--btn"><i class="fa-solid fa-circle-left"></i>Back</a>
                                    @endif
                                </div>
                                <div class="col-xl-8 col-lg-10 col-md-9 col-sm-12 col-xs-12 col-12">
                                    <input type="hidden" id="invoice_ref_number" name="invoice_ref_number" value="{{!empty($invoice_details) && $invoice_details['invoice_ref_number']? $invoice_details['invoice_ref_number'] : '' }}" />
                                    <input type="hidden" id="invoice_part_ids" name="invoice_part_total_count" value="{{!empty($invoice_details) ? $invoice_details['invoice_part_total_count'] : '[0]' }}" />
                                    <input type="hidden" name="invoice_status" value="{{!empty($invoice_details && $invoice_details['status']) ? $invoice_details['status'] : ''}}">
                                    <input type="hidden" name="transaction_type" value="{{!empty($invoice_details && $invoice_details['transaction_type']) ? $invoice_details['transaction_type'] : ''}}">
                                    <input type="hidden" name="payment_option" value="{{!empty($invoice_details && $invoice_details['payment_option']) ? $invoice_details['payment_option'] : ''}}">
                                    <input type="hidden" name="reconcile_status" value="{{ (!empty($invoice_details['payment']) && $invoice_details['payment'][0]['reconcile_transaction']['is_reconciled']) ? $invoice_details['payment'][0]['reconcile_transaction']['is_reconciled'] : (isset($invoice_details['reconcile_status']) ? $invoice_details['reconcile_status'] : '') }}">

                                    <button type="reset" class="btn sumb--btn reset--btn"><i class="fa fa-ban"></i>Clear Invioce</button>
                                    <!-- <button type="button" class="btn sumb--btn preview--btn" onclick="previewInvoice()"><i class="fa-solid fa-eye" ></i>Preview</button> -->
                                    <button hidden type="submit" id="save_invoice_submit" name="save_invoice"  class="btn sumb--btn" value="Save Invoice"><i class="fa-solid fa-floppy-disk"></i>Save</button>

                                    <button type="button" id="save_invoice" class="btn sumb--btn" value="Save"><i class="fa-solid fa-floppy-disk"></i>Save</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </div>
</div>

@include('includes.footer')

<script>

    function confirmPaymentDatePop(status, id, due_amount){
        $("#invoice_payment_date_error").removeClass('alert alert-danger');
        $("#invoice_payment_date_error").html('');

        $("#invoice_amount_paid_error").removeClass('alert alert-danger');
        $("#invoice_amount_paid_error").html('');

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

    <?php if(!empty($invoice_id)){?>
        $(document).on('click', '#mark_as_sent', function(event) {
            var url = "{{URL::to('/invoice/{id}/email-status')}}";
                url = url.replace('{id}', "{{$invoice_id}}");
                location.href = url;
        });
    <?php }?>
    function prtg() {
        $('#pqty').show();
        $('#puprice').show();
    }
    function prts() {
        $('#pqty').hide();
        $('#puprice').hide();
    }

    function sendInvoice(){
        <?php if(!empty($invoice_details) && $type == 'edit') {?>
            $('#send_invoice_modal').modal({
                backdrop: 'static',
                keyboard: true,
                show: true
            });
            var total = '{{ number_format($invoice_details['total_amount'], 2) }}';
            var due_date = $.datepicker.formatDate( "D dd-M-yy", new Date());

            $("#send_invoice_to_emails").val('{{$invoice_details['client_email']}}');
            $("#send_invoice_from").val('{{$userinfo[1]}}');
            $("#send_invoice_subject").val("Invoice INV-00000"+'{{$invoice_details['transaction_number'] }}' + ' from '+ '{{$userinfo[1]}}'+ ' for '+ '{{$invoice_details['client_email']}}');
            $("#send_invoice_message").val("Hi,"+"\n\n" + "Here's invoice INV-00000{{$invoice_details['transaction_number'] }} for $"+total+"."+"\n\n" +"The amount outstanding of $"+total+" is due on {{ $invoice_details['due_date'] }}."+"\n\n" + "Thanks, "+"\n\n" + "{{$userinfo[1]}}");
        <?php }?>
    }
    $(function() {

        $('#invoice_history_btn').on('click', function () {
            $('#invoice_history_toggle_text').text() == "Show" ? $('#invoice_history_toggle_text').text('Hide') : $('#invoice_history_toggle_text').text('Show');
        });

        <?php if(!empty($invoice_details['invoice_clone']) && $invoice_details['invoice_clone']){ ?>
            $("#invoice_form :input").prop('disabled', false);
            $("#send_invoice_pop_up :input").prop('disabled', false);

        <?php }
        else if((!empty($invoice_details['invoice_clone']) && !$invoice_details['invoice_clone']) || !empty($invoice_details) && (isset($invoice_details['invoice_sent']) && !$invoice_details['invoice_sent'] && $invoice_details['status'] == 'Recalled') ){ ?>
                $("#invoice_form :input").prop('disabled', true);
                var rowIndex = $('#invoice_part_ids').val();
                rowIndex = JSON.parse(rowIndex);
                for(var i = 0; i<rowIndex.length; i++)
                {
                    $("#invoice_parts_tax_rate_"+i).prop('disabled', false);
                }
                $(".invoice_item").prop('disabled', false);
                $("#send_invoice_drop_down").prop('disabled', false);
                $("#save_invoice").prop('disabled', false);
                $("#send_invoice").prop('disabled', false);
        <?php }
         else if((!empty($invoice_details['invoice_clone']) && !$invoice_details['invoice_clone']) || !empty($invoice_details) && (isset($invoice_details['invoice_sent']) && $invoice_details['invoice_sent'] || $invoice_details['status'] == 'Voided' || $invoice_details['status'] == 'Paid' || $invoice_details['status'] == 'PartlyPaid') ){  ?>
            $("#invoice_form :input").prop('disabled', true);
            $("#send_invoice_pop_up :input").prop('disabled', true);
        <?php }?>


        function setDatepickerPos(input, inst) {
            var rect = input.getBoundingClientRect();
            // use 'setTimeout' to prevent effect overridden by other scripts
            setTimeout(function () {
                var scrollTop = $("body").scrollTop();
                inst.dpDiv.css({ top: rect.top + input.offsetHeight + scrollTop });
            }, 0);
        }

        <?php if(!empty($invoice_details && $invoice_details['issue_date']) ){?>
            $('#invoice_issue_date').datepicker({ dateFormat: 'dd/mm/yy' });
        <?php } else{?>
            $('#invoice_issue_date').datepicker({ dateFormat: 'dd/mm/yy' }).datepicker('setDate', 'today');
        <?php }?>
        $('#invoice_duedate').datepicker({ dateFormat: 'dd/mm/yy' });
        $( "#invoice_payment_date" ).datepicker({ dateFormat: 'dd/mm/yy', beforeShow: function (input, inst) { setDatepickerPos(input, inst) } });


        $('#save_invoice').on('click', function () {
            $("#invoice_form :input").prop('disabled', false);
            $("#send_invoice_pop_up :input").prop('disabled', false);
            $("#save_invoice_submit").click();
        });

        $('#part_save_button').on('click', function () {

            var myform = $('#partform');
            if (document.querySelector('#partform').reportValidity())  {
                var fprocess = $('#fprocess').val();

                var formdata2 = $("form#partform").serializeArray();

                if (fprocess == 'a') {
                    $.ajaxSetup({
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    });
                    $.ajax({
                        url: "{{ url('/invoice-particulars-add') }}",
                        method: 'post',
                        data: formdata2,
                        success: function(result){
                            var rparse = JSON.parse(result);
                            if(rparse.chk == 'success') {
                                var myhtml = '<tr id="part_'+rparse.id+'" data-type="'+rparse.type+'"><td scope="row" id="part_qty_'+rparse.id+'">'+rparse.qty+'</td><td id="part_desc_'+rparse.id+'" style="text-align: left;">'+rparse.desc+'</td><td id="part_uprice_'+rparse.id+'" data-amt="'+rparse.upriceno+'">'+rparse.uprice+'</td><td id="part_amount_'+rparse.id+'" data-amt="'+rparse.amountno+'">'+rparse.amount+'</td><td><button class="btn sumb--btn editpart" type="button" data-partid="'+rparse.id+'" data-toggle="modal" data-target="#particulars"><i class="fa-regular fa-pen-to-square"></i></button> <button class="btn sumb--btn delepart" type="button" data-partid="'+rparse.id+'"><i class="fas fa-trash-alt"></i></button></td></tr>';
                                $("#grandtotal").html('$'+rparse.grand_total);
                                $("#gtotal").val(rparse.grand_total);
                                $('#partstable tr:last').prev().after(myhtml);
                                $('#particulars').modal('toggle');
                                $('#tnoparts').hide();
                            }
                        }
                    });
                } else if (fprocess == 'e') {

                    $.ajaxSetup({
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    });
                    $.ajax({
                        url: "{{ url('/invoice-particulars-add') }}",
                        method: 'post',
                        data: formdata2,
                        success: function(result){
                            var rparse = JSON.parse(result);
                            if(rparse.chk == 'success') {
                                var myhtml = '<td scope="row" id="part_qty_'+rparse.id+'">'+rparse.qty+'</td><td id="part_desc_'+rparse.id+'" style="text-align: left;">'+rparse.desc+'</td><td id="part_uprice_'+rparse.id+'" data-amt="'+rparse.upriceno+'">'+rparse.uprice+'</td><td id="part_amount_'+rparse.id+'" data-amt="'+rparse.amountno+'">'+rparse.amount+'</td><td ><button class="btn sumb--btn editpart" type="button" data-partid="'+rparse.id+'" data-toggle="modal" data-target="#particulars"><i class="fa-regular fa-pen-to-square"></i></button> <button class="btn sumb--btn delepart" type="button" data-partid="'+rparse.id+'"><i class="fas fa-trash-alt"></i></button></td>';
                                $("#grandtotal").html('$'+rparse.grand_total);
                                $("#gtotal").val(rparse.grand_total);
                                $('#part_'+rparse.id).html(myhtml);
                                $('#particulars').modal('toggle');
                                $('#tnoparts').hide();
                            }
                        }
                    });
                }
            } else {
                console.log("error!");
            }
        });
        $("#addnewpart").on('click', function () {
            $('#fprocess').val('a');
            $('#partid').val(0);
            $("#part_qty").val('');
            $("#part_desc").val('');
            $("#part_uprice").val('');
            $("#part_amount").val('');
            $("#form_goods").addClass("active"); $("#form_services").removeClass("active");
            $("#form_radio_goods").prop("checked", true); $("#form_radio_services").prop("checked", false);
            $('#pqty').show();
            $('#puprice').show();
        });

        $('#partstable').on('click', ".editpart", function () {
            var partid = $(this).data('partid');
            var type = $("#part_"+partid).data('type');
            var qty = $("#part_qty_"+partid).html();
            var desc = $("#part_desc_"+partid).html();
            var uprice = $("#part_uprice_"+partid).data('amt');
            var amount = $("#part_amount_"+partid).data('amt');
            $('#fprocess').val('e');
            $('#partid').val(partid);
            $("#part_qty").val(qty);
            $("#part_desc").val(desc);
            $("#part_uprice").val(uprice);
            $("#part_amount").val(amount);
            if (type == "goods") {
                $("#form_goods").addClass("active"); $("#form_services").removeClass("active");
                $("#form_radio_goods").prop("checked", true); $("#form_radio_services").prop("checked", false);
                $('#pqty').show();
                $('#puprice').show();
            } else {
                $("#form_services").addClass("active"); $("#form_goods").removeClass("active");
                $("#form_radio_services").prop("checked", true); $("#form_radio_goods").prop("checked", false);
                $('#pqty').hide();
                $('#puprice').hide();
            }
        });

        $('.dcc_click').on('click', function () {
            var clientid = $(this).data('myid');
            var clientname = $("#data_name_"+clientid).html();
            var clientdesc = $("#data_desc_"+clientid).html();

            $('#client_name').val( $("#data_name_"+clientid).html() );
            $('#client_email').val( $("#data_email_"+clientid).val() );
            $('#client_phone').val( $("#data_phone_"+clientid).val() );
            $('#client_address').val( $("#data_address_"+clientid).html() );
            $('#invoice_details').val( $("#data_details_"+clientid).val() );
            $('.form--recentsearch').hide();
        });


        //hide search by default

        $('.form--recentsearch').hide();
        $('li.add--newactclnt').hide();

        //Client Name Search

        $('#client_name').on('keyup', function() {

            $('.form--recentsearch.clientname').show();
            var value = $(this).val().toLowerCase();
            var clientList = $(".clientname .form--recentsearch__result li button");
            var matchedItems = $(".clientname .form--recentsearch__result li button").filter(function() {
                return $(this).text().toLowerCase().indexOf(value) > -1;
            });
            console.log(clientList);

            if(value == ''){
                $('.form--recentsearch.clientname').hide();
                $('.clientname li.add--newactclnt input').prop('checked',false);
                $('#client_name').removeClass('saveNewRecord');
                $('#client_email').val('');
                $('#client_phone').val('');

            } else if($('#client_name').hasClass('saveNewRecord')) {

                if(matchedItems.length !=0) {
                    $('.clientname li.add--newactclnt').hide();
                    $('.clientname li.add--newactclnt input').prop('checked',false);
                    $('#client_name').removeClass('saveNewRecord');
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

        //New Record -- Add New Icon

        $('li.add--newactclnt input').on('click', function () {

            if(this.id == 'save_client') {
                if($('#save_client').is(':checked')){
                    $('#client_name').addClass('saveNewRecord');
                    $('#client_email').val('');
                    $('#client_phone').val('');
                } else {
                    $('#client_name').removeClass('saveNewRecord');
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

        $('#partstable').on('input', '.autoresizing', function () {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });

        $('#partstable').on('focus', '.autoresizing', function () {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    });

    let $body = $(this);

    $(window).on('beforeunload', function(){

        $body.find('#pre-loader').show();

    });

    $(function () {

        $body.find('#pre-loader').hide();
    })

</script>
</body>

</html>
<!-- end document-->
