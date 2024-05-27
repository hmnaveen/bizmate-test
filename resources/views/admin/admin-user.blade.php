@include('includes.head')
@include('includes.user-header')

<!-- :( -->
<div class="page-container">
	@include('includes.user-top')
	<div class="main-content">
		<div class="section__content section__content--p30">
			<div class="container-fluid">
				<section>
                    <h3 class="sumb--title m-b-20">User Management</h3>
                </section>

				<section>
					<div class="userManage--wrap row">
						<div class="col-xl-6 col-lg-6">
							<div class="userManage--stats userTotal--stats sumb--putShadowbox">
								<div class="row">
									<div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-4">
									@if(!empty($users_count))
										<div class="block total">
											
											<span>{{$users_count['active_users'] + $users_count['inactive_users'] }}</span>
										
											Total Users
										</div>
									</div>
									<div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-4">
										<div class="block active">
											<span>{{$users_count['active_users']}}</span>
											Active Users
										</div>
									</div>
									<div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-4">
										<div class="block deacti">
											<span>{{$users_count['inactive_users']}}</span>
											Inactive Users
										</div>
									</div>
									@endif
								</div>
								<a data-attr='Add' class="add-edit--user" href="javascript:void(0)"><i class="fa-solid fa-circle-plus"></i>Add User</a>
							</div>
						</div>
						<div class="col-xl-6 col-lg-6">
							<div class="userManage--stats subscription--stats sumb--putShadowbox">
								<div class="row">
									<div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
										<div class="block lite">
											<div>Lite</div>
											@if(!empty($users_count['account_type_count']))
												@php $active = 0; $inactive=0;
													$active = isset($users_count['account_type_count']['user']['active']) ? $users_count['account_type_count']['user']['active'] : 0;
													$inactive = isset($users_count['account_type_count']['user']['inactive']) ? $users_count['account_type_count']['user']['inactive'] : 0;
												@endphp
												<span class="active">Active: {{ $active }}</span>
												<span class="inactive">Inactive: {{ $inactive }}</span>
												<span class="total">Total: {{ $active + $inactive }}</span>
											@endif
										</div>
									</div>
									<div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
										<div class="block basic">
											<div>Basic</div>
											@if(!empty($users_count['account_type_count']))

												@php $active = 0; $inactive=0;
													$active = isset($users_count['account_type_count']['user_reg']['active']) ? $users_count['account_type_count']['user_reg']['active'] : 0;
													$inactive = isset($users_count['account_type_count']['user_reg']['inactive']) ? $users_count['account_type_count']['user_reg']['inactive'] : 0;
												@endphp
												<span class="active">Active: {{ $active }}</span>
												<span class="inactive">Inactive: {{ $inactive }}</span>
												<span class="total">Total: {{ $active + $inactive }}</span>
											@endif
										</div>
									</div>
									<div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
										<div class="block pro">
											<div>Pro</div>
											@if(!empty($users_count['account_type_count']))
													@php $active = 0; $inactive=0;
														$active = isset($users_count['account_type_count']['user_pro']['active']) ? $users_count['account_type_count']['user_pro']['active'] : 0;
														$inactive = isset($users_count['account_type_count']['user_pro']['inactive']) ? $users_count['account_type_count']['user_pro']['inactive'] : 0;
													@endphp
												<span class="active">Active: {{ $active }}</span>
												<span class="inactive">Inactive: {{ $inactive }}</span>
												<span class="total">Total: {{ $active + $inactive }}</span>
											@endif
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</section>

				<section>
					<div class="row" >
						<div class="col-xl-12">
							<form action="/user-tab"  method="GET" enctype="multipart/form-data" id="search_form_user_admin">

								<div class="row">
                                    <div class="col-xl-3 col-lg-3">
                                        <div class="form-input--wrap">
                                            <label class="form-input--question" for="">Name</label>
                                            <div class="form--inputbox row">
                                                <div class="col-12">
												<input type="text" id="search_name" name="search-name" placeholder="Name" value="{{!empty($filterData) && isset($filterData['search-name']) ? $filterData['search-name'] : ''}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-3 col-lg-3">
                                        <div class="form-input--wrap">
                                            <label class="form-input--question" for="">Email</label>
                                            <div class='form--inputbox row'>
												<div class="col-12">
													<input type="text" id="search_email" name="search-email" placeholder="Email" value="{{!empty($filterData) && isset($filterData['search-email']) ? $filterData['search-email'] : ''}}">
												</div>
											</div>
                                        </div>
                                    </div>

                                    <div class="col-xl-3 col-lg-3">
                                        <div class="form-input--wrap">
                                            <label class="form-input--question" for="">Member Since</label>
                                            <div class="date--picker row">
												<div class="col-12">
													<input type="text" id="filter-date-created" name="date-created" placeholder='DD/MM/YYYY'  readonly value="{{!empty($filterData) && isset($filterData['date-created']) ? $filterData['date-created'] : ''}}">
												</div>
											</div>
                                        </div>
                                    </div>

                                    <div class="col-xl-3 col-lg-3">
										<div class="form-input--wrap">
											<label class="form-input--question" for="">Account Type</label>
											<select class="form-input--dropdown" id="account-type-filter" name="account-type" value="" >
												<option value="">All</option>
												<option {{!empty($filterData) && isset($filterData['account-type']) && ($filterData['account-type'] == 'user') ? 'selected' : ''}} value='user'>[B]izMate Lite</option>
												<option {{!empty($filterData) && isset($filterData['account-type']) && ($filterData['account-type'] == 'user_reg') ? 'selected' : ''}} value='user_reg'>[B]izMate Basic</option>
												<option {{!empty($filterData) && isset($filterData['account-type']) && ($filterData['account-type'] == 'user_pro') ? 'selected' : ''}} value='user_pro'>[B]izMate Pro</option>
												<option {{!empty($filterData) && isset($filterData['account-type']) && ($filterData['account-type'] == 'accountant') ? 'selected' : ''}} value='accountant'>[B]izMate Accountant</option>
												<option {{!empty($filterData) && isset($filterData['account-type']) && ($filterData['account-type'] == 'admin') ? 'selected' : ''}} value='admin'>[B]izMate Admin</option>
											</select>
										</div>
                                    </div>
                                    
                                    <div class="manageUsers-list--btns col-12" style="text-align: right;">
										<button type="submit"class="btn sumb--btn" value="Search" ><i class="fa-solid fa-magnifying-glass"></i>Search</button>
										<button type="button" class="btn sumb--btn sumb-clear-btn" onclick="window.location='/user-tab'"><i class="fa-solid fa-circle-xmark"></i>Clear Search</button>
                                    </div>
                                </div>
							</form>
						</div>	
					</div>
				</section>

				<section class="p-b-40">
					<div class="row">
						<div class="col-12">
							<div class="sumb--recentlogdements userManage--table sumb--putShadowbox">
								<div class="table-responsive">
									<table class="admin-user-table">
										<thead>
											<tr>
												<th style="border-top-left-radius: 7px">Email</th>
												<th>Name</th>
												<th>Account type</th>
												<th onclick="searchItems('created_at','Asc')" id='user-created_at' style="cursor:pointer">Date Created<i class="{{ $direction == 'Desc' ? 'fa fa-caret-down' : 'fa fa-caret-up' }}"></i></th>
												<th>Status</th>
												<th>Invoice</th>
												<th>Expense</th>
												<th width="10%" class="sumb--recentlogdements__actions" style="border-top-right-radius: 7px">Options</th>
											</tr>
										</thead>
										<tbody>
											@if (empty($users['data']))
												<tr>
													<td colspan="8" style="padding: 30px 15px; text-align:center;">No Data At This time.</td>
												</tr>
											@else
												@foreach ($users['data'] as $user)
													<?php 
														$invoice_expense_count = collect($user['transaction_collections'])->groupBy('transaction_type')->map(function ($transaction){
															return $transaction->count();
														});
													?>
													<tr>

														<td>{{$user['email']}}</td>
														<td >{{$user['fullname']}}</td>
														<td class="account--type">
															
															[B]izMate <span class="{{ $user['accountype'] == 'admin' ? 'admin' : ( $user['accountype'] == 'accountant' ? 'accountant' : ($user['accountype'] == 'user' ? 'lite' : ($user['accountype'] == 'user_reg' ? 'basic' :  ($user['accountype'] == 'user_pro' ? 'pro' : '' ) ) )) }}"></span>
														
														</td>
														<td>{{date('d-m-Y', strtotime($user['created_at']))}}</td>

														<td>
															<div class="{{$user['active'] ? 'user--status-active' : 'user--status-deacti' }}">{{$user['active'] ? 'Active' : 'Inactive' }}</div>
														</td>

														<td style="text-align:right">
															{{ $invoice_expense_count && isset($invoice_expense_count['invoice']) ? $invoice_expense_count['invoice'] : '' }}
														</td>

														<td style="text-align:right">
															{{ $invoice_expense_count && isset($invoice_expense_count['expense']) ? $invoice_expense_count['expense'] : '' }}
														</td>

														<td class='sumb--recentlogdements__actions'>
															<div class="sumb--fileSharebtn dropdown">
																<a class="fileSharebtn" href="#" role="dropdown" id="userManageOptions" data-toggle="dropdown" aria-expanded="true"><i class="fa-solid fa-square-caret-down"></i></a>
																<div class="dropdown-menu dropdown-menu-right userManage--options" aria-labelledby="userManageOptions">
																	<a title="Edit Profile" class="dropdown-item add-edit--user" data-attr-role="{{$user['accountype']}}" data-attr-id={{$user['encId']}} data-attr="update">
																		<i class="fa-solid fa-pen-to-square"></i> Edit Profile
																	</a>
																	<a title="Delete Profile" class='dropdown-item delete-user' data-attr-name='{{$user['fullname']}}' data-attr-id={{$user['encId']}} data-attr-role="{{$user['accountype']}}">
																		<i class="fa-solid fa-trash"></i> Delete Profile
																	</a>
																	<a title="Account Status" class='dropdown-item toggle-status' data-attr-id={{$user['encId']}} data-attr-status={{$user['active']}} data-attr-role="{{$user['accountype']}}">
																		<i class="fa-solid {{$user['active'] == 1 ? 'fa-toggle-off' : 'fa-toggle-on'}}"></i> {{$user['active'] == 1 ? 'Deactivate Profile' : 'Reactivate Profile'}}
																	</a>
																	<a title="Generate New Password" data-attr-role="{{$user['accountype']}}" class='dropdown-item send-new-pass' data-attr-id={{$user['encId']}}>
																		<i class="fa fa-envelope" aria-hidden="true"></i> Send Password Reset
																	</a>
																</div>
															</div>
														</td>

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
												<a id='first-page'
													href="javascript:void(0)"
													data-attr="{{ $users['first_page_url']}}"  type="button" class="btn btn-outline-secondary">
													<i class="fas fa-angle-double-left"></i>
												</a>
												<a id='prev-page-user'
												href="javascript:void(0)"
												data-attr="{{$prev}}" 
												type="button" 
												class="{{$users['current_page'] <= 1 ? 'disabled' : ''}} btn btn-outline-secondary" ><i class="fas fa-angle-left"></i>
												</a>
												<a  href="javascript:void(0)" 
													type="button"
													class="btn btn-outline-secondary" >Page {{$users['current_page']}} of <span id='lpu'>{{$users['last_page']}}</span>
												</a>
												<a id='next-page-user' href="javascript:void(0)" 
												type="button"
												data-attr="{{$next}}"
												class="{{$users['current_page'] >= $users['last_page'] ? 'disabled' : ''}} btn btn-outline-secondary" >
												<i class="fas fa-angle-right"></i>
												</a>
												<a  href="javascript:void(0)"
													id = "last-page"
													type="button"
													data-attr="{{ $users['last_page_url'] }}"
													class="btn btn-outline-secondary"><i class="fas fa-angle-double-right"></i>
												</a>
												<div class="btn-group" role="group">

													<button id="btnGroupDrop1" type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
														Display: {{$users['per_page']}} Items
													</button>
													<div id=data-per-page class="dropdown-menu" aria-labelledby="btnGroupDrop1">
														<a data-attr='1' class="dropdown-item">1 Item</a>
														<a data-attr='5' class="dropdown-item">5 Items</a>
														<a data-attr='10' class="dropdown-item">10 Items</a>
														<a data-attr='25' class="dropdown-item">25 Items</a>
														<a data-attr='50' class="dropdown-item">50 Items</a>
														<a data-attr='100' class="dropdown-item">100 Items</a>
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
			</div>
		</div>
	</div>
</div>


<!-- add/edit modal -->
@csrf
<div class="modal fade modal-reskin" id="mdl-add-edit-user" role="dialog" style="z-index: 99999;">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
		    <div class="modal-header mdl-header">
				<h5 class="modal-title" id="userManageAddEdit--header"></h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
		    </div>
		    <div class="modal-body clearfix">
		    	<div class="sumb-alert alert alert-danger" role="alert" style="display:none;">
                   
                </div>
		        <div class="form-group">
                    <label>User Email Address</label>
                    <input id='add-edit-email' class="au-input au-input--full" type="email" name="email" placeholder="Email">
                </div>
                <div class="form-group">
                    <label>Full Name</label>
                    <input id='add-edit-name' class="au-input au-input--full" type="fullname" name="fullname" placeholder="Full Name">
                </div>
                <div class="form-group">
                	<label>Account type</label>
	                <select class="form-input--dropdown" id="add-edit-account-type" name="account-type">

						<option value='accountant'>Bizmate Accountant</option>
						<option value='user'>Bizmate Lite</option>
						<option value='user_reg'>Bizmate</option>
						<option value='user_pro'>Bizmate Pro</option>
	                	<option value='admin'>Administrator</option>
	                </select>
	            </div>

                <div class="form-group admin-user-password-container">

                    <label>Password</label>
                    <input id='add-edit-pass' class="au-input au-input--full" type='password' name="password1" placeholder="Password">

                    <div class='eye-container'>
	                    <i class="toggle-pass fa-solid fa-eye-slash" style='cursor:pointer;'></i>
	                    <i class="toggle-pass fa-solid fa-eye" style='cursor:pointer;display:none;'></i>
	                </div>
                    <button type="button" class="btn sumb-btn--yellow">
                    	generate password
                    </button>

                </div>

             <!--    <div class="form-group">
                	<div>
                		<label>User status</label>
                	</div>
                	<label>Activated</label>
                		<input type="radio" id="active" name="sub-user-stat" checked="checked" value=1>
                	<label>Deactivated</label>
						<input type="radio" id="deactive" name="sub-user-stat" value=0>
				</div> -->
					
		    </div>

		    <div class="modal-footer">
		        <button id='submit-add-edit-user-btn' type="button" class="submit-admin-gen-user btn sumb-btn--yellow"></button>
		    </div>
		</div>
	</div>
</div>

@include('includes.footer')
<script>
	// :(
	$(function() {
		//init
		$( "#filter-date-created").datepicker({ dateFormat: 'dd/mm/yy' });
		let $body = $(this);
		let params = new URLSearchParams(window.location.search);

		let filters = Array.from(params.keys()).reduce(

			  (acc, val) => ({ ...acc, [val]: params.get(val) }),
			  {}

		);
		
		//input field for submiting
		$fullname = $body.find('#add-edit-name');
		// $pass 	  = $body.find('#add-edit-pass');
		$email    = $body.find('#add-edit-email');
		$accountType = $body.find('#add-edit-account-type');
		$submitUserBtn = $body.find('#submit-add-edit-user-btn');
		$submitUserHeader = $body.find('#userManageAddEdit--header');
		$passContianer = $body.find('.admin-user-password-container');
		$msgBox = $body.find('.sumb-alert');
		$passContianer.hide();

		///events
		$('#mdl-add-edit-user').on('hidden.bs.modal', function () {
		  	clearModal();
		});
		$body.delegate('.send-new-pass','click',function(){
			
			let me = $(this);
			sendNewGenPass(me.attr('data-attr-id'));



		});
		$body.delegate('.delete-user','click',function(){

			let me = $(this);
			if(me.attr('data-attr-role') == 'admin'){
				Swal.fire({
				  icon: 'error',
				  title: 'Oops...',
				  text: 'Can\'t delete admin users'
				})
			}else{
				
				Swal.fire({
				  title: 'Are you sure?',
				  text: 'Are you sure you want to remove ' + me.attr('data-attr-name') + '?',
				  icon: 'warning',
				  showCancelButton: true,
				  confirmButtonColor: '#fdb917',
				  confirmButtonText: 'Yes'
				}).then((result) => {
				  	if (result.isConfirmed) {

				  		delUser( me.attr('data-attr-id') )

				  	}
				});
			}
			
			

		});
		$body.delegate(".submit-admin-gen-user",'click',function(){

			let me = $(this),
			data = {

				'fullname' : $fullname.val(),
				'email' : $email.val(),
				// 'password' : $pass.val(),
				'accountype' : $accountType.find(':selected').val() 
				
			}
			if(me.attr('data-attr') == 'update'){
				
				updateUser( data,me.attr('data-attr-id') );

			}else{

				signUp(data);

			}	

		});
		$body.delegate('#first-page','click',function(){

			let me = $(this);
			window.location = b(me,filters,1);
			
		});
		$body.delegate('#last-page','click',function(){

			let me = $(this);
			window.location = b(me,filters,me.attr('data-attr').split('?')[1].split('=')[1]);

		});
		$body.delegate('#next-page-user','click', function(){

			let me = $(this);

			if( 'page' in filters == false ){
				let l = me.attr('data-attr') + window.location.search.replace('?', '&');
				window.location = l;

			}else{
				
				window.location = window.location.href.replace('page='+filters.page ,'page='+(parseInt(filters.page)+1));

			}
			

		});
		$body.delegate('#prev-page-user','click', function(){

			let me = $(this);
			if( 'page' in filters == false ){

				let l = me.attr('data-attr') + window.location.search.replace('?', '&');
				window.location = l;

			}else{

				window.location = window.location.href.replace('page='+filters.page ,'page='+(parseInt(filters.page)-1));	
			}
			

		});
		

		$body.delegate('#data-per-page  a','click',function(){
			let me = $(this);

			if( 'ipp' in filters == false ){

				window.location = window.location.href + a() + 'ipp='+ me.attr('data-attr');

			}else{

				window.location = window.location.search.replace('ipp='+filters.ipp ,'ipp='+me.attr('data-attr') );

			}

		});

		$body.delegate('#user-created_at','click', function(){
			let sort = a();
			if('direction' in filters == false){
				if(me.attr('data-attr') == 'Desc'){
					sort+='direction=Asc';
				}else{
					sort+='direction=Desc' ;
				}

				window.location = window.location.href+sort;
			}else{
				
				if(filters.direction == 'Desc'){
					window.location = window.location.search.replace('direction=Desc','direction=Asc');
				}else{
					window.location = window.location.search.replace('direction=Asc','direction=Desc');
				}

			}
			
		});

		$body.delegate(".add-edit--user",'click',function(){
			
			let me = $(this);
			
			$body.find('#mdl-add-edit-user').modal('show');
			if(me.attr('data-attr') == 'update'){
				// $passContianer.hide();
				$submitUserBtn.attr('data-attr-id',me.attr('data-attr-id'));
				// $email.attr('disabled',true);
				showUser(me.attr('data-attr-id'));
				$submitUserBtn.text('Update User');
				$submitUserHeader.text('Update User');
				$submitUserHeader.addClass('update');
				
			} else {
				// $passContianer.show();
				clearModal();
				$submitUserBtn.text('Add User');
				$submitUserHeader.text('Add New User');
				$submitUserHeader.removeClass('update');

			}
			$submitUserBtn.attr('data-attr',me.attr('data-attr'));
			

		});

		$body.delegate(".admin-user-password-container button",'click',function(){

			let me = $(this);
			$body.find('#add-edit-pass').val(randomString(16));

		});

		$body.delegate(".toggle-pass",'click',function(){

			let me = $(this);

			if( me.hasClass('fa-eye-slash') ){

				$body.find('.fa-eye-slash').hide();
				$body.find('.fa-eye').show();

			}else{

				$body.find('.fa-eye').hide();
				$body.find('.fa-eye-slash').show();

			}

			if($pass.attr('type') != 'password'){
				
				$pass.attr('type', 'password');

			}else{

				$pass.removeAttr('type'); 
					
			}
		});
		$body.delegate('.toggle-status','click',function(){

			let me = $(this);
			toogleUserStatus(me.attr('data-attr-id') , me.attr('data-attr-status') );
		});
	});
	//functions
	// let randomString = function(length) {
	//     let result = '';
	//     const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	//     const charactersLength = characters.length;
	//     let counter = 0;
	//     while (counter < length) {
	//       result += characters.charAt(Math.floor(Math.random() * charactersLength));
	//       counter += 1;
	//     }
	//     return result;
	// }
	let b = function(obj1, obj2,p){
		if( 'page' in obj2 == false ){
			return obj1.attr('data-attr') + window.location.search.replace('?', '&');
		}else{
			return window.location.search.replace('page='+obj2.page, 'page='+p);	
		}
	}
	let a = function(){
		return window.location.search == '' ? '?' : '&';
	}
	function uploadPhoto(){
		
	}
	function sendNewGenPass(id){
		$body.find('#pre-loader').show();
		$.ajaxSetup({
	        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
	    });
	    $.ajax ({
	        type    : "GET",
	        url     :  '/' + id + '/generated-password',     
	        dataType: "JSON",
	        success : function(data) {
	        	$body.find('#pre-loader').hide()
	        	console.log(data);
	        	Swal.fire({
					title: 'Success',
					text: 'You have successfully sent a new password',
					confirmButtonColor: '#fdb917',
					icon: 'success'
				})
			    // .then((res) => {

			    // 	window.location = window.location.href;

			    // });
	        	

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
	//copied
	function searchItems(orderBy, direction){
        
        if(orderBy && direction){
        		
            $("#search_form_user_admin").append('<input id="orderBy" type="hidden" name="orderBy" value='+orderBy+' ><input id="direction" type="hidden" name="direction" value='+direction+' >');

       	}
        $("#search_form_user_admin").submit();

    }
    //
    function showUser(id){
    	
    	$.ajaxSetup({
	        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
	    });
	    $.ajax ({
	        type    : "GET",
	        url     :  '/' + id + '/user',     
	        dataType: "JSON",
	        success : function(data) {
	        	
	        	$fullname.val(data.data.fullname);
	        	$email.val(data.data.email);
	        	$accountType.val(data.data.accountype);
	        	
	        },
	        error: function(e){
	            
	        	

	        }

	    });

    }
	function signUp(data){
		let html = '';
	    $.ajaxSetup({
	        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
	    });
	    $body.find('#pre-loader').show();
	    $.ajax ({
	        type    : "POST",
	        url     :  '/admin-add-user',
	        data  : data,         
	        dataType: "JSON",
	        success : function(data) {
	        	$body.find('#pre-loader').hide();
	        	$body.find('#mdl-add-edit-user').modal('hide');
	        	Swal.fire(
			      'Success',
			      'User has been added',
			      'success'
			    ).then((res) => {

			    	window.location = window.location.href;

			    });
	           
	        },
	        error: function(e){
	        	$body.find('#pre-loader').hide();
	        	$msgBox.show();
	            $msgBox.removeClass('alert-primary');
	            $msgBox.find('li').remove();
	            $.each(e.responseJSON.errors,function(index,item){
	                html+='<li>'+item+'</li>' 
	            });
            	$msgBox.append(html);
	            
	        } 
	    });


	}
	function updateUser(data,id){
		let html = '';
	    $.ajaxSetup({
	        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
	    });
	    $body.find('#pre-loader').show();
	    $.ajax ({
	        type    : "PUT",
	        url     :  '/user/'+id,
	        data  : data,         
	        dataType: "JSON",
	        success : function(data) {
	        	$body.find('#pre-loader').hide();
	        	$body.find('#mdl-add-edit-user').modal('hide');
	        	Swal.fire(
			      'Success',
			      'Succesfully Updated',
			      'success'
			    ).then((res) => {

			    	window.location = window.location.href;

			    });
	           
	        },
	        error: function(e){
	        	
	        	if(e.responseJSON.errors === undefined){

	        		html='<li>'+e.responseJSON.message+'</li>';

	        	}else{

	        		$.each(e.responseJSON.errors,function(index,item){
	                	html+='<li>'+item+'</li>' 
	            	});

	        	}
	        	$body.find('#pre-loader').hide();
	        	$msgBox.show();
	            $msgBox.removeClass('alert-primary');
	            $msgBox.find('li').remove();
	            
            	$msgBox.append(html);
	            
	        } 
	    });
	}
	function delUser(id){

		$.ajaxSetup({
	        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
	    });
		$.ajax ({
	        type    : "DELETE",
	        url     :  '/user/' + id,     
	        dataType: "JSON",
	        success : function(data) {
	        	Swal.fire(
			      'Deleted!',
			      'User has been deleted',
			      'success'
			    ).then((res) => {
			    	window.location = window.location.href;
			    });
	        },
	        error: function(e){
	            
	        }
	    });
	}
	function toogleUserStatus($id,$status){
		$.ajaxSetup({
	        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
	    });
		$.ajax ({
	        type    : "PUT",
	        url     :  'user/status/' + $id,
	        data : {

	        	'active' : $status,

	        },   
	        dataType: "JSON",
	        success : function(data) {
	        	Swal.fire({
					title: 'Succesfully ' + ($status == 1 ? 'Deactivated' : 'Activated'),
					text: data.message,
				  	icon: 'success',
					confirmButtonColor: '#fdb917',
				}).then((res) => {

			    	window.location = window.location.href;

			    });
	        	
	        },
	        error: function(e){
	            Swal.fire({
				  icon: 'error',
				  title: 'Oops...',
				  text: e.responseJSON.message
				})
	        }
	    });

	}
	function clearModal(){

		$msgBox.hide();
		$msgBox.html('');
		// $pass.val('');
		$email.val('');
		$fullname.val('');
		$accountType.val('admin');
		$body.find('.fa-eye').hide();
		$email.removeAttr('disabled');
		// $pass.attr('type', 'password');
		$body.find('.fa-eye-slash').show();
		$submitUserBtn.removeAttr('data-attr-id');

	}



</script>
