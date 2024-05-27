var transaction_ids = [];
var payment_option = '';

function getDate(index)
{
    $("#issue_date_"+index).datepicker({ dateFormat: 'dd/mm/yy' });
}

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
                            <button type="button" class="invoice_item" data-myid="'+counter+'" onclick=addReconcileChartAccount("'+encodeURI(val['id'])+'","'+id+'","'+rowIndex+'");>\n\
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
                                <select name="invoice_default_tax_'+rowId+'" id="invoice_default_tax_'+rowId+'" class="form-input--dropdown" onchange="transactionCalculation('+rowId+',0)">\
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
                            <input placeholder="Search your item list" autocomplete="off" data-toggle="dropdown" type="text" id="item_name_code_'+rowId+'_0" name="item_name_code_'+rowId+'_0" onkeyup="searchItems(this,'+rowId+',0)" value="">\
                            <input type="hidden" id="item_part_code_'+rowId+'_0" name="item_part_code_'+rowId+'_0" value="">\
                            <input type="hidden" id="item_part_name_'+rowId+'_0" name="item_part_name_'+rowId+'_0" value="">\
                            <ul class="search_items_'+rowId+'_0 dropdown-menu invoice-expenses--dropdown" id="invoice_item_list_'+rowId+'_0"></ul>\
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

        getReconcileTaxRates(rowId,0);

        if(payment_option == 'direct_payment')
        {
            getReconcileItemList(rowId,0);
        }

        appendBody(rowId, rowIndex, td, transaction, chart_account);
        $('#reconcile_transaction_part_ids_'+rowId).val(JSON.stringify(index));

    }
}


