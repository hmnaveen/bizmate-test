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
                    <h3 class="sumb--title m-b-20">Add Bank Accounts</h3>
                </section>
                <section>
                    <input type="hidden" id="clientToken" value="{{ !empty($consentUrl) ? $consentUrl : '' }}">
                    <div class="row">
                        <div class="col-xl-6 col-lg-7 col-md-8">
                            <iframe src="{{$consentUrl}}" id="addBankFrame"></iframe>
                        </div>
                    </div>
                    <a href="/bank/accounts" class="btn sumb--btn m-b-70"><i class="fa-solid fa-circle-left"></i> Back</a>
                </section>
            </div>
        </div>
    </div>
</div>

@include('includes.footer')

<script>
     
    //alert($('#clientToken').val());
    //window.location = $('#clientToken').val();
</script>