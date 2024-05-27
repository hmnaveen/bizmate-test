<html>
  <head>
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
      
      <style>
        .invoice-header {
          background-color: #f8f9fa;
          width: 100%;
        }

        .header--title {
          vertical-align: middle;
          text-align: right;
          padding-right: 40px;
          font-size: 35px;
        }

        .header--logo {
          padding: 15px;
        }

        .invoice-info {
          background-color: #ffffff;
          padding: 20px;
          border: 1px solid #e1e1e1;
        }
        .invoice-details {
          background-color: #f8f9fa;
          padding: 20px;
          border: 1px solid #e1e1e1;
        }
        .invoice-footer {
          background-color: #ffffff;
          padding: 20px;
          text-align: right;
          border-top: 1px solid #e1e1e1;
        }

        table {
          font-size: 0.8rem;
        }

        .due--footer {
          font-size: 18px
        }

        .footer {
          font-size: 12px
        }

      </style>
  </head>
    <body>

      <table class="invoice-header">
        <tr>
          <td class="header--logo">
            @if (!empty($inv['logo'])) <img src="{{ $inv['logobase64'] }}" width="150"> @endif
          </td>
          <td class="header--title">
            <b>INVOICE</b>
          </td>
        <tr>
      </table>


      <table style="width: 100%; margin-bottom: 30px;">
        <td style="vertical-align:top; width: 33%;" class="invoice-info">
              <strong style="display: block;">Client:</strong>
              <strong style="display: block;">{{ $inv['client_name'] }}</strong>
                {{ $inv['client_email'] }}<br>
                @if (!empty($inv['client_phone'])) {{ $inv['client_phone'] }}<br> @endif
                <!-- @if (!empty($inv['client_address'])) {!! nl2br(e($inv['client_address'])) !!}  @endif -->
            
        </td>
        <td style="vertical-align:top; width: 33%;" class="invoice-info">
            <strong style="display: block;">From:</strong>
            <strong style="display: block;">{{ $inv['invoice_name'] }}</strong>
              {{ $inv['invoice_email'] }}<br>
              @if (!empty($inv['invoice_phone'])) {{ $inv['invoice_phone'] }}<br> @endif
              {{ $inv['invoice_address'] }}<br>
        </td>
        <td style="vertical-align:top;  width: 33%;" class="invoice-info">
            <b>Invoice Number:</b> {{ 'INV-'.str_pad($inv['invoice_number'], 6, '0', STR_PAD_LEFT) }}<br>
            <b>Invoice Date:</b> {{ $inv['invoice_date'] }}<br>
            @if (!empty($inv['invoice_due_date'])) <b>Due Date:</b> {{ $inv['invoice_due_date'] }} @endif
        </td>
      </table>

      <div class="invoice-details">
        <table class="table table-bordered">
          <thead>
              <tr>
              <th>Item</th>
              <th>Qty</th>
              <th>Description</th>
              <th>Unit Price</th>
              <th>Tax Rate</th>
              <th>Amount</th>
              </tr>
          </thead>
          <tbody>
              @foreach ($inv['inv_parts'] as $ip)
              
              <tr>
                <td>{{ $ip['parts_name'] }}</td>
                <td>{{ !empty($ip['parts_quantity']) ? $ip['parts_quantity'] : '-' }}</td>
                <td>{!! nl2br(e($ip['parts_description'])) !!}</td>
                <td>{{ !empty($ip['parts_unit_price']) ? '$'.number_format($ip['parts_unit_price'], 2, ".", ",") : 0}}</td>
                <td>{{ $ip['invoice_tax_rates']['tax_rates'].'%' }}</td>
                <td>{{ '$'.number_format($ip['parts_amount'], 2, ".", ",") }}</td>
              </tr>
              @endforeach
          </tbody>
        </table>
      </div>
      
      <div class="invoice-details" style="margin: 30px 0px 10px;">
        <table class="table table-bordered">
          <tbody>
            <tr>
              <td style="text-align: right">Subtotal (excl GST)</td>
              <td style="text-align: right">{{'$'.number_format($inv['invoice_sub_total'], 2, ".", ",") }}</td>
            </tr>
            <tr>
              <td style="text-align: right">Total GST</td>
              <td style="text-align: right">{{'$'.number_format($inv['invoice_total_gst'], 2, ".", ",") }}</td>
            </tr>
            <tr>
              <td style="text-align: right"><strong>Total:</strong></td>
              <td style="text-align: right"><strong>{{'$'.number_format($inv['invoice_total_amount'], 2, ".", ",") }}</strong></td>
            </tr>
          </tbody>
        </table>
      </div>

      @if (!empty($inv['invoice_due_date']))
        <div class="due--footer"><strong>Due Date: {{ $inv['invoice_due_date'] }}</strong></div>
      @endif
      

      <div class="footer">
        Additional notes and payment information (e.g Bank Account)<br>
        {!! nl2br(e($inv['invoice_terms'])) !!}
      </div>

      @if (!empty($inv['pdfpreview']))
        <small><a href="/pdf/{{$inv['invoice_pdf']}}" target="_blank">Invoice link: {{$inv['invoice_pdf']}}</a></small>
      @endif
  </body>
</html>