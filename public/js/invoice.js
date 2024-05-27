function openPopUpModel(id, from)
{
    console.log(from);
    if(from != 'addAccount')
    {
        $("#invoice_item_chart_accounts_parts").val('');
        $("#invoice_item_chart_accounts_parts_id").val('');

        $("#invoice_item_part_row_id").val('');
        $("#invoice_item_part_row_id").val(id);
        $("#invoice_item_code").val('');
        $("#invoice_item_name").val('');
        $("#invoice_item_unit_price").val('');
        $("#invoice_item_tax_rate").val('');
        $("#invoice_item_description").val('');
    }

    $('#newItemModal').modal({
        backdrop: 'static',
        keyboard: true, 
        show: true
    });
}

function openNewAddAccountPopUpModel(id, from)
{
    $("#invoice_chart_accounts_type_id").val('');
    // $("#invoice_account_part_row_id").val('');

    $("#invoice_account_part_row_id").val('');
    $("#invoice_account_part_row_id").val(id);
    $("#invoice_chart_accounts_code").val('');
    $("#invoice_chart_accounts_name").val('');
    $("#invoice_chart_accounts_description").val('');
    $("#invoice_chart_accounts_tax_rate").val('');
    if(from == 'addItem'){
        $("#add_account_from").val('addItem');
        $("#invoice_account_part_row_id").val($("#invoice_item_part_row_id").val());
        $(".close").click();
    }
    else{
        $("#add_account_from").val('');
        $("#invoice_item_chart_accounts_parts").val('');
        $("#invoice_item_chart_accounts_parts_id").val('');
    }
    
    $('#newAddAccountModal').modal({
        backdrop: 'static',
        keyboard: true, 
        show: true
    });
}

