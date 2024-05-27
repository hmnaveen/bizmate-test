@include('includes.head')
@include('includes.user-header')


<!-- PAGE CONTAINER-->
<div class="page-container">

    @include('includes.user-top')

    <!-- MAIN CONTENT-->
    <div class="main-content p-b-30">
        <div class="section__content section__content--p30">
            <div class="container-fluid">

                <section>
                    <div class="row m-b-20 ">
                        <div class="col-xl-6">
                            <h3 class="sumb--title">Activity Statements</h3>
                        </div>
                        <div class="col-xl-6" style="text-align: right;">
                            <a href="/bas/settings" class="activity--statement_btn"><i class="fa fa-cog"></i>Settings</a>
                        </div>
                        
                    </div>
                </section>

                <div class="alert alert-success" style="display:none">
                    Statement updated successfully
                </div>

                <section>

                    <form action="/bas/statement" method="post" enctype="multipart/form-data" class="form-horizontal" id="activity_statement_form">
                        @csrf
                        <div class="activity--statement sumb--putShadowbox">

                            <div id="actvt--wrap">
                                <ul class="nav">
                                    <li>
                                        <a href="#summary" data-toggle="tab" class="active"><i class="fa-solid fa-file-lines"></i>Summary</a>
                                    </li>
                                    <li>
                                        <a href="#transactions_by_tax_rate" data-toggle="tab"><i class="fa-solid fa-file-waveform"></i>Transactions by Tax Rate</a>
                                    </li>
                                </ul>  

                                <div class="tab-content">
                                    <div id="summary" class="tab-pane active">

                                        <!-- First Row -->

                                        <div class="row first--row">

                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                                                <h3>{{!empty($statement) ? date('F Y', strtotime($statement['end_date'])) : ''}} <?php if(!empty($statement['activity_statement_status'])){ ?> <span class="<?php echo $statement['activity_statement_status'] ?>"><?php echo $statement['activity_statement_status'] ?> </span> <?php } else { ?> <span class="draft">Draft</span> <?php } ?></h3>
                                                <p>ABN: {{!empty($statement) ? $statement['abn'] : ''}} | GST Accounting Method: <span>{{!empty($statement) ? $statement['gst_accounting_method'] : ''}}</span></p>
                                            </div>
                                            
                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12" id="amount_refundable_header_div">
                                                <h3>{{!empty($statement) ? number_format($statement['payment_amount'], 2) : 0 }}</h3>
                                                {{!empty($statement && $statement['payment_type']) ? "Amount Refundable" :  "Amount Payable" }}
                                            </div>
                                            
                                        </div>


                                        <div class="scrollable-x">

                                        <!-- Second Row -->

                                        @if(!empty($statement && $statement['gst_activity']))

                                            <hr class="form-cutter">

                                            <div class="row second--row">
                                                <div class="col-xl-12">
                                                    <h4>Goods and Services Tax</h4>
                                                    <p>{{!empty($statement && $statement['gst_activity']) ? $statement['gst_activity']['date'] : ''}}</p>
                                                </div>

                                                <div class="actvt--table">
                                                    <table>
                                                        <tbody>
                                                            <tr>
                                                                <td width="50px">
                                                                    <div class="tagging">G1</div>
                                                                </td>
                                                                <td width="50px">
                                                                    Total Sales
                                                                </td>
                                                                <td class="text-align-right">
                                                                    GST inclusive
                                                                </td>
                                                                <td class="text-align-right">
                                                                    <button type="button" data-toggle="modal" data-target="#adjustment_g1_activity_modal" class="btn sumb--btn adjust--btn">Adjust G1</button>
                                                                </td>  
                                                                <td class="text-align-right" width="100px">
                                                                    <input type="float" readonly id="total_sales_g1" name="total_sales_g1" class="readOnly" value="{{!empty($statement && $statement['gst_activity']) ? number_format($statement['gst_activity']['total_sales_g1'], 2) : 0}}">
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>

                                            </div>
                                            @endif

                                            <!-- Third Row -->

                                            @if(!empty($statement && $statement['paygw_activity'] && $statement['payg_withhold_period'] != 'none'))

                                            <hr class="form-cutter">

                                            <div class="row third--row">
                                                <div class="col-xl-12">
                                                    <h4>PAYG Tax Withheld</h4>
                                                    <p>{{!empty($statement && $statement['paygw_activity']) ? $statement['paygw_activity']['date'] : ''}}</p>
                                                </div>

                                                <div class="actvt--table">
                                                    <table>
                                                        <tbody>
                                                            <tr>
                                                                <td style="width:50px">
                                                                    <div class="tagging">W1</div>
                                                                </td>
                                                                <td width="100%">
                                                                    Total salary, wages and other payments
                                                                </td>
                                                                <td>
                                                                    <input type="float" id="total_salary_paygw" name="payg_withheld_w1" value="{{ !empty($statement) && isset($statement['paygw_activity']['payg_withheld_w1']) ? $statement['paygw_activity']['payg_withheld_w1'] : 0 }}">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <div class="tagging">W2</div>
                                                                </td>
                                                                <td>
                                                                    Amounts withheld from payments at W1
                                                                </td>
                                                                    
                                                                <td>
                                                                    <input type="float" id="paygw_w1" name="payg_withheld_w2" value="{{ !empty($statement) && isset($statement['paygw_activity']['payg_withheld_w2']) ? $statement['paygw_activity']['payg_withheld_w2'] : 0 }}" onkeyup="gstCalculation('paygw_w1')">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <div class="tagging">W4</div>
                                                                </td>
                                                                <td>
                                                                    Amounts withheld where no ABN is quoted
                                                                </td>
                                                                    
                                                                <td>
                                                                    <input type="float" id="paygw_no_abn_quoted" name="payg_withheld_w4" value="{{ !empty($statement) && isset($statement['paygw_activity']['payg_withheld_w4']) ? $statement['paygw_activity']['payg_withheld_w4'] : 0 }}" onkeyup="gstCalculation('paygw_no_abn_quoted')">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <div class="tagging">W3</div>
                                                                </td>
                                                                <td>
                                                                    Other amounts withheld (excluding shown at W2 or W4)
                                                                </td>
                                                                
                                                                <td>
                                                                    <input type="float" id="paygw_others" name="payg_withheld_w3" value="{{ !empty($statement) && isset($statement['paygw_activity']['payg_withheld_w3']) ? $statement['paygw_activity']['payg_withheld_w3'] : 0 }}" onkeyup="gstCalculation('paygw_others')">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <div class="tagging">W5</div>
                                                                </td>
                                                                <td>
                                                                    Total withheld W2 + W3 + W4
                                                                </td>
                                                                
                                                                <td>
                                                                    <input type="float" readonly class="readOnly" id="total_paygw_w2_w3_w4" name="payg_withheld_w5" value="{{ !empty($statement) && isset($statement['paygw_activity']['payg_withheld_w5']) ? $statement['paygw_activity']['payg_withheld_w5'] : 0 }}">
                                                                </td>
                                                            </tr>       
                                                        </tbody>
                                                    </table>
                                                </div>
                                                
                                            </div>

                                            @endif

                                            <!-- Fourth Row -->

                                            @if(!empty($statement && $statement['paygi_activity'] && $statement['payg_income_tax_method'] != 'none'))

                                                <hr class="form-cutter">

                                                @if($statement['payg_income_tax_method'] == 'incometaxamount')
                                                    <div class="row fourth--row">
                                                        <div class="col-xl-12">
                                                            <h4>PAYG Income Tax Instalment (Option 1)</h4>
                                                            <p>{{!empty($statement && $statement['paygi_activity']) ? $statement['paygi_activity']['date'] : ''}}</p>
                                                        </div>

                                                        <div class="actvt--table">
                                                            <table>
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="width:50px">
                                                                            <div class="tagging">T7</div>
                                                                        </td>
                                                                        <td>
                                                                            Instalment (copy from BAS)
                                                                        </td>
                                                                        <td>
                                                                            <input type="float" id="instalment_t7" name="instalment_t7" value="{{ !empty($statement) && isset($statement['paygi_activity']['option_1']['instalment_t7']) ? $statement['paygi_activity']['option_1']['instalment_t7'] : 0 }}"  onkeyup="gstCalculation('instalment_t7')">
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td></td>
                                                                        <td style="font-weight: 600;">
                                                                            If varying this amount, complete T8, T9 and T4
                                                                        </td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td style="width:50px">
                                                                            <div class="tagging">T8</div>
                                                                        </td>
                                                                        <td>
                                                                            Estimated tax for the year
                                                                        </td>
                                                                        <td>
                                                                            <input type="float" id="yearly_estimated_tax_t8" name="yearly_estimated_tax_t8" value="{{ !empty($statement) && isset($statement['paygi_activity']['option_1']['yearly_estimated_tax_t8']) ? $statement['paygi_activity']['option_1']['yearly_estimated_tax_t8'] : 0 }}">
                                                                        </td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td style="width:50px">
                                                                            <div class="tagging">T9</div>
                                                                        </td>
                                                                        <td>
                                                                            Varied amount for the quarter
                                                                        </td>
                                                                    
                                                                        <td>
                                                                            <input type="float" id="quarterly_varied_amount_t9" name="quarterly_varied_amount_t9" value="{{ !empty($statement) && isset($statement['paygi_activity']['option_1']['quarterly_varied_amount_t9']) ? $statement['paygi_activity']['option_1']['quarterly_varied_amount_t9'] : 0 }}"  onkeyup="gstCalculation('quarterly_varied_amount_t9')">
                                                                        </td>
                                                                    </tr>

                                                                
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>

                                                @elseif($statement['payg_income_tax_method'] == 'incometaxrate')
                                                    <div class="row fourth--row">
                                                        <div class="col-xl-12">
                                                            <h5>PAYG Income Tax Instalment (Option 2)</h5>
                                                            <p>{{!empty($statement && $statement['paygi_activity']) ? $statement['paygi_activity']['date'] : ''}}</p>
                                                        </div>
                                                        
                                                        <div class="actvt--table">
                                                            <table>
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="width:50px">
                                                                            <div class="tagging">T1</div>
                                                                        </td>
                                                                        <td width="100%">
                                                                            PAYG instalment income
                                                                        </td>
                                                                        <td>
                                                                            <input type="float" id="instalment_t1" name="instalment_t1" value="{{ !empty($statement) && isset($statement['paygi_activity']['option_2']['instalment_t1']) ? $statement['paygi_activity']['option_2']['instalment_t1'] : 0 }}"  onkeyup="gstCalculation('instalment_t1')">
                                                                        </td>
                                                                    </tr>
                                                                
                                                                    <tr>
                                                                        <td style="width:50px">
                                                                            <div class="tagging">T2</div>
                                                                        </td>
                                                                        <td>
                                                                            Instalment rate (enter ATO percentage e.g. 3.50)
                                                                        </td>
                                                                        <td>
                                                                            <input type="number" max="999" id="instalment_rate_percentage_t2" name="instalment_rate_percentage_t2" value="{{ !empty($statement) && isset($statement['paygi_activity']['option_2']['instalment_rate_percentage_t2']) ? $statement['paygi_activity']['option_2']['instalment_rate_percentage_t2'] : 0 }}" onkeyup="gstCalculation('instalment_rate_percentage_t2')">
                                                                        </td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td style="width:50px">
                                                                            <div class="tagging">T3</div>
                                                                        </td>
                                                                        <td>
                                                                            New varied rate (enter as a percentage e.g. 3.50)
                                                                        </td>
                                                                    
                                                                        <td>
                                                                            <input type="float" maxlength="3" max="100" id="varied_rate_percentage_t3" name="varied_rate_percentage_t3" value="{{ !empty($statement) && isset($statement['paygi_activity']['option_2']['varied_rate_percentage_t3']) ? $statement['paygi_activity']['option_2']['varied_rate_percentage_t3'] : 0 }}"  onkeyup="gstCalculation('varied_rate_percentage_t3')">
                                                                        </td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td style="width:50px">
                                                                            <div class="tagging">T11</div>
                                                                        </td>
                                                                        <td>
                                                                            T1 x T2 (or x T3)
                                                                        </td>
                                                                        <td>
                                                                            <input type="float" readonly id="income_tax_instalment_t11" name="income_tax_instalment_t11" class="readOnly" value="{{ !empty($statement) && isset($statement['paygi_activity']['option_2']['income_tax_instalment_t11']) ? $statement['paygi_activity']['option_2']['income_tax_instalment_t11'] : 0 }}"  onkeyup="gstCalculation('quarterly_varied_amount_t9')">
                                                                        </td>
                                                                    </tr>

                                                                
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="row">
                                                    <div class="actvt--table">
                                                        <table>
                                                            <tbody>
                                                                <tr>
                                                                    <td style="width:50px">
                                                                        <div class="tagging">T4</div>
                                                                    </td>
                                                                    <td width="100%">
                                                                        Reason code for variation	
                                                                    </td>
                                                                    <td>
                                                                        <select class="form-input--dropdown" id="reason_code_t4" name="reason_code_t4" value="">
                                                                            <option id="" value="">None</option>
                                                                            <option id="" value="21" {{!empty($statement) && isset($statement['paygi_activity']['reason_code_t4']) && $statement['paygi_activity']['reason_code_t4'] == '21' ? "selected": 0 }} >21 &nbsp; Change in investments</option>
                                                                            <option id="" value="22" {{!empty($statement) && isset($statement['paygi_activity']['reason_code_t4']) && $statement['paygi_activity']['reason_code_t4'] == '22' ? "selected": 0 }} >22 &nbsp; Current business structure not continuing</option>
                                                                            <option id="" value="23" {{!empty($statement) && isset($statement['paygi_activity']['reason_code_t4']) && $statement['paygi_activity']['reason_code_t4'] == '23' ? "selected": 0 }} >23 &nbsp; Significant change in trading conditions</option>
                                                                            <option id="" value="24" {{!empty($statement) && isset($statement['paygi_activity']['reason_code_t4']) && $statement['paygi_activity']['reason_code_t4'] == '24' ? "selected": 0 }} >24 &nbsp; Internal business restructure and PAYG</option>
                                                                            <option id="" value="25" {{!empty($statement) && isset($statement['paygi_activity']['reason_code_t4']) && $statement['paygi_activity']['reason_code_t4'] == '25' ? "selected": 0 }} >25 &nbsp; Change in legislation or product mix</option>
                                                                            <option id="" value="26" {{!empty($statement) && isset($statement['paygi_activity']['reason_code_t4']) && $statement['paygi_activity']['reason_code_t4'] == '26' ? "selected": 0 }} >26 &nbsp; Financial market changes</option>
                                                                            <option id="" value="27" {{!empty($statement) && isset($statement['paygi_activity']['reason_code_t4']) && $statement['paygi_activity']['reason_code_t4'] == '27' ? "selected": 0 }} >27 &nbsp; Use of income tax losses</option>
                                                                        </select>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                </div>

                                            @endif

                                            <!-- Fifth Row -->


                                            <hr class="form-cutter">

                                            <div class="row fifth--row">
                                                <div class="col-xl-12">
                                                    <h5>Amounts you owe the Tax Office</h5>
                                                </div>

                                                <div class="actvt--table">
                                                    <table>
                                                        <tbody>
                                                        @if(!empty($statement && $statement['gst_activity']))
                                                            <tr>
                                                                <td style="width:50px">
                                                                    <div class="tagging">1A</div>
                                                                </td>
                                                                <td>
                                                                    GST on sales
                                                                </td>
                                                                <td class="text-align-right">
                                                                    <button type="button" data-toggle="modal" data-target="#adjustment_1a_activity_modal" class="btn sumb--btn adjust--btn">Adjust 1A</button>
                                                                </td>
                                                                <td width="220px">
                                                                    <input type="float" readonly id="gst_on_sales" name="gst_sales_1a"  class="form-control" value="{{!empty($statement && $statement['gst_activity']) ? $statement['gst_activity']['gst_sales_1a'] : 0 }}">
                                                                </td>
                                                            </tr>
                                                        @endif
                                                        
                                                        @if(!empty($statement && $statement['paygw_activity'] && $statement['payg_withhold_period'] != 'none'))
                                                            <tr>
                                                                <td style="width:50px">
                                                                    <div class="tagging">4</div>
                                                                </td>
                                                                <td colspan="2">
                                                                    PAYG tax withheld
                                                                </td>

                                                                <td width="220px">
                                                                    <input type="float" readonly id="payg_tax_withheld" name="payg_tax_withheld"  class="form-control" value="{{!empty($statement) && isset($statement['paygw_activity']['payg_tax_withheld']) ? $statement['paygw_activity']['payg_tax_withheld'] : 0 }}">
                                                                </td>
                                                            </tr>
                                                        @endif
                                                        @if(!empty($statement && $statement['paygi_activity'] && $statement['payg_income_tax_method'] != 'none'))
                                                            <tr>
                                                                <td style="width:50px">
                                                                    <div class="tagging">5A</div>
                                                                </td>
                                                                <td colspan="2">
                                                                    PAYG income tax instalment
                                                                </td>

                                                                <td width="220px">
                                                                    <input type="float" readonly id="payg_income_tax_instalment_5a" name="payg_income_tax_instalment_5a"  class="form-control" value="{{!empty($statement) && isset($statement['paygi_activity']['payg_income_tax_instalment_5a']) ? $statement['paygi_activity']['payg_income_tax_instalment_5a'] : 0 }}" >
                                                                </td>
                                                            </tr>
                                                        @endif
                                                            <tr>
                                                                <td style="width:50px">
                                                                    <div class="tagging">8A</div>
                                                                </td>
                                                                <td colspan="2">
                                                                    Total owed to the ATO
                                                                </td>

                                                                <td width="220px">
                                                                    <input style="font-weight:bold" type="float" readonly id="total_owed_to_ato" name="total_owed_to_ato"  class="form-control" value="{{!empty($statement) && isset($statement['total_owed_to_ato']) ? $statement['total_owed_to_ato'] : 0 }}">
                                                                    <input type="float" hidden readonly id="total_owed_to_ato_hidden" name=""  class="form-control" value="{{!empty($statement) && isset($statement['total_owed_to_ato']) ? $statement['total_owed_to_ato'] : '' }}">
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <!-- Sixth Row -->

                                            <hr class="form-cutter">

                                            <div class="row sixth--row">
                                                <div class="col-xl-12">
                                                    <p><h5>Amounts the Tax Office owes you</h5></p>
                                                </div>

                                                <div class="actvt--table">
                                                    <table>
                                                        <tbody>
                                                        @if(!empty($statement && $statement['gst_activity']))
                                                            <tr>
                                                                <td style="width:50px">
                                                                    <div class="tagging">1B</div>
                                                                </td>
                                                                <td>
                                                                    GST on purchases
                                                                </td>
                                                                <td class="text-align-right">
                                                                    <button type="button" data-toggle="modal" data-target="#adjustment_1b_activity_modal" class="btn sumb--btn adjust--btn">Adjust 1B</button>
                                                                </td> 
                                                                <td width="220px">
                                                                    <input type="text" readonly id="gst_on_purchase" name="gst_purchases_1b"  class="form-control" value="{{!empty($statement) ? $statement['gst_activity']['gst_purchases_1b'] : '' }}">
                                                                </td>
                                                            </tr>
                                                        @endif
                                                        @if(!empty($statement && $statement['paygi_activity'] && $statement['payg_income_tax_method'] != 'none'))
                                                            <tr>
                                                                <td style="width:50px">
                                                                    <div class="tagging">5B</div>
                                                                </td>
                                                                <td colspan="2">
                                                                    Credit from PAYG income tax instalment variation	
                                                                </td>

                                                                <td width="220px">
                                                                    <input type="float" id="payg_income_tax_instalment_credit" name="payg_income_tax_instalment_credit"  class="form-control" value="{{!empty($statement) && isset($statement['paygi_activity']['payg_income_tax_instalment_credit']) ? $statement['paygi_activity']['payg_income_tax_instalment_credit'] : 0 }}" onkeyup="gstCalculation('payg_income_tax_instalment_credit')">
                                                                </td>
                                                            </tr>
                                                        @endif
                                                            <tr>
                                                                <td style="width:50px">
                                                                    <div class="tagging">8B</div>
                                                                </td>
                                                                <td colspan="2">
                                                                    Total owed by the ATO	
                                                                </td> 
                                                                <td width="220px">
                                                                    <input style="font-weight:bold" type="text" readonly id="total_owed_by_ato" name="total_owed_by_ato"  class="form-control" value="{{!empty($statement) && isset($statement['total_owed_by_ato']) ? $statement['total_owed_by_ato'] : 0 }}">
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        
                                            
                                            <!-- Seventh Row -->

                                            <hr class="form-cutter">

                                            <div class="row seventh--row">
                                                <div class="col-xl-12">
                                                    <h5>
                                                        <span id="payment_type_span_text">
                                                            {{ !empty($statement && $statement['payment_type']) ? 'Refund' : ((!empty($statement) && !$statement['payment_type']) ? 'Payment' : '')}}
                                                        </span>
                                                    </h5>
                                                </div>

                                                    <div class="actvt--table">
                                                        <table>
                                                            <tbody>
                                                                <tr>
                                                                    <td style="width:50px">
                                                                        <div  class="tagging">9</div>
                                                                    </td>
                                                                    <td id="payment_type_sub_text">
                                                                        
                                                                        {{ !empty($statement && $statement['payment_type']) ? 'Your refund amount' : ((!empty($statement) && !$statement['payment_type']) ? 'Your payment amount' : '')}}
                                                                        
                                                                    </td>
                                                                    <td width="220px">
                                                                        <div id="amount_refundable_div" style="">
                                                                            
                                                                            <input style="text-align:center" class="readOnly" type="float" readonly id="payment_amount" name="payment_amount" value="{{!empty($statement) ? number_format($statement['payment_amount'], 2) : '' }}">
                                                                            <input type="float" hidden readonly id="payment_amount_hidden" name=""  class="form-control" value="{{!empty($statement) ? isset($statement['paygw_activity']['payg_withheld_w5']) ?  number_format($statement['payment_amount'] - $statement['paygw_activity']['payg_withheld_w5'], 2) : number_format($statement['payment_amount'], 2) : '' }}">
                                                                            
                                                                            <input type="text" hidden readonly id="payment_type" name="payment_type"  class="form-control" value="{{!empty($statement) ? $statement['payment_type'] : '' }}">
                                                                            <input type="text" hidden readonly id="payment_type_hidden"  class="form-control" value="{{!empty($statement) ? $statement['payment_type'] : '' }}">
                                                                        </div> 
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>

                                                    </div>
                                            </div>

                                            

                                            <!-- Form Navigation -->
                                            <div class="row">

                                                <div id="draft_finalise_btn_div" style="{{ !empty($statement) && isset($statement['activity_statement_status']) && $statement['activity_statement_status'] == 'finalise' ? 'display:none':'display:block' }}">
                                                    <button type="button" class="btn sumb--btn" onclick="window.location='/bas/overview'"><i class="fa-solid fa-circle-left"></i>Back</button>
                                                    <button type="button" id="save_draft" name="save_draft" class="btn sumb--btn" value="draft" onclick="submitForm('draft');"><i class="fa-solid fa-floppy-disk"></i>Save as Draft</button>
                                                    <button type="button"  data-toggle="modal" data-target="#finalise_activity_modal" class="btn sumb--btn" ><i class="fa-solid fa-circle-check"></i>Finalise</button>
                                                </div>

                                                <div id="delete_btn_div" style="{{ !empty($statement) && isset($statement['activity_statement_status']) && $statement['activity_statement_status'] == 'finalise' ? 'display:block':'display:none' }}">
                                                    
                                                    <input type="hidden" name="gst_calculation_period" value="{{!empty($statement) ? $statement['gst_calculation_period'] : ''}}">
                                                    <input type="hidden" name="payg_withholding_period" value="{{!empty($statement) ? $statement['payg_withhold_period'] : ''}}">
                                                    <input type="hidden" name="gst_accounting_method" value="{{!empty($statement) ? $statement['gst_accounting_method'] : ''}}">
                                                    <input type="hidden" name="payg_income_tax_method" value="{{!empty($statement) ? $statement['payg_income_tax_method'] : ''}}">

                                                    <input type="hidden" name="abn" value="{{!empty($statement) ? $statement['abn'] : ''}}">
                                                    <input type="hidden" name="start_date" value="{{!empty($statement) ? $statement['start_date'] : ''}}">
                                                    <input type="hidden" name="end_date" value="{{!empty($statement) ? $statement['end_date'] : ''}}">

                                                    <input type="hidden" id="adjustment_g1" name="adjustment_g1" value="{{ !empty($statement['gst_activity']) && isset($statement['gst_activity']['adjustment_g1']) ? json_encode($statement['gst_activity']['adjustment_g1']) : '[]' }}">
                                                    <input type="hidden" id="adjustment_1a" name="adjustment_1a" value="{{ !empty($statement['gst_activity']) && isset($statement['gst_activity']['adjustment_1a']) ? json_encode($statement['gst_activity']['adjustment_1a']) : '[]' }}">
                                                    <input type="hidden" id="adjustment_1b" name="adjustment_1b" value="{{ !empty($statement['gst_activity']) && isset($statement['gst_activity']['adjustment_1b']) ? json_encode($statement['gst_activity']['adjustment_1b']) : '[]' }}">

                                                    <input type="hidden" id="finalise" name="finalise" value="finalise">
                                                    <input type="hidden" id="" name="activity_statement_status" value="">

                                                    <button type="button" id="delete_activity_backbtn" class="btn sumb--btn" onclick="window.location='/bas/overview'"><i class="fa-solid fa-circle-left"></i>Back</button>

                                                    <button type="button" id="delete_activity_btn" class="btn sumb--btn" data-toggle="modal" data-target="#delete_activity_modal"><i class="fa-solid fa-trash-can"></i>Delete</button>

                                                </div>
                                                
                                            </div>

                                            <!-- History -->

                                            <div class="row history--bastax">

                                                <div class="col-xl-12">
                                                    <h5><i class="fa-solid fa-clock-rotate-left"></i> History</h5>
                                                </div>

                                                @if(!empty($statement['gst_activity']))
                                                    <table id="adjustments">
                                                        @if(isset($statement['gst_activity']['adjustment_g1']))
                                                            @foreach($statement['gst_activity']['adjustment_g1'] as $adjustment_g1)
                                                                <tr>
                                                                    <td>
                                                                        <b>Adjustment</b> to Total sales (G1) of ${{ $adjustment_g1['adjust_by'] }} by {{ $userinfo[1] }}
                                                                    </td>
                                                                    <td width="220px">
                                                                        {{ date("d/m/Y", strtotime($adjustment_g1['created_at'])) }}
                                                                    </td>
                                                                </tr>
                                                            
                                                            @endforeach
                                                        @endif

                                                        @if(isset($statement['gst_activity']['adjustment1_a']))
                                                            @foreach($statement['gst_activity']['adjustment1_a'] as $adjustment_1a)  
                                                                <tr>
                                                                    <td>
                                                                        <b>Adjustment</b> to GST on sales (1A) of ${{ $adjustment_1a['adjust_by'] }} by {{ $userinfo[1] }}
                                                                    </td>
                                                                    <td width="220px">
                                                                        {{ date("d/m/Y", strtotime($adjustment_1a['created_at'])) }}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endif

                                                        @if(isset($statement['gst_activity']['adjustment1_b']))
                                                            @foreach($statement['gst_activity']['adjustment1_b'] as $adjustment1_b)
                                                                <tr>
                                                                    <td>
                                                                        <b>Adjustment</b> to GST on purchases (1B) of ${{ $adjustment1_b['adjust_by'] }} by {{ $userinfo[1] }}
                                                                    </td>
                                                                    <td width="220px">
                                                                        {{ date("d/m/Y", strtotime($adjustment1_b['created_at'])) }}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    </table>
                                                @endif
                                            </div>

                                        </div>


                                    </div>

                                    <div id="transactions_by_tax_rate" class="tab-pane">

                                        <section>
                                            <div class="transactionsByTaxRate--table">
                                                <div class="table-responsive">
                                                    @if(!empty($tax_rates))
                                                    @foreach($tax_rates as $transaction)
                                                        <h3>{{!empty($transaction['tax_rates_name']) ? $transaction['tax_rates_name'] : ''}}</h3>
                                                        <table>
                                                            <thead>
                                                                <tr>
                                                                    <th id="invoice_issue_date" style="border-top-left-radius: 7px;">Date </th>
                                                                    <th id="invoice_number">Account</th>
                                                                    <th id="client_name">Reference</th>
                                                                    <th id="client_email">Details</th>
                                                                    <th id="invoice_status">Gross</th>
                                                                    <th id="invoice_total_amount">GST</th>
                                                                    <th style="border-top-right-radius: 7px;">Net</th>
                                                                </tr>
                                                            </thead>

                                                            <tbody>
                                                                
                                                                @if(!empty($transaction['accounts']))
                                                                    @foreach($transaction['accounts'] as $accounts)  
                                                                        <tr>
                                                                            <td>{{!empty($accounts['date']) ? $accounts['date'] : ''}}</td>
                                                                            <td>{{!empty($accounts['chart_accounts_particulars_name']) ? $accounts['chart_accounts_particulars_name'] : ''}}</td>
                                                                            <td>----</td>
                                                                            <td> {{!empty($accounts['parts_description'])  ? $accounts['parts_description'] : ''}}</td>
                                                                            <td> {{!empty($accounts['parts_gross_amount']) ? number_format($accounts['parts_gross_amount'], 2) : '0.00'}}</td>
                                                                            <td> {{!empty($accounts['parts_gst_amount']) ? number_format($accounts['parts_gst_amount'], 2) : '0.00'}} </td>
                                                                            <td>{{!empty($accounts['parts_net_amount']) ? number_format($accounts['parts_net_amount'], 2) : '0.00'}}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                @endif

                                                                <tr class="tableTotal">
                                                                    <td colspan="4" rowspan="2" style="border-bottom-left-radius: 7px;">Total {{!empty($transaction['tax_rates_name']) ? $transaction['tax_rates_name'] : ''}}</td>
                                                                    <td>{{!empty($transaction['total_gross_amount'])  ? number_format($transaction['total_gross_amount'], 2) : '0.00'}}</td>
                                                                    <td>{{!empty($transaction['total_gst_amount'])  ? number_format($transaction['total_gst_amount'], 2) : '0.00'}}</td>
                                                                    <td>{{!empty($transaction['total_net_amount']) ? number_format($transaction['total_net_amount'], 2) : '0.00'}}</td>
                                                                </tr>
                                                                <tr class="tableTotal--tags">
                                                                    <td>Gross</td>
                                                                    <td>GST</td>
                                                                    <td style="border-bottom-right-radius: 7px;">Net</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>

                                                        <hr>
                                                        @endforeach
                                                    @endif

                                                    <div class="nothing--follows">--- Nothing Follows ---</div>
                                                </div>
                                            </div>
                                        </section>


                                    </div>
                                </div>

                            </div>

                        </div>
                    </form>

                </section>
                
            </div>
        </div>
    </div>
