    <div class="row">
        <div class="col-sm-6 col-md-6 col-xl-6">
            <h3 style="padding:10px">Profit and Loss</h3>
        </div>
    </div>
    <br>
    <table class="invoice_list">
        <thead>
            
            <tr>
                <th style="font-weight:bold"> <h5>Account</h5> </th>
                @foreach($items as $item)
                    <th style="font-weight:bold">
                        <h5>{{$item['display_date']}}</h5>
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <tr><td colspan="<?php echo sizeof($items) + 1 ?>"></td></tr>
            <tr>
                <th style="font-weight:bold"><h5>Sales</h5></th>
            </tr>
            @foreach($items_metadata['invoice'] as $particular_name)
                <tr>
                    <td>
                        {{ $particular_name }}
                    </td>

                    @foreach($items as $item)
                        <?php
                            $total_particular = 0;$account_id='';$start_date='';$end_date='';
                            $transaction_type_index = array_search('invoice', array_column($item['transactions'], 'transaction_type'));

                            if($transaction_type_index !== false){
                                $accounts_index = array_search($particular_name, array_column($item['transactions'][$transaction_type_index]['accounts'], 'chart_accounts_particulars_name'));
                                if($accounts_index !== false){
                                    $start_date = $item['transactions'][$transaction_type_index]['accounts'][$accounts_index]['start_date'];
                                    $end_date = $item['transactions'][$transaction_type_index]['accounts'][$accounts_index]['end_date'];
                                    
                                    $account_id = $item['transactions'][$transaction_type_index]['accounts'][$accounts_index]['account_id'];
                                    $particulars = $item['transactions'][$transaction_type_index]['accounts'][$accounts_index]['particulars'];

                                    if(!empty($particulars)){
                                        foreach($particulars as $particular){
                                            $total_particular += $particular['parts_amount'];
                                        }
                                    }
                                }
                            }
                        ?>
                        <td data-format="0.00" style="text-align: right;">
                            {{$total_particular == 0 ? '-' : number_format($total_particular, 2)}}
                        </td>
                    @endforeach
                </tr>
            @endforeach
            

            <tr>
                <td><b>Total Sales</b></td>
                
                @foreach($items as $item)
                    <td  data-format="0.00" style="text-align: right;">
                        <b>{{ $item['total_invoice'] ? number_format($item['total_invoice'], 2) : '' }}</b>                                      
                    </td>
                @endforeach
            </tr>

    <!-------------------Invoice ends -----------------------------------------> 

        <tr><td colspan="<?php echo sizeof($items) + 1 ?>"></td></tr>

    <!-------------------Expense Starts ----------------------------------------->                    
            <tr>
                <th style="font-weight:bold"><h5>Operating Expenses</h5></th>
            </tr>
            @foreach($items_metadata['expense'] as $particular_name)
                <tr>
                    <td>
                        {{$particular_name}}
                    </td>

                    @foreach($items as $item)
                        <?php
                            $total_particular = 0;
                            $transaction_type_index = array_search('expense', array_column($item['transactions'], 'transaction_type'));

                            if($transaction_type_index !== false){
                                $accounts_index = array_search($particular_name, array_column($item['transactions'][$transaction_type_index]['accounts'], 'chart_accounts_particulars_name'));
                                if($accounts_index !== false){
                                    $start_date = $item['transactions'][$transaction_type_index]['accounts'][$accounts_index]['start_date'];
                                    $end_date = $item['transactions'][$transaction_type_index]['accounts'][$accounts_index]['end_date'];
                                    
                                    $account_id = $item['transactions'][$transaction_type_index]['accounts'][$accounts_index]['account_id'];
                                    $particulars = $item['transactions'][$transaction_type_index]['accounts'][$accounts_index]['particulars'];

                                    if(!empty($particulars)){
                                        foreach($particulars as $particular){
                                            $total_particular += $particular['parts_amount'];
                                        }
                                    }
                                }
                            }
                        ?>
                        <td data-format="0.00" style="text-align: right;">
                            {{$total_particular == 0 ? '-' : number_format($total_particular, 2)}}

                        </td>
                    @endforeach
                </tr>
            @endforeach

            <tr>
                <td><b>Total Operating Expenses</b></td>
                
                @foreach($items as $item)
                    <td data-format="0.00" style="text-align: right;">
                        <b>{{ $item['total_expense'] ? number_format($item['total_expense'], 2) : '' }}</b>                                      
                    </td>
                @endforeach
            </tr>
        <!-------------------Expense ends -----------------------------------------> 
            <tr><td colspan="<?php echo sizeof($items) + 1 ?>"></td></tr>
            <tr>
                <th><h5>Total Net Profit</h5></th>
                
                @foreach($items as $item)
                    <td data-format="0.00" style="text-align: right;">
                        <b>{{ $item['total_net_profit'] ? number_format($item['total_net_profit'], 2) : '' }}</b>                                      
                    </td>
                @endforeach
            </tr>
        </tbody>
    </table>
