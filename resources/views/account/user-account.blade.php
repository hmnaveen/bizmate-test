@include('includes.head')
@include('includes.user-header')

<div id="deactivate_account_modal" class="modal fade modal-reskin" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title deactivateicon--header" id="exampleModalLabel">Deactivate Account</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to deactivate your account?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close--btn" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary deactivate--btn" id="deactivate_account"
                    value="">Deactivate Account</button>
            </div>
        </div>
    </div>
</div>


<div class="page-container">
    @include('includes.user-top')
    <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid">
                <section>
                    <h3 class="sumb--title m-b-20">Account Details</h3>
                </section>

                <section>
                    <div class="row accountsettings--fields">
                        @csrf
                        <div class="col-xl-7 col-lg-7 col-md-7">

                            <div class="form-input--wrap">
                                <label class="form-input--question">Full Name</label>
                                <div class="form--inputbox row">
                                    <div class="col-12">
                                        <input id="user-fullname" type="fullname" name="fullname"
                                            placeholder="Full Name" value="{{ $userinfo[1] }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-input--wrap">
                                <label class="form-input--question">User Email Address</label>
                                <div class="form--inputbox row">
                                    <div class="col-12">
                                        <input id="user-email" type="email" name="email" placeholder="Email"
                                            value='{{$userinfo[2]}}'>
                                    </div>
                                </div>
                            </div>


                            <div class="account-user-password-container">

                                <div class="form-input--wrap" id="togglePassword--wrap">
                                    <label class="form-input--question">Password</label>
                                    <div class="form--inputbox row">
                                        <div class="col-12">
                                            <input id="user-pass" type='password' name="password1"
                                                placeholder="Password" value="">
                                            <span class="toggle-password--update1 fa-solid fa-eye"></span>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-input--wrap" id="togglePassword--wrap">
                                    <label class="form-input--question">Retry Password</label>
                                    <div class="form--inputbox row">
                                        <div class="col-12">
                                            <input id="retry-user-pass" type='password' name="password1" value=''
                                                placeholder="Password">
                                            <span class="toggle-password--update2 fa-solid fa-eye"></span>
                                        </div>
                                    </div>
                                </div>



                                <div class="form-group">
                                    <button id="generate-user-pass" type="button"
                                        class="btn sumb-btn--yellow account--generatepass">
                                        Generate Password
                                    </button>

                                </div>


                            </div>

                        </div>
						
                        <div class="col-xl-5 col-lg-5 col-md-5">
                            <div class="account--profileicon">

                                <img src="{{(!isset($userinfo[5]) ? '../img/blankpic.png' : $userinfo[5] == '') ? '../img/blankpic.png' : storage::url($userinfo[5]) }}"
                                    id="user-pic" alt="Profile Image" />

                                <button id='upload-new-btn' type="button" class="btn sumb-btn--yellow">Upload A New
                                    Photo</button>
                            </div>

                            <div class="account--deetswrap">
                                <div class="account--settype">
                                    <label>Account Type</label>
                                    <div>
                                        [B]izMate
                                        <span id="user-account-type">
                                            {{ $userinfo[3] == 'admin' ? 'Administrator' : ($userinfo[3] == 'accountant'
                                            ? 'Accountant' : ($userinfo[3] == 'user' ? 'Lite' : ($userinfo[3] ==
                                            'user_reg' ? 'Bizmate' : ($userinfo[3] == 'user_pro' ? 'Bizmate Pro' : '' )
                                            ) )) }}
                                        </span>
                                    </div>
                                </div>

                                <div class="account--verificationstatus">
                                    <label>Account Verification Status</label>
                                    <div>
                                        <span class="{{$userinfo[4] == 'unverified' ? 'red-txt' : 'green-txt'}}"
                                            id="user-account-verification">{{$userinfo[4]}}</span>
                                    </div>
                                </div>

                                <div class="account--actstatus">
                                    <label>Account Status</label>
                                    <div>
                                        <span class="{{ $userinfo[8]==1 ? 'green-txt' : 'red-txt' }}"
                                            id='user-account-status'>{{$userinfo[8]==1 ? 'Activated' :
                                            'Deactivated'}}</span>
                                    </div>
                                </div>
                                <div class="account--reqdeact">
                                    <a id='deactive-user-account'>Deactivate My Account</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section>
                    <button id="update-user-details" type="button"
                        class="btn sumb-btn--yellow account--updateprofile">Update Profile
					</button>
                </section>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-reskin" id="mdl-upload-photo" role="dialog" style="z-index: 99999;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">

                <h5 class="modal-title uploadphoto--header" id="exampleModalLabel">Upload Photo</h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
                </button>
            </div>

            <div class="modal-body clearfix">
		
                <div class="profilepic--preview m-b-15">
                    <img src="{{(!isset($userinfo[5]) ? '../img/blankpic.png' : $userinfo[5] == '') ? '../img/blankpic.png' : storage::url($userinfo[5]) }}"
                        id="preview_image" alt="Profile Image" />
			
                </div>

                <div class="sumb-invoicesettings-upload-container d-flex align-items-center justify-content-center">
                    <div id="sumb-file-upload-container">

                        <div class="sumb-invoicesettings-dropzone">
                            <i class="fa-solid fa-upload"></i>
                            <p>Upload your photo</p>
                            <p class="muted">Drag & drop here or select your file manually</p>
                        </div>

                        <input type="file" id="user-img-file" oninput='UpdatePhotoPreview()' />

                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button id='set-photo' type="button" class="btn sumb-btn--yellow uploadphoto--btn">Upload Photo</button>
            </div>
        </div>
    </div>