</div>


<!-- Adjustment G1 pop-up -->
<div id="adjustment_g1_activity_modal" class="modal fade modal-reskin" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title statementAdjustment--header" id="exampleModalLabel">Total sales (G1) adjustment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
                </button>
            </div>
            <div class="modal-body">

                <div class="modal-desc--bastax">
                    Manual adjustments will make changes to this G label amount. If you're not sure about making adjustments 
                    to your activity statement, contact a SUMB Certified Advisor.
                    
                    <span>Note: This adjustment will impact the gross value for this G label only</span>
                </div>

                <div class="form-input--wrap">
                    <label class="form-input--question">Adjustment amount</label>
                    <div class="form--inputbox row">
                        <div class="col-12">
                            <input type="float" class="form-control" id="adjustment_g1_amount">
                        </div>
                    </div>
                    
                </div>

                <div class="form-input--wrap">
                    <label class="form-input--question">Reason for adjustment (max 200 characters)</label>
                    <textarea class="form-control" id="adjustment_g1_reason"></textarea>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close--btn" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary save--btn" id="adjustment_g1_btn" value="">Adjust</button>
            </div>
        </div>
    </div>
</div>
<!-- Adjustment G1 pop-up ends-->

<!-- Adjustment 1A pop-up -->
<div id="adjustment_1a_activity_modal" class="modal fade modal-reskin" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title statementAdjustment--header" id="exampleModalLabel">GST on sales (1A) adjustment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <div class="modal-desc--bastax">
                    Manual adjustments will make changes to your activity statement amount. 
                    If you're not sure about making adjustments to your activity statement, 
                    contact a SUMB Certified Advisor.
                </div>

                <div class="form-input--wrap">
                    <label class="form-input--question">Adjustment amount</label>
                    <div class="form--inputbox row">
                        <div class="col-12">
                        <input type="float" class="form-control" id="adjustment_1a_amount">
                        </div>
                    </div>
                    
                </div>

                <div class="form-input--wrap">
                    <label class="form-input--question">Reason for adjustment (max 200 characters)</label>
                    <textarea class="form-control" id="adjustment_1a_reason"></textarea>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close--btn" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary save--btn" id="adjustment_1a_btn" value="">Adjust</button>
            </div>
        </div>
    </div>
