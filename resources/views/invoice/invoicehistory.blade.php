@include('includes.head')
@include('includes.user-header')

<div class="modal fade" id="delete_invoice_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Delete Invoice</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this invoice <span id="delete_invoice_number"></span>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="delete_invoice" value="">Delete</button>
      </div>
    </div>
  </div>
</div>

<!-- PAGE CONTAINER-->
<div class="page-container">

    @include('includes.user-top')

    <!-- MAIN CONTENT-->
    <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid">

                <section>
                    <h3 class="sumb--title">Invoice History</h3>
                </section>

                <section>
                    <div class="row">
                        <div class="col-xl-12">
                            <form action="/invoice/history"  method="GET" enctype="multipart/form-data" id="search_form">
                                <div class="row">
                                    <div class="col-xl-4 col-lg-4 order-xl-1">
                                        <div class="form-input--wrap">
                                            <label class="form-input--question" for="">Invoice No.</label>
                                            <div class="form--inputbox row">
                                                <div class="col-12">
                                                    <input type="text" id="search_number_email_amount" name="search_number_email_amount" placeholder="Invoice No., Email, Amount"  value="{{!empty($search_number_email_amount) ? $search_number_email_amount : ''}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-4 col-lg-4 order-xl-2">
                                        <div class="form-input--wrap">
                                            <label class="form-input--question" for="">Start Date</label>
                                            <div class="date--picker row">
                                                <div class="col-12">
                                                    <input type="text" id="start_date" name="start_date" placeholder="Date('MM/DD/YYYY')"  readonly value="{{!empty($start_date) ? $start_date : ''}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-4 col-lg-4 order-xl-3">
                                        <div class="form-input--wrap">
                                            <label class="form-input--question" for="">End Date</label>
                                            <div class="date--picker row">
                                                <div class="col-12">
                                                    <input type="text" id="end_date" name="end_date" placeholder="Date('MM/DD/YYYY')"  readonly value="{{!empty($end_date) ? $end_date : ''}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="invoice-list--btns col-xl-4 col-lg-6 col-md-6 col-sm-12 order-xl-6 order-lg-2 order-md-2 order-sm-1 order-1" style="text-align: right;">
                                        <button type="button" name="search_invoice" class="btn sumb--btn " value="Search" onclick="searchItems(null, null, '{{$filterBy}}')"><i class="fa-solid fa-magnifying-glass"></i>Search</button>
                                        <button type="button" class="btn sumb--btn sumb-clear-btn" onclick="clearSearchItems()"><i class="fa-solid fa-circle-xmark"></i>Clear Search</button>
                                    </div>
                                </div>
                            </form>

                            <div class="sumb--recentlogdements sumb--putShadowbox">
                                <div class="table-responsive">
                                    <table class="invoice_list">
                                        <thead>
                                            <tr>
                                                <th style="border-top-left-radius: 7px;" id="invoice_issue_date" > Invoice Number </th>
                                                <th id="invoice_number" >User</th>
                                                <th id="client_name" >Action</th>
                                                <th id="client_email" >Date</th>
                                                <th id="invoice_status" >Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (empty($invoice_history['total']))
                                            <tr>
                                                <td colspan="8" style="padding: 30px 15px; text-align:center;">No Data At This time.</td>
                                            </tr>
                                            @else
                                                @foreach ($invoice_history['data'] as $history)
                                            <tr>
                                                <td>{{ 'INV-'. str_pad($history['invoice_number'], 6, '0', STR_PAD_LEFT) }}</td>
                                                <td>{{ $history['user_name'] }}</td>
                                                <td>{{ $history['action']}}</td>
                                                <td>{{ $history['date'] }}</td>
                                                <td>{{ $history['time'] }}</td>
                                            </tr>
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
                                                        Display: {{$invoice_history['per_page']}} Items
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
                        </div>
                    </div>
                </section>

                <section>
                    &nbsp;
                </section>
                
                
            </div>
        </div>
    </div>
</div>

<!-- END PAGE CONTAINER-->


@include('includes.footer')
</body>

</html>

<script>
    function deleteInvoice(transaction_number, id){        
        $("#delete_invoice_number").text('');
        $("#delete_invoice_number").text('INV-000000'+transaction_number);
        $("#delete_invoice").val('');
        $("#delete_invoice").val(id);
        
        $('#delete_invoice_modal').modal({
            backdrop: 'static',
            keyboard: true, 
            show: true
        });
    }

    $(document).on('click', '#delete_invoice', function(event) {
        var invoice_id = $("#delete_invoice").val();
        
        var url = "{{URL::to('/invoice/{id}/delete')}}";
        url = url.replace('{id}', invoice_id);
        location.href = url;
    });

    $(function() {
        $( "#start_date" ).datepicker();
        $( "#end_date" ).datepicker();
        
    });

    <?php if(!empty($orderBy)){?>
        <?php if($direction == 'ASC'){?> 
            $("#"+ '{{$orderBy}}').append('&nbsp;<i class="fas fa-sort-down"></i>');    
        <?php } if($direction == 'DESC'){?>
            $("#"+ '{{$orderBy}}').append('&nbsp;<i class="fas fa-sort-up"></i>');    
        <?php }?> 
    <?php }?>

    function clearSearchItems(){
        $("#search_number_email_amount").val('');
        $("#start_date").val('');
        $("#end_date").val('');
        return false;
    }

    function searchItems(orderBy, direction, filterBy){
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
        $("#search_form").submit();
    }
</script>
<!-- end document-->