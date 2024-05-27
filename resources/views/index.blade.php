@include('includes.head')


<div class="container">
    <div class="login-wrapper col-xl-8 col-lg-11 col-xs-12 sumb--putShadowbox">
        <div class="login-content row">

            <div class="login-content-left col-xl-4 col-lg-4 col-md-12">
                <img src="img/sumb_logo.png" class="login--logo">

                <img src="img/bizmate-horizontal-forapp.png" class="bizmate--logo">

                <div class="register-link">
                    Don't have an account yet? <a href="/signup">Sign up here</a>
                </div>
            </div>
                        
            <div class="login-content-right col-xl-8 col-lg-8 col-md-12">

                <nav style='display:none;'>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <a class="nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">User Login</a>
                        <a class="nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Accountant Login</a>
                    </div>

                                
                </nav>
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                        @isset($err) 
                        <div class="sumb-alert alert alert-{{ $errors[$err][1] }}" role="alert">
                            {{ $errors[$err][0] }}
                        </div>
                        @endisset
                        <div class="login-form">
                            <form action="\login" method="post">
                                @csrf
                                <div class="form-group">
                                    <label class="sumb-text--black">User Email Address</label>
                                    <input class="au-input au-input--full" type="email" name="email" placeholder="Email" @if (!empty($request['email'])) value="{{ $request['email'] }}" @endif>
                                </div>
                                <div class="form-group" id="togglePassword--wrap">
                                    <label class="sumb-text--black">Password</label>
                                    <input id="togglePassword--input_login" class="au-input au-input--full" type="password" name="password" placeholder="Password">

                                    <span class="toggle-password--login fa-solid fa-eye"></span>
                                </div>
                                <div class="login-checkbox m-b-10">
                                    <label class="sumb-text--black">
                                        <input type="checkbox" name="remember"><span>Remember Me</span>
                                    </label>
                                    <label>
                                        <a style="cursor: pointer" onclick="forgotPasswordModal()">Forgotten Password?</a>
                                    </label>
                                </div>
                                <button class="au-btn au-btn--block sumb-btn--yellow m-b-30" type="submit">sign in</button>

                                <div class="social-login-content">
                                    <span class="sumb-text--black">OR</span>
                                </div>

                                <div class="social-button">
                                    <div class="row">
                                        <div class="col-12">
                                            <div id="g_id_onload"
                                                data-client_id="{{env('GOOGLE_CLIENT_ID')}}"
                                                data-context="signin"
                                                data-ux_mode="popup"
                                                data-callback="googleLogin"
                                                data-nonce=""
                                                data-auto_prompt="false">
                                            </div>
                                            <div class="g_id_signin w-full"
                                            data-type="standard"
                                            data-size="large"
                                            data-theme="outline"
                                            data-shape="rectangular"
                                            data-text="continue_with"></div>
                                        </div>
                                        <!--
                                        <div class="col-12">
                                            <a class="facebook">Continue with Facebook</a>
                                        </div>
                                        -->
                                    </div>
                                </div>
                            </form>
                                        
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                        <div class="login-form">
                            <form action="\login" method="post">
                                @csrf
                                <div class="form-group">
                                    <label class="sumb-text--black">Accountant Email Address</label>
                                    <input class="au-input au-input--full" type="email" name="email" placeholder="Email" @if (!empty($request['email'])) value="{{ $request['email'] }}" @endif>
                                </div>
                                <div class="form-group">
                                    <label class="sumb-text--black">Password</label>
                                    <input class="au-input au-input--full" type="password" name="password" placeholder="Password">
                                </div>
                                <div class="login-checkbox m-b-10">
                                    <label class="sumb-text--black">
                                        <input type="checkbox" name="remember"><span>Remember Me</span>
                                    </label>
                                    <label>
                                        <a style="cursor: pointer" onclick="forgotPasswordModal()">Forgotten Password?</a>
                                    </label>
                                </div>
                                <button class="au-btn au-btn--block sumb-btn--yellow m-b-30" type="submit">sign in</button>
                                <div class="social-login-content">
                                    <span class="sumb-text--black">OR</span>

                                    <label class="sumb-text--black">Continue with social media</label>

                                    <ul class="social-button">
                                        <li><a href="#" class="facebook" title="Facebook">&nbsp;</a></li>
                                        <li><a href="#" class="twitter" title="Twitter">&nbsp;</a></li>
                                        <li><a href="#" class="linkedIn" title="LinkedIn">&nbsp;</a></li>
                                        <li><a href="#" class="google" title="Google">&nbsp;</a></li>
                                    </ul>
                                </div>
                            </form>
                                        
                        </div>
                    </div>


                </div>
            </div>
            <!--Show Only on Mobiles/Tablets-->
            <div class="login-content-left show-mobile--tablet col-md-12">
                <div class="register-link">
                    Don't have an account yet? <a href="/signup">Sign up here</a>
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
<!-- forgot password modal -->
<!-- <form action="/verify-email" method="post" enctype="multipart/form-data"> -->
    @csrf
    <div id="forgot-password-modal" class="modal fade modal-reskin modal-deleteItem" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="">Change Password</h5>
                </div>
                <div class='loader-cont' style="padding-top:5px;"><span class="loader" style="display:block;"></span></div>
                <div class="modal-body">
                    <div id='modal-alert' class="sumb-alert alert" role="alert" style="display: none;">

                        <span class='verify-msg'></span>
                    </div>
                    <input id='reset-email' class="au-input au-input--full" type="email" name="email" placeholder="Email">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close--btn" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="submit-email-reset">Reset</button>
                </div>
            </div>
        </div>

        
    </div>
<!-- </form> -->




<script>

    //Toogle Password User
    $("body").on('click', '.toggle-password--login', function() {
        $(this).toggleClass("fa-eye fa-eye-slash");
        var input = $("#togglePassword--input_login");
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