@include('includes.head')
@include('includes.user-header')

<!-- PAGE CONTAINER-->
<div class="page-container">

    @include('includes.user-top')

    <!-- MAIN CONTENT-->
        <div class="main-content">
            
            <div class="section__content section__content--p30 p-b-30">
                <div class="container-fluid">
                    <section>
                        <div class="welcome--message m-b-20">
                            <h3>Welcome to SUMB [B]izMate @if($userinfo[3] == 'user')<span>Lite</span>@elseif($userinfo[3] == 'user_pro')<span>Pro</span>@endif! Your partner in managing your business finances!</h3>
                            Keep track of your expenses and create and send client invoices with [B]izmate! We're excited to work with you! 
                        </div>

                        <hr class="form--separator">

                        <h3 class="sumb--title">
                            <?php if($userinfo[3] == 'user_pro'){ ?>
                                Pro User Dashboard
                            <?php } elseif($userinfo[3] == 'user'){ ?>
                                Lite User Dashboard
                            <?php } elseif($userinfo[3] == 'accountant'){ ?>
                                Accountant Dashboard
                            <?php } elseif($userinfo[3] == 'manager'){ ?>
                                Manager Dashboard
                            <?php } elseif($userinfo[3] == 'admin'){ ?>
                                Administrator Dashboard
                            <?php } else { ?>
                                User Dashboard
                            <?php } ?>
                        </h3>
                    </section>

                    <section>
                        <div class="sumb--graphs row">
                            <div class="col-xl-6 col-lg-6 col-md-6">
                                <div class="sumb--graphbox sumb--dashstatbox sumb--putShadowbox invoices-block">
                                    <h5>
                                        Invoices
                                        <span>Invoices owed to you</span>
                                    </h5>

                                    <div class="Invoices-wrap">
                                        <canvas id="InvoicesChart"></canvas>
                                    </div>

                                    <div class="block-deets">
                                        <ul>
                                            <?php
                                                if(!empty($total_invoice_counts)){
                                                    $invoice_status = array();
                                                    foreach ($total_invoice_counts as $isummary) {
                                                        if ($isummary['transaction_type'] == "invoice") {
                                                            $invoice_status[$isummary['status']] = array('status'=>$isummary['status'],'total'=>$isummary['total'], 'status_count'=>$isummary['status_count']);
                                                        }
                                                    } 

                                                    if (!empty($invoice_status['Paid']['status_count'])) { ?>
                                                        <li class="Paid">
                                                            <span><?php echo $invoice_status['Paid']['status_count']?></span> Paid Invoice <u>$<?php echo number_format($invoice_status['Paid']['total'], 2) ?></u>
                                                        </li>
                                                    <?php } 

                                                    if (!empty($invoice_status['PartlyPaid']['status_count'])) { ?>
                                                        <li class="PartlyPaid">
                                                            <span><?php echo $invoice_status['PartlyPaid']['status_count']?></span> Partly Paid Invoice <u>$<?php echo number_format($invoice_status['PartlyPaid']['total'], 2) ?></u>
                                                        </li>
                                                    <?php } 

                                                    if (!empty($invoice_status['Unpaid']['status_count'])) { ?>
                                                        <li class="Unpaid">
                                                            <span><?php echo $invoice_status['Unpaid']['status_count']?></span> Unpaid Invoice <u>$<?php echo number_format($invoice_status['Unpaid']['total'], 2) ?></u>
                                                        </li>
                                                    <?php } 

                                                    if (!empty($invoice_status['Recalled']['status_count'])) { ?>
                                                        <li class="Recalled">
                                                            <span><?php echo $invoice_status['Recalled']['status_count']?></span> Recalled Invoice <u>$<?php echo number_format($invoice_status['Recalled']['total'], 2) ?></u>
                                                        </li>
                                                    <?php } 

                                                    if (!empty($invoice_status['Voided']['status_count'])) { ?>
                                                        <li class="Voided">
                                                            <span><?php echo $invoice_status['Voided']['status_count']?></span> Voided Invoice <u>$<?php echo number_format($invoice_status['Voided']['total'], 2) ?></u>
                                                        </li>
                                                    <?php } 
                                                }
                                            ?>
                                        </ul>
                                        @if($userinfo[3] == 'user')        
                                            <a href="/basic/invoice/create" class="add--btn">New Invoice</a>
                                        @else
                                            <a href="/invoice/create" class="add--btn">New Invoice</a>    
                                        @endif
                                    </div>

                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6">
                                <div class="sumb--graphbox sumb--dashstatbox sumb--putShadowbox expense-block">
                                    <h5>
                                        Expenses
                                        <span>Bills you need to pay</span>
                                    </h5>

                                    <div class="Expenses-wrap invoices--expenses_page">
                                        <canvas id="ExpensesChart"></canvas>
                                    </div>
                                    
                                    <div class="block-deets">
                                        <ul>
                                            <?php
                                                if(!empty($total_invoice_counts)){
                                                    $expense_status = array();
                                                    foreach ($total_invoice_counts as $isummary) {
                                                        if ($isummary['transaction_type'] == "expense") {
                                                            $expense_status[$isummary['status']] = array('status'=>$isummary['status'],'total'=>$isummary['total'], 'status_count'=>$isummary['status_count']);
                                                        }
                                                    } 

                                                    if (!empty($expense_status['Paid']['status_count'])) { ?>
                                                        <li class="Paid">
                                                            <span><?php echo $expense_status['Paid']['status_count']?></span> Paid Expense <u>$<?php echo number_format($expense_status['Paid']['total'], 2) ?></u>
                                                        </li>
                                                    <?php } 

                                                    if (!empty($expense_status['PartlyPaid']['status_count'])) { ?>
                                                        <li class="PartlyPaid">
                                                            <span><?php echo $expense_status['PartlyPaid']['status_count']?></span> Partly Paid Expense <u>$<?php echo number_format($expense_status['PartlyPaid']['total'], 2) ?></u>
                                                        </li>
                                                    <?php } 

                                                    if (!empty($expense_status['Unpaid']['status_count'])) { ?>
                                                        <li class="Unpaid">
                                                            <span><?php echo $expense_status['Unpaid']['status_count']?></span> Unpaid Expense <u>$<?php echo number_format($expense_status['Unpaid']['total'], 2) ?></u>
                                                        </li>
                                                    <?php } 

                                                    if (!empty($expense_status['Recalled']['status_count'])) { ?>
                                                        <li class="Recalled">
                                                            <span><?php echo $expense_status['Recalled']['status_count']?></span> Recalled Expense <u>$<?php echo number_format($expense_status['Recalled']['total'], 2) ?></u>
                                                        </li>
                                                    <?php } 

                                                    if (!empty($expense_status['Voided']['status_count'])) { ?>
                                                        <li class="Voided">
                                                            <span><?php echo $expense_status['Voided']['status_count']?></span> Voided Expense <u>$<?php echo number_format($expense_status['Voided']['total'], 2) ?></u>
                                                        </li>
                                                    <?php } 
                                                }
                                            ?>
                                        </ul>
                                        <!-- <ul>
                                            <li class="draft"><span>23</span> Draft Invoices <u>20000</u></li>
                                            <li class="pending"><span>160</span> Awaiting Payment <u>20000</u></li>
                                            <li class="overdue"><span>134</span> Overdue <u>20000</u></li>
                                        </ul> -->
                                        @if($userinfo[3] == 'user')
                                            <a href="/basic/expense-create" class="add--btn">New Expense</a>
                                        @else
                                            <a href="/expense-create" class="add--btn">New Expense</a>
                                        @endif
                                    </div>

                                </div>
                            </div>
                            <div class="col-xl-12 col-lg-12 col-md-12">
                                <div class="sumb--graphbox sumb--dashstatbox sumb--putShadowbox">
                                    <h5>Year to Date Summary</h5>

                                    <div class="SummaryChart-wrap">
                                        <canvas id="SummaryChart"></canvas>
                                    </div>
                                    <div class="ytdlegend for-dashboard">
                                        <div class="row">
                                            <div class="col-xl-9 col-lg-8 col-md-12">
                                                <span class="invoice--legend">Invoice Current Year
                                                    <u>
                                                        <?php 
                                                            $current_year_invoice = []; 
                                                            $result = array_map(function ($items) use(&$current_year_invoice)  {
                                                                    if($items['year'] == date('Y')){
                                                                        // echo "<pre>"; var_dump($items); echo "</pre>"; 
                                                                        echo number_format(array_sum(array_column($items['data'], 'total')),2);
                                                                        array_filter($items['data'],function($v,$k) use(&$current_year_invoice){
                                                                            $current_year_invoice[] = $v['total'];
                                                                        },ARRAY_FILTER_USE_BOTH);
                                                                    }                
                                                            }, $invoice_line_chart_data);
                                                        ?>
                                                    </u>
                                                </span> 
                                                <span class="invoice--legend">Invoice Previous Year
                                                    <u>
                                                        <?php 
                                                            $previous_year_invoice = []; 
                                                            $result = array_map(function ($items) use(&$previous_year_invoice)  {
                                                                    if($items['year'] == (date('Y')-1)){
                                                                        echo number_format(array_sum(array_column($items['data'], 'total')),2);
                                                                        array_filter($items['data'],function($v,$k) use(&$previous_year_invoice){
                                                                            $previous_year_invoice[] = $v['total'];
                                                                        },ARRAY_FILTER_USE_BOTH);
                                                                    }                
                                                            }, $invoice_line_chart_data);
                                                        ?>
                                                    </u>
                                                </span>
                                                <span class="expenses--legend">Expenses Current Year
                                                    <u>
                                                        <?php 
                                                            $current_year_expense = []; 
                                                            $result = array_map(function ($items) use(&$current_year_expense)  {
                                                                    if($items['year'] == date('Y')){
                                                                        echo number_format(array_sum(array_column($items['data'], 'total')),2);
                                                                        array_filter($items['data'],function($v,$k) use(&$current_year_expense){
                                                                            $current_year_expense[] = $v['total'];
                                                                        },ARRAY_FILTER_USE_BOTH);
                                                                    }                
                                                            }, $expense_line_chart_data);
                                                        ?>
                                                    </u>
                                                </span> 
                                                <span class="expenses--legend">Expenses Previous Year
                                                    <u>
                                                        <?php 
                                                            $previous_year_expense = []; 
                                                            $result = array_map(function ($items) use(&$previous_year_expense)  {
                                                                    if($items['year'] == (date('Y')-1)){
                                                                        echo number_format(array_sum(array_column($items['data'], 'total')), 2);
                                                                        array_filter($items['data'],function($v,$k) use(&$previous_year_expense){
                                                                            $previous_year_expense[] = $v['total'];
                                                                        },ARRAY_FILTER_USE_BOTH);
                                                                    }                
                                                            }, $expense_line_chart_data);
                                                        ?>
                                                    </u>
                                                </span>
                                            </div>
                                            <div class="col-xl-3 col-lg-4 col-md-12">
                                                <div class="ytdlegend--dropdown-wrap">
                                                    <select name="languages" id="ytdlegend--dropdown" class="form-input--dropdown">
                                                        <option value="1">View All</option>
                                                        <option value="2">View Invoices</option>
                                                        <option value="3">View Expenses</option>
                                                    </select>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </section>
                    
                    <!--


                    <section>
                        <div class="sumb--addtl1 row" style="margin-bottom: 0px !important;">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">

                                    <h4 class="sumb--title2">Announcements</h4>
                                    <div class="sumb--dashboardAnn sumb--putShadowbox">
                                        <ul>
                                            <li>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</li>
                                            <li>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc ullamcorper ullamcorper magna, vel convallis arcu facilisis eget.</li>
                                            <li><a href="">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</a></li>
                                        </ul>
                                    </div>

                                    <h4 class="sumb--title2">Needs Action</h4>
                                    <div class="sumb--dashboardRequest sumb--putShadowbox">
                                        <ul>
                                            <li>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</li>
                                            <li>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc ullamcorper ullamcorper magna, vel convallis arcu facilisis eget.</li>
                                            <li><a href="">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</a></li>
                                        </ul>
                                    </div>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                    <h4 class="sumb--title2">Calendar</h4>
                                    <div class="sumb--dashboardCal sumb--putShadowbox">
                                        <div id="calendar"></div>
                                    </div>

                            </div>
                        </div>
                    </section>

                    

                    <section class="m-b-10">

                        <h4 class="sumb--title2">Recent Lodgements</h4>
                        
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="sumb--recentlogdements sumb--putShadowbox">

                                    <div class="table-responsive">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th style="border-top-left-radius: 7px;">date</th>
                                                    <th>order ID</th>
                                                    <th>name</th>
                                                    <th>lodgement type</th>
                                                    <th>status</th>
                                                    <th class="sumb--recentlogdements__actions" style="border-top-right-radius: 7px;">actions</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <tr>
                                                    <td>2018-09-29</td>
                                                    <td>100398</td>
                                                    <td><a href="acct-users-details.php">UZUMAKI TRUST</a></td>
                                                    <td>Trust Registration</td>
                                                    <td class="sumb--recentlogdements__status_acc">Accepted</td>
                                                    <td class="sumb--recentlogdements__actions"><a href="#"><i class="fa-solid fa-eye"></i></a></td>
                                                </tr>
                                                <tr>
                                                    <td>2018-09-28</td>
                                                    <td>100397</td>
                                                    <td><a href="acct-users-details.php">JOSEPH TORRES</a></td>
                                                    <td>ABN Registration</td>
                                                    <td class="sumb--recentlogdements__status_proc">Being Process</td>
                                                    <td class="sumb--recentlogdements__actions"><a href="#"><i class="fa-solid fa-eye"></i></a></td>
                                                </tr>
                                                <tr>
                                                    <td>2018-09-27</td>
                                                    <td>100396</td>
                                                    <td><a href="acct-users-details.php">STARTING AT THE BOTTOM</a></td>
                                                    <td>Business Name Registration</td>
                                                    <td class="sumb--recentlogdements__status_rej">Rejected</td>
                                                    <td class="sumb--recentlogdements__actions"><a href="#"><i class="fa-solid fa-eye"></i></a></td>
                                                </tr>
                                                <tr>
                                                    <td>2018-09-26</td>
                                                    <td>100395</td>
                                                    <td><a href="acct-users-details.php">SUPER HUMANS PTY LTD</a></td>
                                                    <td>Company Registration</td>
                                                    <td class="sumb--recentlogdements__status_rev">Manual Review</td>
                                                    <td class="sumb--recentlogdements__actions"><a href="#"><i class="fa-solid fa-eye"></i></a></td>
                                                </tr>
                                                <tr>
                                                    <td>2018-09-25</td>
                                                    <td>100393</td>
                                                    <td><a href="acct-users-details.php">JANE TORRES</a></td>
                                                    <td>ABN Registration</td>
                                                    <td class="sumb--recentlogdements__status_acc">Accepted</td>
                                                    <td class="sumb--recentlogdements__actions"><a href="#"><i class="fa-solid fa-eye"></i></a></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6" class="sumb--recentlogdements__tableender">
                                                        5 Recent Lodgements
                                                        <br>
                                                        <a href="#">View All Lodgements</a>
                                                    </td>
                                                </tr>

                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="p-b-20">

                        <h4 class="sumb--title2">Recent Employment Document Files</h4>
                        
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="sumb--recentlogdements sumb--putShadowbox">

                                    <div class="table-responsive">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th style="border-top-left-radius: 7px;">date</th>
                                                    <th>form ID</th>
                                                    <th>employee name</th>
                                                    <th>category</th>
                                                    <th>document type</th>
                                                    <th>status</th>
                                                    <th class="sumb--recentlogdements__actions" style="border-top-right-radius: 7px;">actions</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                            <tr>
                                                    <td>2018-09-29</td>
                                                    <td>100398</td>
                                                    <td>ROMEO TORRES</td>
                                                    <td>Probation Letter</td>
                                                    <td>Successful Probation</td>
                                                    <td class="sumb--recentlogdements__status_acc">Completed</td>
                                                    <td class="sumb--recentlogdements__actions">
                                                        <a href="#"><i class="fa-solid fa-pen-to-square"></i></a>
                                                        <a href="#"><i class="fa-solid fa-file-arrow-down"></i></a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>2018-09-29</td>
                                                    <td>100398</td>
                                                    <td>ROMEO TORRES</td>
                                                    <td>Probation Letter</td>
                                                    <td>Unsuccessful Probation</td>
                                                    <td class="sumb--recentlogdements__status_acc">Completed</td>
                                                    <td class="sumb--recentlogdements__actions">
                                                        <a href="#"><i class="fa-solid fa-pen-to-square"></i></a>
                                                        <a href="#"><i class="fa-solid fa-file-arrow-down"></i></a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>2018-09-29</td>
                                                    <td>100398</td>
                                                    <td>ROMEO TORRES</td>
                                                    <td>Employment Letter</td>
                                                    <td>Letter of Engagement</td>
                                                    <td class="sumb--recentlogdements__status_acc">Completed</td>
                                                    <td class="sumb--recentlogdements__actions">
                                                        <a href="#"><i class="fa-solid fa-pen-to-square"></i></a>
                                                        <a href="#"><i class="fa-solid fa-file-arrow-down"></i></a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>2018-09-29</td>
                                                    <td>100398</td>
                                                    <td>ROMEO TORRES</td>
                                                    <td>Warning Letter</td>
                                                    <td>First/Second Warning Letter</td>
                                                    <td class="sumb--recentlogdements__status_acc">Completed</td>
                                                    <td class="sumb--recentlogdements__actions">
                                                        <a href="#"><i class="fa-solid fa-pen-to-square"></i></a>
                                                        <a href="#"><i class="fa-solid fa-file-arrow-down"></i></a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>2018-09-29</td>
                                                    <td>100398</td>
                                                    <td>ROMEO TORRES</td>
                                                    <td>Warning Letter</td>
                                                    <td>Final Warning Letter</td>
                                                    <td class="sumb--recentlogdements__progbar">
                                                        50% Completed
                                                        <div class="__progbarWrap">
                                                            <div class="__progbarWrap--status" style="width: 50%">&nbsp;</div>
                                                        </div>
                                                    </td>
                                                    <td class="sumb--recentlogdements__actions">
                                                        <a href="#"><i class="fa-solid fa-pen-to-square"></i></a>
                                                    </td>
                                                </tr>
                                                
                                                <tr>
                                                    <td colspan="7" class="sumb--recentlogdements__tableender">
                                                        5 Recent Employment Document Files
                                                        <br>
                                                        <a href="#">View All Employment Document Files</a>
                                                    </td>
                                                </tr>

                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                -->

                    
                </div>
            </div>
        </div>
    <!-- END MAIN CONTENT-->
