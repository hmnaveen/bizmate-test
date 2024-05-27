<header class="header-desktop_ltst {{ ($userinfo[3] == 'admin') ? 'admin--header' : '' }}">
    <div class="row">
        <div class="col-xl-10 col-lg-10 col-md-10 col-sm-10 col-xs-10 col-10">
                <a class="logo" href='https://set-up-my-business.com.au/bizmate/' target="_blank">
                    <img src="/images/sumb-white.webp" alt="[B]izMate" style="max-width: 135px;" />
                </a>
            <ul class="list-unstyled navbar--desktop">
                <li class="{{ (str_contains(url()->current(), 'dashboard')) ? 'current--page' : '' }}">
                    <a href="/dashboard">
                        <span><i class="fas fa-home"></i>Dashboard</span>
                    </a>
                </li>

                <li class="dropdown {{ (str_contains(url()->current(), 'invoice')) || (str_contains(url()->current(), 'expense')) ? 'current--page' : '' }}">
                    <a href="#" class="dropdown-toggle " id="TransactionDrop" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span>
                            <i class="fa-solid fa-money-bill-transfer"></i>Transactions</span>
                        </span>
                    </a>
                    @if($userinfo && $userinfo[3] == 'user_reg' || $userinfo[3] == 'user_pro' || $userinfo[3] == 'accountant' || $userinfo[3] == 'admin')
                        <div class="dropdown-menu" aria-labelledby="TransactionDrop">
                            <a class="dropdown-item" href="/invoice">Invoice</a>
                            <a class="dropdown-item" href="/expense">Expense</a>
                            <!-- <a class="dropdown-item" href="/chart-accounts">Chart of Accounts</a> -->
                        </div>
                    @endif
                    @if($userinfo && $userinfo[3] == 'user')
                        <div class="dropdown-menu" aria-labelledby="TransactionDrop">
                            <a class="dropdown-item" href="/basic/invoice">Invoice</a>
                            <a class="dropdown-item" href="/basic/expense">Expense</a>
                        </div>
                    @endif
                </li>
                <!--
                <li class="dropdown {{ (str_contains(url()->current(), 'profit-loss')) || (str_contains(url()->current(), 'reports')) ? 'current--page' : '' }}">
                    <a href="#" class="dropdown-toggle " id="ReportingDrop" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span>
                            <i class="fa-solid fa-file-contract"></i>Reports</span>
                        </span>
                    </a>
                    
                    <div class="dropdown-menu" aria-labelledby="ReportingDrop">
                        <a class="dropdown-item" href="/basic/profit-loss">Profit & Loss</a>
                        <a class="dropdown-item" href="/basic/reports">Business Activity</a>
                    </div>
                    
                </li>-->
                
                @if($userinfo && $userinfo[3] == 'user_reg' || $userinfo[3] == 'user_pro' || $userinfo[3] == 'accountant' || $userinfo[3] == 'admin')
                    <li class="dropdown {{ (Request::segment(1) === 'profit-loss') || (Request::segment(1) === 'reports') || (Request::segment(1) === 'bas') || (Request::segment(1) === 'balance-sheet') ? 'current--page' : '' }}">
                        <a href="#" class="dropdown-toggle " id="AccountingDrop" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span>
                                <i class="fa-solid fa-file-contract"></i>Reports</span>
                            </span>
                        </a>

                        <div class="dropdown-menu" aria-labelledby="ReportingDrop">
                            <a class="dropdown-item" href="/profit-loss">Profit & Loss</a>
                            <a class="dropdown-item" href="/reports">Business Activity</a>
                            <a class="dropdown-item" href="/balance-sheet">Balance Sheet</a>
                            <a class="dropdown-item" href="/bas/overview">BAS & TAX</a>
                        </div>
                        
                        <!-- <div class="dropdown-menu" aria-labelledby="AccountingDrop">
                            <a class="dropdown-item" href="/profit-loss">Profit & Loss</a>
                            <a class="dropdown-item" href="/balance-sheet">Balance Sheet</a>
                            <a class="dropdown-item" href="/reports">Transactions</a>
                            <a class="dropdown-item" href="/bankAccounts">Bank Accounts</a>
                            <a class="dropdown-item" href="/bankTransactions">Account Transactions</a>
                            <a class="dropdown-item" href="/reconcileTransactions">Reconcile Transactions</a>
                        </div> -->
                    </li>
                    <li class="dropdown {{ (Request::segment(1) === 'chart-accounts') || (Request::segment(1) === 'bank') || (Request::segment(1) === 'account') ? 'current--page' : '' }}">
                        <a href="#" class="dropdown-toggle " id="AccountingDrop" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span>
                                <i class="fa-solid fa-chart-column"></i>Accounting</span>
                            </span>
                        </a>

                        <div class="dropdown-menu" aria-labelledby="AccountingDrop">
                            <a class="dropdown-item" href="/chart-accounts">Chart of Accounts</a>
                            @if($userinfo[3] == 'user_pro' || $userinfo[3] == 'admin' || $userinfo[3] == 'accountant')
                                <a class="dropdown-item" href="/bank/accounts">Bank Accounts</a>
                                <a class="dropdown-item" href="/bank/account/transactions">Bank Transactions</a>
                                <a class="dropdown-item" href="/bank/transaction/reconcile">Reconcile Transactions</a>
                            @endif
                        </div>
                    </li>

                    
                    @if($userinfo[3] == 'admin')
                        <li class="{{ Request::segment(1) === 'user-tab' ? 'current--page' : '' }}">
                            <a href="#" class="dropdown-toggle " id="AdminDrop" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span><i class="fa-solid fa-user-gear"></i>Admin</span>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="AdminDrop">
                                <a href='\user-tab' class="dropdown-item" href="">User</a>
                            </div>
                        </li>
                    @endif
                @endif
                
            </ul>
        </div>

        <div class="col-xl-2 col-lg-2 col-md-2 col-sm-2 col-xs-2 col-2">
            <div class="account-wrap">
                <div class="account-item clearfix js-item-menu">

                <div class="image"><img src="{{ (!isset($userinfo[5]) ? '/img/blankpic.png'  : $userinfo[5] == '') ? '/img/blankpic.png'  : Storage::url($userinfo[5])}}" alt="{{ $userinfo[1] }}" /></div>
                <!-- <div class="image"><img src="/img/{{ $userinfo[5] }}" alt="{{ $userinfo[1] }}" /></div> -->
                    <!-- <div class="image"><img src="{{Storage::url($userinfo[5])}}" alt="{{ $userinfo[1] }}" /></div> -->
                    
                    <div class="account-dropdown js-dropdown">
                        <div class="info clearfix">
                            <div class="image"><a><img src="{{ (!isset($userinfo[5]) ? '/img/blankpic.png'  : $userinfo[5] == '') ? '/img/blankpic.png'  : Storage::url($userinfo[5])}}" alt="{{ $userinfo[1] }}" /></a></div>
                            <div class="content">
                                <h5 class="name"><a>{{ $userinfo[1] }}</a></h5><span class="email">{{ $userinfo[2] }}</span>
                                <div class="account--type">
                                    You're using [B]izMate 
                                    @if($userinfo[3] == 'user')
                                    <span>Lite</span>
                                    @elseif($userinfo[3] == 'user_pro')
                                    <span>Pro</span>
                                    @elseif($userinfo[3] == 'admin')
                                    <span>Admin</span>
                                    @elseif($userinfo[3] == 'accountant')
                                    <span>Accountant</span>
                                    @endif
                                </div>
                            </div>
                           
                        </div>
                        <div class="account-dropdown__body">
                            <div class="account-dropdown__item"><a href="/user-account"><i class="zmdi zmdi-account"></i>Account</a></div>

                            @if($userinfo[3] == 'user')
                                <div class="account-dropdown__item"><a href="/basic/invoice/settings">Invoice Settings</a></div>
                            @else
                                <div class="account-dropdown__item"><a href="/invoice/settings">Invoice Settings</a></div>
                            @endif

                            <div class="account-dropdown__item"><a href="https://set-up-my-business.com.au/bizmate/" target="_blank">About [B]izMate <i class="fa-solid fa-arrow-up-right-from-square" style="position: relative; top: -1px; left: 1px; font-size: 12px;"></i></a></div>
                        </div>
                        <div class="account-dropdown__footer">
                            <a href="/logout" onclick="return confirm('Are you sure you want to log out?')"><i class="zmdi zmdi-power"></i>Logout</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tax-bas--button">
                <a href="https://set-up-my-business.com.au/your-tax-agent/" target="_blank">Tax & BAS<span>Lodgement</span></a>
            </div>

            <div class="application-wrap">
                <div class="application-item clearfix js-item-menu">
                    <div class="image"><i class="zmdi zmdi-apps"></i></div>
                    
                    <div class="application-dropdown js-dropdown">
                        <ul class="list-unstyled application-lists">
                            <li>
                                <a href="https://set-up-my-business.com.au/virtual-accounting-consultation/" target="_blank"><span><i class="zmdi zmdi-account-box-phone"></i></span>Call an Accountant</a>
                            </li>
                            <li>
                                <a href="https://set-up-my-business.com.au/domain-registration/" target="_blank"><span><i class="fas fa-window-maximize"></i></span>Create your own Website</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            @if($userinfo && $userinfo[3] == 'user')
                <div class="account-wrap getPro" style="right: 170px; display: none;">
                    <div class="account-item clearfix js-item-menu">
                        <button name="save_expense"  type="submit" class="btn getPro--btn" onclick="upgradeToProSoon()">Get Pro</button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    
