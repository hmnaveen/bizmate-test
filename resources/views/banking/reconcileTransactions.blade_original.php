@include('includes.head')
@include('includes.user-header')

<!-- PAGE CONTAINER-->
<div class="page-container">

    @include('includes.user-top')
    @csrf
    <!-- MAIN CONTENT-->
    <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid">
                <section>
                    <h3 class="sumb--title">Reconcile Bank Transactions</h3>
                </section>
                <br>
                <h4>Bank Accounts</h4>
                @if(count($accounts) < 1)
                    <h5 style="color: red;">No Bank Accounts Found.</h5>
                @else
                <select style="border: none;" name="bankAccount" id="bankAccount" >
                @if(!empty($id))
                    @foreach ($accounts as $account)
                        <option {{ ( $account['account_id'] == $id ) ? 'selected' : '' }} value="{{ $account['account_id'] }}">{{ $account['account_name'] }}</option>
                    @endforeach
                @else
                    <option value="">Select Bank Account</option>
                @endif
                </select> 
                <div class="sumb--recentlogdements sumb--putShadowbox">
                    @if(count($transactions['data'])  == 0)
                        <div class="container overflow-hidden">
                            <div class="row">
                                <div class="col">
                                    <div class="p-3 border bg-light">No Transactions Found</div>
                                </div>
                            </div>
                        </div>
                    @else
                    <div class="row">
                        <div class="col-4">
                            <p>Bank Transactions</p>
                            <hr>
                        </div>
                        <div class="col-8">
                            <p>Transactions in SUM[B]</p>
                            <hr>
                        </div>
                    </div>
                    @php($k = 0)
                    @foreach($transactions['data'] as $transaction)
                        <div class="container reconcileTransactions">
                            <div class="row gx-5">
                                <div class="col-4" style="font-size: small;">
                                    <div class="p-3 border bg-light">
                                        <div class="row">
                                            <div class="col-8">
                                                <p><?php echo substr($transaction['post_date'],0,10); ?></p>
                                                <hr>
                                                <p>{{ $transaction['description'] }}</p>
                                            </div>
                                            <div class="col-4">
                                                <p>{{ ucfirst($transaction['direction']) }}</p>
                                                <hr>
                                                <p>{{ abs($transaction['amount']) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-1" style="font-size: small;">
                                    @foreach($appTransactions as $appTransaction)
                                            @if($appTransaction['total_amount'] == abs($transaction['amount']) && (($appTransaction['transaction_type'] == 'invoice' && $transaction['direction'] == 'credit') || ($appTransaction['transaction_type'] == 'expense' && $transaction['direction'] == 'debit'))) 
                                                <button id="transactionFormCancel_{{ $k }}" onclick="reconcileTransactions('{{ $k }}', '{{$transaction['id']}}', '{{$appTransaction['id']}}', 1)" class="btn sumb--btn" style="margin-top:50px">Ok</button>
                                                @break;
                                            @endif
                                    @endforeach
                                </div>
                                <div class="col-6" style="font-size: small;">
                                    <div class="p-3 border bg-light transactions">
                                        @php($i = 0)
                                        @php($j = 0)
                                        @foreach($appTransactions as $appTransaction)
                                            @if($appTransaction['total_amount'] == abs($transaction['amount']) && (($appTransaction['transaction_type'] == 'invoice' && $transaction['direction'] == 'credit') || ($appTransaction['transaction_type'] == 'expense' && $transaction['direction'] == 'debit'))) 
                                                @php($i++)
                                            @endif
                                        @endforeach

                                        <ul class="nav flex-column">
                                            <li class="nav-item">
                                                <a class="nav-link active" aria-current="page" href="#">Active</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="#">Link</a>
                                            </li>
                                        </ul>
                                        <div class="tab">
                                            <button id="defaultOpen" class="tablinks_{{ $k }}" onclick="openTab(event, 'Match_{{ $k }}','{{ $k }}','{{ $i }}')">Match</button>
                                            <button class="tablinks_{{ $k }}" onclick="openTab(event, 'Create_{{ $k }}','{{ $k }}','{{ $i }}')">Create</button>
                                        </div>

                                        <div id="Create_{{ $k }}" class="tabcontent_{{ $k }}" name="createtransaction">
                                            <!-- <button id="createMatch" onclick="openTransactionForm('{{ $k }}')" class="btn btn-primary">Create Transaction</button> -->
                                            <form>
                                                <div class="form-row">
                                                    
                                                </div>
                                                <div class="form-row">
                                                
                                                </div>
                                                
                                                <button id="addTransactionDetails_{{ $k }}" onclick="openTransactionForm('{{ $k }}')" class="btn sumb--btn" type="button">Add Details</button>
                                            </form>
                                        </div>

                                        <div id="Match_{{ $k }}" class="tabcontent_{{ $k }}">
                                            @if($i > 0)
                                                @foreach($appTransactions as $appTransaction)
                                                    @if($appTransaction['total_amount'] == abs($transaction['amount']) && (($appTransaction['transaction_type'] == 'invoice' && $transaction['direction'] == 'credit') || ($appTransaction['transaction_type'] == 'expense' && $transaction['direction'] == 'debit'))) 
                                                        <div class="row">
                                                            <div class="col-8">
                                                                <p>{{ $appTransaction['issue_date'] }}</p>
                                                                <hr>
                                                                <p>{{ $appTransaction['client_name'] }}</p>
                                                            </div>
                                                            <div class="col-4">
                                                                <p>{{ ucfirst($appTransaction['transaction_type']) }}</p>
                                                                <hr>
                                                                <p>{{ $appTransaction['total_amount'] }}</p>
                                                            </div>
                                                        </div>
                                                        @break
                                                    @endif
                                                @endforeach
                                                @else
                                                    <div class="row">
                                                        <p style="padding-left: 15px;">No Match Found</p>
                                                    </div>
                                            @endif
                                            @if($i > 1)
                                                <div class="row">
                                                    <div id="otherMatches_{{ $k }}" class="col-8">
                                                        <li onclick="openOtherMatches('{{ abs($transaction['amount']) }}','{{ $k }}')" id="otherMatch_{{ $k }}"><a  href="javascript:">{{ $i-1 }} Other Matches Found</a></li>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>

                            <!-- create a matching transaction div -->
                            <div name="createTransactionForm" id="createTransactionForm_{{ $k }}">
                                <form id="transaction-form-create_{{ $k }}" action="/expense-save" method="post" enctype="multipart/form-data">
                                    <div class="form-row">
                                        <div class="col-xl-6">
                                            <label for="client_name" class="form-input--question">
                                                Recipient's Name
                                            </label>
                                            <div class="form--inputbox recentsearch--input row">
                                                <div class="searchRecords col-12">
                                                    <input type="text" id="client_name" name="client_name" required class="form-control" placeholder="Search Client Name" aria-label="Client Name" aria-describedby="button-addon2" autocomplete="off"  value="">
                                                    
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
                                        <div class="col-xl-6">
                                            <div class="form-input--wrap">
                                                <label class="form-input--question" for="expense_date_{{ $k }}">Date <span>MM/DD/YYYY</span></label>
                                                <div class="date--picker row">
                                                    <div class="col-12">
                                                        <input type="text" id="expense_date" name="expense_date_{{ $k }}" required class="form-control" value="">
                                                        @error('expense_date')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                        <div class="row expsenses--table">
                                            <div class="col-xl-12">
                                                <div class="table-responsive">
                                                    <table id="partstable_{{ $k }}">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col" style="width:150px; min-width:150px;">Description</th>
                                                                <th scope="col" style="width:20px; min-width:20px;">Qty</th>
                                                                <th scope="col" style="width:20px; min-width:20px;">Unit Price</th>
                                                                <th scope="col" style="width:140px; min-width:140px;">Account</th>
                                                                <th scope="col" style="width:100px; min-width:100px;">Tax Rate</th>
                                                                <th scope="col" style="width:40px; min-width:40px;">Amount</th>
                                                                <th scope="col" style="width:40px; min-width:40px;">&nbsp;</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <textarea name="expense_description_{{ $k }}_[]" id="expense_description" step="any" class="autoresizing" required></textarea>
                                                                </td>
                                                                <td>
                                                                    <input type="number" id="item_quantity" name="item_quantity_{{ $k }}_[]" step="any"  required>
                                                                </td>
                                                                <td>
                                                                    <input type="number" id="item_unit_price" name="item_unit_price_{{ $k }}_[]" step="any"  required>
                                                                </td>
                                                                <td>

                                                                    <div class="form-input--wrap">
                                                                        <div class="row">
                                                                            <div class="col-12 for--tables">
                                                                                <select class="form-input--dropdown" data-live-search="true" id="item_account" name="item_account_{{ $k }}_[]" required>
                                                                                    <option value="">select</option>
                                                                                    
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <input type="hidden" name="expense_tax_{{ $k }}_[]" id="expense_tax" value="">
                                                                    <div class="form-input--wrap">
                                                                        <div class="row">
                                                                            <div class="col-12 for--tables">
                                                                                <select class="form-input--dropdown"  id="expense_tax" name="expense_tax_{{ $k }}_[]" value="" onchange="getTaxRates(this)">
                                                                                    <option selected value="">Tax Rate Option</option>    
                                                                                    
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <input class="input--readonly" readonly id="expense_amount" name="expense_amount_{{ $k }}_[]" type="number" step="any" value=""  required>
                                                                </td>
                                                                <td class="tableOptions">
                                                                    <button class="btn sumb--btn delepart_{{ $k }}" type="button" ><i class="fa-solid fa-trash-alt"></i></button>
                                                                </td>
                                                            </tr>
                                                            
                                                            
                                                            <tr class="add--new-line_{{ $k }}">
                                                                <td colspan="7">
                                                                    <button class="btn sumb--btn" type="button" onclick="addNewLine('{{ $k }}')" id="addnewline_{{ $k }}"><i class="fa-solid fa-circle-plus"></i>Add New Line</button> 
                                                                </td>
                                                            </tr>
                                                        
                                                            
                                                            <tr class="invoice-separator">
                                                                <td colspan="7">&nbsp;</td>
                                                            </tr>

                                                            <tr class="expenses-tax--option">
                                                                <td colspan="4">&nbsp;</td>
                                                                <td>Tax Option</td>
                                                                <td colspan="2">
                                                                    <div class="form-input--wrap">
                                                                        <div class="col-12 for--tables">
                                                                            <select name="tax_type_{{ $k }}" id="tax_type_{{ $k }}" class="form-input--dropdown">
                                                                            
                                                                                <option value="0">Incl. Tax</option>
                                                                                <option value="1">Excl. Tax</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>

                                                            <tr class="invoice-total--subamount">
                                                                <td colspan="4" rowspan="3">
                                                                    &nbsp;
                                                                </td>
                                                                <td>Subtotal</td>
                                                                <td colspan="2">
                                                                    <input readonly required id="expense_total_amount_{{ $k }}" step="any" name="expense_total_amount_{{ $k }}" type="number" value="">
                                                                
                                                                </td>
                                                            </tr>

                                                            <tr class="invoice-total--gst">
                                                                <td>Total GST</td>
                                                                <td colspan="2">
                                                                    <input type="number" required readonly step="any" name="total_gst_{{ $k }}" id="total_gst_{{ $k }}" value="">
                                                                    
                                                                </td>
                                                            </tr>

                                                            <tr class="invoice-total--amountdue">
                                                                <td><strong>Total</strong></td>
                                                                <td colspan="2">
                                                                    <strong id="grandtotal"></strong>
                                                                    <input type="number" required readonly step="any" class="grandtotal" name="total_amount_{{ $k }}" id="total_amount_{{ $k }}" value="">
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
                                                    <button id="transactionFormCancel_{{ $k }}" onclick="transactionFormCancel('{{ $k }}')" class="btn sumb--btn">Cancel</button>
                                                </div> 
                                                <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 col-xs-12 col-12">
                                                    <button value="save_transaction_{{ $k }}" name="save_transaction_{{ $k }}" style="float: right;" type="submit" class="btn sumb--btn"><i class="fa-solid fa-floppy-disk"></i> Save</button>
                                                    <!-- <button style="float: right;" type="button" onclick="previewExpense()" class="btn sumb--btn preview--btn"><i class="fa-solid fa-eye"></i> Preview</button> -->
                                                    <button id="createTransactionFormClear_{{ $k }}" style="float: right;" type="reset" class="btn sumb--btn reset--btn"><i class="fa fa-ban"></i> Clear Expense</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- matchable transactions display div -->
                                    <div class="match_{{ $k }} sumb--recentlogdements matchBox sumb--putShadowbox">

                                    </div>
                                </form>
                            </div>
                            <br>
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
                                            Display: {{$transactions['per_page']}} Items
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
        $("#bankAccount").on("change", function(){
             id = $("#bankAccount").val();
            // var url = "{{ route('reconcileTransactions', ':id') }}";
            // url = url.replace(':id', id);
	        // location.href = url;
            // url.searchParams.append('accountID', id);
            

            if ('URLSearchParams' in window) {
                var searchParams = new URLSearchParams(window.location.search);
                searchParams.forEach((value, key) => {
                    if(key == 'accountID'){
                        searchParams.delete(key);
                    }
                    // console.log(key);
                });
                searchParams.set('accountID', id);
                searchParams.set('page', 1);
                window.location.search = searchParams.toString();
            }
        });
    });

    $(document).ready(function () {

        $("#date").datepicker();
        $("#due_date").datepicker();

        $(".sumb--recentlogdements.matchBox").hide();
        //$("#createTransactionForm").hide();

        $('.reconcileTransactions').children().each(function () {
            if($(this).attr('name') == "createTransactionForm"){
               // console.log($(this).attr('name'));
                $(this).hide();
            }
        });

        $('.transactions').children().each(function () {
            if($(this).attr('name') == "createtransaction"){
                //console.log("dsdsdas");
                $(this).hide();
            }
        });
    });

    function openOtherMatches(matchAmount,id){
        accountID = $("#bankAccount").val();
       // console.log(matchAmount);
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });
        $.ajax({
            method: 'GET',
            url: "/get-reconcile-matches/?amount="+matchAmount+"&accountID="+accountID,
            success: function(response){
                try{
                    response = JSON.parse(response);
                    if(response && response.status == "success"){
                        var matchListID = "match_list_"+id;
                        //console.log(response);
                        $(".match_"+id+ ".sumb--recentlogdements").show();
                        $(".match_"+id+ ".sumb--recentlogdements").append(
                            '<div class="table-responsive">\n'+
                            '<table class='+matchListID+'>\n'+
                            '<thead><tr><th></th><th>Expense date </th>\n'+
                            '<th>Client </th>\n'+
                            '<th>Refrence </th>\n'+
                            '<th>Spent </th>\n'+
                            '<th>Received </th></tr></thead><tbody>\n');

                        response.data.forEach(element => {
                            var expenseAmount = "";
                            var invoiceAmount = "";
                            if(element['transaction_type'] == "expense"){
                                expenseAmount =  element["total_amount"];
                            }else{
                                invoiceAmount =  element["total_amount"];
                            }
                            $(".match_list_"+id).append(
                            '<tr><td> <input class="" type="checkbox" id="checkboxNoLabel" value="" aria-label="..."></td>\n'+
                            '<td>'+element["issue_date"]+'</td>\n'+
                            '<td>'+element["client_name"]+'</td>\n'+
                            '<td>'+element["transaction_number"]+'</td>\n'+

                            '<td>'+expenseAmount+'</td>\n'+
                            '<td>'+invoiceAmount+'</td>\n'+
                            '</tr>');
                        });
                        var cancelMatchDiv = "cancelMatchDiv_" + id;
                        $(".match_list_"+id).append('</tbody>');
                        $(".match_list_"+id).append(
                            '<div id='+cancelMatchDiv+' class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12 col-12">\n'+
                            '<button  onclick="cancelMatchDiv('+id+')" class="btn sumb--btn">Cancel</button>\n'+
                            '</div>');

                        $("#otherMatches_"+id).hide();

                         

                        // $("#chart_accounts_type_id").val(response.data['chart_accounts_type_id']);
                        // $("#chart_accounts_part_id").val(response.data['id']);
                        // $("#chart_accounts_parts_code").val(response.data['chart_accounts_particulars_code']);
                        // $("#chart_accounts_parts_name").val(response.data['chart_accounts_particulars_name']);
                        // $("#chart_accounts_description").val(response.data['chart_accounts_particulars_description']);
                        // $("#chart_accounts_tax_rate").val(response.data['invoice_tax_rates']['id']);
                    }else if(response.status == "error"){
                        alert(esponse.err);
                    }
                }
                catch(error){
                }
            },
            error:function(error){ 
            }
        });
    } 

    function cancelMatchDiv(id){
       // $("#cancelMatchDiv_"+id).on('click', function (){
        $(".match_"+id+ ".sumb--recentlogdements").empty();
        $(".match_"+id+ ".sumb--recentlogdements").hide();
            $("#otherMatches_"+id).show();
       // });
    }

    function transactionFormCancel(id){
        $("#createTransactionForm_"+id).hide();
    }

    function openTab(evt, tabName, id, matchCount) {
        if(tabName == ("Create_"+id)){
            $("#Match_"+id).hide();
        }else{
            $("#Create_"+id).hide();
            $(".match_"+id+ ".sumb--recentlogdements").empty();
            $("#otherMatches_"+id).show();
            $("#createTransactionForm_"+id).hide();
        }
        // console.log(matchCount);
        // console.log(id);
        // Declare all variables
        var i, tabcontent, tablinks;

        // Get all elements with class="tabcontent" and hide them
        tabcontent = document.getElementsByClassName("tabcontent_"+id);
       // console.log(tabcontent);
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

        // Get the element with id="defaultOpen" and click on it
       // document.getElementById("defaultOpen").click();
    }

    $(function() {
       // $("#expense_date").datepicker().datepicker('setDate', 'today');
        $("#expense_date").datepicker();
        // $("#expense_due_date").datepicker();
        $('.dcc_click').on('click', function () {
            //console.log('clicked!');
            //console.log( $(this).data('myid') );
            var clientid = $(this).data('myid');
            var clientname = $("#data_name_"+clientid).html();
           // var clientdesc = $("#data_desc_"+clientid).html();
            //console.log(clientdesc);
            $('#client_name').val(clientname);
            //$('#invoice_details').val(clientdesc);
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
    function addNewLine(id){    
        // $('#addnewline_'+id).on('click', function(){
       
        $('#partstable_'+id+ ' tr.add--new-line_'+id).before(
            '<tr><td><textarea name=\"expense_description_'+id+'_[]\" id=\"expense_description\" class=\"autoresizing\" required></textarea></td>\n'+
            '<td><input type=\"number\" step="any" id=\"item_quantity\" name=\"item_quantity_'+id+'_[]\" required \"></td>\n'+
            '<td><input type=\"number\" step="any" id=\"item_unit_price\" name=\"item_unit_price_'+id+'_[]\" required \"></td>\n'+
            '<td>'+
                '<select style="border: none;" class="selectpicker" data-live-search="true" id=\"item_account\" name=\"item_account_'+id+'_[]\" step="any" required>\n'+
                    '<option value="">select</option>\n'+
                    '<option value="400-Advertising">400 - Advertising</option>\n'+
                    '<option value="404-Bank Fees">404 - Bank Fees</option>\n'+
                    '<option value="408-Cleaning">408 - Cleaning</option>\n'+
                    '<option value="412-Consulting & Accounting">412 - Consulting & Accounting</option>\n'+
                    '<option value="420-Entertainment">420 - Entertainment</option>\n'+
                    '<option value="425-Freight & Courier">425 - Freight & Courier</option>\n'+
                    '<option value="429-General Expenses">429 - General Expenses</option>\n'+
                    '<option value="433-Insurance">433 - Insurance</option>\n'+
                    '<option value="437-Interest Expense">437 - Interest Expense</option>\n'+
                    '<option value="441-Legal expenses">441 - Legal expenses</option>\n'+
                    '<option value="445-Light, Power, Heating">445 - Light, Power, Heating</option>\n'+
                    '<option value="449-Motor Vehicle Expenses">449 - Motor Vehicle Expenses</option>\n'+
                    '<option value="453-Office Expenses">453 - Office Expenses</option>\n'+
                    '<option value="461-Printing & Stationery">461 - Printing & Stationery</option>\n'+
                    '<option value="469-Rent">469 - Rent</option>\n'+
                    '<option value="473-Repairs and Maintenance">473 - Repairs and Maintenance</option>\n'+
                    '<option value="485-Subscriptions">485 - Subscriptions</option>\n'+
                    '<option value="489-Telephone & Internet">489 - Telephone & Internet</option>\n'+
                    '<option value="493-Travel National">493 - Travel - National</option>\n'+
                    '<option value="494-Travel International">494 - Travel - International</option>\n'+
                    '<option value="710-Office Equipment">710 - Office Equipment</option>\n'+
                    '<option value="720-Computer Equipment">720 - Computer Equipment</option>\n'+
                '</select>\n'+
            '</td>'+
            '<td><select style=\"border: none;\" name=\"expense_tax_'+id+'_[]\" id=\"expense_tax\" required><option value=\"0\">Tax Exempt</option><option value=\"10\">General Tax</option></select></td>\n'+
            '<td><input readonly id=\"expense_amount\" name=\"expense_amount_'+id+'_[]\" type=\"number\" step="any" required></td>\n'+
            '<td class=\"tableOptions\">\n'+
                '<button class=\"btn sumb-del-btn delepart_'+id+'" type=\"button\" ><i class=\"fa-solid fa-trash\"></i></button>\n'+
            '</td></tr>');
        // });
    }

    // window.onload = function(){
    //     document.getElementsByName("createTransactionForm").style.display='none';
    // };

    function openTransactionForm(id){
        $("#createTransactionForm_"+id).show();
        loadPartsTable(id);
    }

   // $(document).ready(function () {
    function loadPartsTable(id){
                
        //row total Amount,form total amoount, total tax
        var body = $('#partstable_'+id).children('tbody').first();
        body.on('change', 'input[type="number"]', function() {
            var cells = $(this).closest('tr').children('td');
            var value1 = cells.eq(1).find('input').val();
            var value2 = cells.eq(2).find('input').val();
            var value3 = cells.eq(5).find('input').val(value1 * value2);

            var calculated_total_sum = 0;
            var calculated_total_gst_amount = 0;
            expense_amount_array = [];
            expense_tax_array = [];

            $('[name="expense_amount_'+ id +'_[]"]').each(function() {
                expense_amount_array.push(Number(this.value));
            })
            $('[name="expense_tax_'+ id +'_[]"]').each(function() {
                expense_tax_array.push(Number(this.value));
            })
           
            if($("#tax_type_"+id).val() == 0)
            {
               
                $('[name="expense_amount_'+ id +'_[]"]').each(function (index) {
                    calculated_total_sum += parseFloat(expense_amount_array[index]);
                    if(expense_tax_array[index] > 0){
                        var subractbleTaxAmount = (expense_amount_array[index]) * (100 / (100 + (expense_tax_array[index])))
                        var taxAmount = expense_amount_array[index] - subractbleTaxAmount;
                        calculated_total_gst_amount += parseFloat(taxAmount);
                    }
                });
                $("#expense_total_amount_"+id).val(Number(calculated_total_sum.toFixed(2)));
                $("#total_gst_"+id).val(Number(calculated_total_gst_amount.toFixed(2)));
                $("#total_amount_"+id).val(Number(calculated_total_sum.toFixed(2)));
            }
            else if($("#tax_type_"+id).val() == 1)
            {
                $('[name="expense_amount_'+ id +'_[]"]').each(function (index) {

                    calculated_total_sum += parseFloat(expense_amount_array[index]);
                    if(expense_tax_array[index] > 0)
                    {
                    calculated_total_gst_amount += parseFloat((expense_amount_array[index] * expense_tax_array[index])/100);
                    }   
                });
                    
                $("#expense_total_amount_"+id).val(Number(calculated_total_sum.toFixed(2)));
                $("#total_gst_"+id).val(Number(calculated_total_gst_amount.toFixed(2)));
                var total_amount = calculated_total_sum + calculated_total_gst_amount;
                $("#total_amount_"+id).val(Number(total_amount.toFixed(2)));
            }
        });

        body.on('change', $('#expense_tax_'+id), function() {
            var calculated_total_sum = 0;
            var calculated_total_gst_amount = 0;
            expense_amount_array = [];
            expense_tax_array = [];

            $('[name="expense_amount_'+ id +'_[]"]').each(function() {
                expense_amount_array.push(Number(this.value));
            })
            $('[name="expense_tax_'+ id +'_[]"]').each(function() {
                expense_tax_array.push(Number(this.value));
            })

            if($('#tax_type_'+id).val() == 0)
            {
                $('[name="expense_amount_'+ id +'_[]"]').each(function (index) {
                    
                calculated_total_sum += parseFloat(expense_amount_array[index]);
                if(expense_tax_array[index] > 0){
                    var subractbleTaxAmount = (expense_amount_array[index]) * (100 / (100 + (expense_tax_array[index])))
                    var taxAmount = expense_amount_array[index] - subractbleTaxAmount;
                    calculated_total_gst_amount += parseFloat(taxAmount);
                }
                });
                $("#expense_total_amount_"+id).val(Number(calculated_total_sum.toFixed(2)));
                $("#total_gst_"+id).val(Number(calculated_total_gst_amount.toFixed(2)));
                $("#total_amount_"+id).val(Number(calculated_total_sum.toFixed(2)));
            }
            else if($("#tax_type_"+id).val() == 1)
            {
                $('[name="expense_amount_'+ id +'_[]"]').each(function (index) {

                    calculated_total_sum += parseFloat(expense_amount_array[index]);
                    if(expense_tax_array[index] > 0)
                    {
                    calculated_total_gst_amount += parseFloat((expense_amount_array[index] * expense_tax_array[index])/100);
                    }   
                });
                    
                $("#expense_total_amount_"+id).val(Number(calculated_total_sum.toFixed(2)));
                $("#total_gst_"+id).val(Number(calculated_total_gst_amount.toFixed(2)));
                var total_amount = calculated_total_sum + calculated_total_gst_amount;
                $("#total_amount_"+id).val(Number(total_amount.toFixed(2)));
            }
        });

        body.on('change', $("#tax_type_"+id), function() {
            var calculated_total_sum = 0;
            var calculated_total_gst_amount = 0;
            expense_amount_array = [];
            expense_tax_array = [];

            $('[name="expense_amount_'+ id +'_[]"]').each(function() {
                expense_amount_array.push(Number(this.value));
            })
            $('[name="expense_tax_'+ id +'_[]"]').each(function() {
                expense_tax_array.push(Number(this.value));
            })

            if($("#tax_type_"+id).val() == 0)
            {
                $('[name="expense_amount_'+ id +'_[]"]').each(function (index) {
                    
                calculated_total_sum += parseFloat(expense_amount_array[index]);
                if(expense_tax_array[index] > 0){
                    var subractbleTaxAmount = (expense_amount_array[index]) * (100 / (100 + (expense_tax_array[index])))
                    var taxAmount = expense_amount_array[index] - subractbleTaxAmount;
                    calculated_total_gst_amount += parseFloat(taxAmount);
                }
                });
                $("#expense_total_amount_"+id).val(Number(calculated_total_sum.toFixed(2)));
                $("#total_gst_"+id).val(Number(calculated_total_gst_amount.toFixed(2)));
                $("#total_amount_"+id).val(Number(calculated_total_sum.toFixed(2)));
            }
            else if($("#tax_type_"+id).val() == 1)
            {
                $('[name="expense_amount_'+ id +'_[]"]').each(function (index) {

                    calculated_total_sum += parseFloat(expense_amount_array[index]);
                    if(expense_tax_array[index] > 0)
                    {
                    calculated_total_gst_amount += parseFloat((expense_amount_array[index] * expense_tax_array[index])/100);
                    }   
                });
                    
                $("#expense_total_amount_"+id).val(Number(calculated_total_sum.toFixed(2)));
                $("#total_gst_"+id).val(Number(calculated_total_gst_amount.toFixed(2)));
                var total_amount = calculated_total_sum + calculated_total_gst_amount;
                $("#total_amount_"+id).val(Number(total_amount.toFixed(2)));
            }
        });

        body.on('click', '.delepart_'+id, function(){ 
        $(this).parents('tr').remove();

        var calculated_total_sum = 0;
            var calculated_total_gst_amount = 0;
            expense_amount_array = [];
            expense_tax_array = [];

            $('[name="expense_amount_'+ id +'_[]"]').each(function() {
                expense_amount_array.push(Number(this.value));
            })
            $('[name="expense_tax_'+ id +'_[]"]').each(function() {
                expense_tax_array.push(Number(this.value));
            })

            if($("#tax_type_"+id).val() == 0)
            {
                $('[name="expense_amount_'+ id +'_[]"]').each(function (index) {
                    
                calculated_total_sum += parseFloat(expense_amount_array[index]);
                if(expense_tax_array[index] > 0){
                    var subractbleTaxAmount = (expense_amount_array[index]) * (100 / (100 + (expense_tax_array[index])))
                    var taxAmount = expense_amount_array[index] - subractbleTaxAmount;
                    calculated_total_gst_amount += parseFloat(taxAmount);
                }
                });
                $("#expense_total_amount_"+id).val(Number(calculated_total_sum.toFixed(2)));
                $("#total_gst_"+id).val(Number(calculated_total_gst_amount.toFixed(2)));
                $("#total_amount_"+id).val(Number(calculated_total_sum.toFixed(2)));
            }
            else if($("#tax_type_"+id).val() == 1)
            {
                $('[name="expense_amount_'+ id +'_[]"]').each(function (index) {

                    calculated_total_sum += parseFloat(expense_amount_array[index]);
                    if(expense_tax_array[index] > 0)
                    {
                    calculated_total_gst_amount += parseFloat((expense_amount_array[index] * expense_tax_array[index])/100);
                    }   
                });
                    
                $("#expense_total_amount_"+id).val(Number(calculated_total_sum.toFixed(2)));
                $("#total_gst_"+id).val(Number(calculated_total_gst_amount.toFixed(2)));
                var total_amount = calculated_total_sum + calculated_total_gst_amount;
                $("#total_amount_"+id).val(Number(total_amount.toFixed(2)));
            }
        });
    };
    
    function reconcileTransactions(rowId, bank_transaction_id, transaction_collection_id, is_reconciled)
    {
        var account_id = $("#bankAccount").val();
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });
        $.ajax({
            method: 'PUT',
            url: "/reconcile-transaction/?bank_transaction_id="+bank_transaction_id+"&transaction_collection_id="+transaction_collection_id+"&account_id="+account_id+"&is_reconciled="+is_reconciled,
            success: function(response){
                try{
                    response = JSON.parse(response);
                    if(response && response.status == "success"){
                        location.reload();
                    }else if(response.status == "error"){
                        alert(esponse.err);
                    }
                }
                catch(error){
                }
            },
            error:function(error){ 
            }
        });
    }

</script>