</div>
<!-- Adjustment 1A pop-up ends-->

<!-- Adjustment 1B pop-up -->
<div id="adjustment_1b_activity_modal" class="modal fade modal-reskin" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title statementAdjustment--header" id="exampleModalLabel">GST on purchases (1B) adjustment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
                </button>
            </div>
            <div class="modal-body">

                <div class="modal-desc--bastax">
                    Manual adjustments will make changes to your activity statement amount. 
                    If you're not sure about making adjustments to your activity statement, 
                    contact a SUMB Certified Advisor.
                </div>

                <div class="form-input--wrap">
                    <label class="form-input--question">Adjustment amount</label>
                    <div class="form--inputbox row">
                        <div class="col-12">
                            <input type="float" class="form-control" id="adjustment_1b_amount">
                        </div>
                    </div>
                    
                </div>

                <div class="form-input--wrap">
                    <label class="form-input--question">Reason for adjustment (max 200 characters)</label>
                    <textarea class="form-control" id="adjustment_1b_reason"></textarea>
                </div>

            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close--btn" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary save--btn" id="adjustment_1b_btn" value="">Adjust</button>
            </div>
        </div>
    </div>
</div>
<!-- Adjustment 1B pop-up ends-->


<!-- Finalise activity confirmation pop-up -->
<div id="finalise_activity_modal" class="modal fade modal-reskin" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title finalisebastax--header" id="exampleModalLabel">Finalise then lodge with the ATO</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
        </button>
      </div>
      <div class="modal-body">
        <div class="modal-desc--bastax" style="border-bottom: 0px; margin: 0px; padding: 0px;">
            Finalising means the activity statement is recorded in [B]izMate. Next, you'll need to file with the ATO. This can be done through [B]izMate Tax, via the ATO's online portal, a connected app, or by post.
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary close--btn" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary finalisebastax--btn" id="finalise_activity" value="">Finalise</button>
      </div>
    </div>
  </div>
