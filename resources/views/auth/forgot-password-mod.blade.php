<?php $pagetitle = 'Reset My Password' ?>

@include('includes.head')

<div class="d-flex justify-content-center align-items-center" style="height: 100vh;">
    <div class="col-xl-5 col-lg-5 col-md-6 col-sm-9 col-11">

        <div class="reset--password-container">
            <h4>Reset My Password</h4>
            @csrf
            <div id='modal-alert' class="sumb-alert alert" role="alert" style="display: none;">
                <span class='sub-err-msg'></span>
                <a id='redirect-link' href='#' style="display: none;">Redirect link</a>
            </div>
            <div class="form-group" id="togglePassword--wrap">
                <label class="sumb-text--black">New Password</label>
                <input id='pass' class="au-input au-input--full" type="password" name="password1" placeholder="New Password" required>

                <span class="toggle-password--reset1 fa-solid fa-eye"></span>
            </div>
            <div class="form-group" id="togglePassword--wrap">
                <label class="sumb-text--black">Retry Password</label>
                <input id='re-pass' class="au-input au-input--full" type="password" name="password2" placeholder="Retry Password" required>

                <span class="toggle-password--reset2 fa-solid fa-eye"></span>
            </div>
            <button id='submit-new-pass' class="au-btn au-btn--block sumb-btn--yellow m-t-30" type="submit" data-attr-email = "{{$email}}" data-attr="{{$enc_id}}">update password</button>               
        </div>
    </div>
</div>

<script>

    //Toogle Password
    $("body").on('click', '.toggle-password--reset1', function() {
        $(this).toggleClass("fa-eye fa-eye-slash");
        var input = $("#pass");
        if (input.attr("type") === "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });

    $("body").on('click', '.toggle-password--reset2', function() {
        $(this).toggleClass("fa-eye fa-eye-slash");
        var input = $("#re-pass");
        if (input.attr("type") === "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });
    
</script>


@include('includes.footer')