</div>
<!-- END PAGE CONTAINER-->

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>

<script>

//get Dates of the week

const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun","Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
const NextWeek = new Date();
const PrevWeek = new Date();

// Next Week
const firstDayNextWeek = new Date(NextWeek.setDate(NextWeek.getDate() - NextWeek.getDay() + 8));
const lastDayNextWeek = new Date(NextWeek.setDate(NextWeek.getDate() - NextWeek.getDay() + 7));
const NextWeekDates = firstDayNextWeek.getDate()+' '+ monthNames[firstDayNextWeek.getMonth()]+' - '+lastDayNextWeek.getDate()+' '+ monthNames[lastDayNextWeek.getMonth()];

//Previous Week
const firstDayPrevWeek = new Date(PrevWeek.setDate(PrevWeek.getDate() - PrevWeek.getDay() - 6));
const lastDayPrevWeek = new Date(PrevWeek.setDate(PrevWeek.getDate() - PrevWeek.getDay() + 7));
const PrevWeekDates = firstDayPrevWeek.getDate()+' '+ monthNames[firstDayPrevWeek.getMonth()]+' - '+lastDayPrevWeek.getDate()+' '+ monthNames[lastDayPrevWeek.getMonth()];



Chart.defaults.font.family = "Montserrat";

//Invoices Chart
const InvoicesChart = document.getElementById("InvoicesChart");

