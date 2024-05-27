@include('includes.head')
@include('includes.user-header')


<!-------Invoice due amount and date alert pop-up--------------->
<form action="/basic/expense-status-change"  method="GET" enctype="multipart/form-data"> 
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
                                        <input type="float" id="expense_amount_paid" name="amount_paid" placeholder=""  value="" required onfocusin="basicInvoiceRemoveComma('expense_amount_paid')" onfocusout="basicInvoiceAddComma('expense_amount_paid')">
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

    @include('includes.user-top')
    
    <!-- MAIN CONTENT-->
    <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid">
            <section> 
                @if($type == 'create')
                <h3 class="sumb--title">New Expense</h3>
                @elseif($type == 'edit')

                <h3 class="sumb--title">Edit Expense ({{ 'EXP-00000'. $expense_details['transaction_number'] }})</h3>
                @elseif($type == 'view')
                <h3 class="sumb--title">
                    Expense {{ 'EXP-00000'. $expense_details['transaction_number'] }}
                    <span class="invoice--status-icon {{$expense_details['status']}}">
                        @if(!empty($expense_details) && $expense_details['status'] == 'Voided')   
                            {{$expense_details['status']}}
                        @elseif(!empty($expense_details) && $expense_details['status'] == 'Paid')
                            {{$expense_details['status']}}
                        @endif
                    </span>
                    
                </h3>
                    
                <div class="invoice--status-deets">This expense entry is on Read Only mode. Entries flagged as <u>{{$expense_details['status']}}</u> cannot be edited.</div>
                @endif
            </section>

            <hr class="form-cutter">
            
            <section>
               
                @if($type == 'edit')
                    <form id="expense-form-edit" action="/basic/expense/{{ $expense_details['id'] }}/update" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    {{ method_field('put') }}
                @elseif($type == 'create')
                    <form id="expense-form-create" action="/basic/expense-save" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                @elseif($type == 'view')
                    <form id="expense-form-view" action="" method="" enctype="multipart/form-data">
                    {{ csrf_field() }}
                @endif
                        <div class="row">
                            <div class="col-xl-6">
                                <div class="row">

                                    <div class="col-xl-12">
                                        <div class="form-input--wrap">
                                            <label class="form-input--question">Expense Number <span>Read-Only</span></label>
                                            <div class="form--inputbox readOnly row">
                                                <div class="col-12">
                                                    <input type="text" id="expense_number" name="" required readonly value="{{ !empty($expense_details['transaction_number']) ? 'EXP-'. str_pad($expense_details['transaction_number'], 6, '0', STR_PAD_LEFT)  : 'EXP-'. str_pad($data['expenses_count'], 6, '0', STR_PAD_LEFT) }}">
                                                    <input type="hidden" id="expense_number" name="expense_number" required readonly="" value="{{ !empty($expense_details['transaction_number']) ? $expense_details['transaction_number']  : $data['expenses_count'] }}">

                                                    @error('expense_number')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-12">
                                        <div class="form-input--wrap">
                                            <label class="form-input--question" for="expense_date">Date <span>DD/MM/YYYY</span></label>
                                            <div class="date--picker row">
                                                <div class="col-12">
                                                    <input type="text" id="expense_date" name="expense_date" required readonly value="{{  !empty($expense_details['issue_date']) ? $expense_details['issue_date'] :  date('d/m/Y') }}">
                                                    @error('expense_date')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-12">
                                        <div class="form-input--wrap">
                                            <label class="form-input--question" for="expense_due_date">Due Date <span>DD/MM/YYYY</span></label>
                                            <div class="date--picker row">
                                                <div class="col-12">
                                                    <input type="text" id="expense_due_date" name="expense_due_date" required readonly value="{{ !empty($expense_details && $expense_details['due_date']) ? $expense_details['due_date'] : '' }}" autocomplete="off">
                                                    @error('expense_due_date')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-12">
                                        <div class="form-input--wrap">
                                            <label for="client_name" class="form-input--question">
                                                Recipient's Name
                                            </label>
                                            <div class="form--inputbox recentsearch--input row">
                                                <div class="searchRecords col-12">
                                                    <input type="text" id="client_name" name="client_name" required class="form-control" placeholder="Search Client Name" aria-label="Client Name" aria-describedby="button-addon2" autocomplete="off"  value="{{ !empty($expense_details['client_name']) ? $expense_details['client_name'] : '' }}">
                                                    @error('client_name')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form--recentsearch clientname row">
                                                <div class="col-12">
                                                    <div class="form--recentsearch__result">
                                                        <ul>
                                                            @if (empty($exp_clients))
                                                                <li>You dont have any contacts at this time</li>
                                                            @else
                                                                @php $counter = 0; @endphp
                                                                @foreach ($exp_clients as $ec)
                                                                    @php $counter ++; @endphp
                                                                    <li>
                                                                        <button type="button" class="dcc_click" data-myid="{{ $counter }}">
                                                                            <span id="data_name_{{ $counter }}">{{ $ec['client_name'] }}</span>
                                                                        </button>
                                                                    </li>
                                                                @endforeach
                                                            @endif

                                                            <li class="add--newactclnt">
                                                                <label for="savethisrep">
                                                                    <input type="checkbox" id="savethisrep" name="savethisrep" value="yes" class="form-check-input" {{ !empty($form['save_client']) ? 'checked' : '' }}>
                                                                    <div class="option--title">
                                                                        Add as a new contact?
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

                                </div>
                                
                            </div>
                            <div class="col-xl-6">
                                <div class="row" style="height: 95.7%;">

                                    <div class="col-xl-12">

                                        <div class="sumb-expense-upload-container d-flex align-items-center justify-content-center">

                                            <div id="sumb-file-upload-container">
                                                
                                                <div class="sumb-expense-dropzone">
                                                    <i class="fa-solid fa-upload"></i>
                                                    <p>Upload a file</p>
                                                    <p class="muted">
                                                        Drag & drop here or select your file manually<br>
                                                        Accepted file types (.pdf, .png, .jpg, .jpeg)
                                                    </p>
                                                </div>

                                                <input id="file_upload" name="file_upload" accept="image/jpg,image/jpeg,image/png,application/pdf" type="file" class="sumb-expense-dropzone-input" onchange="logoUpload()">
                                                <input type="hidden" id="file_url" name="file_url" value="{{ !empty($expense_details['logo']) ? $expense_details['logo'] : '' }}">
                                                @error('file_upload')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror

                                            </div>

                                            <div id="sumb-receipt-container">
                                                <!-- pdf upload  -->
                                                <iframe id="pdf-preview" src="{{ !empty($expense_details['logo']) ? asset($expense_details['logo']).'#toolbar=0' : '' }}"></iframe>

                                                <div class="sumb-expense-receipt-actions d-flex">
                                                    <div role="presentation" data-ref="toggled-wrapper">
                                                        <button class="btn sumb--btn delepart deleFile" type="button" ><i class="fa-solid fa-trash-alt"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                
                            </div>
                        </div>
                        <hr class="form-cutter">
                        <!-- <div class="row">
                            <div class="col-xl-8">
                                &nbsp;
                            </div>

                            <div class="col-xl-4">
                                <div class="form-input--wrap">
                                    <label class="form-input--question">
                                        Tax options
                                    </label>
                                    <div class="row">
                                        <div class="col-12">
                                            <select name="tax_type" id="tax_type" class="form-input--dropdown" value="">
                                                @if(empty($expense_details['default_tax']))
                                                    <option value="0">Tax Inclusive</option>
                                                    <option value="1">Tax Exclusive</option>
                                                @else
                                                    <option <?php echo ($expense_details['default_tax']) ==  'tax_inclusive' ? ' selected="selected"' : '';?> value="0">Tax Inclusive</option>
                                                    <option <?php echo ($expense_details['default_tax']) ==  'tax_exclusive' ? ' selected="selected"' : '';?> value="1">Tax Exclusive</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                        <div class="row expsenses--table">
                            <div class="col-xl-12">
                                <div class="table-responsive">
                                    <table id="partstable">
                                        <thead>
                                            <tr>
                                                <th scope="col" style="width:200px; min-width:200px;">Description</th>
                                                <th scope="col" style="width:20px; min-width:20px;">Qty</th>
                                                <th scope="col" style="width:20px; min-width:20px;">Unit Price</th>
                                                <th scope="col" style="width:100px; min-width:100px;">Tax Rate</th>
                                                <th scope="col" style="width:40px; min-width:40px;">Amount</th>
                                                <th scope="col" style="width:40px; min-width:40px;">&nbsp;</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                                @if (empty($expense_particulars)) 
                                                <tr>
                                                    <td>
                                                        <textarea name="expense_description[]" id="expense_description" step="any" class="autoresizing" required></textarea>
                                                    </td>
                                                    <td>
                                                        <input type="float" id="item_quantity" name="item_quantity[]" step="any"  required>
                                                    </td>
                                                    <td>
                                                        <input type="float" id="item_unit_price" name="item_unit_price[]" step="any"  required onfocusin="basicExpenseRemoveComma(this)" onfocusout="basicExpenseAddComma(this)">
                                                    </td>
                                                    <td hidden="hidden">
                                                        <div class="form-input--wrap">
                                                            <div class="row">
                                                                <div class="col-12 for--tables">
                                                                    <select class="form-input--dropdown" data-live-search="true" id="item_account" name="item_account[]">
                                                                        <option value="">select</option>
                                                                        @if(!empty($chart_account))
                                                                            @foreach ($chart_account as $particulars)
                                                                                <option value="{{$particulars['id']}}">{{ $particulars['chart_accounts_particulars_code'] }} - {{ $particulars['chart_accounts_particulars_name'] }}</option>
                                                                            @endforeach
                                                                        @endif
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if(!empty($tax_rates))

                                                            <!-- <div class="btn-group sumb--dashboardDropdown transaction--filter" role="group">
                                                                <button id="expense_tax_id" name="expense_tax_id[]"  type="button" data-toggle="dropdown" aria-expanded="false"  value="" >
                                                                    Tax Rate Option
                                                                </button>

                                                                <input type="hidden" name="expense_tax[]" id="expense_tax" value="">
                                                                <input type="hidden" name="expense_tax_name[]" id="expense_tax_name" value="">

                                                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop_type">

                                                                    @foreach($tax_rates as $tax_rate)
                                                                        <a class="dropdown-item" href="javascript:void(0)" hidden="hidden" id="{{'tax_rate_id_'.$tax_rate['id'].'_0'}}" >{{ !empty($tax_rate['id']) ? $tax_rate['id'] : ''}}</a>
                                                                        <a class="dropdown-item" href="javascript:void(0)" type="button"  id="{{$tax_rate['id'].'_0'}}"  onclick="getBasicExpenseTaxOptions(this,'{{$tax_rate['tax_rates']}}', '{{$tax_rate['id'].'_0'}}', '{{$tax_rate['id']}}')">{{ $tax_rate['tax_rates_name'] }}</a>
                                                                    @endforeach

                                                                </div>
                                                            </div> -->


                                                            <input type="hidden" name="expense_tax_id[]" id="expense_tax_id" value="">
                                                            <div class="form-input--wrap">
                                                                <div class="row">
                                                                    <div class="col-12 for--tables">
                                                                        <select class="form-input--dropdown"  id="expense_tax" name="expense_tax[]" value="" onchange="getTaxRates(this)" required>
                                                                            <option selected value="">Tax Rate Option</option>    
                                                                            @foreach($tax_rates as $tax_rate)
                                                                                <!-- <option hidden="hidden" id="{{'tax_rate_id_'.$tax_rate['id'].'_0'}}" value="{{$tax_rate['id']}}"></option> -->
                                                                                <option id="{{$tax_rate['id'].'_0'}}" value="{{$tax_rate['id'].'#|#'.$tax_rate['tax_rates']}}">{{$tax_rate['tax_rates_name']}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <input class="input--readonly" readonly id="expense_amount" name="expense_amount[]" type="float" step="any" value="{{ !empty($prts['parts_amount']) ? $prts['parts_amount'] : 0.00 }}"  required>

                                                    </td>
                                                    <td class="tableOptions">
                                                        <button class="btn sumb--btn delepart" type="button" ><i class="fa-solid fa-trash-alt"></i></button>
                                                    </td>
                                                </tr>
                                            @else
                                            
                                            <tr>
                                                @foreach ($expense_particulars as $prts)
                                                
                                                <td>
                                                    <textarea name="expense_description[]" id="expense_description" step="any" class="autoresizing" required>{{ !empty($prts['parts_description']) ? $prts['parts_description'] : '' }}</textarea>
                                                </td>
                                                
                                                <td>
                                                    <input type="float" id="item_quantity" name="item_quantity[]" value="{{ !empty($prts['parts_quantity']) ? $prts['parts_quantity'] : '' }}"  required>
                                                </td>
                                                <td>
                                                    <input type="float" id="item_unit_price" name="item_unit_price[]" value="{{ !empty($prts['parts_unit_price']) ? number_format($prts['parts_unit_price'], 2) : 0.00 }}" step="any"  required  onfocusin="basicExpenseRemoveComma(this)" onfocusout="basicExpenseAddComma(this)">
                                                </td>
                                                <td hidden="hidden">
                                                    <div class="form-input--wrap">
                                                        <div class="row">
                                                            <div class="col-12 for--tables">
                                                                <select class="form-input--dropdown" data-live-search="true" id="item_account" name="item_account[]" step="any">
                                                                    @if(!empty($chart_account))
                                                                        @foreach ($chart_account as $particulars)
                                                                            <option {{!empty($prts['chart_accounts_particulars']) && $prts['chart_accounts_particulars']['id'] == $particulars['id'] ? 'selected="selected"' : '' }} value="{{$particulars['id']}}">{{ $particulars['chart_accounts_particulars_code'] }} - {{ $particulars['chart_accounts_particulars_name'] }}</option>
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if(!empty($tax_rates))

                                                        <!-- <div class="btn-group sumb--dashboardDropdown transaction--filter" role="group">
                                                            <button id="expense_tax_id" name="expense_tax_id[]"  type="button" data-toggle="dropdown" aria-expanded="false"  value="" >
                                                                {{!empty($prts) && isset( $prts['invoice_tax_rates']) ? $prts['invoice_tax_rates']['tax_rates_name'] : $prts['parts_tax_rate_name'] }}
                                                            </button>

                                                            <input type="hidden" name="expense_tax[]" id="expense_tax" value="">
                                                            <input type="hidden" name="expense_tax_name[]" id="expense_tax_name" value="">

                                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop_type">

                                                                @foreach($tax_rates as $tax_rate)
                                                                    <a class="dropdown-item" href="javascript:void(0)" hidden="hidden" id="{{'tax_rate_id_'.$tax_rate['id'].'_0'}}" >{{ !empty($tax_rate['id']) ? $tax_rate['id'] : ''}}</a>
                                                                    <a class="dropdown-item" href="javascript:void(0)" type="button"  id="{{$tax_rate['id'].'_0'}}"  onclick="getBasicExpenseTaxOptions(this, '{{$tax_rate['tax_rates']}}', '{{$tax_rate['id'].'_0'}}', '{{$tax_rate['id']}}')">{{ $tax_rate['tax_rates_name'] }}</a>
                                                                @endforeach

                                                            </div>
                                                        </div> -->
                                                        <input type="hidden" name="expense_tax_id[]" id="expense_tax_id" value="{{!empty($prts['parts_tax_rate_id']) ? $prts['invoice_tax_rates']['tax_rates'] : ''}}">
                                                        <div class="form-input--wrap">
                                                            <div class="row">
                                                                <div class="col-12 for--tables">
                                                                    <select class="form-input--dropdown" id="expense_tax" name="expense_tax[]" onchange="getTaxRates(this)" value="">
                                                                        <option selected value="">Tax Rate Option</option>    
                                                                        @foreach($tax_rates as $tax_rate)
                                                                            <option  id="{{$tax_rate['id'].'_0'}}" {{!empty($prts['parts_tax_rate_id']) && $prts['parts_tax_rate_id'] == $tax_rate['id'] ? 'selected="selected"' : ''}} value="{{$tax_rate['id'].'#|#'.$tax_rate['tax_rates']}}">{{$tax_rate['tax_rates_name']}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </td>
                                                
                                                <td>
                                                    <input class="input--readonly" type="float" id="expense_amount" name="expense_amount[]" value="{{ !empty($prts['parts_amount']) ? number_format($prts['parts_amount'], 2) : 0.00 }}"  required>
                                                </td>
                                                <td class="tableOptions">
                                                    <button class="btn sumb--btn delepart" type="button"><i class="fas fa-trash-alt"></i></button>
                                                </td>
                                            </tr>
                                                @endforeach
                                            @endif
                                            
                                            <tr class="add--new-line" style="display: {{ empty($expense_details['status']) || $expense_details['status'] === 'Unpaid' ? '' : 'none' }}">
                                                <td colspan="6">
                                                    <button class="btn sumb--btn" type="button" id="addnewline"><i class="fa-solid fa-circle-plus"></i>Add New Line</button> 
                                                </td>
                                            </tr>
                                            
                                            @error('expense_description.*')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                            @error('item_quantity.*')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                            @error('item_unit_price.*')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                            @error('expense_tax.*')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                            @error('expense_amount.*')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                            

                                            <tr class="invoice-separator">
                                            <td colspan="6">
                                                hs
                                            </td>
                                        </tr>
                                        <tr class="expenses-tax--option">
                                            <td colspan="3"></td>
                                            <td colspan="1">Tax Option</td>
                                            <td colspan="3">
                                                <div class="form-input--wrap">
                                                    <div class="col-12 for--tables">
                                                        <select name="tax_type" id="tax_type" class="form-input--dropdown">
                                                        @if(empty($expense_details['default_tax']))
                                                            <option value="0">Tax Inclusive</option>
                                                            <option value="1">Tax Exclusive</option>
                                                        @else
                                                            <option <?php echo ($expense_details['default_tax']) ==  'tax_inclusive' ? ' selected="selected"' : '';?> value="0">Tax Inclusive</option>
                                                            <option <?php echo ($expense_details['default_tax']) ==  'tax_exclusive' ? ' selected="selected"' : '';?> value="1">Tax Exclusive</option>
                                                        @endif
                                                        </select>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="invoice-total--subamount">
                                            <td colspan="3" rowspan="{{ empty($expense_details['status']) || $expense_details['status'] === 'Paid' || $expense_details['status'] === 'Voided' ? '5' : '6' }}">
                                            </td>
                                            <td colspan="1">Subtotal (excl GST)</td>
                                            <td colspan="3">
                                                <input readonly  id="expense_total_amount" step="any" name="expense_total_amount" type="float" value="{{!empty($expense_details) ? number_format($expense_details['sub_total'], 2) : 0 }}">
                                            </td>
                                        </tr>

                                        <tr class="invoice-total--gst">
                                            <td colspan="1" id="" >Total GST {{!empty($expense_details) && $expense_details['total_gst'] > 0 ? '10%' : '0%'}}</td>
                                            <td colspan="3">
                                                <input type="float"  readonly step="any" name="total_gst" id="total_gst" value="{{!empty($expense_details) ? number_format($expense_details['total_gst'], 2) : 0 }}">
                                            </td>
                                        </tr>

                                        <?php if(!empty($expense_details) && $expense_details['amount_paid']>0){ ?>
                                            <tr class="invoice-total--paid">
                                                <td colspan="1">
                                                    Total amount
                                                </td>
                                                <td colspan="3">
                                                    <input type="float" id="" name="" readonly value="{{!empty($expense_details) ? number_format($expense_details['amount_paid'] + $expense_details['total_amount'], 2) : 0 }}">
                                                </td>
                                            </tr>
                                            <tr class="invoice-partial--paid">
                                                <td colspan="4">
                                                    <span><i class="fa-solid fa-list-check"></i>Payment History</span>

                                                    <table class="history--list">
                                                        <?php if(!empty($payment_history)){
                                                                foreach($payment_history as $payment){?>
                                                                    <tr>
                                                                        <td>Less payment on <span><?php echo !empty($payment) ? $payment['date'] : ''; ?></span></td>
                                                                        <td>-{{ !empty($payment) ? number_format($payment['amount_paid'], 2) : 0 }}</td>
                                                                    </tr>
                                                            <?php }}?>
                                                        <!-- Add TR here -->
                                                    </table>
                                                </td>
                                            </tr>
                                        <?php }?>

                                        <tr class="invoice-total--amountdue">
                                            <td colspan="1"><strong>Amount Due</strong></td>
                                            <td colspan="3">
                                                <input type="float"  readonly step="any" class="grandtotal" name="total_amount" id="total_amount" value="${{!empty($expense_details) ? number_format($expense_details['total_amount'], 2) : 0 }}">
                                            </td>
                                        </tr>
                                        @if($type=='edit' && ($expense_details['status'] == 'Unpaid' || $expense_details['status'] == 'PartlyPaid'))
                                            <tr class="settlepayment--invoice">
                                                <td></td>
                                                <td colspan="2">
                                                    <a  class="btn sumb--btn" data-toggle="collapse" href="#" role="button" onclick="confirmPaymentDatePop('Paid', {{$expense_details['id']}}, {{!empty($expense_details) ? $expense_details['total_amount'] : 0 }});">
                                                        <i class="fa-solid fa-floppy-disk"></i>Add Payment
                                                    </a>
                                                </td>
                                            </tr>
                                        @endif

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-navigation">
                            <div class="form-navigation--btns row">
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12 col-12">
                                <a href="/basic/expense" class="btn sumb--btn"><i class="fa-solid fa-circle-left"></i> Back</a>
                                </div> 
                                <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 col-xs-12 col-12">
                                    <button value="save_expense" name="save_expense" style="float: right;" type="submit" class="btn sumb--btn"><i class="fa-solid fa-floppy-disk"></i> Save</button>
                                    <!--<button style="float: right;" type="button" onclick="previewExpense()" class="btn sumb--btn preview--btn"><i class="fa-solid fa-eye"></i> Preview</button>-->
                                    <button style="float: right;" type="reset" class="btn sumb--btn reset--btn expenses--page"><i class="fa fa-ban"></i> Clear Expense</button>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="transaction_id" value="{{ !empty($expense_details['id']) ? $expense_details['id'] : '' }}">
                    </form>
            </section>
        </div>
    </div>
  </div>
 </div>
</div>


<!-- Expense preview model -->
<div class="modal fade bd-example-modal-lg modal-reskin" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog invoice--preview">
    <div class="modal-content">

        <div class="modal-header">
            <h5 class="modal-title invoiceprev--header" id="staticBackdropLabel">Expense Preview</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
            </button>
        </div>

        <!------- Expense Preview Modal ------>

        <div class="modal-body">
            <div class="container">

                <div class="container">
                    <center>
                        <h2 class="mb-4 mt-2">Expense Preview</h4>
                    </center>

                    <div class="invoicetable--header">
                        <div class="row">
                            <div class="col-xl-6 col-lg-6">
                                <ul class="list-unstyled">
                                    <li>To: <span id="expense_preview_to"></span></li>
                                    <li>Invoice number: <span id="expense_preview_expense_number"></span></li>
                                    <li>Issued: <span id="expense_preview_issue_date"></span></li>
                                    <li>Due: <span id="expense_preview_due_date"></span></li>
                                </ul>
                            </div>
                            <div class="col-xl-6 col-lg-6 invoicetable--header_from">
                                <ul class="list-unstyled">
                                    <li>From: <span id="expense_preview_from">{{$userinfo['1']}}</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive mt-3">
                        <table class="table table-striped" id="expense_preview_parts">
                            <thead>
                                <tr>
                                    <th scope="col" style="width:320px; min-width:320px;">Description</th>
                                    <th scope="col" style="width:100px; min-width:100px;">QTY</th>
                                    <th scope="col" style="width:120px; min-width:120px;">Unit Price</th>
                                    <th scope="col" style="width:120px; min-width:120px;">Tax</th>
                                    <th scope="col" style="width:120px; min-width:120px;">Amount</th>
                                </tr>
                            </thead>
                            <tbody id="expense_preview_parts_rows"></tbody>
                                
                        </table>
                    </div>

                    <div class="row mt-4 invoice--extrainfo">
                        <div class="col-xl-8 col-lg-8">
                            <p class="invoice--paymentnotes mb-1">
                                Add additional notes and payment information (e.g Bank Account)
                            </p>
                            <p class="invoice--paymentnotes">
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque in maximus orci. Sed augue lectus, ultrices sit amet enim nec, commodo sodales lacus. Phasellus ultricies molestie eleifend. Interdum et malesuada fames ac ante ipsum primis in faucibus. Nam eu felis ante. Suspendisse sed ex sed felis semper elementum.
                            </p>
                        </div>
                        <div class="col-xl-4 col-lg-4">
                            <table class="table table-clear invoice--paymentinfo">
                                <tbody>
                                    <tr>
                                        <td>
                                            <strong>Subtotal</strong>
                                        </td>
                                        <td class="center" id="expense_preview_sub_total"></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>Total Tax %</strong>
                                        </td>
                                        <td class="center" id="expense_preview_total_tax"></td>
                                    </tr>
                                    <tr class="invoice--paymentinfo-total_amount">
                                        <td>
                                            <strong>Total</strong>
                                        </td>
                                        <td class="center" id="expense_preview_total_amount"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary close--btn" data-dismiss="modal">Close</button>
        </div>
    <!--------------END---------------------->
    </div>
  </div>
</div>

  

<!-- END PAGE CONTAINER-->


@include('includes.footer')

<script>

var counts = 0;
    $(document).ready(function () {
        $(".bd-example-modal-lg").on("hidden.bs.modal", function () {
    // put your default event here
            $("#expense_preview_to").text($("#client_name").val());
            $("#expense_preview_expense_number").text();
            $("#expense_preview_issue_date").text();
            $("#expense_preview_due_date").text();

                // $("#expense_preview_parts_rows").last().remove();
            $("#expense_preview_parts_rows").empty()
            $("#expense_preview_sub_total").text();
            $("#expense_preview_total_tax").text();
            $("#expense_preview_total_amount").text();
        });
    });

    function previewExpense(){
        var to = $("#client_name").val();
        
            $("#expense_preview_to").text($("#client_name").val());
            $("#expense_preview_expense_number").text($("#expense_number").val());
            $("#expense_preview_issue_date").text($("#expense_date").val());
            $("#expense_preview_due_date").text($("#expense_due_date").val());

            expense_description_array = [];
            expense_item_quantity_array = [];
            expense_item_unit_price_array = [];
            expense_amount_array = [];
            expense_tax_array = [];

            $('[name="expense_description[]"]').each(function() {
                expense_description_array.push(this.value);
            })
            $('[name="item_quantity[]"]').each(function() {
                expense_item_quantity_array.push(Number(this.value));
            })
            $('[name="item_unit_price[]"]').each(function() {
                expense_item_unit_price_array.push(Number(this.value));
            })
            $('[name="expense_amount[]"]').each(function() {
                expense_amount_array.push(Number(this.value));
            })
            $('[name="expense_tax[]"]').each(function() {
                expense_tax_array.push(Number(this.value));
            })
            
             $("#partstable #expense_description").each(function (index) {
                $("#expense_preview_parts_rows").append(
                     '<tr><td>'+expense_description_array[index]+'</td>\n'+
                     '<td>'+expense_item_quantity_array[index]+'</td>\n'+
                     '<td>'+expense_item_unit_price_array[index]+'</td>\n'+
                     '<td>'+expense_tax_array[index]+'</td>\n'+
                     '<td>'+expense_amount_array[index]+'</td>\n'+
                    '</tr>');
             });
             $("#expense_preview_sub_total").text($("#expense_total_amount").val());
             $("#expense_preview_total_tax").text($("#total_gst").val());
             $("#expense_preview_total_amount").text($("#total_amount").val());

            $(".bd-example-modal-lg").modal({
                show: true
            });
        }

   $(function() {
        $("#expense_date").datepicker({ dateFormat: 'dd/mm/yy' });
        $("#expense_due_date").datepicker({ dateFormat: 'dd/mm/yy' });
        $("#expense_payment_date").datepicker({ dateFormat: 'dd/mm/yy', beforeShow: function (input, inst) { setDatepickerPos(input, inst) } });     

        $('.dcc_click').on('click', function () {
            var clientid = $(this).data('myid');
            var clientname = $("#data_name_"+clientid).html();
            $('#client_name').val(clientname);
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
            
            if(value == ''){
                $('.form--recentsearch.clientname').hide();
                $('.clientname li.add--newactclnt input').prop('checked',false);
                $('#client_name').removeClass('saveNewRecord');

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

            if(this.id == 'savethisrep') {
                if($('#savethisrep').is(':checked')){
                    $('#client_name').addClass('saveNewRecord');
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
    });

     //Add new row on Table Particulars

    $('#addnewline').on('click', function(){
        counts++;
        $('#partstable tr.add--new-line').before(
            '<tr><td><textarea name=\"expense_description[]\" id=\"expense_description\" class=\"autoresizing\" required></textarea></td>\n'+
            '<td><input type=\"float\" step="any" id=\"item_quantity\" name=\"item_quantity[]\" required \"></td>\n'+
            '<td><input type=\"float\" step="any" id=\"item_unit_price\" name=\"item_unit_price[]\" required \" onfocusin="basicExpenseRemoveComma(this)" onfocusout="basicExpenseAddComma(this)"></td>\n'+
            '<td hidden="hidden">\
                <div class=\"form-input--wrap\"><div class=\"row\"><div class=\"col-12 for--tables\">'+
                '<select class=\"form-input--dropdown\" data-live-search="true" id=\"item_account_'+counts+'\" name=\"item_account[]\" step="any">\n'+
                '</select>\n'+
            '</div></div></div></td>'+
            '<td><input type=\"hidden\" name=\"expense_tax_id[]\" id=\"expense_tax_id\" value=""><div class=\"form-input--wrap\"><div class=\"row\">'+
            '<div class=\"col-12 for--tables\"><select name=\"expense_tax[]\" id=\"expense_tax_'+counts+'\" onchange=getTaxRates(this) class=\"form-input--dropdown\" required></select></div></div></div>\n'+
            '</td>\n'+
            '<td><input class=\"input--readonly\" readonly id=\"expense_amount\" name=\"expense_amount[]\" type=\"float\" step="any" required></td>\n'+
            '<td class=\"tableOptions\">\n'+
                '<button class=\"btn sumb--btn delepart\" type=\"button\" ><i class=\"fa-solid fa-trash-alt\"></i></button>\n'+
            '</td></tr>');
        
        getBasicExpenseChartAccountsParticularsList(counts);
        getTaxRatesList(counts);
    });
       
    function getBasicExpenseChartAccountsParticularsList(id){
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });
        $.ajax({
        method: "GET",
        url: "/basic/expense-chart-accounts",
        // data: post_data,
        success:function(response){
            try{
                response = JSON.parse(response);
                if(response && response.status == "success"){
                    // $("#client_details").show();
                    $("#item_account_"+id).empty();
                    var counter = 0;
                    $("#item_account_"+id).append('<option value="">select</option>')
                    
                    $.each(response.data,function(key,value){
                        $("#item_account_"+id).append('\n\<option value='+value['id']+'>'+value['chart_accounts_particulars_code']+' - '+value['chart_accounts_particulars_name']+'</option>');
                    });
                }else if(response.status == "error"){
                    alert(esponse.err);
                   
                }
            }catch(error){
                // alertBottom(null,'Something went wrong, try again later');
            }
        },
            error:function(error){ 
                // alertBottom(null,"Something went wrong, please try again later");
            }
        });
    }

    function getTaxRatesList(id){
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });
        $.ajax({
        method: "GET",
        url: "/basic/expense-tax-rates",
        // data: post_data,
        success:function(response){
            try{
                response = JSON.parse(response);
                if(response && response.status == "success"){
                    // $("#client_details").show();
                    $("#expense_tax_"+id).empty();
                    var counter = 0;
                    $("#expense_tax_"+id).append('<option selected value="">Tax Rate Option</option>')
                    
                    $.each(response.data,function(key,value){
                        $("#expense_tax_"+id).append('\n\<option value='+value['id']+'#|#'+value['tax_rates']+'>'+value['tax_rates_name']+'</option>');
                    });
                   
                }else if(response.status == "error"){
                    alert(esponse.err);
                }
            }catch(error){
                // alertBottom(null,'Something went wrong, try again later');
            }
        },
            error:function(error){ 
                // alertBottom(null,"Something went wrong, please try again later");
            }
        });
    }


    $('#partstable').on('input', '.autoresizing', function () {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    
    $(document).ready(function () {

        //view page restrict editing for status void and paid
        $("#expense-form-view :input").prop('disabled', true); 

        <?php if(!empty($expense_details['status']) && $expense_details['status'] == 'PartlyPaid'){ ?>
            $("#expense-form-edit :input").prop('disabled', true); 
        <?php }?>
        //file upload hide show scenario handle
        if($('#pdf-preview').attr('src'))
        {
            $('#sumb-file-upload-container').hide();
        }else{
            $('#sumb-receipt-container').hide();
        }

       
        //
        $('.deleFile').on('click',function(){
           
            $('#pdf-preview').attr("src", "");
            $('#sumb-receipt-container').hide();
            $('#file_url').val(" ");
            $('#sumb-file-upload-container').show();
        })
              
        
        //row total Amount,form total amoount, total tax
        var body = $('#partstable').children('tbody').first();
        body.on('change', 'input[type="float"]', function() {
            var cells = $(this).closest('tr').children('td');
            var value1 = cells.eq(1).find('input').val();
            var value2 = Number(cells.eq(2).find('input').val().replace(/\,/g,'')).toFixed(2);
            var value3 = cells.eq(5).find('input').val(parseFloat(value1 * value2).toFixed(2).replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));

            var calculated_total_sum = 0;
            var calculated_total_gst_amount = 0;
            expense_amount_array = [];
            expense_tax_array = [];
            expense_tax_array_id = [];
            
            $('[name="expense_amount[]"]').each(function() {
                expense_amount_array.push(Number(this.value.replace(/\,/g,'')).toFixed(2));
            })
            $('[name="expense_tax[]"]').each(function() {
                // expense_tax_array_id.push(Number(this.value));
                const selected_expense_tax_rate = this.value.split("#|#");
                expense_tax_array_id.push(Number(selected_expense_tax_rate[0]));
                expense_tax_array.push(Number(selected_expense_tax_rate[1]));
                
            })
            // $('[name="expense_tax_id[]"]').each(function() {
            //     expense_tax_array.push(Number(this.value));
            // })
            
            if($("#tax_type").val() == 0)
            {

                $("#partstable #expense_amount").each(function (index) {
                    
                calculated_total_sum += parseFloat(expense_amount_array[index]);
                    
                if(expense_tax_array[index] > 0){
                    var subractbleTaxAmount = (expense_amount_array[index]) * (100 / (100 + (expense_tax_array[index])))
                    var taxAmount = expense_amount_array[index] - subractbleTaxAmount;
                    calculated_total_gst_amount += parseFloat(taxAmount);
                }
                });
                $("#expense_total_amount").val(parseFloat(calculated_total_sum).toFixed(2).replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));
                $("#total_gst").val(parseFloat(calculated_total_gst_amount).toFixed(2).replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));
                $("#total_amount").val(parseFloat(calculated_total_sum).toFixed(2).replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));
            }
            else if($("#tax_type").val() == 1)
            {
                $("#partstable #expense_amount").each(function (index) {

                    calculated_total_sum += parseFloat(expense_amount_array[index]);
                    if(expense_tax_array[index] > 0)
                    {
                        calculated_total_gst_amount += parseFloat((expense_amount_array[index] * expense_tax_array[index])/100);
                    }   
                });
                    
                $("#expense_total_amount").val(parseFloat(calculated_total_sum).toFixed(2).replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));
                $("#total_gst").val(parseFloat(calculated_total_gst_amount).toFixed(2).replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));
                var total_amount = calculated_total_sum + calculated_total_gst_amount;
                $("#total_amount").val(parseFloat(total_amount).toFixed(2).replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));
            }
        });

        body.on('change', $("#tax_type"), function() {

            var calculated_total_sum = 0;
            var calculated_total_gst_amount = 0;
            expense_amount_array = [];
            expense_tax_array = [];
            expense_tax_id_array = [];

            $('[name="expense_amount[]"]').each(function() {
                expense_amount_array.push(Number(this.value.replace(/\,/g,'')).toFixed(2));
            })
            $('[name="expense_tax[]"]').each(function() {
                const selected_expense_tax_rate = this.value.split("#|#");
                expense_tax_id_array.push(Number(selected_expense_tax_rate[0]));
                expense_tax_array.push(Number(selected_expense_tax_rate[1]));
            })
            // $('[name="expense_tax_id[]"]').each(function() {
            //     expense_tax_array.push(Number(this.value));
            // })

            if($("#tax_type").val() == 0)
            {
                $("#partstable #expense_amount").each(function (index) {
                    
                calculated_total_sum += parseFloat(expense_amount_array[index]);
                if(expense_tax_array[index] > 0){
                    var subractbleTaxAmount = (expense_amount_array[index]) * (100 / (100 + (expense_tax_array[index])))
                    var taxAmount = expense_amount_array[index] - subractbleTaxAmount;
                    calculated_total_gst_amount += parseFloat(taxAmount);
                }
                });
                $("#expense_total_amount").val(parseFloat(calculated_total_sum).toFixed(2).replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));
                $("#total_gst").val(parseFloat(calculated_total_gst_amount).toFixed(2).replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));
                $("#total_amount").val(parseFloat(calculated_total_sum).toFixed(2).replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));
            }
            else if($("#tax_type").val() == 1)
            {
                $("#partstable #expense_amount").each(function (index) {
                    calculated_total_sum += parseFloat(expense_amount_array[index]);
                    if(expense_tax_array[index] > 0)
                    {
                        calculated_total_gst_amount += parseFloat((expense_amount_array[index] * expense_tax_array[index])/100);
                    }   
                });
                    
                $("#expense_total_amount").val(parseFloat(calculated_total_sum).toFixed(2).replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));
                $("#total_gst").val(parseFloat(calculated_total_gst_amount).toFixed(2).replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));
                var total_amount = calculated_total_sum + calculated_total_gst_amount;
                $("#total_amount").val(parseFloat(total_amount).toFixed(2).replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));
            }
        });
       
        body.on('click', '.delepart', function(){ 
            $(this).parents('tr').remove();
            
            var calculated_total_sum = 0;
            var calculated_total_gst_amount = 0;
            expense_amount_array = [];
            expense_tax_array = [];
            expense_tax_id_array = [];

            $('[name="expense_amount[]"]').each(function() {
                expense_amount_array.push(Number(this.value.replace(/\,/g,'')).toFixed(2));
            })
            $('[name="expense_tax[]"]').each(function() {
                const selected_expense_tax_rate = this.value.split("#|#");
                expense_tax_id_array.push(Number(selected_expense_tax_rate[0]));
                expense_tax_array.push(Number(selected_expense_tax_rate[1]));
            })
            // $('[name="expense_tax_id[]"]').each(function() {
            //     expense_tax_array.push(Number(this.value));
            // })

            if($("#tax_type").val() == 0)
            {
                $("#partstable #expense_amount").each(function (index) {
                    
                calculated_total_sum += parseFloat(expense_amount_array[index]);
                if(expense_tax_array[index] > 0){
                    var subractbleTaxAmount = (expense_amount_array[index]) * (100 / (100 + (expense_tax_array[index])))
                    var taxAmount = expense_amount_array[index] - subractbleTaxAmount;
                    calculated_total_gst_amount += parseFloat(taxAmount);
                }
                });
                $("#expense_total_amount").val(parseFloat(calculated_total_sum).toFixed(2).replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));
                $("#total_gst").val(parseFloat(calculated_total_gst_amount).toFixed(2).replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));
                $("#total_amount").val(parseFloat(calculated_total_sum).toFixed(2).replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));
            }
            else if($("#tax_type").val() == 1)
            {
                $("#partstable #expense_amount").each(function (index) {

                    calculated_total_sum += parseFloat(expense_amount_array[index]);
                    if(expense_tax_array[index] > 0)
                    {
                        calculated_total_gst_amount += parseFloat((expense_amount_array[index] * expense_tax_array[index])/100);
                    }   
                });
                    
                $("#expense_total_amount").val(parseFloat(calculated_total_sum).toFixed(2).replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));
                $("#total_gst").val(parseFloat(calculated_total_gst_amount).toFixed(2).replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));
                var total_amount = calculated_total_sum + calculated_total_gst_amount;
                $("#total_amount").val(parseFloat(total_amount).toFixed(2).replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));
            }
        });
    });

    function logoUpload(){
        $('#sumb-file-upload-container').hide();
        const fileInput = document.getElementById('file_upload');
        const selectedFile = fileInput.files[0];
        const url = URL.createObjectURL( selectedFile );

        $('#pdf-preview').attr("src", url);

        var fd = new FormData();
        fd.append( "fileInput", $("#file_upload")[0].files[0]);

        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });
        $.ajax({
            method: "POST",
            url: "{{ url('basic/expense-logo-upload') }}",
            data: fd,
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            success : function(response){
                $('#sumb-receipt-container').show();
                $("#file_url").val(response.logo);
            },
            error : function(error){
                alert(error.responseJSON.message);
            }
        });
    }


    function getTaxRates(obj){
        var cell = $(obj).closest('tr').children('td');
        var tax_rate_id = cell.eq(4).find('[name="expense_tax[]"]').val();

        gstCalculation(obj);
        return ;
        var post_data = {
            tax_ids : tax_rate_id
        }
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });
        $.ajax({
        method: "GET",
        url: "/basic/expense-tax-rates/"+tax_rate_id,
        // data: post_data,
        success:function(response){
            try{
                response = JSON.parse(response);
                
                if(response && response.status == "success"){
                    var expense_tax_ids = [];
                    $.each(response.data,function(key,value){
                        cell.eq(4).find('[id="expense_tax_id"]').val(value['tax_rates']);
                    });

                    gstCalculation(obj);

                }else if(response.status == "error"){
                    alert(esponse.err);
                }
            }catch(error){
                // alertBottom(null,'Something went wrong, try again later');
            }
        },
            error:function(error){ 
                // alertBottom(null,"Something went wrong, please try again later");
            }
        });
    }

    function gstCalculation(obj){
    // body.on('change', $("#expense_tax"), function() {
        var calculated_total_sum = 0;
        var calculated_total_gst_amount = 0;
        expense_amount_array = [];
        expense_tax_array = [];
        expense_tax_id_array = [];

        
        $('[name="expense_amount[]"]').each(function() {
            expense_amount_array.push(Number(this.value.replace(/\,/g,'')).toFixed(2));
        })
        $('[name="expense_tax[]"]').each(function() {
            const selected_expense_tax_rate = this.value.split("#|#");
            expense_tax_id_array.push(Number(selected_expense_tax_rate[0]));
            expense_tax_array.push(Number(selected_expense_tax_rate[1]));
        })
        // $('[name="expense_tax_id[]"]').each(function() {
        //     const selected_expense_tax_rate = this.value.split("#|#");
        //     console.log(selected_expense_tax_rate);
        //     expense_tax_array.push(Number(selected_expense_tax_rate[0]));
        // })



        if($("#tax_type").val() == 0)
        {
            $("#partstable #expense_amount").each(function (index) {
                
            calculated_total_sum += parseFloat(expense_amount_array[index]);
            if(expense_tax_array[index] > 0){
                var subractbleTaxAmount = (expense_amount_array[index]) * (100 / (100 + (expense_tax_array[index])))
                var taxAmount = expense_amount_array[index] - subractbleTaxAmount;
                calculated_total_gst_amount += parseFloat(taxAmount);
            }
            });
            
            $("#expense_total_amount").val(parseFloat(calculated_total_sum).toFixed(2).replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));
            $("#total_gst").val(parseFloat(calculated_total_gst_amount).toFixed(2).replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));
            $("#total_amount").val(parseFloat(calculated_total_sum).toFixed(2).replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));
        }
        else if($("#tax_type").val() == 1)
        {
            $("#partstable #expense_amount").each(function (index) {

                calculated_total_sum += parseFloat(expense_amount_array[index]);
                if(expense_tax_array[index] > 0)
                {
                    calculated_total_gst_amount += parseFloat((expense_amount_array[index] * expense_tax_array[index])/100);
                }
            });
                
            $("#expense_total_amount").val(parseFloat(calculated_total_sum).toFixed(2).replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));
            $("#total_gst").val(parseFloat(calculated_total_gst_amount).toFixed(2).replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));
            var total_amount = calculated_total_sum + calculated_total_gst_amount;
            $("#total_amount").val(parseFloat(total_amount).toFixed(2).replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));
        }
    }
       
    function basicExpenseRemoveComma(obj)
    {
        var unit_price = $(obj).closest('td').find('[name="item_unit_price[]"]').val();
        $(obj).closest('td').find('[name="item_unit_price[]"]').val(Number(unit_price.replace(/\,/g,'')).toFixed(2));
    }

    function basicExpenseAddComma(obj)
    {
        var unit_price = $(obj).closest('td').find('[name="item_unit_price[]"]').val();
        $(obj).closest('td').find('[name="item_unit_price[]"]').val(Number(unit_price.replace(/\,/g,'')).toLocaleString(undefined, {maximumFractionDigits: 2, minimumFractionDigits: 2}));

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

    function setDatepickerPos(input, inst) {
        var rect = input.getBoundingClientRect();
        // use 'setTimeout' to prevent effect overridden by other scripts
        setTimeout(function () {
            var scrollTop = $("body").scrollTop();
    	    inst.dpDiv.css({ top: rect.top + input.offsetHeight + scrollTop });
        }, 0);
    }

    function getBasicExpenseTaxOptions(obj, selected_tax_rate, selected_option_tag_id, tax_rate_id)
    {
        var tax_rate = $(obj).closest('td').find('[name="expense_tax_id[]"]').val('');
        var tax_rate_name = $(obj).closest('td').find('[name="expense_tax_id[]"]').text('');
      
        // $("#"+id).val('');
        // $("#"+id).text('');

        $(obj).closest('td').find('[name="expense_tax_id[]"]').val(selected_tax_rate);
        $(obj).closest('td').find('[name="expense_tax_id[]"]').text($("#"+selected_option_tag_id).text());
        $(obj).closest('td').find('[name="expense_tax[]"]').val(tax_rate_id)

        // $("#"+id).val(selected_tax_rate);
        // $("#"+id).text($("#"+selected_option_tag_id).text());

        if($(obj).closest('td').find('[name="expense_tax[]"]').val(tax_rate_id)){
            getTaxRates(obj);
            gstCalculation(obj);
        }
    }


</script>
</body>

</html>
<!-- end document-->