</div>
<!-- Finalise activity confirmation pop-up ends-->
  

<!-- Delete finalised activity warning pop-up -->

<div id="delete_activity_modal" class="modal fade modal-reskin" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title deleteicon--header" id="exampleModalLabel">Are you sure you want to delete this statement?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
                </button>
            </div>
            <div class="modal-body">
                If you have lodged this activity statement with ATO, we recommend you export the statement before you delete.
            </div>
            <div class="modal-footer">
                <button type="button" id="delete_activity_close" class="btn btn-secondary close--btn" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary delete--btn" id="delete_activity" name="delete_activity" onclick="deleteDraftedActivities({{!empty($statement) && isset($statement['id']) ? $statement['id'] : ''}}, {{json_encode($statement['start_date'])}}, {{json_encode($statement['end_date'])}})">Delete</button>
            </div>
        </div>
    </div>
</div>
<!-- Delete finalised activity warning pop-up ends-->


<!-- END PAGE CONTAINER-->

@include('includes.footer')
</body>

</html>


<script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>


<script>

$(function() {
    <?php if(!empty($statement) && isset($statement['activity_statement_status']) && $statement['activity_statement_status'] == 'finalise'){?>
        $("#activity_statement_form :input").prop('disabled', true);
        $("#delete_activity_btn").prop('disabled', false);
        $("#delete_activity_backbtn").prop('disabled', false);
    <?php } ?>
});