const dataInvoices = {
    label: "Amount",
    <?php if(!empty($bar_chart_data)){?>
    data: [
        <?php 
            array_map(function ($items) {
                        $invoice_array = array_filter($items['weekly_transactions'],function($v,$k){
                            return $v['transaction_type'] == 'invoice';
                        },ARRAY_FILTER_USE_BOTH);
                $invoice_total = array_sum(array_column($invoice_array, 'total'));
                echo $invoice_total ? $invoice_total ."," : 0 .",";
            }, $bar_chart_data);
        ?>
    ],
    <?php }?>
    lineTension: 0,
    fill: false,
    backgroundColor: ['#e5e5e5','#e5e5e5','#fdb917','#fee29f','#fee29f'],
    borderRadius: 5
};

const MonthlyInvoicesData = {
  labels: ["Older", PrevWeekDates,"This Week", NextWeekDates, "Future"],
  datasets: [dataInvoices]
};

const InvoicesBar = new Chart(InvoicesChart, {
  type: 'bar',
  data: MonthlyInvoicesData,
  options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: false },
        scales: {
            x: {
                grid: {display: false},
                ticks: { color: '#28282a',
                    font: {
                        size: 8
                    }
                },
                border: { display: false }
            },
            y: {
                grid: { color: '#e5e5e5'},
                border: { display: false }
            },
        }
    }
});


