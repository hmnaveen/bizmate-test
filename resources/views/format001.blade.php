<html>
<head>
</head>
<body style="max-width:800px;">
    <h1>Invoice</h1>
    @if (!empty($inv['logo'])) 
    <img src="{{ storage_path('app/public/uploads/a71ed73925a75dae44b71bc161131adb.png') }}" style="width: 200px; height: 200px">

    <br><br>
     @endif
    <table style="width: 100%;">
        <td style="vertical-align:top;">
            <p>
            <strong><i>Client:</i></strong><br>
            <strong>{{ $inv['client_name'] }}</strong><br>
            {{ $inv['client_email'] }}<br>
            @if (!empty($inv['client_phone'])) {{ $inv['client_phone'] }}<br> @endif
            @if (!empty($inv['client_address'])) {!! nl2br(e($inv['client_address'])) !!} <br> @endif
            </p>
        </td>
        <td style="vertical-align:top;">
            <p>
                <strong><i>From:</i></strong><br>
                <strong>{{ $inv['invoice_name'] }}</strong><br>
                {{ $inv['invoice_email'] }}<br>
                @if (!empty($inv['invoice_phone'])) {{ $inv['invoice_phone'] }}<br> @endif
            </p>
        </td>
        <td style="vertical-align:top;">
            <p>
                <b>Invoice Number:</b> {{ str_pad($inv['transaction_id'], 10, '0', STR_PAD_LEFT) }}<br>
                <b>Invoice Date:</b> {{ date('m/d/Y', strtotime($inv['invoice_date'])) }}<br>
                @if (!empty($inv['invoice_due_date'])) <b>Due Date:</b> {{ date('m/d/Y', strtotime($inv['invoice_due_date'])) }}<br> @endif 
            </p>
        </td>
    </table>
    
    <h3>Invoice details</h3>
    <table style="width: 100%;">
        <thead>
            <tr>
                <th scope="col" style="min-width:50px">Item</th>
                <th scope="col" style="min-width:50px">Qty</th>
                <th scope="col" style="min-width:50px">Description</th>
                <th scope="col" style="min-width:50px">Unit Price</th>
                <th scope="col" style="min-width:50px">Tax Rate</th>
                <th scope="col" style="min-width:50px">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($inv['inv_parts'] as $ip)
            <tr>
                <td>{{ !empty($ip['invoice_parts_quantity']) ? $ip['invoice_parts_quantity'] : '-' }}</td>
                <td>{!! nl2br(e($ip['invoice_parts_description'])) !!}</td>
                <td style="">{{ !empty($ip['invoice_parts_unit_price']) ? '$'.number_format($ip['invoice_parts_quantity'], 2, ".", ",") : '-' }}</td>
                <td style="">{{ '$'.number_format($ip['invoice_parts_amount'], 2, ".", ",") }}</td>
                <td style="">{{ '$'.number_format($ip['invoice_parts_amount'], 2, ".", ",") }}</td>
                <td style="">{{ '$'.number_format($ip['invoice_parts_amount'], 2, ".", ",") }}</td>
            </tr>
            @endforeach
            
            <tr class="invoice-separator">
                                        <td colspan="5">hs</td>
                                    </tr>

                                    <tr class="invoice-total--subamount">
                                        <td colspan="2" rowspan="3"></td>
                                        <td>Subtotal (excl GST)</td>
                                        <td colspan="2">
                                           
                                        </td>
                                    </tr>

                                    <tr class="invoice-total--gst">
                                        <td id="invoice_total_gst_text" >Total GST</td>
                                        <td colspan="2">
                                        </td>
                                    </tr>

                                    <tr class="invoice-total--amountdue">
                                        <td><strong>Amount Due</strong></td>
                                        <td colspan="2">
                                            <strong id="grandtotal"></strong>
                                        </td>
                                    </tr>
            
        </tbody>
    </table>
    <br><br><br>
    <div class="footer">{!! nl2br(e($inv['invoice_terms'])) !!}</div>
    @if (!empty($inv['pdfpreview']))
    <small><a href="/pdf/{{$inv['invoice_pdf']}}" target="_blank">Invoice link: {{$inv['invoice_pdf']}}</a></small>
    @endif
</body>
</html>