function addNewAccount(id)
{
    var id = $("#"+id).val();

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
                    if($("#add_account_from").val() == 'addItem'){
                        openPopUpModel(id,'addAccount');
                        $("#invoice_chart_account_list").empty();

                        $("#invoice_chart_account_list").append('<li id="add_new_invoice_chart_account" class="add-new--btn"><a href="#" data-toggle="modal" data-target="#newAddAccountModal" onclick=openNewAddAccountPopUpModel('+id+',"addItem")><i class="fa-solid fa-circle-plus"></i>New Account</a></li>')

                        $.each(response.data,function(key,value){
                            $("#invoice_chart_account_list").append(
                                '<li class="accounts-group--label">'+value['chart_accounts_name']+'</li>'
                            );
                            $.each(value['chart_accounts_particulars'],function(k,val){
                                $("#invoice_chart_account_list").append('\n\<li>\n\
                                <button type="button" class="invoice_item" data-myid="'+counter+'" onclick=addInvoiceChartAccount('+val['id']+','+id+',"addItem")>\n\
                                <span id="data_name_'+counter+'">'+val['chart_accounts_particulars_code']+' - '+val['chart_accounts_particulars_name']+'</span>\n\
                                    </button></li>');
                            });
                        });

                        if($("#invoice_item_tax_rate option:selected").attr('id')){
                            const selected_option = $("#invoice_item_tax_rate option:selected").attr('id');
                            $("#"+selected_option).removeAttr("selected");
                        }

                        $("#invoice_item_tax_rate").val('');
                        // var tax_rate_id = $("#invoice_chart_accounts_tax_rate").val();
                        
                        $("#invoice_item_tax_rate").val($("#invoice_chart_accounts_tax_rate").val()).change();
                        
                        $("#invoice_item_chart_accounts_parts_id").val(response.id);
                        $("#invoice_item_chart_accounts_parts").val(post_data.chart_accounts_parts_code+' - '+post_data.chart_accounts_parts_name);
                        
                        $(".close").click();
                    }else{
                        $("#invoice_chart_account_list_"+id).empty();
                        var counter = 0;
                        $("#invoice_chart_account_list_"+id).append('<li id="add_new_invoice_chart_account_'+id+'" class="add-new--btn"><a href="#" data-toggle="modal" data-target="#newAddAccountModal" onclick=openNewAddAccountPopUpModel('+id+')><i class="fa-solid fa-circle-plus"></i>New Account</a></li')

                        $.each(response.data,function(key,value){
                            $("#invoice_chart_account_list_"+id).append(
                                '<li class="accounts-group--label">'+value['chart_accounts_name']+'</li>'
                            );
                            $.each(value['chart_accounts_particulars'],function(k,val){
                                $("#invoice_chart_account_list_"+id).append('\n\<li><div style="padding: 10px;border-bottom: 1px solid lightgrey">\n\
                                <button type="button" class="invoice_item" data-myid="'+counter+'" onclick=addInvoiceChartAccount("'+encodeURI(val['id'])+'","'+id+'");>\n\
                                <span id="data_name_'+counter+'">'+val['chart_accounts_particulars_code']+' - '+val['chart_accounts_particulars_name']+'</span>\n\
                                                            </button></div></li>');
                            });
                        });
                        
                        if($("#invoice_parts_tax_rate_"+id+" option:selected").attr('id')){
                            const selected_option = $("#invoice_parts_tax_rate_"+id+" option:selected").attr('id');
                            $("#"+selected_option).removeAttr("selected");
                        }

                        $("#invoice_parts_chart_accounts_parts_id_"+id).val('');
                        $("#invoice_parts_chart_accounts_"+id).val('');
                        $("#invoice_parts_chart_accounts_code_"+id).val('');
                        $("#invoice_parts_chart_accounts_name_"+id).val('');
                        $("#invoice_parts_tax_rate_"+id).val('');
                        $("#invoice_parts_tax_rate_id_"+id).val('');

                        $("#invoice_parts_chart_accounts_parts_id_"+id).val(response.id);

                        $("#invoice_parts_chart_accounts_"+id).val(post_data.chart_accounts_parts_code+' - '+post_data.chart_accounts_parts_name);
                        $("#invoice_parts_chart_accounts_name_"+id).val(post_data.chart_accounts_parts_name);
                        $("#invoice_parts_chart_accounts_code_"+id).val(post_data.chart_accounts_parts_code);

                        // var tax_rate_id = $("#invoice_chart_accounts_tax_rate").val();
                        
                        $("#invoice_parts_tax_rate_id_"+id).val(invoice_chart_accounts_tax_id_val[0]);
                        
                        // $("#invoice_parts_tax_rate_"+id+ " option[id='"+tax_rate_id+"_"+id+"']").attr("selected", "selected");
                        $("#invoice_parts_tax_rate_"+id).val($("#invoice_chart_accounts_tax_rate").val()).change();
                        
                        $(".close").click();
                    }
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

function addInvoiceChartAccount(chart_accounts_parts_id, rowId, from)
{
    if(chart_accounts_parts_id && rowId>=0){
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });
        $.ajax({
            method: "GET",
            url: "/chart-accounts-parts/" + chart_accounts_parts_id,
            success : function(response){
                if(from == 'addItem'){
                    if($("#invoice_item_tax_rate option:selected").attr('id')){
                        const selected_option = $("#invoice_item_tax_rate option:selected").attr('id');
                        $("#"+selected_option).removeAttr("selected");
                    }

                    $("#invoice_item_tax_rate").val('');
                    $("#invoice_item_chart_accounts_parts_id").val('');
                    $("#invoice_item_chart_accounts_parts").val('');

                    $("#invoice_item_chart_accounts_parts").val(response['data']['chart_accounts_particulars_code']+' - '+response['data']['chart_accounts_particulars_name']);
                    $("#invoice_item_chart_accounts_parts_id").val(response['data']['id']);
                    
                    $("#invoice_item_tax_rate").val(response['data']['invoice_tax_rates']['id']+"#|#"+response['data']['invoice_tax_rates']['tax_rates']).change();

                }else{
                    if($("#invoice_parts_tax_rate_"+rowId+" option:selected").attr('id')){
                        const selected_option = $("#invoice_parts_tax_rate_"+rowId+" option:selected").attr('id');
                        $("#"+selected_option).removeAttr("selected");
                    }

                    $("#invoice_parts_chart_accounts_parts_id_"+rowId).val('');
                    $("#invoice_parts_chart_accounts_"+rowId).val('');
                    $("#invoice_parts_tax_rate_id_"+rowId).val('');

                    $("#invoice_parts_chart_accounts_"+rowId).val(response['data']['chart_accounts_particulars_code']+' - '+response['data']['chart_accounts_particulars_name']);
                    $("#invoice_parts_chart_accounts_parts_id_"+rowId).val(response['data']['id']);

                    const tax_rate_id = response['data']['invoice_tax_rates']['id'] ? response['data']['invoice_tax_rates']['id'] : '';
                    const tax_rate = response['data']['invoice_tax_rates']['id'] ? response['data']['invoice_tax_rates']['tax_rates'] : '';

                    $("#invoice_parts_tax_rate_"+rowId).val(tax_rate_id+"#|#"+tax_rate).change();

                    $("#invoice_parts_tax_rate_id_"+rowId).val(tax_rate_id);
                }
            },
            error : function(error){ 
                alert(error.responseJSON.message);
            }
        });
    }
}