function gstCalculation(id)
{
    
    let paygw1 = $("#paygw_w1").val() ? Number($("#paygw_w1").val()).toFixed(2) : 0;
    let paygw_no_abn_quoted = $("#paygw_no_abn_quoted").val() ? Number($("#paygw_no_abn_quoted").val()).toFixed(2) : 0;
    let paygw_others = $("#paygw_others").val() ? Number($("#paygw_others").val()).toFixed(2) : 0;

    let total_paygw_w2_w3_w4 = (parseFloat(paygw1) + parseFloat(paygw_no_abn_quoted) + parseFloat(paygw_others)).toFixed(2);

    $("#total_paygw_w2_w3_w4").val(total_paygw_w2_w3_w4);
    $("#payg_tax_withheld").val(total_paygw_w2_w3_w4);

    let instalment_t7 = 0; let quarterly_varied_amount_t9 = 0; let gst_on_saless = 0; let gst_on_purchase = 0; let income_tax_instalment_t11 = 0; let payg_income_tax_instalment_5a = 0;
    
    let payg_income_tax_t7_t9 = parseFloat($("#quarterly_varied_amount_t9").val()) > 0 ? parseFloat($("#quarterly_varied_amount_t9").val()).toFixed(2) : (parseFloat($("#instalment_t7").val()) > 0 ? parseFloat($("#instalment_t7").val()).toFixed(2) : 0);
    
    let payg_income_tax_instalment_credit = parseFloat($("#payg_income_tax_instalment_credit").val()) > 0 ? parseFloat($("#payg_income_tax_instalment_credit").val()).toFixed(2) : 0;
    
    gst_on_saless = $("#gst_on_sales").val() >= 0 ? parseFloat($("#gst_on_sales").val()).toFixed(2) : 0;
    
    gst_on_purchase = $("#gst_on_purchase").val() >= 0 ? parseFloat($("#gst_on_purchase").val()).toFixed(2) : 0;
    
    let instalment_t1 = $("#instalment_t1").val() >= 0 ? parseFloat($("#instalment_t1").val()).toFixed(2) : 0;
    
    let instalment_rate_percentage_t2 = $("#instalment_rate_percentage_t2").val() >0 ? parseFloat($("#instalment_rate_percentage_t2").val()).toFixed(2) : 0;

    let varied_rate_percentage_t3 = $("#varied_rate_percentage_t3").val() >0 ? parseFloat($("#varied_rate_percentage_t3").val()).toFixed(2) : 0;

    if(instalment_t1 >= 0)
    {
        income_tax_instalment_t11 = varied_rate_percentage_t3 > 0 ? (instalment_t1 * varied_rate_percentage_t3 / 100) : (instalment_rate_percentage_t2 > 0 ? instalment_t1 * instalment_rate_percentage_t2 / 100 : 0);

        $("#income_tax_instalment_t11").val(parseFloat(income_tax_instalment_t11).toFixed(2));        
    }

    $("#payg_income_tax_instalment_5a").val((parseFloat(income_tax_instalment_t11) + parseFloat(payg_income_tax_t7_t9)).toFixed(2));

    payg_income_tax_instalment_5a = $("#payg_income_tax_instalment_5a").val() >= 0 ? $("#payg_income_tax_instalment_5a").val() : 0;

    var total_owed_to_atos = (parseFloat(gst_on_saless) + parseFloat(total_paygw_w2_w3_w4) + parseFloat(payg_income_tax_instalment_5a)).toFixed(2);

    $("#total_owed_to_ato").val(parseFloat(total_owed_to_atos).toFixed(2));

    let total_owed_by_ato = (parseFloat(gst_on_purchase) + parseFloat(payg_income_tax_instalment_credit)).toFixed(2);
    $("#total_owed_by_ato").val(total_owed_by_ato);


    var payment = 0;
    if(parseFloat(total_owed_by_ato) >= parseFloat(total_owed_to_atos))
    {
        payment = (parseFloat(total_owed_by_ato) - parseFloat(total_owed_to_atos)).toFixed(2);

        $("#payment_type_span_text").text('Refund');
        $("#payment_type_sub_text").text('Your Refund Amount');
        $("#payment_type_text").text('Amount Refundable');
        $("#payment_type").val(1);
    }
    else if(parseFloat(total_owed_to_atos) > parseFloat(total_owed_by_ato))
    {
        payment = (parseFloat(total_owed_to_atos) - parseFloat(total_owed_by_ato)).toFixed(2);

        $("#payment_type_span_text").text('Payment');
        $("#payment_type_sub_text").text('Your Payment Amount');
        $("#payment_type_text").text('Amount Payable');
        $("#payment_type").val(0);
    }

    $("#payment_amount").val(payment);
    $("#payment_amount_header").text(payment);

}