//Expenses Chart
const ExpensesChart = document.getElementById("ExpensesChart");

const dataExpenses = {
    label: "Amount",
    <?php if(!empty($bar_chart_data)){?>
    data: [
        <?php 
            array_map(function ($items) {
                        $expense_array = array_filter($items['weekly_transactions'],function($v,$k){
                            return $v['transaction_type'] == 'expense';
                        },ARRAY_FILTER_USE_BOTH);
                $expense_total = array_sum(array_column($expense_array, 'total'));
                echo $expense_total ? $expense_total ."," : 0 .",";
            }, $bar_chart_data);
        ?>
    ],
    <?php }?>
    lineTension: 0,
    fill: false,
    backgroundColor: ['#e5e5e5','#e5e5e5','#fdb917','#fee29f','#fee29f'],
    borderRadius: 5
};

const MonthlyExpensesData = {
  labels: ["Older", PrevWeekDates, "This Week", NextWeekDates, "Future"],
  datasets: [dataExpenses]
};

const ExpensesBar = new Chart(ExpensesChart, {
  type: 'bar',
  data: MonthlyExpensesData,
  options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: false },
        scales: {
            x: {
                grid: {display: false},
                ticks: { color: '#28282a',
                    font: {
                        size: 8
                    }
                },
                border: { display: false }
            },
            y: {
                grid: { color: '#e5e5e5'},
                border: { display: false }
            },
        }
    }
});

