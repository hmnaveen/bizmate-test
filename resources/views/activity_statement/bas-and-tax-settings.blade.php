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
                    <div class="bas-tax--settings_center row d-flex align-items-center justify-content-center">

                        <div class="col-xl-5 col-lg-6 col-md-7 col-sm-9">
                            <h3 class="sumb--title m-b-15">Activity Statements Settings</h3>

                            <div class="bas-tax--settings_wrap sumb--putShadowbox">

                                <!-- Show this on first time setting up -->

                                <div class="bastax--initialsettings">
                                    <i class="fa-solid fa-circle-exclamation"></i>This will be the settings covered in the Activity Statement you are creating. Please select necessary coverage of the data you will be preparing the activity statement for.
                                </div>

                                <form action="/bas/settings" method="post" enctype="multipart/form-data" class="form-horizontal" id="">
                                @csrf

                                    <div class="bas-tax--settings gst-calculation--period">
                                        <label>GST Calculation Period</label>

                                        <div class="button--sets">
                                            <div role="presentation" class="tab1  {{ !empty($bas_and_tax_settings) && $bas_and_tax_settings['gst_calculation_period'] == 'monthly' ? 'active' : '' }}">
                                                <a id="tab-01" aria-controls="tab-01" role="tab" data-toggle="tab">Monthly</a>
                                                <input hidden type="radio" id="gst_monthly" name="gst_calculation_period" value="monthly" {{!empty($bas_and_tax_settings) && $bas_and_tax_settings['gst_calculation_period'] == "monthly" ? "checked" : ""}}>
                                            </div>
                                            <div role="presentation" class="tab1 {{ !empty($bas_and_tax_settings) && $bas_and_tax_settings['gst_calculation_period'] == 'quarterly' ? 'active' : '' }}">
                                                <a id="tab-02" aria-controls="tab-02" role="tab" data-toggle="tab">Quarterly</a>
                                                <input hidden type="radio" id="gst_quarterly" name="gst_calculation_period" value="quarterly" {{!empty($bas_and_tax_settings) && $bas_and_tax_settings['gst_calculation_period'] == "quarterly" ? "checked" : ""}}>
                                            </div>
                                            <div role="presentation" class="tab1 {{ !empty($bas_and_tax_settings) && $bas_and_tax_settings['gst_calculation_period'] == 'annually' ? 'active' : '' }}">
                                                <a  id="tab-03" aria-controls="tab-03" role="tab" data-toggle="tab">Annually</a>
                                                <input hidden type="radio" id="gst_annually" name="gst_calculation_period" value="annually" {{!empty($bas_and_tax_settings) && $bas_and_tax_settings['gst_calculation_period'] == "annually" ? "checked" : ""}}>
                                            </div>
                                        </div>

                                        @error('gst_calculation_period')
                                            <div class="alert alert-danger m-t-20 m-b-0">{{ $message }}</div>
                                        @enderror

                                    </div>

                                    <div class="bas-tax--settings gst-accounting--method">
                                        <label>GST Accounting Method</label>

                                        <div class="button--sets">
                                            <div role="presentation" class="tab2 {{ !empty($bas_and_tax_settings) && $bas_and_tax_settings['gst_accounting_method'] == 'cash' ? 'active' : '' }}">
                                                <a id="tab-01" aria-controls="tab-01" role="tab" data-toggle="tab" >Cash</a>
                                                <input hidden type="radio" id="gst_cash" name="gst_accounting_method" value="cash" {{!empty($bas_and_tax_settings) && $bas_and_tax_settings['gst_accounting_method'] == "cash" ? "checked" : ""}}>
                                            </div>
                                            <div role="presentation" class="tab2 {{ !empty($bas_and_tax_settings) && $bas_and_tax_settings['gst_accounting_method'] == 'accrual' ? 'active' : '' }}">
                                                <a id="tab-02" aria-controls="tab-02" role="tab" data-toggle="tab" >Accrual</a>
                                                <input hidden type="radio" id="radio2" name="gst_accounting_method" value="accrual" {{!empty($bas_and_tax_settings) && $bas_and_tax_settings['gst_accounting_method'] == "accrual" ? "checked" : ""}}>
                                            </div>
                                        </div>

                                        @error('gst_accounting_method')
                                            <div class="alert alert-danger m-t-20 m-b-0">{{ $message }}</div>
                                        @enderror

                                    </div>

                                    <div class="bas-tax--settings payg-withholding--period">
                                        <label>PAYG Withholding Period</label>

                                        <div class="button--sets">
                                            <div role="presentation" class="tab3 {{ !empty($bas_and_tax_settings) && $bas_and_tax_settings['payg_withhold_period'] == 'none' ? 'active' : '' }}">
                                                <a  id="tab-03" aria-controls="tab-03" role="tab" data-toggle="tab" >None</a>
                                                <input hidden type="radio" id="payg_withhold_radio1" name="payg_withhold_period" value="none" {{!empty($bas_and_tax_settings) && $bas_and_tax_settings['payg_withhold_period'] == "none" ? "checked" : ""}}>
                                            </div>
                                            <div role="presentation" class="tab3 {{ !empty($bas_and_tax_settings) && $bas_and_tax_settings['payg_withhold_period'] == 'monthly' ? 'active' : '' }}">
                                                <a id="tab-01" aria-controls="tab-01" role="tab" data-toggle="tab" >Monthly</a>
                                                <input hidden type="radio" id="payg_withhold_radio2" name="payg_withhold_period" value="monthly" {{!empty($bas_and_tax_settings) && $bas_and_tax_settings['payg_withhold_period'] == "monthly" ? "checked" : ""}}>
                                            </div>
                                            <div role="presentation" class="tab3 {{ !empty($bas_and_tax_settings) && $bas_and_tax_settings['payg_withhold_period'] == 'quarterly' ? 'active' : '' }}">
                                                <a id="tab-02" aria-controls="tab-02" role="tab" data-toggle="tab" >Quarterly</a>
                                                <input hidden type="radio" id="payg_withhold_radio3" name="payg_withhold_period" value="quarterly" {{!empty($bas_and_tax_settings) && $bas_and_tax_settings['payg_withhold_period'] == "quarterly" ? "checked" : ""}}>
                                            </div>
                                        </div>

                                        @error('payg_withhold_period')
                                            <div class="alert alert-danger m-t-20 m-b-0">{{ $message }}</div>
                                        @enderror

                                    </div>

                                    <div class="bas-tax--settings payg-income-tax--method">
                                        <label>PAYG Income Tax Method</label>

                                        <div class="button--sets">
                                            <div role="presentation" class="tab4 {{ !empty($bas_and_tax_settings) && $bas_and_tax_settings['payg_income_tax_method'] == 'none' ? 'active' : '' }}">
                                                <a  id="tab-03" aria-controls="tab-03" role="tab" data-toggle="tab" >None<span>Option 1</span></a>
                                                <input hidden  type="radio" id="payg_income_none" name="payg_income_tax_method" value="none" {{!empty($bas_and_tax_settings) && $bas_and_tax_settings['payg_income_tax_method'] == "none" ? "checked" : ""}}>
                                            </div>
                                            <div role="presentation" class="tab4 {{ !empty($bas_and_tax_settings) && $bas_and_tax_settings['payg_income_tax_method'] == 'incometaxamount' ? 'active' : '' }}">
                                                <a id="tab-01" aria-controls="tab-01" role="tab" data-toggle="tab" >Amount<span>Option 2</span></a>
                                                <input hidden  type="radio" id="payg_income_amount" name="payg_income_tax_method" value="incometaxamount" {{ !empty($bas_and_tax_settings) && $bas_and_tax_settings['payg_income_tax_method'] == "incometaxamount" ? "checked" : "" }}>
                                            </div>
                                            <div role="presentation" class="tab4 {{!empty($bas_and_tax_settings) && $bas_and_tax_settings['payg_income_tax_method'] == 'incometaxrate' ? 'active' : '' }}">
                                                <a id="tab-02" aria-controls="tab-02" role="tab" data-toggle="tab" >Income x Rate<span>Option 3</span></a>
                                                <input hidden type="radio" id="payg_income_rate" name="payg_income_tax_method" value="incometaxrate" {{ !empty($bas_and_tax_settings) && $bas_and_tax_settings['payg_income_tax_method'] == "incometaxrate" ? "checked" : "" }}>
                                            </div>
                                        </div>

                                        @error('payg_income_tax_method')
                                            <div class="alert alert-danger m-t-20 m-b-0">{{ $message }}</div>
                                        @enderror

                                    </div>
                                    
                                    <div class="form-navigation">
                                        <div class="form-navigation--btns row">
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12 col-12">
                                                <a href="/bas/overview" class="btn sumb--btn"><i class="fa-solid fa-circle-left"></i> Back</a>
                                            </div>
                                            <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 col-xs-12 col-12">
                                                <button type="button" id="bas_and_tax_settings"  class="btn sumb--btn" value="Save Settings"><i class="fa-solid fa-floppy-disk"></i> Save</button>
                                                <button type="submit" hidden id="submit_bas_and_tax" name="save_bas_and_tax_settings"  class="btn sumb--btn" value="Save Settings"><i class="fa-solid fa-floppy-disk"></i> Save</button>

                                            </div>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>


                </section>
                
            </div>
        </div>
    </div>