$(function() {

    let $body = $(this);

    $("#finalise_activity").click(function () {
        $('.close').click();
        submitForm('finalise');
    });

    $("#adjustment_g1_btn").click(function () {
        
        var adjustment_g1_index = $("#adjustment_g1").val();
        adjustment_g1_index = JSON.parse(adjustment_g1_index);

        let adjustment_g1_amount = $("#adjustment_g1_amount").val();
        let adjustment_g1_reason = $("#adjustment_g1_reason").val();
        var total_sales_g1 = Number($("#total_sales_g1").val().replace(/\,/g,'')).toFixed(2);

        let total_sales_g1_g = (parseFloat(total_sales_g1) + parseFloat(adjustment_g1_amount)).toFixed(2);
        $("#total_sales_g1").val(total_sales_g1_g);
        let new_object = { adjust_by: adjustment_g1_amount, reason: adjustment_g1_reason };

        adjustment_g1_index.push(new_object);
        
        $("#adjustment_g1").val(JSON.stringify(adjustment_g1_index));
        $('.close').click();
        
        submitForm('draft');
    });

    $("#adjustment_1a_btn").click(function () {
        
        let adjustment_1a_index = $("#adjustment_1a").val();
        adjustment_1a_index = JSON.parse(adjustment_1a_index);

        let adjustment_1a_amount = $("#adjustment_1a_amount").val();
        let adjustment_1a_reason = $("#adjustment_1a_reason").val();
        var total_sales_1a = Number($("#gst_on_sales").val().replace(/\,/g,'')).toFixed(2);

        total_sales_1a = (parseFloat(total_sales_1a) + parseFloat(adjustment_1a_amount)).toFixed(2);
        $("#gst_on_sales").val(total_sales_1a);

        gstCalculation(null);

        let new_object = { adjust_by: adjustment_1a_amount, reason: adjustment_1a_reason };

        adjustment_1a_index.push(new_object);
        $("#adjustment_1a").val(JSON.stringify(adjustment_1a_index));
        $('.close').click();

        submitForm('draft');
    });

    $("#adjustment_1b_btn").click(function () {
        
        let adjustment_1b_index = $("#adjustment_1b").val();
        adjustment_1b_index = JSON.parse(adjustment_1b_index);

        let adjustment_1b_amount = $("#adjustment_1b_amount").val();
        let adjustment_1b_reason = $("#adjustment_1b_reason").val();
        var gst_on_purchase_1b = Number($("#gst_on_purchase").val().replace(/\,/g,'')).toFixed(2);

        gst_on_purchase_1b = (parseFloat(gst_on_purchase_1b) + parseFloat(adjustment_1b_amount)).toFixed(2);
        $("#gst_on_purchase").val(gst_on_purchase_1b);

        gstCalculation(null);

        let new_object = { adjust_by: adjustment_1b_amount, reason: adjustment_1b_reason };

        adjustment_1b_index.push(new_object);
        $("#adjustment_1b").val(JSON.stringify(adjustment_1b_index));
        $('.close').click();

        submitForm('draft');
    });
});