//YTD Summary Chart
const SummaryChart = document.getElementById("SummaryChart");

const dataInvoiceCurrent = {
    label: "Invoice Current Year",
    data: [<?php echo implode(",", $current_year_invoice); ?>],
    lineTension: 0,
    fill: false,
    borderColor: '#fdc53e',
    backgroundColor: '#fdc53e',
    radius: 3,
    borderWidth: 2
};

const dataInvoicePrevious = {
    label: "Invoice Previous Year",
    data: [<?php echo implode(",", $previous_year_invoice); ?>],
    lineTension: 0,
    borderColor: '#f9e0a3',
    backgroundColor: '#f9e0a3',
    radius: 3,
    borderWidth: 2
};

const dataExpenseCurrent = {
    label: "Expenses Current Year",
    data: [<?php echo implode(",", $current_year_expense); ?>],
    lineTension: 0,
    fill: false,
    borderColor: '#7e7e84',
    backgroundColor: '#7e7e84',
    radius: 3,
    borderWidth: 2
};

const dataExpensePrevious = {
    label: "Expenses Previous Year",
    data: [<?php echo implode(",", $previous_year_expense); ?>],
    lineTension: 0,
    borderColor: '#ccc',
    backgroundColor: '#ccc',
    radius: 3,
    borderWidth: 2
};