function getChartAccountsParticularsList(id)
{
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });
    $.ajax({
        method: "GET",
        url: "/chart-accounts-parts",
        success : function(response){
            $("#invoice_chart_account_list_"+id).empty();
            var counter = 0;
            $("#invoice_chart_account_list_"+id).append('<li id="add_new_invoice_chart_account_'+id+'" class="add-new--btn"><a href="#" data-toggle="modal" data-target="#newAddAccountModal" onclick=openNewAddAccountPopUpModel('+id+')><i class="fa-solid fa-circle-plus"></i>New Account</a></li>')
            
            $.each(response.data,function(key,value){
                $("#invoice_chart_account_list_"+id).append(
                    '<li class="accounts-group--label">'+value['chart_accounts_name']+'</li>'
                );
                $.each(value['chart_accounts_particulars'],function(k,val){
                    $("#invoice_chart_account_list_"+id).append('\n\<li>\n\
                    <button type="button" class="invoice_item" data-myid="'+counter+'" onclick=addInvoiceChartAccount("'+encodeURI(val['id'])+'","'+id+'");>\n\
                        <span id="data_name_'+counter+'">'+val['chart_accounts_particulars_code']+' - '+val['chart_accounts_particulars_name']+'</span>\n\
                    </button></li>');
                });
            });
        },
        error : function(error){ 
            alert(error.responseJSON.message);
        }
    });
}
//Add new row on Table Particulars
function addInvoiceParts(transaction_type, payment_option)
{
    var rowIndex = [0];
    rowIndex = $('#invoice_part_ids').val();
    rowIndex = JSON.parse(rowIndex);
    if(rowIndex.length>0){
        rowIndex = parseInt(Math.max(...rowIndex))+1;
    }else{
        rowIndex = 1;
    }
    var ulId = JSON.parse($('#invoice_part_ids').val())[0];

    if($("#invoice_default_tax").val() == 'no_tax'){
        $("#invoice_parts_tax_rate_"+rowIndex).css("display", "none");
    }
    
    $("#partstable tr.add--new-line").before('<tr class="invoice_parts_form_cls" id="invoice_parts_row_id_'+rowIndex+'" >\
            <td style="display:'+(transaction_type == "invoice" || transaction_type == "receive_money" || transaction_type == "spend_money" ? 'auto' : 'none' )+'"><input placeholder="Search your item list" autocomplete="off" data-toggle="dropdown" type="text" id="invoice_parts_name_code_'+rowIndex+'" name="invoice_parts_name_code_'+rowIndex+'" onkeyup="searchInvoiceparts(this, '+rowIndex+')" value="">\
            <input type="hidden" id="invoice_parts_code_'+rowIndex+'" name="invoice_parts_code_'+rowIndex+'" value="">\
            <input type="hidden" id="invoice_parts_name_'+rowIndex+'" name="invoice_parts_name_'+rowIndex+'" value="">\
                <ul class="search_items_'+rowIndex+' dropdown-menu invoice-expenses--dropdown" id="invoice_item_list_'+rowIndex+'">\
                </ul>\
            </td>\
            <td style="display : '+((transaction_type == "aroverpayment" || transaction_type == "apoverpayment") ? 'none' : '' )+'"><input type="number" id="invoice_parts_quantity_'+rowIndex+'" name="invoice_parts_quantity_'+rowIndex+'" value="" onchange=invoicepartsQuantity('+rowIndex+') required></td>\
            <td><textarea class="autoresizing" id="invoice_parts_description_'+rowIndex+'" name="invoice_parts_description_'+rowIndex+'" value="" required></textarea></td>\
            <td><input type="float" id="invoice_parts_unit_price_'+rowIndex+'" name="invoice_parts_unit_price_'+rowIndex+'" value="" onchange="invoicepartsQuantity('+rowIndex+')"; onfocusin=removeComma("invoice_parts_unit_price_'+rowIndex+'") onfocusout=addComma("invoice_parts_unit_price_'+rowIndex+'") required>\
                <input type="hidden" id="invoice_parts_gst_'+rowIndex+'" name="invoice_parts_gst_'+rowIndex+'" value="">\
            </td>\
            <td>\
                <input data-toggle="dropdown" type="text" id="invoice_parts_chart_accounts_'+rowIndex+'" name="invoice_parts_chart_accounts_'+rowIndex+'"  value="" required>\
                <input type="hidden" id="invoice_parts_chart_accounts_code_'+rowIndex+'" name="invoice_parts_chart_accounts_code_'+rowIndex+'" value="">\
                <input type="hidden" id="invoice_parts_chart_accounts_name_'+rowIndex+'" name="invoice_parts_chart_accounts_name_'+rowIndex+'" value="">\
                <input type="hidden" id="invoice_parts_chart_accounts_parts_id_'+rowIndex+'" name="invoice_parts_chart_accounts_parts_id_'+rowIndex+'" value="">\
                <ul class="dropdown-menu invoice-expenses--dropdown" id="invoice_chart_account_list_'+rowIndex+'">\
                </ul>\
            </td>\
            <td>\
                <input type="hidden" name="invoice_parts_tax_rate_id_'+rowIndex+'" id="invoice_parts_tax_rate_id_'+rowIndex+'" value="">\
                <input type="hidden" name="invoice_parts_tax_rate_name_'+rowIndex+'" id="invoice_parts_tax_rate_name_'+rowIndex+'" value="">\
                <div class="form-input--wrap">\
                    <div class="row">\
                        <div class="col-12 for--tables">\
                            <select class="form-input--dropdown" id="invoice_parts_tax_rate_'+rowIndex+'" name="invoice_parts_tax_rate_'+rowIndex+'" onchange="invoicepartsQuantity('+rowIndex+');getTaxRates('+rowIndex+')" required>\
                            </select>\
                        </div>\
                    </div>\
                </div>\
            </td>\
            <td><input class="input--readonly" type="float" readonly id="invoice_parts_amount_'+rowIndex+'" name="invoice_parts_amount_'+rowIndex+'" value="" required>\n\
            </td><td class="tableOptions"><button class="btn sumb--btn delepart" type="button" onclick=deleteInvoiceParts('+rowIndex+')><i class="fas fa-trash-alt"></i></button></td></tr>');
    
    getInvoiceTaxRates(rowIndex);
    if(transaction_type == "invoice" || transaction_type == "receive_money" || transaction_type == "spend_money")
    {
        getInvoiceItemList(rowIndex);
    }
    
    getChartAccountsParticularsList(rowIndex);
    addOrRemoveInvoicePartsIds('add',rowIndex);
}