</header>


<!-- Mobile View -->

<header class="header-mobile {{ ($userinfo[3] == 'admin') ? 'admin--header' : '' }}">
    <div class="header-mobile__bar">
        <div class="container-fluid">
            <div class="header-mobile-inner">
                <a class="logo" href="https://set-up-my-business.com.au/bizmate/" target="_blank">
                    <img src="/images/sumb-white.webp" alt="SUMB" style="max-width: 130px;" />
                </a>

                <button class="hamburger hamburger--slider" type="button">
                    <span class="hamburger-box">
                        <span class="hamburger-inner"></span>
                    </span>
                </button>

            </div>
            <nav class="navbar-mobile">
                <ul class="navbar-mobile__list list-unstyled">

                @if($userinfo && $userinfo[3] == 'user')
                    <li class="goproicon--mobile" style="display: none;">
                        <a class="goproicon--btn_mobile" onclick="upgradeToProSoon()"><span>Get Pro (Coming Soon)</span></a>
                    </li>
                @endif

                    <li class="account--type">You're using [B]izMate 
                        @if($userinfo[3] == 'user')
                        <span>Lite</span>
                        @elseif($userinfo[3] == 'user_pro')
                        <span>Pro</span>
                        @elseif($userinfo[3] == 'admin')
                        <span>Admin</span>
                        @elseif($userinfo[3] == 'accountant')
                        <span>Accountant</span>
                        @endif
                    </li>
                    
                    <li><a href="/dashboard"><span><i class="fas fa-home"></i></span>Dashboard</a></li>

                @if($userinfo && $userinfo[3] == 'user_reg' || $userinfo[3] == 'user_pro'  || $userinfo[3] == 'accountant' || $userinfo[3] == 'admin')
                    <li class="has-sub">
                        <a class="js-arrow" href="#"><span><i class="fa-solid fa-money-bill-transfer"></i></span>Transactions <i class="zmdi zmdi-chevron-down"></i></a>
                        <ul class="list-unstyled navbar__sub-list js-sub-list" style="display:none;">
                            <li><a href="/invoice">Invoice</a></li>
                            <li><a href="/expense">Expense</a></li>
                            <!-- <li><a href="/invoice/settings">Invoice Settings</a></li>
                            <li><a href="/chart-accounts">Chart of Accounts</a></li> -->
                        </ul>
                    </li>
                    <li class="has-sub">
                        <a class="js-arrow" href="#"><span><i class="fa-solid fa-file-contract"></i></span>Reports <i class="zmdi zmdi-chevron-down"></i></a>
                        <ul class="list-unstyled navbar__sub-list js-sub-list" style="display:none;">
                            <li><a href="/profit-loss">Profit & Loss</a></li>
                            <li><a href="/reports">Business Activity</a></li>
                            <li><a href="/balance-sheet">Balance Sheet</a></li>
                            <li><a href="/bas/overview">BAS & TAX</a></li>
                        </ul>
                    </li>
                    <li class="has-sub">
                        <a class="js-arrow" href="#"><span><i class="fa-solid fa-chart-column"></i></span>Accounting <i class="zmdi zmdi-chevron-down"></i></a>
                        <ul class="list-unstyled navbar__sub-list js-sub-list" style="display:none;">
                            <li><a href="/chart-accounts">Chart of Accounts</a></li>    

                            @if($userinfo[3] != 'user' || $userinfo[3] != 'user_reg')
                                <li><a href="/bank/accounts">Bank Accounts</a></li>
                                <li><a href="/bank/account/transactions">Account Transactions</a></li>
                                <li><a href="/bank/transaction/reconcile">Reconcile Transactions</a></li>
                            @endif
                        </ul>
                    </li>

                    @if($userinfo[3] == 'admin')

                        <li class="has-sub">
                            <a class="js-arrow" href="#"><span><i class="fa-solid fa-user-gear"></i></span>Admin <i class="zmdi zmdi-chevron-down"></i></a>
                            <ul class="list-unstyled navbar__sub-list js-sub-list" style="display:none;">
                                <li><a href="/user-tab">Users</a></li>
                            </ul>
                        </li>

                    @endif


                @endif
                @if($userinfo && $userinfo[3] == 'user')
                    <li class="has-sub">
                        <a class="js-arrow" href="#"><span><i class="fa-solid fa-money-bill-transfer"></i></span>Transactions <i class="zmdi zmdi-chevron-down"></i></a>
                        <ul class="list-unstyled navbar__sub-list js-sub-list" style="display:none;">
                            <li><a href="/basic/invoice">Invoice</a></li>
                            <li><a href="/basic/expense">Expense</a></li>
                        </ul>
                    </li>
                @endif

                    <!--<li class="has-sub">
                        <a class="js-arrow" href="#"><span><i class="fa-solid fa-file-contract"></i></span>Reports <i class="zmdi zmdi-chevron-down"></i></a>
                        <ul class="list-unstyled navbar__sub-list js-sub-list" style="display:none;">
                            <li><a href="/basic/profit-loss">Profit & Loss</a></li>
                            <li><a href="/basic/reports">Business Activity</a></li>
                        </ul>
                    </li>-->

                    <li class="tax-bas--mobile_button">
                        <a href="https://set-up-my-business.com.au/your-tax-agent/" target="_blank">
                            Tax & BAS Lodgement
                        </a>
                    </li>

                    <li class="has-sub">
                        <a class="js-arrow" href="#"><span><i class="zmdi zmdi-apps"></i></span>Other Applications <i class="zmdi zmdi-chevron-down"></i></a>
                        <ul class="list-unstyled navbar__sub-list js-sub-list with--icons" style="display:none;">
                            <li>
                                <a href="https://set-up-my-business.com.au/virtual-accounting-consultation/" target="_blank"><span><i class="zmdi zmdi-account-box-phone"></i></span>Call an Accountant</a>
                            </li>
                            <li>
                                <a href="https://set-up-my-business.com.au/domain-registration/" target="_blank"><span><i class="fas fa-window-maximize"></i></span>Create your own Website</a>
                            </li>
                        </ul>
                    </li>

                    <li class="has-sub">
                        <a class="js-arrow" href="#"><span><i class="fa-solid fa-user"></i></span>Profile <i class="zmdi zmdi-chevron-down"></i></a>
                        <ul class="list-unstyled navbar__sub-list js-sub-list" style="display:none;">
                            @if($userinfo[3] == 'user')
                                <li><a href="/basic/invoice/settings">Invoice Settings</a></li>
                            @else
                                <li><a href="/invoice/settings">Invoice Settings</a></li>
                            @endif
                        </ul>
                    </li>
                    <li><a href="https://set-up-my-business.com.au/bizmate/" target="_blank"><span><i class="zmdi zmdi-info"></i></span>About [B]izMate</a></li>
                    <li><a href="/logout" onclick="return confirm('Are you sure you want to log out?')"><span><i class="zmdi zmdi-power"></i></span>Logout</a></li>


                    
                </ul>
            </nav>
        </div>
        </div>
    