function appendBody(rowId, rowIndex, td, transaction, chart_account)
{
    var chart_account_temp = '';
    var counter=0;
    chart_account_temp+= '<ul class="dropdown-menu invoice-expenses--dropdown" id="invoice_chart_account_list_'+rowId+'_'+rowIndex+'">'
            +'<li id="add_new_invoice_chart_account_'+rowId+"_"+rowIndex+'" class="add-new--btn"><a href="#" data-toggle="modal" data-target="#newAddAccountModal" onclick=openNewAddAccountPopUpModelReconcile('+rowId+','+rowIndex+')><i class="fa-solid fa-circle-plus"></i>New Account</a></li>';
            $.each(chart_account,function(key,value){
                chart_account_temp+='<li class="accounts-group--label">'+value['chart_accounts_name']+'</li>'
                $.each(value['chart_accounts_particulars'],function(k,val){
                    chart_account_temp+='<li><button type="button" class="invoice_item" data-myid="'+counter+'" onclick=addReconcileChartAccount("'+encodeURI(val['id'])+'","'+rowId+'","'+rowIndex+'");>'
                        +'<span id="data_name_'+counter+'">'+val['chart_accounts_particulars_code']+' - '+val['chart_accounts_particulars_name']+'</span></button></li>'
                });
            });
    chart_account_temp+='</ul>';

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
                        '+chart_account_temp+'\
                    </td>\
                    <td id="invoice_parts_tax_rate_td_'+rowId+'">\
                        <input type="hidden" name="invoice_parts_tax_rate_id_'+rowId+'_0" id="invoice_parts_tax_rate_id_'+rowId+'_0" value="">\
                        <input type="hidden" name="invoice_parts_tax_rate_name_'+rowId+'_0" id="invoice_parts_tax_rate_name_'+rowId+'_0" value="">\
                        <div class="form-input--wrap">\
                            <div class="row">\
                                <div class="col-12 for--tables">\
                                    <select class="form-input--dropdown" id="invoice_parts_tax_rate_'+rowId+'_0" name="invoice_parts_tax_rate_'+rowId+'_0" onchange="transactionCalculation('+rowId+',0);getTaxRatePercentage('+rowId+',0);" required style="display : '+(transaction['default_tax'] == "no_tax"  ? 'none' : 'block' )+' ">\
                                    </select>\
                                </div>\
                            </div>\
                        </div>\
                    </td>\
                    <td>\
                        <input class="input--readonly" readonly id="invoice_parts_amount_'+rowId+'_0" name="invoice_parts_amount_'+rowId+'_0" type="float" value="'+transaction['amount']+'">\
                    </td>\
                    <td class="tableOptions">\
                        <button class="btn sumb--btn delepart" type="button" onclick=deleteReconcileParts("'+rowId+'", 0) ><i class="fas fa-trash-alt"></i></button>\
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
                                <select name="invoice_default_tax_'+rowId+'" id="invoice_default_tax_'+rowId+'" class="form-input--dropdown" onchange="transactionCalculation('+rowId+', 0)">\
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


function openTransactionForm(id, type)
{
    $("#createTransactionForm_"+id).show();
    $("#transactions_div_"+id).attr("hidden","hidden");

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
    else if(createTransactionAndReconcile)
    {
        const display = window.getComputedStyle(createTransactionAndReconcile).visibility;

        if(display == 'visible' || display == 'inline-block')
        {
            $("#create_transaction_and_reconcile_"+id).attr("style", "visibility: hidden");
        }
    }
}

function transactionFormCancel(id)
{
    transaction_ids = [];
    $("#createTransactionForm_"+id).hide();

    cancelForm(id);
}

function reconcileTransactionFormCancel(id)
{
    transaction_ids = [];
    $("#transactions_div_"+id).attr("hidden","hidden");

    cancelForm(id);
}

function cancelForm(id)
{
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


function openTab(evt, tabName, id, matchCount)
{
    const transactionReconcileBtn = document.getElementById('transactionReconcile_'+id);
    const createAndReconcileBtn = document.getElementById('create_transaction_and_reconcile_'+id);
    const transactionDiscussBtn = document.getElementById('transaction_discussion_btn_'+id);

    const reconcileBtnDisplay = transactionReconcileBtn ? window.getComputedStyle(transactionReconcileBtn).visibility : '';
    const createAndReconcileBtnDisplay = createAndReconcileBtn ? window.getComputedStyle(createAndReconcileBtn).visibility : '';
    const discussBtnDisplay = transactionDiscussBtn ? window.getComputedStyle(transactionDiscussBtn).visibility : '';

    if(tabName == ("Create_"+id)){

        $("#transactions_div_"+id).attr("hidden","hidden");
        $("#Match_"+id).hide();
        $("#discuss_"+id).hide();

        // if((reconcileBtnDisplay == 'visible' || reconcileBtnDisplay == 'inline-block') || (discussBtnDisplay == 'visible' || discussBtnDisplay == 'inline-block'))
        // {
            $(transactionReconcileBtn).attr("style", "visibility: hidden");
            $(transactionDiscussBtn).remove();

            if(createAndReconcileBtn)
            {
                window.getComputedStyle(createAndReconcileBtn).visibility;
                $(createAndReconcileBtn).attr("style", "visibility: visible");
            }
        // }

    }else if(tabName == ("Match_"+id)){
        $("#Create_"+id).hide();
        $("#discuss_"+id).hide();

        $(createAndReconcileBtn).attr("style", "visibility: hidden");
        $(transactionDiscussBtn).remove();
        $(transactionReconcileBtn).attr("style", "visibility: visible");

        $(".match_"+id+ ".sumb--recentlogdements").empty();
        $("#otherMatches_"+id).show();
        $("#createTransactionForm_"+id).hide();
    }
    else if(tabName == ("discuss_"+id)){
        $("#Match_"+id).hide();
        $("#Create_"+id).hide();

        // if((createAndReconcileBtnDisplay == 'visible' || createAndReconcileBtnDisplay == 'inline-block') || (reconcileBtnDisplay == 'visible' || reconcileBtnDisplay == 'inline-block'))
        // {
            $(createAndReconcileBtn).attr("style", "visibility: hidden");
            $(transactionReconcileBtn).attr("style", "visibility: hidden");
            if(transactionDiscussBtn)
            {
                $(transactionDiscussBtn).attr("style", "visibility: visible");
            }
        // }

        $(".match_"+id+ ".sumb--recentlogdements").empty();
        $("#otherMatches_"+id).hide();
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

function getClient(index, obj, classname)
{
    $("#create_transaction_and_reconcile_"+index).remove();
    $(classname+'.form--recentsearch.clientname_'+index).show();
    var value = $("#"+obj.id).val().toLowerCase();

    var clientList = $(classname+".clientname_"+index+" .form--recentsearch__result li button");
    var matchedItems = $(classname+".clientname_"+index+" .form--recentsearch__result li button").filter(function() {
        return $(this).text().toLowerCase().indexOf(value) > -1;
    });

    if(value == ''){
        $(classname+'.form--recentsearch.clientname_'+index).hide();
        $(classname+'.clientname_'+index+' li.add--newactclnt input').prop('checked',false);
        $("#"+obj.id).removeClass('saveNewRecord');

    } else if($("#"+obj.id).hasClass('saveNewRecord')) {

        if(matchedItems.length !=0) {
            $(classname+'.clientname_'+index+' li.add--newactclnt').hide();
            $(classname+'.clientname_'+index+' li.add--newactclnt input').prop('checked',false);
            $("#"+obj.id).removeClass('saveNewRecord');
            matchedItems.toggle(true);
        } else {
            $(classname+'.form--recentsearch.clientname_'+index).hide();
        }

    } else {
        clientList.toggle(false);
        matchedItems.toggle(true);

        if (matchedItems.length == 0) {
            $(classname+'.clientname_'+index+' li.add--newactclnt').show();
        } else {
            $(classname+'.clientname_'+index+' li.add--newactclnt input').prop('checked',false);
            $(classname+'.clientname_'+index+' li.add--newactclnt').hide();
        }
    }

    $(classname+' li.add--newactclnt input').on('click', function () {
        if(this.id == 'savethisrep') {
            if($(classname+' #savethisrep').is(':checked')){
                $("#"+obj.id).addClass('saveNewRecord');
            } else {
                $("#"+obj.id).removeClass('saveNewRecord');
            }
            $(classname+'.form--recentsearch.clientname_'+index).hide();
        } else {
            if($(classname+' #savethisrep_deets').is(':checked')){
                $("#"+obj.id).addClass('saveNewRecord');
            } else {
                $("#"+obj.id).removeClass('saveNewRecord');
            }
            $(classname+'.form--recentsearch.clientname_'+index).hide();
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
        $("#transactionReconcile_"+index).attr("style", "visibility: hidden");
        $("#create_transaction_and_reconcile_"+index).remove();

        $("#add--deets_"+index).append(
            '<button id="create_transaction_and_reconcile_'+index+'" onclick=createTransactionAndReconcile('+encodeURI(index)+') class="createT--btn"><i class="fa-solid fa-code-merge" style="margin-right: 5px"></i>Reconcile</button>'
        );
    }else{
        $("#create_transaction_and_reconcile_"+index).attr("style", "display: none");
    }
}

function discuss(currentTextId, existingTextId, index,transactionId)
{
    const newText = $("#"+currentTextId).val();
    const existingText = $("#"+existingTextId).val();
    if(newText.trim().length >0 ||  existingText.trim().length >0)
    {
        $("#transactionReconcile_"+index).attr("style", "visibility: hidden");
        $("#create_transaction_and_reconcile_"+index).attr("style", "visibility: hidden");
        $("#transaction_discussion_btn_"+index).remove();

        $("#discussTransBTN_"+index).prepend(
            '<a id="transaction_discussion_btn_'+index+'" onclick=saveDiscussion('+currentTextId+','+existingTextId+','+encodeURI(index)+','+encodeURI(transactionId)+')><i class="zmdi zmdi-pin-assistant"></i>Save</a>'
        );
    }else{
        $("#transaction_discussion_btn_"+index).remove();
    }
}

function saveDiscussion(currentTextId, existingTextId, index, transactionId)
{
    let discussArray = '';
    const currentText = $("#"+currentTextId.id).val().trim();
    const existingText = $("#"+existingTextId.id).val().trim();

    if(existingText.length >0 ){
        const diff = (diffMe, diffBy) => diffMe.split(diffBy)
        discussArray = diff(currentText, existingText)
    }
    var postData = {
        existing_discussion : existingText,
        current_discussion : currentText,
        discussions : discussArray,
        transaction_id : transactionId
    };

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });
    $.ajax({
        method: "POST",
        url: "/transaction/"+transactionId+"/discuss",
        data: postData,
        dataType: "json",
        success:function(response){
            $("#transaction_discussion_btn_"+index).attr("style", "visibility: hidden");
            response['discussion'];
            $("#"+existingTextId.id).val(response['discussion']);
        },
        error:function(error){
            alert(error.responseJSON.message);
        }
    });
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
            items_td = '<td><input placeholder="Search your item list" autocomplete="off" data-toggle="dropdown" type="text" id="item_name_code_'+i+'_'+rowIndex+'" name="item_name_code_'+i+'_'+rowIndex+'" onkeyup="searchItems(this,'+i+','+rowIndex+')" value="" required>\
            <input type="hidden" id="item_part_code_'+i+'_'+rowIndex+'" name="item_part_code_'+i+'_'+rowIndex+'" value="">\
            <input type="hidden" id="item_part_name_'+i+'_'+rowIndex+'" name="item_part_name_'+i+'_'+rowIndex+'" value="">\
                <ul class="search_items_'+i+'_'+rowIndex+' dropdown-menu invoice-expenses--dropdown" id="invoice_item_list_'+i+'_'+rowIndex+'">\
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
                            <select class="form-input--dropdown" id="invoice_parts_tax_rate_'+i+'_'+rowIndex+'" name="invoice_parts_tax_rate_'+i+'_'+rowIndex+'" onchange="transactionCalculation('+i+','+rowIndex+');getTaxRatePercentage('+i+','+rowIndex+');" required  >\
                            </select>\
                        </div>\
                    </div>\
                </div>\
            </td>\
            <td><input class="input--readonly" type="float" readonly id="invoice_parts_amount_'+i+'_'+rowIndex+'" name="invoice_parts_amount_'+i+'_'+rowIndex+'" value="" required>\n\
            </td><td class="tableOptions"><button class="btn sumb--btn delepart" type="button" onclick=deleteReconcileParts('+i+','+rowIndex+')><i class="fas fa-trash-alt"></i></button></td></tr>');

    if(payment_option == 'direct_payment')
    {
        getReconcileItemList(i,rowIndex);
    }

    getReconcileChartAccountsParticularsList(i, rowIndex);
    getReconcileTaxRates(i,rowIndex);
    addOrRemoveReconcilePartsIds('add', i, rowIndex);
}

function addOrRemoveReconcilePartsIds(action_type, i, id){
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
    }

    $('#reconcile_transaction_part_ids_'+i).val(JSON.stringify(rowIndex));
}

function getReconcileItemList(i,id)
{
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });
    $.ajax({
        method: "POST",
        url: "/invoice-items",
        // data: post_data,
        success:function(response){
            $("#invoice_item_list_"+i+"_"+id).empty();
            var counter = 0;

            // $("#invoice_item_list_"+i+"_"+id).append('\
            //     <li id="add_new_invoice_item_'+i+'_'+id+'" class="add-new--btn">\
            //         <a href="#" data-toggle="modal" data-target="#newItemModal" onclick="openPopUpModel('+id+')"><i class="fa-solid fa-circle-plus"></i>New Item</a>\
            //     </li>\
            // ');

            $.each(response.data,function(key,value){
                counter++;
                $("#invoice_item_list_"+i+"_"+id).append('\n\<li>\n\
                    <button type="button" class="invoice_item" data-myid="'+counter+'" onclick=getItemsById("'+encodeURI(value['id'])+'","'+i+'","'+id+'");>\n\
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

function getReconcileTaxRates(i,rowId){
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

function getTaxRatePercentage(i,rowId){
    if($("#invoice_parts_tax_rate_"+i+"_"+rowId+" option:selected").attr('id'))
    {
        $("#invoice_parts_tax_rate_"+i+"_"+rowId+" option:selected").attr('id');
        let selected_invoice_parts_tax_rate = $("#invoice_parts_tax_rate_"+i+"_"+rowId).val().split('#|#');
        $("#invoice_parts_tax_rate_id_"+i+"_"+rowId).val('');
        $("#invoice_parts_tax_rate_id_"+i+"_"+rowId).val(selected_invoice_parts_tax_rate[0]);
    }
}

function getItemsById(itemId, i, rowId){
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

                $("#item_name_code_"+i+"_"+rowId).val('');
                $("#item_part_name_"+i+"_"+rowId).val('');
                $("#item_part_code_"+i+"_"+rowId).val('');
                $("#invoice_parts_quantity_"+i+"_"+rowId).val('');
                $("#invoice_parts_description_"+i+"_"+rowId).val('');
                $("#invoice_parts_unit_price_"+i+"_"+rowId).val('');
                // $("#invoice_parts_amount_"+rowId).val('');
                $("#invoice_parts_tax_rate_"+i+"_"+rowId).val('');
                $("#invoice_parts_tax_rate_id_"+i+"_"+rowId).val('');
                $("#invoice_parts_chart_accounts_"+i+"_"+rowId).val('');
                $("#invoice_parts_chart_accounts_parts_id_"+i+"_"+rowId).val('');

                $("#item_name_code_"+i+"_"+rowId).val(response['data']['invoice_item_code']+' : '+response['data']['invoice_item_name']);
                $("#item_part_name_"+i+"_"+rowId).val(response['data']['invoice_item_name']);
                $("#item_part_code_"+i+"_"+rowId).val(response['data']['invoice_item_code']);
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

function getReconcileChartAccountsParticularsList(i,id){
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
                    <button type="button" class="invoice_item" data-myid="'+counter+'" onclick=addReconcileChartAccount("'+encodeURI(val['id'])+'","'+i+'","'+id+'");>\n\
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

function addReconcileChartAccount(chart_accounts_parts_id, i,rowId, from)
{
    if(chart_accounts_parts_id && rowId >= 0){
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
    var sub_total = parseFloat($('#bank_transaction_amount_'+rowId).val().replace(/\,/g,'')).toFixed(2);

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
        unit_price : parseFloat(sub_total).toFixed(2),
        sub_total : parseFloat(sub_total).toFixed(2),
        total_gst : parseFloat(total_gst).toFixed(2),
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
        },
        error: function(e){

        }
    });
}

function deleteReconcileParts(i,rowId){
    var rowIndex = [0];
    rowIndex = $('#reconcile_transaction_part_ids_'+i).val();
    rowIndex = JSON.parse(rowIndex);
    if(rowIndex.length>1){
        addOrRemoveReconcilePartsIds("delete", i, rowId);
        transactionCalculation(i, rowId);
    }
}

function saveTransaction(index, obj)
{
    let bank_transaction_amount = parseFloat($('#bank_transaction_amount_'+index).val().replace(/\,/g,'')).toFixed(2);
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
        data: $('#transaction-form-create_'+index).serialize()+'&account_id='+$("#bank_account_id").val(),
        success: function(response){
            if(transaction_ids.indexOf(response.data['id']) == -1 ){
                transaction_ids.push(response.data['id']);
            }

            let transaction_subtotal = parseFloat($("#transaction_subtotal_"+index).val());
            let spent_receive_money = parseFloat($("#spent_receive_money_"+index).val());

            $("#createTransactionForm_"+index).hide();
            $("#transactions_div_"+index).removeAttr('hidden');

            //------------------Call reusbale function------------------------------//
            var data = reusableTransactionTypes(response.data);

            $('#transactions_'+index).append('\
                <div class="fmtransac_wrap create--new" id="matching_transactions_'+index+'_'+encodeURI(response.data['id'])+'">\
                    <input type="checkbox" checked id="matching_transactions_checkbox_'+index+'_'+encodeURI(response.data['id'])+'" value='+encodeURI(response.data['id'])+' onclick=addOrRemoveTransactions("'+encodeURI(index)+'","'+encodeURI(response.data['id'])+'","'+encodeURI(response.data['transaction_type'])+'","'+encodeURI(response.data['transaction_sub_type'])+'")>\
                    <label for="matching_transactions_checkbox_'+index+'_'+encodeURI(response.data['id'])+'">\
                        <div class="row align-items-center">\
                            <div class="deets col-xl-5 col-lg-5 col-md-4 col-sm-12">\
                                <div class="name">'+data.get("transaction_type_text") + response.data['client_name']+'</div>\
                                '+response.data['issue_date']+'\
                            </div>\
                            <div class="reference col-xl-4 col-lg-4 col-md-4 col-sm-12">\
                                ----------\
                            </div>\
                            <div class="amount col-xl-3 col-lg-3 col-md-4 col-sm-12">\
                                <div class="viewableAmount" id="viewableAmount_'+response.data['id']+'">\
                                    '+data.get("spend_money")+'\
                                    '+data.get("receive_money")+'\
                                </div>\
                                    '+data.get("trans_type")+'\
                            </div>\
                        </div>\
                    </label>\
                </div>');


            $('#selected_transactions_table_'+index).append('\
                <div class="fmselectedtransac--item" id=selected_transactions_'+encodeURI(index)+'_'+encodeURI(response.data['id'])+'>\
                    <div class="row align-items-center">\
                        <div class="deets col-xl-5 col-lg-5 col-md-5 col-sm-12">\
                            <div class="name">'+data.get("transaction_type_text") + response.data['client_name']+'</div>\
                            '+response.data['issue_date']+'\
                        </div>\
                        <div class="reference col-xl-4 col-lg-4 col-md-4 col-sm-12">\
                            ----------\
                        </div>\
                        <div class="amount col-xl-3 col-lg-3 col-md-3 col-sm-12">\
                            '+data.get("spend_money_input")+'\
                            '+data.get("receive_money_input")+'\
                            '+data.get("trans_type")+'\
                        </div>\
                    </div>\
                </div>');

            let sum = parseFloat(transaction_subtotal) + (parseFloat(data.get("total_amount")) > 0 ? parseFloat(data.get("total_amount")) : 0);

            $("#transaction_subtotal_"+index).val(sum.toFixed(2));
            // $("#spent_receive_money_"+index).val((spent_receive_money + (parseFloat(data.get("spend_money")) > 0 ? parseFloat(data.get("spend_money")) : parseFloat(receive_money))).toFixed(2));
            $("#spent_receive_money_"+index).val((spent_receive_money + (parseFloat(data.get("total_amount")) > 0 ? parseFloat(data.get("total_amount")) : 0)).toFixed(2));

            $("#spent_receive_money_text_"+index).text("$"+parseFloat(data.get("total_amount")).toFixed(2)); //received
            $("#sub_money_text_"+index).text(data.get("trans_type"));
            $("#spent_receive_money_header_text_"+index).text(data.get("header_text"));


            let total_matched = (parseFloat(bank_transaction_amount) - parseFloat($("#transaction_subtotal_"+index).val().replace(/\,/g,''))).toFixed(2);
            $("#total_matched_"+index).val(total_matched);

            $("#transaction_minor_adjustment_"+index).val('0');

            disableOrEnableReconcileButton(parseFloat($("#spent_receive_money_"+index).val()).toFixed(2), bank_transaction_amount, total_matched, index);
        },
        error:function(errors){
            if(errors.responseJSON)
            {
                $("#validation_error_div").attr("style", "display:block");
                $.each(errors.responseJSON.errors, function (key, val) {
                    $("#validation_error").append('<li>'+val+'</li>');
                });
            }
        }
    });
}

function reusableTransactionTypes(data)
{
    var map = new Map();

    map.set("total_amount", data['total_amount']); map.set("spend_money", ''); map.set("spend_money_input", '');
    map.set("receive_money", ''); map.set("receive_money_input", ''); map.set("transaction_type_text", '');

    if(data['transaction_type'] == 'spend_money' || data['transaction_type'] == 'apprepayment' || data['transaction_type'] == 'apoverpayment' || data['transaction_type'] == 'expense' || (data['transaction_type'] == 'payment' && data['transaction_sub_type'] == 'spent')){

        let spend_money_input ='<input type="float" readonly id=spend_money_'+encodeURI(data['id'])+' name=transaction_money_'+encodeURI(data['id'])+' value='+encodeURI(data['total_amount'])+'>';
        map.set("trans_type", "Spend");
        map.set("spend_money", data['total_amount']);
        map.set("spend_money_input", spend_money_input);
        map.set("header_text", "spent");

    }
    else if(data['transaction_type'] == 'receive_money' || data['transaction_type'] == 'arprepayment' || data['transaction_type'] == 'aroverpayment' || data['transaction_type'] == 'invoice' || (data['transaction_type'] == 'payment' && data['transaction_sub_type'] == 'received')){

        let receive_money_input ='<input type="float" readonly id=receive_money_'+encodeURI(data['id'])+' name=transaction_money_'+encodeURI(data['id'])+' value='+encodeURI(data['total_amount'])+'>';
        map.set("trans_type", "Received");
        map.set("receive_money", data['total_amount']);
        map.set("receive_money_input", receive_money_input);
        map.set("header_text", "received");
    }
    if(data['transaction_type'] == 'payment')
    {
        map.set("transaction_type_text","Payment: ");
    }
    else if(data['transaction_type'] == 'arprepayment' || data['transaction_type'] == 'apprepayment')
    {
        map.set("transaction_type_text","Prepayment: ");
    }
    else if(data['transaction_type'] == 'aroverpayment' || data['transaction_type'] == 'apoverpayment')
    {
        map.set("transaction_type_text","Over Payment: ");
    }

    return map;
}

function findAndMatchTransactions(index, type)
{
    let transaction_type = [];
    type == 'debit' ? transaction_type.push('spend_money','expense','apprepayment','apoverpayment') : transaction_type.push('invoice','receive_money','arprepayment','aroverpayment');
    let bank_transaction_amount = parseFloat($("#bank_transaction_amount_"+index).val().replace(/\,/g,''));

    let data = {
        transaction_type : type,
        bank_account_id : $("#bank_account_id").val(),
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
            $('#transactions_'+index).empty();
            $('#selected_transactions_table_'+index).empty();

            $('#selected_transactions_table_'+index).append('\
                <div class="novalue_wrap novalue_selected--'+index+'">\
                    <i class="fa-solid fa-triangle-exclamation"></i>No Selected Transaction/s\
                </div>\
            ');

            var ctrFindTransact = 0;
            $.each(data.transactions,function(key,value){

                //------------------Call reusbale function------------------------------//
                var details = reusableTransactionTypes(value);

                let split = "";
                if(value['transaction_type'] == 'invoice'){
                    split+= ('<a class="splitBTN" onclick=splitTransactionPopUp("receive_money_'+value['id']+'","'+index+'","'+encodeURI(JSON.stringify(value))+'") return false><i class="zmdi zmdi-arrow-split"></i>Split</a>');
                }
                if(value['transaction_type'] == 'expense'){
                    split+= ('<a class="splitBTN" onclick=splitTransactionPopUp("spend_money_'+value['id']+'","'+index+'","'+encodeURI(JSON.stringify(value))+'") return false><i class="zmdi zmdi-arrow-split"></i>Split</a>');
                }

                var refNoData = "";
                if(!value['transaction_number']){
                    refNoData = "----------";
                } else {
                    refNoData = "Ref#: "+('00000000000' + value['transaction_number']).slice(-12);
                }

                $('#transactions_'+index).append('\
                    <div class="fmtransac_wrap" id="matching_transactions_'+index+'_'+value['id']+'">\
                        <input type="checkbox" id="matching_transactions_checkbox_'+index+'_'+value['id']+'" value="'+value['id']+'" onclick=addOrRemoveTransactions("'+encodeURI(index)+'","'+encodeURI(value['id'])+'","'+encodeURI(value['transaction_type'])+'","'+encodeURI(value['transaction_sub_type'])+'")>\
                        <label for="matching_transactions_checkbox_'+index+'_'+value['id']+'">\
                            <div class="row align-items-center">\
                                <div class="deets col-xl-5 col-lg-5 col-md-4 col-sm-12">\
                                    <div class="name">'+details.get("transaction_type_text")+value['client_name']+'</div>\
                                    '+value['issue_date']+'\
                                </div>\
                                <div class="reference col-xl-3 col-lg-2 col-md-3 col-sm-12">\
                                    '+refNoData+'\
                                </div>\
                                <div class="amount col-xl-3 col-lg-3 col-md-4 col-sm-12">\
                                    <input type="hidden" readonly id="match_transaction_spend_money_'+value['id']+'" value='+encodeURI(details.get("spend_money"))+' >\
                                    <input type="hidden" readonly id="match_transaction_receive_money_'+value['id']+'" value='+encodeURI(details.get("receive_money"))+' >\
                                    <div class="viewableAmount" id="viewableAmount_'+value['id']+'">\
                                        '+encodeURI(details.get("spend_money"))+'\
                                        '+encodeURI(details.get("receive_money"))+'\
                                    </div>\
                                        '+details.get("trans_type")+'\
                                </div>\
                            </div>\
                        </label>\
                        <div class="spltBTN_wrap" id="split_transaction_btn_'+value['id']+'" style="display: none;">'+split+'</div>\
                    </div>');

                $('#selected_transactions_table_'+index).append('\
                    <div class="fmselectedtransac--item" id=selected_transactions_'+encodeURI(index)+'_'+encodeURI(value['id'])+' style="display:none">\
                        <div class="row align-items-center">\
                            <div class="deets col-xl-5 col-lg-5 col-md-5 col-sm-12">\
                                <div class="name">'+details.get("transaction_type_text")+value['client_name']+'</div>\
                                '+value['issue_date']+'\
                            </div>\
                            <div class="reference col-xl-4 col-lg-4 col-md-4 col-sm-12">\
                                '+refNoData+'\
                            </div>\
                            <div class="amount col-xl-3 col-lg-3 col-md-3 col-sm-12">\
                                '+details.get("spend_money_input")+'\
                                '+details.get("receive_money_input")+'\
                                '+details.get("trans_type")+'\
                            </div>\
                        </div>\
                    </div>');

                ctrFindTransact++
            });

            if(ctrFindTransact <= 1) {
                $("#transactions_"+index).append('\
                    <div class="novalue_wrap">\
                        <i class="fa-solid fa-triangle-exclamation"></i>No Records Found\
                    </div>\
                ');
            }

            $("#total_matched_"+index).val(bank_transaction_amount.toFixed(2));
            $("#total_matched--text_"+index).text('Needs Adjustment');
            $("#transaction_subtotal_"+index).val('0.00');
            $("#spent_receive_money_"+index).val('0.00');
            $("#transaction_minor_adjustment_"+index).val('0.00');

            if (type == 'debit') {
                $("#spent_receive_money_text_"+index).text("$"+bank_transaction_amount.toFixed(2)); //spent
                $("#sub_money_text_"+index).text("Spent"); //spent
                $("#spent_receive_money_header_text_"+index).text("spent");
            } else {
                $("#spent_receive_money_text_"+index).text("$"+bank_transaction_amount.toFixed(2)); //received
                $("#sub_money_text_"+index).text("Received");
                $("#spent_receive_money_header_text_"+index).text("received");
            }

            disableOrEnableReconcileButton(parseFloat($("#spent_receive_money_"+index).val()).toFixed(2), bank_transaction_amount, parseFloat($("#total_matched_"+index).val()).toFixed(2), index);
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

function addOrRemoveTransactions(rowIndex, id, transaction_type, transaction_sub_type)
{

    let transaction_subtotal = parseFloat($("#transaction_subtotal_"+rowIndex).val().replace(/\,/g,'')).toFixed(2);
    let spent_receive_money = parseFloat($("#spent_receive_money_"+rowIndex).val().replace(/\,/g,'')).toFixed(2);
    let bank_transaction_amount = Number($("#bank_transaction_amount_"+rowIndex).val().replace(/\,/g,''));

    let transaction_amount = 0;

    if(transaction_type === 'invoice' || transaction_type === 'receive_money' || transaction_type === 'arprepayment' || transaction_type === 'aroverpayment' || (transaction_type === 'payment' && transaction_sub_type === 'received'))
    {
        transaction_amount = parseFloat($("#receive_money_"+id).val().replace(/\,/g,'')).toFixed(2);
    }
    if(transaction_type === 'expense' || transaction_type === 'spend_money' || transaction_type === 'apprepayment' || transaction_type === 'apoverpayment' || (transaction_type === 'payment' && transaction_sub_type === 'spent'))
    {
        transaction_amount = parseFloat($("#spend_money_"+id).val().replace(/\,/g,'')).toFixed(2);
    }

    let transaction_minor_adjustment = Number($("#transaction_minor_adjustment_"+rowIndex).val().replace(/\,/g,'')).toFixed(2);
    var match_transaction_ele = document.getElementById("matching_transactions_checkbox_"+rowIndex+"_"+id);

    if(match_transaction_ele.checked)
    {
        $(".novalue_selected--"+rowIndex).hide();

        $("#selected_transactions_checkbox_"+id).prop("checked", true);
        document.getElementById("selected_transactions_"+rowIndex+"_"+id).style.display = "block";

        $("#transaction_subtotal_"+rowIndex).val((parseFloat(transaction_subtotal) + parseFloat(transaction_amount)).toFixed(2));
        transaction_subtotal = $("#transaction_subtotal_"+rowIndex).val();

        $("#spent_receive_money_"+rowIndex).val((parseFloat(transaction_subtotal) + parseFloat(transaction_minor_adjustment)).toFixed(2));
        let total_matched = (bank_transaction_amount - parseFloat($("#spent_receive_money_"+rowIndex).val().replace(/\,/g,''))).toFixed(2);
        $("#total_matched_"+rowIndex).val(parseFloat(total_matched).toFixed(2));

        if(transaction_ids.indexOf(Number(id)) === -1)
        {
            transaction_ids.push(Number(id));
        }
        if(transaction_type == 'expense' || transaction_type == 'invoice')
        {
            $("#split_transaction_btn_"+id).attr("style", "display: block");
        }

        disableOrEnableReconcileButton(parseFloat($("#spent_receive_money_"+rowIndex).val().replace(/\,/g,'')).toFixed(2), bank_transaction_amount, total_matched, rowIndex);
    }else{

        $("#matching_transactions_checkbox_"+rowIndex+"_"+id).prop("checked", false);
        document.getElementById("selected_transactions_"+rowIndex+"_"+id).style.display = "none";

        var tableNameID = $("#selected_transactions_table_"+rowIndex+" .fmselectedtransac--item:visible").length;

        if (tableNameID < 1) {
            $(".novalue_selected--"+rowIndex).show();
        }

        $("#transaction_subtotal_"+rowIndex).val((parseFloat(transaction_subtotal) - parseFloat(transaction_amount)).toFixed(2));
        transaction_subtotal = $("#transaction_subtotal_"+rowIndex).val();
        $("#spent_receive_money_"+rowIndex).val((parseFloat(transaction_subtotal) + parseFloat(transaction_minor_adjustment)).toFixed(2));

        let total_matched = (bank_transaction_amount - parseFloat($("#spent_receive_money_"+rowIndex).val().replace(/\,/g,''))).toFixed(2);
        $("#total_matched_"+rowIndex).val(parseFloat(total_matched).toFixed(2));

        const index = transaction_ids.indexOf(Number(id));
        if (index > -1) { // only splice array when item is found
            transaction_ids.splice(index, 1); // 2nd parameter means remove one item only
        }

        if(transaction_type == 'expense' || transaction_type == 'invoice')
        {
            $("#split_transaction_btn_"+id).attr("style", "display: none");
        }

        disableOrEnableReconcileButton(parseFloat($("#spent_receive_money_"+rowIndex).val().replace(/\,/g,'')).toFixed(2), bank_transaction_amount, total_matched, rowIndex);
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
    let balance = parseFloat($("#balance").val().replace(/\,/g,''));
    let part_payment = parseFloat($("#part_payment").val().replace(/\,/g,''));
    let remaining_balance = parseFloat(balance - part_payment).toFixed(2);

    if(isNaN(remaining_balance)) remaining_balance = 0;

    if(part_payment >= balance || part_payment <= 0 || remaining_balance <= 0)
    {
        $(".close").click();
    }else
    {
        const transaction = JSON.parse($('#split_transaction_details').val());
        const index = $('#transaction_index').val();
        let receive_money = '';
        let spend_money = '';
        let transaction_subtotal = parseFloat($("#transaction_subtotal_"+index).val().replace(/\,/g,''));
        var spent_receive_money = parseFloat($("#spent_receive_money_"+index).val().replace(/\,/g,''));
        let bank_transaction_amount = parseFloat($("#bank_transaction_amount_"+index).val().replace(/\,/g,''));

        if(transaction.transaction_type == "invoice")
        {
            $("#split_transaction_btn_"+transaction.id).empty();
            receive_money = remaining_balance;
            const transaction_id = transaction.id;

            $("#match_transaction_receive_money_"+transaction.id).val(part_payment.toFixed(2));
            $("#viewableAmount_"+transaction.id).text(part_payment.toFixed(2));
            $("#receive_money_"+transaction.id).val(part_payment.toFixed(2));
            const unsplit='<a class="splitBTN" onclick=unSplitTransaction("receive_money_'+transaction_id+'","match_transaction_receive_money_'+transaction_id+'","'+index+'","'+encodeURI($('#split_transaction_details').val())+'","'+remaining_balance+'") return false><i class="zmdi zmdi-undo"></i>Undo Split</a>'
            $("#split_transaction_btn_"+transaction.id).append(unsplit);

        }else if(transaction.transaction_type == "expense")
        {
            $("#split_transaction_btn_"+transaction.id).empty();
            spend_money = remaining_balance;
            const transaction_id = transaction.id;

            $("#match_transaction_spend_money_"+transaction.id).val(part_payment.toFixed(2));
            $("#viewableAmount_"+transaction.id).text(part_payment.toFixed(2));
            $("#spend_money_"+transaction.id).val(part_payment.toFixed(2));

            const unsplit= ('<a class="splitBTN" onclick=unSplitTransaction("spend_money_'+transaction_id+'","match_transaction_spend_money_'+transaction_id+'","'+index+'","'+encodeURI($('#split_transaction_details').val())+'","'+remaining_balance+'") return false><i class="zmdi zmdi-undo"></i>Undo Split</a>');
            $("#split_transaction_btn_"+transaction.id).append(unsplit);
        }

        var refNoSplitData = "";
        if(!transaction.transaction_number){
            refNoSplitData = "----------";
        } else {
            refNoSplitData = "Ref#: "+('00000000000' + transaction.transaction_number).slice(-12);
        }

        let splitTransaction='\
                            <div class="splitTrans_wrap" id="split_transaction_tr_'+transaction.id+'">\
                                <div class="row align-items-center">\
                                    <div class="deets col-xl-5 col-lg-5 col-md-4 col-sm-12">\
                                        <div class="name">'+transaction.client_name+'</div>\
                                        '+transaction.issue_date+'\
                                    </div>\
                                    <div class="reference col-xl-3 col-lg-2 col-md-3 col-sm-12">\
                                        '+refNoSplitData+'\
                                    </div>\
                                    <div class="amount col-xl-3 col-lg-3 col-md-4 col-sm-12">\
                                        <div class="viewableAmount">'+spend_money+'\
                                        '+receive_money+'</div>\
                                        After Split\
                                    </div>\
                                </div>\
                            </div>';

        $(splitTransaction).insertAfter("#matching_transactions_"+index+"_"+transaction.id);

        $("#transaction_subtotal_"+index).val((transaction_subtotal - remaining_balance).toFixed(2));
        $("#spent_receive_money_"+index).val((spent_receive_money - remaining_balance).toFixed(2));

        let total_matched = (bank_transaction_amount - parseFloat($("#spent_receive_money_"+index).val())).toFixed(2);
        $("#total_matched_"+index).val(total_matched);

        disableOrEnableReconcileButton(parseFloat($("#spent_receive_money_"+index).val().replace(/\,/g,'')).toFixed(2), bank_transaction_amount, total_matched, index);

        $(".close").click();
    }
}

function unSplitTransaction(selected_transaction_type_id, match_transaction_type_id, index, transaction, remaining_balance)
{
    var transaction = JSON.parse(decodeURI(transaction));
    let bank_transaction_amount = parseFloat($("#bank_transaction_amount_"+index).val().replace(/\,/g,''));

    $("#"+selected_transaction_type_id).val(parseFloat(transaction.total_amount).toFixed(2));
    $("#"+match_transaction_type_id).val(parseFloat(transaction.total_amount).toFixed(2));
    $("#transaction_subtotal_"+index).val((parseFloat($("#transaction_subtotal_"+index).val().replace(/\,/g,'')) + parseFloat(remaining_balance)).toFixed(2));
    $("#spent_receive_money_"+index).val((parseFloat($("#spent_receive_money_"+index).val().replace(/\,/g,'')) + parseFloat(remaining_balance)).toFixed(2));

    let total_matched = (bank_transaction_amount - parseFloat($("#spent_receive_money_"+index).val().replace(/\,/g,''))).toFixed(2);
    $("#total_matched_"+index).val(total_matched);

    $("#split_transaction_btn_"+transaction.id).empty();
    $("#split_transaction_tr_"+transaction.id).remove();

    $("#viewableAmount_"+transaction.id).text(parseFloat(transaction.total_amount).toFixed(2));

    let split = ('<a class="splitBTN" onclick=splitTransactionPopUp("'+selected_transaction_type_id+'","'+index+'","'+encodeURI(JSON.stringify(transaction))+'") return false><i class="zmdi zmdi-arrow-split"></i>Split</a>')
    $("#split_transaction_btn_"+transaction.id).append(split);

    disableOrEnableReconcileButton(parseFloat($("#spent_receive_money_"+index).val().replace(/\,/g,'')).toFixed(2), bank_transaction_amount, total_matched, index);
}

function minorAdjustment(rowIndex)
{
    let transaction_minor_adjustment = Number($("#transaction_minor_adjustment_"+rowIndex).val().replace(/\,/g,'')).toFixed(2);
    let transaction_subtotal = Number($("#transaction_subtotal_"+rowIndex).val().replace(/\,/g,'')).toFixed(2);
    let bank_transaction_amount = parseFloat($("#bank_transaction_amount_"+rowIndex).val().replace(/\,/g,''));

    if(transaction_minor_adjustment <= 0 || transaction_minor_adjustment >= 0)
    {
        transaction_subtotal = (parseFloat(transaction_minor_adjustment) + parseFloat(transaction_subtotal)).toFixed(2);
        $("#spent_receive_money_"+rowIndex).val(transaction_subtotal);

        let total_matched = (bank_transaction_amount - parseFloat(transaction_subtotal)).toFixed(2);
        $("#total_matched_"+rowIndex).val(total_matched);

        disableOrEnableReconcileButton(parseFloat($("#spent_receive_money_"+rowIndex).val().replace(/\,/g,'')).toFixed(2), bank_transaction_amount, total_matched, rowIndex);
    }
}

function reconcileTransactions(rowId, bank_transaction_id, transaction_collection_id, is_reconciled, issue_date, total_amount=0)
{
    var account_id = $("#bank_account_id").val();
    var minor_adjustment = $("#transaction_minor_adjustment_"+rowId).val();
    let total_transaction_amount = $("#spent_receive_money_"+rowId).val();
    let direct_reconcile_action = '';

    if(transaction_collection_id>0)
    {
        if(transaction_ids.indexOf(transaction_collection_id) == -1 ){
            transaction_ids.push(transaction_collection_id);
        }

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
            location.reload();
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

function searchItems(obj,rowId,index)
{
    var value = $("#"+obj.id).val().toLowerCase();
    var matchedItems = $(".search_items_"+rowId+"_"+index+".dropdown-menu li button").filter(function() {
       return $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });

    $('#item_name_code_'+rowId+'_'+index).bind('focusout', function() {
        $('#item_name_code_'+rowId+'_'+index).val('');
        $('#item_name_code_'+rowId+'_'+index).unbind('focusout');
    });
}

function disableOrEnableReconcileButton(spent_receive_money, bank_transaction_amount, total_matched, index)
{
    if(spent_receive_money != bank_transaction_amount)
    {
        $("#transactionReconcile_"+index).prop('disabled', true);
        $("#reconcile_matching_transactions_"+index).prop('disabled', true);
    }else{
        $("#transactionReconcile_"+index).prop('disabled', false);
        $("#reconcile_matching_transactions_"+index).prop('disabled', false);
    }

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
}

function hideTabs(index)
{
    transaction_ids = [];

    $('[id^=transactions_div_]').eq(index).show();
    $('[id^=transactions_div_]').not(':eq('+index+')').hide();

    $('[id^=createTransactionForm_]').eq(index).show();
    $('[id^=createTransactionForm_]').not(':eq('+index+')').hide();

    $('[id^=Match_]').eq(index).show();
    $('[id^=Create_]').eq(index).hide();

    var i, tabcontent, tablinks;

    // Get all elements with class="tabcontent" and hide them
    tabcontent = document.getElementsByClassName("tabcontent_"+index);
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    // Get all elements with class="tablinks" and remove the class "active"
    tablinks = document.getElementsByClassName("tablinks_"+index);

    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    // Show the current tab, and add an "active" class to the button that opened the tab
    document.getElementById('Match_'+index).style.display = "block";

    $('#transactionReconcile_'+index).attr("style", "visibility: hidden");
    $('#transaction_discussion_btn_'+index).attr("style", "visibility: hidden");
}