function getInvoiceItemList(id)
{
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });
    $.ajax({
    method: "POST",
    url: "/invoice-items",
        success : function(response){
            $("#invoice_item_list_"+id).empty();
            var counter = 0;
            $("#invoice_item_list_"+id).append('<li id="add_new_invoice_item_'+id+'" class="add-new--btn"><a href="#" data-toggle="modal" data-target="#newItemModal" onclick=openPopUpModel('+id+')><i class="fa-solid fa-circle-plus"></i>New Item</a></li>')

            $.each(response.data,function(key,value){
                counter++;
                $("#invoice_item_list_"+id).append('\n\<li>\n\
                    <button type="button" class="invoice_item" data-myid="'+counter+'" onclick=getInvoiceItemsById("'+encodeURI(value['id'])+'","'+id+'");>\n\
                    <span id="data_name_'+counter+'">'+value['invoice_item_code']+' : '+value['invoice_item_name']+'</span>\n\
                    <input type="hidden" id="invoice_item_id_'+counter+'" name="invoice_item_id" value="'+value['id']+'">\n\
                    </button></li>');
            });
        },
        error : function(error){
            alert(error.responseJSON.message);
        }
    });
}

function getInvoiceTaxRates(rowId)
{
    if(rowId>=0){
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });
        $.ajax({
            method: "GET",
            url: "/invoice-tax-rates",
            // data: post_data,
            success : function(response){
                $("#invoice_parts_tax_rate_"+rowId).val('');
                $("#invoice_parts_tax_rate_"+rowId).append('<option selected value="0#|#0">Tax Rate Option</option>');
                
                $.each(response.data, function (key, value) {
                    $("#invoice_parts_tax_rate_"+rowId).append('<option id='+value['id']+"_"+rowId+' value='+value['id']+'#|#'+value['tax_rates']+'>'+value['tax_rates_name']+'</option>');
                });
            },
            error : function(error){
                alertBottom(null, error.responseJSON.message);
            }
        });
    }
}