const MonthlyData = {
  labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
  datasets: [ dataInvoiceCurrent,dataInvoicePrevious,dataExpenseCurrent,dataExpensePrevious ]
};

const YTDBar = new Chart(SummaryChart, {
  type: 'line',
  data: MonthlyData,
  options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: false },
        scales: {
            x: {
                grid: {display: false},
                ticks: { color: '#28282a',
                    font: {
                        size: 11
                    }
                },
                border: { display: false }
            },
            y: {
                grid: { color: '#e5e5e5'},
                border: { display: false }
            },
        }
    }
});


$('#ytdlegend--dropdown').on('change', function() {

    var filterSelected = this.value;
    var ctrAll;

    switch(filterSelected) {
        case '1':
            for(ctrAll = 0; ctrAll <=3; ctrAll++){
                YTDBar.data.datasets[ctrAll].hidden = false; 
            }
            $('.invoice--legend').show();
            $('.expenses--legend').show();
            YTDBar.update();
            break;
        case '2':
            YTDBar.data.datasets[0].hidden = false;
            YTDBar.data.datasets[1].hidden = false;
            YTDBar.data.datasets[2].hidden = true;
            YTDBar.data.datasets[3].hidden = true;

            $('.invoice--legend').show();
            $('.expenses--legend').hide();
            YTDBar.update();
            break;
        case '3':
            YTDBar.data.datasets[0].hidden = true;
            YTDBar.data.datasets[1].hidden = true;
            YTDBar.data.datasets[2].hidden = false;
            YTDBar.data.datasets[3].hidden = false;

            $('.invoice--legend').hide();
            $('.expenses--legend').show();
            YTDBar.update();
            break;
        default:
            break;
    }
    
});

</script>


@include('includes.footer-dashboard')
@include('includes.footer')
</body>




</html>
<!-- end document-->