</header>

<section class="ver-alert email--verification_notif" style="display: {{$userinfo[4] == 'unverified' ? 'block;'  : 'none;'}}">
    <div class="verify--me">
        Please <span onclick="verifyEmail(event)">click here</span> to verify your email.
    </div>
</section>


<!-- HEADER DESKTOP-->

<form action="/basic/upgrade" method="post" enctype="multipart/form-data">
    @csrf
    <div id="upgrade_pro_modal" class="modal fade modal-reskin" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title deleteicon--header" id="exampleModalLabel">Upgrade to Pro</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to upgrade to the pro version <span id=""></span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close--btn" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary delete--btn" id="" value="">Upgrade</button>
                </div>
            </div>
        </div>
    </div>
</form>

<div id="upgrade_pro_modal_soon" class="modal fade modal-reskin modal-getPro" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title goproicon--header" id="exampleModalLabel">Pro Version (Coming Soon)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
                </button>
            </div>
            <div class="modal-body">
                We know you want to be a <b><i class="fas fa-atom"></i> Pro</b> and we're working on it!

                <ul>
                    <li>With <b><i class="fas fa-atom"></i> Pro</b> version, you can do a lot more:</li>
                    <li>Feature 1</li>
                    <li>Feature 2</li>
                    <li>Feature 3</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary gopro--btn" id="" value="">Get notified</button>
            </div>
        </div>
    </div>
</div>

<script>
$(function() {
    
});
function upgradeToPro(){
    $('#upgrade_pro_modal').modal({
        backdrop: 'static',
        keyboard: true, 
        show: true
    });
}

function upgradeToProSoon() {
    $('#upgrade_pro_modal_soon').modal({
        backdrop: 'static',
        keyboard: true, 
        show: true
    });

return false;
}
function verifyEmail(event){

$.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

console.log($('.verify--me').text('Sending...'));

$.ajax({
    type       : "POST",
    url        : "/auth/email-verification",
    data       : {
        'email'    : $('.email').text()
    },
    dataType   : 'JSON',
    success    : function (data) {
        $('.verify--me').text('Verification link has been sent to your registered email.');
        $('.verify--me').addClass('sent--vercode')
    },
    error : function(e) {
        $('.verify--me').text('Sorry, something went wrong!');
    }
});
}
</script>