</div>


<!-- Changing settings confirmation pop-up -->
<div id="change_settings_modal" class="modal fade modal-reskin modal-deleteItem" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Changing settings affects draft statements</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
        </button>
      </div>
      <div class="modal-body">
        Draft statements will be deleted. Finalised statements will not be affected.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary close--btn" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary save--btn" id="change_settings" value="">Proceed</button>
      </div>
    </div>
  </div>
</div>
<!-- Changing settings confirmation pop-up ends-->



<!-- END PAGE CONTAINER-->

@include('includes.footer')
</body>

</html>


<script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>


<script>

$(function() {
    let $body = $(this);
   
    $("#change_settings").on('click', function(){
        $("#submit_bas_and_tax").click();
    });
});


$(document).ready(function () {
    $(".tab1").click(function () {
        
        $(".tab1").removeClass("active");
        $(this).addClass("active");

        $(this).find("[name=gst_calculation_period]").prop("checked", false);

        const gst_calculation = $(this).find("[name=gst_calculation_period]").attr('id');
        $("#"+gst_calculation).prop("checked", true);
    });


    $(".tab2").click(function () {

        $(".tab2").removeClass("active");
        $(this).addClass("active");

        $(this).find("[name=gst_accounting_method]").prop("checked", false);

        const gst_accounting = $(this).find("[name=gst_accounting_method]").attr('id');
        $("#"+gst_accounting).prop("checked", true);
    });


    $(".tab3").click(function () {
        
        $(".tab3").removeClass("active");
        $(this).addClass("active");
    
        $(this).find("[name=payg_withhold_period]").prop("checked", false);

        const payg_withhold_period = $(this).find("[name=payg_withhold_period]").attr('id');

        $("#"+payg_withhold_period).prop("checked", true);
    });


    $(".tab4").click(function () {
        
        $(".tab4").removeClass("active");
        $(this).addClass("active");

        $(this).find("[name=payg_income_tax_method]").prop("checked", false);

        const payg_income_tax = $(this).find("[name=payg_income_tax_method]").attr('id');
        $("#"+payg_income_tax).prop("checked", true);
    });
});

$("#bas_and_tax_settings").click(function () {

    let data = {
        gst_calculation_period : $("input[name='gst_calculation_period']:checked").val(),
        gst_accounting_method : $("input[name='gst_accounting_method']:checked").val(),
        payg_withhold_period : $("input[name='payg_withhold_period']:checked").val(),
        payg_income_tax_method : $("input[name='payg_income_tax_method']:checked").val()
    }

    $body.find('#pre-loader').show();

    $.ajaxSetup({headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }});
    $.ajax({
        method: 'post',
        url: "/bas/settings/verify",
        data: data,
        success: function(data){
            $body.find('#pre-loader').hide();
            $("#submit_bas_and_tax").click();
        },
        error: function(e){
            $body.find('#pre-loader').hide();
            
            $('#change_settings_modal').modal({
                backdrop: 'static',
                keyboard: true, 
                show: true
            });
        }
    });
});

</script>
<!-- end document-->