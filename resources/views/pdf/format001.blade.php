<html>
    <head>
    </head>
    <body>
        <h1>Invoice</h1>
        @if (!empty($inv['logo'])) 
            <img src="{{ $inv['logobase64'] }}" style="width: 150px; margin: 20px 0px;">
        @endif

        <table style="width: 100%; font-size: 0.9rem;">
            <td style="vertical-align:top;">
                <p>
                    <strong style="display: block;">Client:</strong>
                    <strong style="display: block;">{{ $inv['client_name'] }}</strong>
                    {{ $inv['client_email'] }}<br>
                    @if (!empty($inv['client_phone'])) {{ $inv['client_phone'] }}<br> @endif
                    <!-- @if (!empty($inv['client_address'])) {!! nl2br(e($inv['client_address'])) !!} <br> @endif -->
                </p>
            </td>
            <td style="vertical-align:top;">
                <p style="padding: 0px 5px;">
                    <strong style="display: block;">From:</strong>
                    <strong style="display: block;">{{ $inv['invoice_name'] }}</strong>
                    {{ $inv['invoice_email'] }}<br>
                    @if (!empty($inv['invoice_phone'])) {{ $inv['invoice_phone'] }}<br> @endif
                    {{ $inv['invoice_address'] }}<br>
                </p>
            </td>
            <td style="vertical-align:top;">
                <p>
                    <b>Invoice Number:</b> {{ 'INV-'.str_pad($inv['invoice_number'], 6, '0', STR_PAD_LEFT) }}<br>
                    <b>Invoice Date:</b> {{ $inv['invoice_date'] }}<br>
                    @if (!empty($inv['invoice_due_date'])) <b>Due Date:</b> {{ $inv['invoice_due_date'] }} @endif 
                </p>
            </td>
        </table>

        <h3>Invoice details</h3>

        <table style="width: 100%; margin-bottom: 80px; font-size: 0.9rem;">
            <thead>
                <tr>
                    <th scope="col" style="min-width:200px">Item</th>
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
                        <td style="text-align: center; padding: 5px;">{{ $ip['parts_name'] }}</td>
                        <td style="text-align: center; padding: 5px;">{{ !empty($ip['parts_quantity']) ? $ip['parts_quantity'] : '-' }}</td>
                        <td style="text-align: center; padding: 5px;">{!! nl2br(e($ip['parts_description'])) !!}</td>
                        <td style="text-align: center; padding: 5px;">{{ !empty($ip['parts_unit_price']) ? '$'.number_format($ip['parts_unit_price'], 2, ".", ",") : 0 }}</td>
                        <td style="text-align: center; padding: 5px;">{{ $ip['invoice_tax_rates']['tax_rates'].'%' }}</td>
                        <td style="text-align: center; padding: 5px;">{{ '$'.number_format($ip['parts_amount'], 2, ".", ",") }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="6" style="height: 30px;">&nbsp;</td>
                </tr>

                <tr>
                    <td colspan="4" rowspan="3">&nbsp;</td>
                    <td style="text-align: right; padding: 5px;">
                        Subtotal (excl GST)
                    </td>
                    <td style="text-align: center; padding: 5px;">
                        {{'$'.number_format($inv['invoice_sub_total'], 2, ".", ",") }}
                    </td>
                </tr>

                <tr>
                    <td style="text-align: right; padding: 5px;">
                        Total GST
                    </td>
                    <td style="text-align: center; padding: 5px;">
                    {{'$'.number_format($inv['invoice_total_gst'], 2, ".", ",") }}
                    </td>
                </tr>

                <tr>
                    <td style="text-align: right; padding: 5px; font-size: 1.2rem;">
                        <strong>Amount Due</strong>
                    </td>
                    <td style="text-align: center; padding: 5px; font-size: 1.2rem;">
                        <strong>
                            {{'$'.number_format($inv['invoice_total_amount'], 2, ".", ",") }}
                        </strong>
                    </td>
                </tr>
            </tbody>
        </table>
    
        @if (!empty($inv['invoice_due_date']))
            <h4>Due Date: {{ $inv['invoice_due_date'] }}</h4>
        @endif
        
        <div style="font-size: 12px; margin: 10px 0px;">Additional notes and payment information (e.g Bank Account)</div>

        <div style="background: #fafafa; padding: 10px; margin-bottom: 10px;">{!! nl2br(e($inv['invoice_terms'])) !!}</div>
        
        @if (!empty($inv['pdfpreview']))
            <small><a href="/pdf/{{$inv['invoice_pdf']}}" target="_blank">Invoice link: {{$inv['invoice_pdf']}}</a></small>
        @endif

    </body>
</html>