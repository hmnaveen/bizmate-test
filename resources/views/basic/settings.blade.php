@include('includes.head')
@include('includes.user-header')

<!----------------- Redirect Pop-up Start-------------------->
<div id="invoice_settings_modal" class="modal fade modal-reskin modal-deleteItem" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" >
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title updateSettings--header" id="exampleModalLabel">Invoice Settings</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
        </button>
      </div>
      <div class="modal-body">
        Invoice settings have been updated. You will be redirected to the dashboard in <span id="timer" style="font-weight:bold"></span>
      </div>
    </div>
  </div>
</div>
<!----------------- Redirect Pop-up Ends-------------------->

<div id="myModal" class="modal fade modal-reskin" role="dialog" tabindex="-1" aria-hidden="true"  aria-labelledby="myModal">
    <div class="modal-dialog modal-lg" role="document">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title invoiceprev--header">Preview Template</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
                </button>
            </div>
            <div class="modal-body" id="invoice_pdf"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close--btn" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="page-container">

    @include('includes.user-top')
    
    <!-- MAIN CONTENT-->
    <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid">
                <section>

                    @if($first_login == 1)
                        <div class="welcome--message m-b-30">
                            <h3>Welcome to SUMB [B]izMate Lite! Your partner in managing your business finances! </h3>
                            Keep track of your expenses and create and send client invoices with [B]izmate Lite! We're excited to work with you!
                        </div>
                    @endif

                    <h3 class="sumb--title">Invoice Settings</h3>

                    
                </section>

                <section>
                    
                    <form action="/basic/invoice/settings/{{$type}}" method="post" enctype="multipart/form-data" class="form-horizontal" id="invoice_form">
                        @csrf

                        <hr class="form-cutter">

                        @if(!empty($error_message)) 
                            <div class="m-t-15  m-b-20 alert alert-danger">{{ $error_message }}</div> 
                        @endif 

                        <h4 class="form-header--title">Business Details</h4>

                        <div class="row">
                            <div class="col-xl-6">

                                <div class="form-input--wrap">
                                    <label class="form-input--question" for="">Business ABN </label>
                                    <div class="form--inputbox row">
                                        <div class="col-12 {{ $first_login == 1 && $errors->isEmpty() ? 'required--first' : ''}}"> 
                                            <input type="text" class="form-control @error('business_abn') is-invalid @enderror" id="business_abn" name="business_abn" placeholder=""  value="{{!empty($invoice_settings) ? $invoice_settings['business_abn'] : old('business_abn') }}">
                                        </div>
                                    </div>
                                    @error('business_abn')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-input--wrap">
                                    <label class="form-input--question" for="">Business Name </label>
                                    <div class="form--inputbox row">
                                        <div class="col-12 {{ $first_login == 1 && $errors->isEmpty() ? 'required--first' : ''}}"> 
                                            <input type="text" class="form-control @error('business_name') is-invalid @enderror" id="business_name" name="business_name" placeholder=""  value="{{!empty($invoice_settings) ? $invoice_settings['business_name'] : old('business_name')}}">
                                        </div>
                                    </div>
                                    @error('business_name')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-input--wrap">
                                    <label class="form-input--question" for="">Business Email </label>
                                    <div class="form--inputbox row">
                                        <div class="col-12 {{ $first_login == 1 && $errors->isEmpty() ? 'required--first' : ''}}">
                                            <input type="text"  class="form-control @error('business_email') is-invalid @enderror" id="business_email" name="business_email" placeholder=""  value="{{!empty($invoice_settings) ? $invoice_settings['business_email'] : old('business_email') }}">
                                        </div>
                                    </div>
                                    @error('business_email')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-input--wrap">
                                    <label class="form-input--question" for="">Business Phone Number </label>
                                    <div class="form--inputbox row">
                                        <div class="col-12 {{ $first_login == 1 && $errors->isEmpty() ? 'required--first' : ''}}">
                                            <input type="text" class="form-control @error('business_phone') is-invalid @enderror" id="business_phone" name="business_phone" placeholder=""  value="{{!empty($invoice_settings) ? $invoice_settings['business_phone'] : old('business_phone')}}">
                                        </div>
                                    </div>
                                    @error('business_phone')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-input--wrap">
                                    <label class="form-input--question" for="">Business Address </label>
                                    <div class="form--inputbox row">
                                        <div class="col-12 {{ $first_login == 1 && $errors->isEmpty() ? 'required--first' : ''}}">
                                            <input type="text" class="form-control @error('business_address') is-invalid @enderror" id="business_address" name="business_address" placeholder=""  value="{{!empty($invoice_settings) ? $invoice_settings['business_address'] : old('business_address')}}">
                                        </div>
                                    </div>
                                    @error('business_address')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-input--wrap">
                                    <label class="form-input--question" for="" >Terms & Payment Advice (Invoice and Statement)</label>
                                    <textarea class="form-control invoice-settings--textarea @error('business_terms_conditions') is-invalid @enderror" id="business_terms_conditions" name="business_terms_conditions"  placeholder="Please make all payments to: &#10;Account Name: XXX &#10;BSB: XXX-XXX &#10;Acct Number: XXXXXXX &#10;Reference: Please use your invoice number as payment reference.">{{!empty($invoice_settings) ? $invoice_settings['business_terms_conditions'] : old('business_terms_conditions')}}</textarea>
                                </div>
                                @error('business_terms_conditions')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror


                            </div>
                            <div class="col-xl-6 ">

                                <div class="form-input--wrap">
                                    <label class="form-input--question" >Company Logo</label>
                                </div>

                                @if (empty($invoice_settings['business_logo']))
                                    <div class="sumb-invoicesettings-preview-container d-flex align-items-center justify-content-center">
                                        <img id="preview-image-before-upload" src="{{env('APP_PUBLIC_DIRECTORY').'no-inage-found.png'}}" alt="preview image" style="max-height: 250px;">
                                    </div>
                                @else
                                    <div class="sumb-invoicesettings-preview-container d-flex align-items-center justify-content-center">
                                        <img id="preview-image-before-upload" src="{{ !empty($invoice_settings && $invoice_settings['business_logo']) ? '/uploads/'.$userinfo[0].'/'.$invoice_settings['business_logo'] : '' }}" alt="preview image" style="max-height: 250px;">
                                        <!-- env('APP_PUBLIC_DIRECTORY') -->
                                    </div>
                                @endif

                                <div class="sumb-invoicesettings-upload-container">
                                    <div id="sumb-file-upload-container">
                                        
                                        <div class="sumb-invoicesettings-dropzone">
                                            <i class="fa-solid fa-upload"></i>
                                            <p>Upload your company logo</p>
                                                <p class="muted">
                                                    Drag & drop here or select your file manually<br>
                                                    Recommended logo dimensions (500px by 500px)<br>
                                                    Accepted file types (.png, .jpg, .jpeg)
                                                </p>  
                                        </div>

                                        <input id="logo_file" name="logo_file" type="file" accept="image/jpg,image/jpeg,image/png" class="form-control-file sumb-expense-dropzone-input" value="">
                                        <input type="hidden" name="logo_path" id="logo_path" value="{{!empty($invoice_settings) ? $invoice_settings['business_logo'] : '' }}">

                                    </div>

                                </div>
                            </div>
                        </div>

                        
                        <hr class="form-cutter">

                        <h4 class="form-header--title">Invoice Templates</h4>
                            
                            <div class="row m-t-20">

                                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-xs-12 col-12">
                                    <div class="invset--tpt_wrap">
                                        <input type="radio" id="radio1" name="business_invoice_format" value="format001" {{!empty($invoice_settings) || (empty($invoice_settings['business_invoice_format']) || $invoice_settings['business_invoice_format'] == 'format001') ? 'checked' : ''}}>
                                        <label for="radio1">
                                            <img src="/images/templates/format001.jpg" alt="Template 01" >
                                            <a onclick="openPopUpModel('format001.pdf')" data-toggle="modal" data-target="#myModal">Preview Template</a>
                                            <span></span>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-xs-12 col-12">
                                    <div class="invset--tpt_wrap">
                                        <input type="radio" id="radio2" name="business_invoice_format" value="format002" {{!empty($invoice_settings) && $invoice_settings['business_invoice_format'] == 'format002' ? 'checked' : 'checked'}}> 
                                        <label for="radio2">
                                            <img src="/images/templates/format002.jpg" alt="Template 02" >
                                            <a onclick="openPopUpModel('format002.pdf')" data-toggle="modal" data-target="#myModal">Preview Template</a>
                                            <span></span>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-xs-12 col-12">
                                    <div class="invset--tpt_wrap">
                                        <input type="radio" id="radio3" name="business_invoice_format" value="format003" {{!empty($invoice_settings) && $invoice_settings['business_invoice_format'] == 'format003' ? 'checked' : ''}}>
                                        <label for="radio3">
                                            <img src="/images/templates/format003.jpg" alt="Template 03">
                                            <a onclick="openPopUpModel('format003.pdf')" data-toggle="modal" data-target="#myModal">Preview Template</a>
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-xs-12 col-12">
                                    <div class="invset--tpt_wrap">
                                        <input type="radio" id="radio4" name="business_invoice_format" value="format004" {{!empty($invoice_settings) && $invoice_settings['business_invoice_format'] == 'format004' ? 'checked' : ''}}>
                                        <label for="radio4">
                                            <img src="/images/templates/format004.jpg" alt="Template 04">
                                            <a onclick="openPopUpModel('format004.pdf')" data-toggle="modal" data-target="#myModal">Preview Template</a>
                                            <span></span>
                                        </label>
                                    </div>
                                </div>


                            </div>
                       
                        <div class="form-navigation">
                            <div class="form-navigation--btns row">
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12 col-12">
                                    <a href="/basic/invoice" class="btn sumb--btn"><i class="fa-solid fa-circle-left"></i> Back</a>
                                </div>
                                <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 col-xs-12 col-12">
                                    <input type="hidden" name="invoice_settings_id" value="{{!empty($invoice_settings) ? $invoice_settings['id'] : ''}}">
                                    <button type="submit" name="save_invoice_settings"  class="btn sumb--btn" value="Save Settings"><i class="fa-solid fa-floppy-disk"></i> Save</button>
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

    <?php 
        if(\Session::has('success') && \Session::get('success'))
        {?>
            $('#invoice_settings_modal').modal({
                backdrop: 'static',
                keyboard: true, 
                show: true
            });
            
            var i = 4;
            (function timer(){
                if (--i < 0) return;
                setTimeout(function(){
                    if(i == 0){
                        var url = "{{URL::to('/dashboard')}}";
                        location.href = url;
                    }else{
                        document.getElementById('timer').innerHTML = i + ' secs';
                        timer();
                    }
                }, 1000);
            })();

        <?php } ?>

    function openPopUpModel(format){
        $("#invoice_pdf").html('');

        $("#invoice_pdf").append('<embed src="/images/templates/'+format+'#toolbar=0&navpanes=0" frameborder="0" width="100%" height="500px">');
        //{{env('APP_PUBLIC_DIRECTORY')}}

        $('#myModal').modal({
            keyboard: true, 
            show: true
        });
    }


    $(document).ready(function (e) {
        $('#logo_file').change(function(){

            var fln = $('#logo_file').val().split('\\').pop().substr(($('#logo_file').val().split('\\').pop().lastIndexOf('.') +1));

            switch(fln) {
                case 'jpg':
                case 'jpeg':
                case 'png':
                    let reader = new FileReader();

                    reader.onload = (e) => { 
                        $('#preview-image-before-upload').attr('src', e.target.result); 
                    }
                    reader.readAsDataURL(this.files[0]); 

                    var fd = new FormData();
                    fd.append( "fileInput", $("#logo_file")[0].files[0]);

                    $.ajaxSetup({
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    });
                    $.ajax({
                        method: "POST",
                        url: "{{ url('/basic/invoice-logo-upload') }}",
                        data: fd,
                        processData: false,
                        contentType: false,
                        success : function(response){
                            $("#logo_path").val(response.logo);
                        },
                        error : function(error){
                            alert(error.responseJSON.message);
                        }
                    });
                    break;
                default:
                    alert('Please upload a .png, .jpg or .jpeg file.');
                    return false;
            }  
            
        });
    });




</script>
</body>

</html>
<!-- end document-->