function submitForm(activity_statement_status)
{

    var data = $('form').serializeArray().reduce(function(obj, item) {
        obj[item.name] = item.value;

        item.name == 'activity_statement_status' ? obj[item.name] = activity_statement_status : '';

        return obj;
    }, {});

    $body.find('#pre-loader').show();

    $.ajaxSetup({headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }});
    $.ajax({
        method: 'post',
        url: "/bas/statement",
        data: data,
        success: function(data){
            $body.find('#pre-loader').hide();
            $('.alert-success').attr('style','display: block');
            
            if(data.statement.activity_statement_status == 'finalise')
            {
                $("#draft_finalise_btn_div").attr('style','display: none');
                $("#delete_btn_div").attr('style','display: block');
                $("#activity_statement_form :input").prop('disabled', true);
                $("#delete_activity_btn").prop('disabled', false);
                $("#delete_activity_backbtn").prop('disabled', false);
            }
            $("#adjustments").empty();
            
            const formatter = new Intl.DateTimeFormat('en-US', { day: '2-digit', month: '2-digit', year: 'numeric' });

            if(data.adjustments['adjustment_g1'] && (data.adjustments['adjustment_g1']).length > 0)
            {
                $("#adjustment_g1").val(JSON.stringify(data.adjustments['adjustment_g1']));

                $.each(data.adjustments['adjustment_g1'],function(key,value){

                    const date = new Date(value['created_at']);
                    const formattedDate = formatter.format(date);

                    $("#adjustments").append(
                        '<tr><td><b>Adjustment</b> to Total sales (G1) of $'+value['adjust_by']+' by {{ $userinfo[1] }}</td>\
                            <td>'+formattedDate+'</td></tr>'
                    );
                });
            }
            

            if(data.adjustments['adjustment1_a'] && (data.adjustments['adjustment1_a']).length > 0)
            {
                $("#adjustment_1a").val(JSON.stringify(data.adjustments['adjustment1_a']));

                $.each(data.adjustments['adjustment1_a'],function(key,value){
                   
                    const date = new Date(value['created_at']);
                    const formattedDate = formatter.format(date);

                    $("#adjustments").append(
                        '<tr><td><b>Adjustment</b> to GST on sales (1A) of $'+value['adjust_by']+' by {{ $userinfo[1] }} </td>\
                            <td>'+formattedDate+'</td></tr>'
                    );
                });
            }


            if(data.adjustments['adjustment1_b'] && (data.adjustments['adjustment1_b']).length > 0)
            {
                $("#adjustment_1b").val(JSON.stringify(data.adjustments['adjustment1_b']));

                $.each(data.adjustments['adjustment1_b'],function(key,value){

                    const date = new Date(value['created_at']);
                    const formattedDate = formatter.format(date);

                    $("#adjustments").append(
                        '<tr><td><b>Adjustment</b> to GST on purchases (1B) of $'+value['adjust_by']+' by {{ $userinfo[1] }}</td>\
                            <td>'+formattedDate+'</td></tr>'
                    );
                });
            }
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

function deleteDraftedActivities(activity_id, start_date, end_date){
    $("#delete_activity_close").click();
    if (activity_id && start_date && end_date)  {
        $body.find('#pre-loader').show();
        data = {activity_id: activity_id, start_date: start_date, end_date: end_date};

        $.ajaxSetup({headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }});
        $.ajax({
            method: 'delete',
            url: "/bas/statement/delete",
            data: data,
            success: function(data){
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
    } else {
        console.log("error!");
    }
}

</script>
<!-- end document-->