function getInvoiceItemsById(itemId, rowId)
{
    
    if(itemId && rowId>=0){
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });
        $.ajax({
            method: "GET",
            url: "/invoice-items/" + itemId,
            success : function(response){
                $("#invoice_parts_tax_rate_"+rowId ).val('');
                if($("#invoice_parts_tax_rate_"+rowId+" option:selected").attr('id')){
                    const selected_option = $("#invoice_parts_tax_rate_"+rowId+" option:selected").attr('id');
                    $("#"+selected_option).removeAttr("selected");

                }

                $("#invoice_parts_name_code_"+rowId).val('');
                $("#invoice_parts_name_"+rowId).val('');
                $("#invoice_parts_code_"+rowId).val('');
                $("#invoice_parts_quantity_"+rowId).val('');
                $("#invoice_parts_description_"+rowId).val('');
                $("#invoice_parts_unit_price_"+rowId).val('');
                $("#invoice_parts_tax_rate_"+rowId).val('');
                $("#invoice_parts_tax_rate_id_"+rowId).val('');
                $("#invoice_parts_chart_accounts_"+rowId).val('');
                $("#invoice_parts_chart_accounts_parts_id_"+rowId).val('');

                $("#invoice_parts_name_code_"+rowId).val(response['data']['invoice_item_code']+' : '+response['data']['invoice_item_name']);
                $("#invoice_parts_name_"+rowId).val(response['data']['invoice_item_name']);
                $("#invoice_parts_code_"+rowId).val(response['data']['invoice_item_code']);
                $("#invoice_parts_quantity_"+rowId).val(response['data']['invoice_item_quantity']);
                $("#invoice_parts_description_"+rowId).val(response['data']['invoice_item_description']);
                $("#invoice_parts_unit_price_"+rowId).val(parseFloat(response['data']['invoice_item_unit_price']).toFixed(2).replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));
                
                const chart_account = response['data']['chart_accounts_parts'] ? response['data']['chart_accounts_parts']['chart_accounts_particulars_code']+' - '+response['data']['chart_accounts_parts']['chart_accounts_particulars_name'] : '';
                $("#invoice_parts_chart_accounts_"+rowId).val(chart_account);
                $("#invoice_parts_chart_accounts_parts_id_"+rowId).val(response['data']['chart_accounts_parts']['id']);
                
                const tax_rate_id = response['data']['tax_rates']['id'] ? response['data']['tax_rates']['id'] : '';
                const tax_rate = response['data']['tax_rates']['id'] ? response['data']['tax_rates']['tax_rates'] : '';
                
                $("#invoice_parts_tax_rate_"+rowId).val(tax_rate_id+"#|#"+tax_rate).change();
                
                $("#invoice_parts_tax_rate_id_"+rowId).val(tax_rate_id);
                
                invoicepartsQuantity(rowId)
            },
            error:function(error){ 
                alert(error.responseJSON.message);
            }
        });
    }
}


function addOrRemoveInvoicePartsIds(action_type, id)
{
    var rowIndex = [0];
    rowIndex = $('#invoice_part_ids').val();
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
        $('#invoice_parts_row_id_'+id).remove();
    }
    $('#invoice_part_ids').val(JSON.stringify(rowIndex));
}

function deleteInvoiceParts(rowId)
{
    var rowIndex = [0];
    rowIndex = $('#invoice_part_ids').val();
    rowIndex = JSON.parse(rowIndex);
    if(rowIndex.length>1){
        addOrRemoveInvoicePartsIds("delete", rowId);
        invoicepartsQuantity(rowId);
    }
}