</div>

@include('includes.footer')

<script>
    // :(
	$(function() {
		$(document).on('click', '#deactive-user-account', function(event) {
			$('#deactivate_account_modal').modal({
				backdrop: 'static',
				keyboard: true,
				show: true
			});
		});

		let $body = $(this);
		// initAutoComplete();
		// getUser('{{$userinfo[7]}}');
		///variables
		$pass = $body.find('#user-pass');
		$retryPass = $body.find('#retry-user-pass');
		$accountType = $body.find('#user-account-type');
		$aVerification = $body.find('#user-account-verification');
		// $address = $body.find('#user-address');
		$accountStatus = $body.find('#user-account-status');
		$fullnameUser = $body.find('#user-fullname');
		$userEmail = $body.find('#user-email');
		// $userCity = $body.find('#user-city');
		// $userZip = $body.find('#user-zip');
		// $userSuburb = $body.find('#user-suburb');
		// $userState = $body.find('#user-state');
		// $userPhone = $body.find('#user-phone-number');
		// $countryCode = $body.find('#contry-code');
		$userPic = $body.find('#user-pic');
		$userFile = $body.find('#user-img-file');

		///events

		$body.delegate('#deactivate_account','click',function(){

			let f = new FormData();

			f.append( 'active', 0 );

			deactivateUserAccount(f);

		});


		$body.delegate('#set-photo','click',function(){

			$body.find('#mdl-upload-photo').modal('hide');

			let f = new FormData();

    		if($userFile.prop("files")[0] != undefined){

    			f.append( 'photo', $userFile.prop("files")[0] );

				updateUserProfile(f);
    		}

		});
		$body.delegate('#upload-new-btn','click',function(){

			$body.find('#mdl-upload-photo').modal({backdrop: 'static', keyboard: false});
			$body.find('#mdl-upload-photo').modal('show');

		});

		// $body.delegate('#user-phone-number','keypress',function(){
		// 	evt = (evt) ? evt : window.event;
		//     var charCode = (evt.which) ? evt.which : evt.keyCode;
		//     if (charCode > 31 && (charCode < 48 || charCode > 57)) {
		//         return false;
		//     }
		//     return true;
		// });
		$body.delegate('#generate-user-pass','click',function(){

			let me = $(this);

			$random = randomString(16);

			$pass.val($random);
			$retryPass.val($random);

		});


		//Toogle Password
		$("body").on('click', '.toggle-password--update1', function() {
			$(this).toggleClass("fa-eye fa-eye-slash");
			var input = $("#user-pass");
			if (input.attr("type") === "password") {
				input.attr("type", "text");
			} else {
				input.attr("type", "password");
			}
		});

		$("body").on('click', '.toggle-password--update2', function() {
			$(this).toggleClass("fa-eye fa-eye-slash");
			var input = $("#retry-user-pass");
			if (input.attr("type") === "password") {
				input.attr("type", "text");
			} else {
				input.attr("type", "password");
			}
		});


    	$body.delegate('#update-user-details','click',function(){
    		let f = new FormData();

    		// if($userFile.prop("files")[0] != undefined){

    		// 	f.append( 'photo', $userFile.prop("files")[0] );
			// 	console.log($userFile.prop("files")[0]);
    		// }
    		if( $pass.val() != ''){

    			f.append( 'user[password]', $pass.val());
				f.append( 'user[password_confirmation]', $retryPass.val());

    		}
			f.append( 'user[fullname]', $fullnameUser.val());
			f.append( 'user[email]', $userEmail.val());
			// f.append( 'user[email_verified_at]', $aVerification.val());
			// f.append( 'user_details[address]', $address.val());
			// f.append( 'user_details[state]', $userState.val());
			// f.append( 'user_details[city]', $userCity.val());
			// f.append( 'user_details[suburb]', $userSuburb.val());
			// f.append('user_details[zip]', $userZip.val());
			// f.append('user_details[mobile_number]', $userPhone.val());
			// f.append('user_details[country_code]', $countryCode.val());

    		updateUserDetail(f);
    	});

	});


	function updateUserDetail(data){
		$body.find('#pre-loader').show();
		$.ajaxSetup({headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }});
		$.ajax ({
	        type    : "post",
	        url     : '/'+'{{$userinfo[7]}}'+'/user-details',
	        data  : data,
	        enctype: 'multipart/form-data',
	        contentType: false,
        	processData: false,
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

		console.log( $userFile.val() );

	}

	function deactivateUserAccount(data){
		$body.find('#pre-loader').show();
		$.ajaxSetup({headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }});
		$.ajax ({
	        type    : "post",
	        url     : '/'+'{{$userinfo[7]}}'+'/deactivate-user-account',
	        data  : data,
	        enctype: 'multipart/form-data',
	        contentType: false,
        	processData: false,
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
	}


	function updateUserProfile(data){
		$body.find('#pre-loader').show();
		$.ajaxSetup({headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }});
		$.ajax ({
	        type    : "post",
	        url     : '/'+'{{$userinfo[7]}}'+'/user-profile',
	        data  : data,
	        enctype: 'multipart/form-data',
	        contentType: false,
        	processData: false,
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

		console.log( $userFile.val() );

	}


	function randomString (length) {
	    let result = '';
	    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	    const charactersLength = characters.length;
	    let counter = 0;
	    while (counter < length) {
	      result += characters.charAt(Math.floor(Math.random() * charactersLength));
	      counter += 1;
	    }
	    return result;
	}
	function uploadFiles(){
		let file = $body.find('#file_upload').file();
		// let fileName = file[0].name+"\n"
	}
	function UpdatePhotoPreview(){
		// $("#user-img-file").hide();
		$('#preview_image').attr('src', URL.createObjectURL(event.target.files[0]));
    	$('#user-pic').attr('src', URL.createObjectURL(event.target.files[0]));
	
	};
	///
	// function initAutoComplete(){

	// 	const input = document.getElementById('user-address');
	// 	const autocomplete = new google.maps.places.Autocomplete(input,{
	// 		types : [],
	// 		componentRestrictions: {'country': ['AU']},
	// 		fields : ['place_id','geometry','name']

	// 	});

	// 	autocomplete.addListener("place_changed", (d) => {
	// 		const place = autocomplete.getPlace();
	// 		console.log(place);

	// 	})

	// }


</script>


<!-- <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCSmnsnpfQO_4Mgm9utnRnt6qA1CzUPoYE&libraries=places&callback=initAutoComplete">
</script> -->

</body>