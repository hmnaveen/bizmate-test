@include('includes.head')


<div class="container">
    <div class="login-wrapper col-xl-8 col-lg-11 col-xs-12 sumb--putShadowbox">
        <div class="login-content row">

            <div class="login-content-left col-xl-4 col-lg-4 col-md-12">
                <img src="img/sumb_logo.png" class="login--logo">

                <img src="img/bizmate-horizontal-forapp.png" class="bizmate--logo">

                <div class="register-link">
                    Do you think you have an account? <a href="/">Login here</a>
                </div>
            </div>
                        
            <div class="login-content-right col-xl-8 col-lg-8 col-md-12">
                        @isset($err) 
                        <div class="sumb-alert alert alert-{{ $errors[$err][1] }}" role="alert">
                            {{ $errors[$err][0] }}
                        </div>
                        @endisset
                        <div class="login-form" style="padding-top: 10px;">
                            <form action="\register" method="post">
                                @csrf
                                <div class="form-check form-switch" style="display:none;">
                                    <input class="form-check-input" name="accountant" type="checkbox" role="switch" id="flexSwitchCheckDefault" @isset($form_accountant) checked @endisset>
                                    <label class="form-check-label" for="flexSwitchCheckDefault"><b>Are you an Accountant?</b></label><br><br>
                                  </div>
                                <div class="form-group">
                                    <label>User Email Address</label>
                                    <input class="au-input au-input--full" type="email" name="email" placeholder="Email" required value="@isset($form_email) {{$form_email}} @endisset">
                                </div>
                                <div class="form-group">
                                    <label>Full Name</label>
                                    <input class="au-input au-input--full" type="fullname" name="fullname" placeholder="Full Name" required value="@isset($form_fullname) {{$form_fullname}} @endisset">
                                </div>
                                <div class="form-group" id="togglePassword--wrap">
                                    <label>Password</label>
                                    <input id="togglePassword--input_signup1" class="au-input au-input--full" type="password" name="password1" placeholder="Password" required>

                                    <span class="toggle-password--signup1 fa-solid fa-eye"></span>
                                </div>
                                <div class="form-group" id="togglePassword--wrap">
                                    <label>Retry Password</label>
                                    <input id="togglePassword--input_signup2" class="au-input au-input--full" type="password" name="password2" placeholder="Retry Password" required>
                                    
                                    <span class="toggle-password--signup2 fa-solid fa-eye"></span>
                                </div>
                                <button class="au-btn au-btn--block sumb-btn--yellow m-t-30 m-b-20" type="submit">Register</button>
                            
                            </form>
                    </div>
                </div>
            </div>
                        
            

        </div>
    </div>

    <div class="footer--login">
        <div class="copyright--login"><span>© <?php echo date("Y"); ?> [B]izmate </span> <a href="https://set-up-my-business.com.au/wp-content/uploads/2023/07/SUMB-Terms-Conditions_SUMB_July2023.pdf" target="_blank">Terms & Conditions</a> | <a href="https://set-up-my-business.com.au/wp-content/uploads/2022/08/Privacy-Policy_SUMB_January2022.pdf" target="_blank">Privacy Policy</a> | <a href="https://set-up-my-business.com.au/contact-us/" target="_blank">Contact Us</a></div>
        <div class="socials--login">
            <a href="https://www.facebook.com/setupmybusinessau" target="_blank" class="facebook" title="Facebook">&nbsp;</a>
            <a href="https://www.instagram.com/setupmybusinessau/" target="_blank" class="instagram" title="Instagram">&nbsp;</a>
            <a href="https://www.linkedin.com/company/setupmybusinessau/" target="_blank" class="linkedin" title="LinkedIn">&nbsp;</a>
            <a href="https://www.youtube.com/channel/UCPvJEPFa1XN0MgSlU1vz6Ww" target="_blank" class="youtube" title="Youtube">&nbsp;</a>
        </div>
    </div>
</div>


<script>

    //Toogle Password
    $("body").on('click', '.toggle-password--signup1', function() {
        $(this).toggleClass("fa-eye fa-eye-slash");
        var input = $("#togglePassword--input_signup1");
        if (input.attr("type") === "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });

    $("body").on('click', '.toggle-password--signup2', function() {
        $(this).toggleClass("fa-eye fa-eye-slash");
        var input = $("#togglePassword--input_signup2");
        if (input.attr("type") === "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });
    
</script>

@include('includes.footer')

</body>

</html>
<!-- end document-->

@include('includes.footer')

</body>

</html>
<!-- end document-->