function invoicepartsQuantity(id)
{
    var rowIndex = $('#invoice_part_ids').val();
    rowIndex = JSON.parse(rowIndex);
    var sub_total=0;
    var total_gst=0;
    var gst_percentage = 0;
    var total_amount_due = 0;
    
    $.each(rowIndex, function (key, rowId) {

        var selected_invoice_parts_tax_rate = $("#invoice_parts_tax_rate_"+rowId).val().split("#|#");
          
        var quantity = $("#invoice_parts_quantity_"+rowId).val();
        var unit_price = Number($("#invoice_parts_unit_price_"+rowId).val().replace(/\,/g,'')).toFixed(2);

        if(quantity || unit_price){
            var total_item_price = (parseFloat((quantity ? quantity : 0 )*( unit_price ? unit_price : 0 )).toFixed(2));
            sub_total = (parseFloat(sub_total) + parseFloat(total_item_price)).toFixed(2);
            
            $("#invoice_parts_amount_"+rowId).val(total_item_price.replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));       
            $("#invoice_sub_total").val(sub_total.replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));
            console.log(total_item_price);
            console.log($("#invoice_default_tax").val());
            if($("#invoice_default_tax").val() == 'tax_exclusive'){
                $(".invoice-total--gst").show();
                total_amount_due = (parseFloat(sub_total) + parseFloat(total_gst)).toFixed(2);
                $("#invoice_total_amount").val(total_amount_due.replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));
                $("#invoice_parts_tax_rate_"+rowId).css("display", "block");

            }
            else if($("#invoice_default_tax").val() == 'no_tax'){
                $("#invoice_total_gst").val(0);
                $(".invoice-total--gst").hide();
                $("#invoice_parts_tax_rate_"+rowId).css("display", "none");
                $("#invoice_total_amount").val(sub_total.replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));
                $("#invoice_total_gst_text").html("Total Tax 0%");
            }
            else{
                $(".invoice-total--gst").show();
                $("#invoice_parts_tax_rate_"+rowId).css("display", "block");
                $("#invoice_total_amount").val(sub_total.replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));
            }
            
            if(parseFloat(selected_invoice_parts_tax_rate[1])>=0 && total_item_price>=0){
                if($("#invoice_default_tax").val() == 'tax_exclusive'){
                    
                    var gst = (total_item_price * selected_invoice_parts_tax_rate[1]/100);
                    total_gst = (parseFloat(total_gst) + gst).toFixed(2);
                    if(selected_invoice_parts_tax_rate[1] > 0)
                        gst_percentage = selected_invoice_parts_tax_rate[1];

                    $("#invoice_total_gst").val(total_gst);
                    total_amount_due = (parseFloat(sub_total) + parseFloat(total_gst)).toFixed(2);
                    $("#invoice_total_amount").val(total_amount_due.replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));
                    $("#invoice_total_gst_text").html("Total Tax "+ gst_percentage +' %');
                    
                }
                else if($("#invoice_default_tax").val() == 'tax_inclusive'){
                    var inclusive_gst = (total_item_price - total_item_price / (1 + selected_invoice_parts_tax_rate[1]/100));
                    total_gst = (total_gst + inclusive_gst);

                    if(selected_invoice_parts_tax_rate[1] > 0)
                        gst_percentage = selected_invoice_parts_tax_rate[1];
                    
                    $("#invoice_total_gst_text").html("Includes Tax "+ gst_percentage +' %');
                    $("#invoice_total_gst").val((parseFloat(total_gst)).toFixed(2));
                }
            }
            else if(parseFloat(selected_invoice_parts_tax_rate[1]) == 0 && total_item_price>=0){
                if($("#invoice_default_tax").val() == 'tax_exclusive'){

                    var gst = (total_item_price * selected_invoice_parts_tax_rate[1]/100);
                    if(parseFloat(total_gst)>0){
                        $("#invoice_total_gst_text").html("Total Tax "+ gst_percentage +' %');
                    }else{
                        $("#invoice_total_gst_text").html("Total Tax "+ selected_invoice_parts_tax_rate[1]+' %');
                    }
                    $("#invoice_total_gst").val(total_gst);

                    total_amount_due = (parseFloat(sub_total) + parseFloat(total_gst)).toFixed(2);
                    $("#invoice_total_amount").val(total_amount_due.replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));
                }
                else if($("#invoice_default_tax").val() == 'tax_inclusive'){
                    var inclusive_gst = (total_item_price - total_item_price / (1 + selected_invoice_parts_tax_rate[1]/100));
        
                    if(parseFloat(total_gst)>0){
                        $("#invoice_total_gst_text").html("Includes Tax "+ gst_percentage +' %');
                    }else{
                        $("#invoice_total_gst_text").html("Includes Tax "+ selected_invoice_parts_tax_rate[1] +' %');
                    }

                    $("#invoice_total_gst").val((parseFloat(total_gst)).toFixed(2));
                }
            }
        }
    });
}

function InvoicepartsUnitPrice(obj)
{
    var rowIds=obj.id.split('_');
    rowIds = parseInt(rowIds[4]);
    var totalUnitPrice = $("#"+obj.id).val()*($("#invoice_parts_quantity_"+rowIds).val() ? $("#invoice_parts_quantity_"+rowIds).val() : 0);
    var subPreviousAmount =  $("#invoice_sub_total").val() - $("#invoice_parts_amount_"+rowIds).val()
    $("#invoice_sub_total").val(subPreviousAmount)
    $("#invoice_parts_amount_"+rowIds).val(totalUnitPrice)
    $("#invoice_sub_total").val((parseFloat($("#invoice_sub_total").val()) + totalUnitPrice).toFixed(2))
    $("#invoice_total_gst").val(($("#invoice_sub_total").val()*0.1).toFixed(2))
    $("#invoice_total_amount").val((parseFloat($("#invoice_sub_total").val()) + parseFloat($("#invoice_total_gst").val())).toFixed(2))
}

function searchInvoiceparts(obj, rowIndex)
{
    var value = $("#"+obj.id).val().toLowerCase();

    var matchedItems = $(".search_items_"+rowIndex+".dropdown-menu li button").filter(function() {
       return $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });

    $('#invoice_parts_name_code_'+rowIndex).bind('focusout', function() {
        $('#invoice_parts_name_code_'+rowIndex).val('');
        $('#invoice_parts_name_code_'+rowIndex).unbind('focusout');
    });
}

