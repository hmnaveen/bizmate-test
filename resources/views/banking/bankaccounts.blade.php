@include('includes.head')
@include('includes.user-header')

<!-- PAGE CONTAINER-->
<div class="page-container">

    @include('includes.user-top')
<!-------Disable account pop-up--------------->
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
        Are you sure you want to disable this account from our Database <span id="delete_invoice_number"></span>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary close--btn" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary delete--btn" id="disable_account_id" value="">Delete</button>
      </div>
    </div>
  </div>
</div>
<!-------Disable account pop-up end--------------->


    <!-- MAIN CONTENT-->
    <div class="main-content">
        <div class="section__content section__content--p30 p-b-50">
            <div class="container-fluid">
                <section>
                    <h3 class="sumb--title m-b-20">Bank Accounts</h3>
                </section>

                <section>
                    <div class="row">
                        <div class="col-xl-7 col-lg-8">
                            <div class="sumb--backAccountsBox sumb--dashstatbox sumb--putShadowbox">
                                
                                @if(count($accounts) < 1)
                                    Start connecting your bank account to [B]izmate to automatically see your transactions.
                                    <div class="bank--cards">
                                        <a href="/bank/accounts/add"><i class="zmdi zmdi-plus-circle-o"></i></a>
                                    </div>
                                @else
                                    <div class="bankAccount--search-wrap">
                                        <input type="text" id="bankAccount--search" autocomplete="off" placeholder="Search Bank Name/Account Name/Account's Last 4-Digits">
                                    </div>
                                    
                                    @foreach($accounts['bank_accounts'] as $account)
                                        <div class="bank--cards connected">
                                            <div class="row">
                                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-8 col-9">
                                                    <div class="account--type">{{ $account["account_name"] }}</div>
                                                </div>
                                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-4 col-3">
                                                    <div class="account--manage">
                                                        <div class="btn-group" role="group">
                                                            <button id="btnGroupDrop_type" type="button" data-toggle="dropdown" aria-expanded="true"><i class="fa-solid fa-gear"></i></button>
                                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="btnGroupDrop_type" x-placement="bottom-start" >
                                                                <a class="dropdown-item" href="/bank/account/transactions?bank_account_id={{ $account['account_id'] }}">Manage Transactions</a>
                                                                <a class="dropdown-item" href="/bank/transaction/reconcile?bank_account_id={{ $account['account_id'] }}">Reconcile Transactions</a>
                                                                <a class="dropdown-item" onclick="disableAccount('{{$account['account_id']}}');" href="javascript:void(0)">Remove Account</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-8 col-lg-8 col-md-8 col-sm-8">

                                                    <div class="account--number">
                                                        @if ($account['class']['type'] == 'credit-card')
                                                            {{ Str::mask($account["account_number"], '*', 0, strlen($account["account_number"])-4 ) }}
                                                        @else
                                                            {{ $account["account_number"] }}
                                                        @endif
                                                    </div>
                                                    <div class="account--name">{{ $account["account_holder"] }}</div>
                                                </div>
                                                <div class="flex-end col-xl-4 col-lg-4 col-md-4 col-sm-4">
                                                    <div class="account--balance {{ $account['avaialable_funds'] < 0 ? 'neg':'pos'}}">{{ $account["avaialable_funds"] }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                    <div class="nobank--cards">
                                        No matching records. Please add your bank account.
                                    </div>
                                    
                                    <a class="add--bankAccount" href="/bank/accounts/add"><i class="zmdi zmdi-plus-circle-o"></i></a>
                                        
                                    
                                @endif
                            </div>

                        </div>
                        
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>

<script>
$(function() {

    let $body = $(this);
    document.addEventListener('DOMContentLoaded', function(){
        
        $('.nobank--cards').hide();
        $('#bankAccount--search').keyup(function(){
            
            var value = $(this).val().toLowerCase().trim();
            var v = value.split("%");

            if(value == ''){
                $('.nobank--cards').hide();
                $('.bank--cards.connected').each(function(){
                    $(this).show();
                });

            } else {
                $('.nobank--cards').hide();
                $('.bank--cards.connected').each(function(j,k) {
                var s = true;
                $.each(v, function(i, x) {
                    if(s){
                        s = $(k).text().toLowerCase().indexOf(x) > -1;
                    }
                });
                    $(this).toggle(s);
                });

            }

            if (!$('.bank--cards.connected:visible').length) {
                $('.nobank--cards').show();
            }
        });

    });



    $(document).on('click', '#disable_account_id', function(event) {
        $("#delete_invoice_modal").hide();
        let accountId = $("#disable_account_id").val();
        
        var data = {
            id : accountId,
        }
        $body.find('#pre-loader').show();

        $.ajaxSetup({headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }});
		$.ajax ({
	        type    : "delete",
	        url     : '/bank/accounts/'+accountId,
            data    : data,
	        enctype: 'multipart/form-data',
	        success : function(data) {
                $body.find('#pre-loader').hide();
	        	Swal.fire(

			      'Success',
			       data.message,
			      'success'

			    ).then((res) => {
			    	window.location = window.location.href;
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
    });
});

function disableAccount(accountId){        
    $("#disable_account_id").val('');
    $("#disable_account_id").val(accountId);

    $('#delete_invoice_modal').modal({
        backdrop: 'static',
        keyboard: true, 
        show: true
    });
}
</script>

@include('includes.footer')