function addInvoiceItem(id)
{
    var id = $("#"+id).val();
    const invoice_tax_id_val =  $("#invoice_item_tax_rate").val() ? $("#invoice_item_tax_rate").val().split("#|#") : '';
    var post_data = {
        invoice_item_code: $("#invoice_item_code").val(),
        invoice_item_name: $("#invoice_item_name").val(),
        invoice_item_unit_price: $("#invoice_item_unit_price").val(),
        invoice_item_tax_rate: invoice_tax_id_val ? invoice_tax_id_val[0] : '',
        invoice_item_tax_rate_id: invoice_tax_id_val ? invoice_tax_id_val[0] : '',
        invoice_item_description: $("#invoice_item_description").val(),
        invoice_item_chart_accounts_parts: $("#invoice_item_chart_accounts_parts").val(),
        invoice_item_chart_accounts_parts_id: $("#invoice_item_chart_accounts_parts_id").val()
    };

    if(post_data.invoice_item_tax_rate_id && post_data.invoice_item_code  && 
        post_data.invoice_item_name && post_data.invoice_item_unit_price && 
        post_data.invoice_item_chart_accounts_parts_id && post_data.invoice_item_chart_accounts_parts
    ){
        
        $("#invoice_item_code_error").removeClass('alert alert-danger');
        $("#invoice_item_code_error").html('');

        $("#invoice_item_name_error").removeClass('alert alert-danger');
        $("#invoice_item_name_error").html('');

        $("#invoice_item_unit_price_error").removeClass('alert alert-danger');
        $("#invoice_item_unit_price_error").html('');

        $("#invoice_item_tax_rate_error").removeClass('alert alert-danger');
        $("#invoice_item_tax_rate_error").html('');

        $("#invoice_chart_accounts_type_error").removeClass('alert alert-danger');
        $("#invoice_chart_accounts_type_error").html('');

            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });
            $.ajax({
            method : "POST",
            url : "/add-invoice-item",
            data : post_data,
            success : function(response){
                $("#invoice_item_list_"+id).empty();
                var counter = 0;
                $("#invoice_item_list_"+id).append('<li id="add_new_invoice_item_'+id+'" class="add-new--btn"><a href="#" data-toggle="modal" data-target="#newItemModal" onclick=openPopUpModel('+id+')><i class="fa-solid fa-circle-plus"></i>New Item</a></li>')
                $.each(response.data,function(key,value){
                    counter++;
                    $("#invoice_item_list_"+id).append('\n\<li>\n\
                    <button type="button" class="invoice_item" data-myid="'+counter+'" onclick=getInvoiceItemsById("'+encodeURI(value['id'])+'","'+id+'");>\n\
                    <span id="data_name_'+counter+'">'+value['invoice_item_code']+ ' : ' +value['invoice_item_name']+'</span>\n\
                                                    </button></li>');
                });

                $("#invoice_parts_tax_rate_"+id).val('');
                if($("#invoice_parts_tax_rate_"+id+" option:selected").attr('id')){
                    const selected_option = $("#invoice_parts_tax_rate_"+id+" option:selected").attr('id');
                    $("#"+selected_option).removeAttr("selected");
                }

                $("#invoice_parts_name_code_"+id).val('');
                $("#invoice_parts_name_"+id).val('');
                $("#invoice_parts_code_"+id).val('');
                $("#invoice_parts_description_"+id).val('');
                $("#invoice_parts_unit_price_"+id).val('');
                $("#invoice_parts_quantity_"+id).val('');
                $("#invoice_parts_tax_rate_"+id).val('');
                $("#invoice_parts_tax_rate_id_"+id).val('');

                $("#invoice_parts_chart_accounts_"+id).val('');
                $("#invoice_parts_chart_accounts_parts_id_"+id).val('');

                $("#invoice_parts_name_code_"+id).val(post_data.invoice_item_code+ ' : ' +post_data.invoice_item_name);
                $("#invoice_parts_name_"+id).val(post_data.invoice_item_name);
                $("#invoice_parts_code_"+id).val(post_data.invoice_item_code);


                $("#invoice_parts_description_"+id).val(post_data.invoice_item_description);
                $("#invoice_parts_unit_price_"+id).val(parseFloat(post_data.invoice_item_unit_price).toFixed(2).replace(/\B(?=(?:\d{3})+(?!\d))/g, ','));
                $("#invoice_parts_quantity_"+id).val(1.00);


                $("#invoice_parts_chart_accounts_"+id).val($("#invoice_item_chart_accounts_parts").val());
                $("#invoice_parts_chart_accounts_parts_id_"+id).val(post_data.invoice_item_chart_accounts_parts_id);
                
                $("#invoice_parts_tax_rate_id_"+id).val(invoice_tax_id_val[0]);
                $("#invoice_parts_tax_rate_"+id).val($("#invoice_item_tax_rate").val()).change();
                
                getChartAccountsParticularsList(id);
                invoicepartsQuantity(id)
                $("#invoice_item_chart_accounts_parts").val('');
                $("#invoice_item_chart_accounts_parts_id").val('');
                $("#invoice_account_part_row_id").val('')

                $(".close").click();
            
            },
            error : function(error){
                $("#invoice_item_code_error").addClass('alert alert-danger');
                $("#invoice_item_code_error").html(error.responseJSON.message);
            }
        });
    }else{
        $("#invoice_item_code_error").addClass('alert alert-danger');
        $("#invoice_item_code_error").html('Code field is required');

        $("#invoice_item_name_error").addClass('alert alert-danger');
        $("#invoice_item_name_error").html('Name field is required');

        $("#invoice_item_unit_price_error").addClass('alert alert-danger');
        $("#invoice_item_unit_price_error").html('Unit price field is required');

        $("#invoice_item_tax_rate_error").addClass('alert alert-danger');
        $("#invoice_item_tax_rate_error").html('Tax rate field is required');

        $("#invoice_chart_accounts_type_error").addClass('alert alert-danger');
        $("#invoice_chart_accounts_type_error").html('Account type field is required');
    }
}

function previewInvoice()
{
    var rowIndex = $('#invoice_part_ids').val();
    rowIndex = JSON.parse(rowIndex);
    $("#invoice_preview_parts_rows tr").remove();
    $("#invoice_preview_sub_total").html('');
    $("#invoice_preview_total_tax").html('');
    $("#invoice_preview_total_amount").html('');
    $("#invoice_preview_to").html('');
    $("#invoice_preview_invoice_number").html('');
    $("#invoice_preview_issue_date").html('');
    $("#invoice_preview_due_date").html('');
    $("#invoice_preview_total_tax_percentage").html('');

    $(".bd-example-modal-lg").modal({
        show: true
    });
    $("#invoice_preview_to").html($("#client_email").val());
    $("#invoice_preview_invoice_number").html($("#invoice_number").val());
    $("#invoice_preview_issue_date").html($('#invoice_issue_date').val());
    $("#invoice_preview_due_date").html($("#invoice_duedate").val());

    $("#invoice_preview_sub_total").html($("#invoice_sub_total").val());
    $("#invoice_preview_total_tax").html($("#invoice_total_gst").val());
    $("#invoice_preview_total_tax_percentage").html($("#invoice_total_gst_text").text());
    $("#invoice_preview_total_amount").html($("#invoice_total_amount").val());
    
    
    for(var i = 0; i<rowIndex.length; i++){
        if($("#invoice_parts_name_code_"+rowIndex[i]).val() || 
            $("#invoice_parts_name_code_"+rowIndex[i]).val() ||
            $("#invoice_parts_quantity_"+rowIndex[i]).val() || 
            $("#invoice_parts_description_"+rowIndex[i]).val() || 
            $("#invoice_parts_unit_price_"+rowIndex[i]).val() ||
            $("#invoice_parts_tax_rate_"+rowIndex[i]).val() ||
            $("#invoice_parts_amount_"+rowIndex[i]).val() ||
            $("#invoice_parts_chart_accounts_"+rowIndex[i]).val() 
        ){
            var selected_invoice_parts_tax_rate = $("#invoice_parts_tax_rate_"+rowIndex[i]).val().split("#|#");
            const tax_rate = $("#invoice_default_tax").val() == 'no_tax' ? 0 : selected_invoice_parts_tax_rate[1];
            $("#invoice_preview_parts").append('<tr>\
                <td>'+$("#invoice_parts_name_code_"+rowIndex[i]).val()+'</td>\
                <td>'+$("#invoice_parts_quantity_"+rowIndex[i]).val()+'</td>\
                <td>'+$("#invoice_parts_description_"+rowIndex[i]).val()+'</td>\
                <td>'+$("#invoice_parts_unit_price_"+rowIndex[i]).val()+'</td>\
                <td>'+$("#invoice_parts_chart_accounts_"+rowIndex[i]).val()+'</td>\
                <td>'+tax_rate+'%</td>\
                <td>'+$("#invoice_parts_amount_"+rowIndex[i]).val()+'</td>\
            </tr> ');
        }
    }
}


function getTaxRates(rowId)
{
    // if($("#invoice_parts_tax_rate_"+rowId+" option:selected").attr('id')){
    //     const selected_option = $("#invoice_parts_tax_rate_"+rowId+" option:selected").attr('id');
    //     $("#invoice_parts_tax_rate_id_"+rowId).val('');
    //     $("#invoice_parts_tax_rate_id_"+rowId).val($("#tax_rate_id_"+selected_option).val());
    // }
    console.log(rowId);
    if($("#invoice_parts_tax_rate_"+rowId+" option:selected").attr('id')){
        const selected_option = $("#invoice_parts_tax_rate_"+rowId+" option:selected").attr('id');
        var selected_invoice_parts_tax_rate = $("#invoice_parts_tax_rate_"+rowId).val().split('#|#');
        $("#invoice_parts_tax_rate_id_"+rowId).val('');
        $("#invoice_parts_tax_rate_id_"+rowId).val(selected_invoice_parts_tax_rate[0]);
       
    }
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