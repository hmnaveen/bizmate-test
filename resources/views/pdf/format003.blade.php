<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

  <style>

    .invoice-header {
      background-color: #f8f9fa;
      width: 100%;
    }

    .header--title {
      vertical-align: middle;
      padding: 20px 30px;
      font-size: 45px;
    }

    .header--logo {
      padding: 15px 20px;
      text-align: right;
    }

    table {
      font-size: 0.8rem;
      width: 100%;
    }

    .table--summary td {
      padding: 10px 15px;
    }

    .due--footer {
      margin-top: 20px;
    }

    .footer {
      font-size: 12px;
    }
  </style>
</head>
<body>
    
    <table class="invoice-header">
      <tr>
        <td class="header--title">
          <b>INVOICE</b>
        </td>
        <td class="header--logo">
          @if (!empty($inv['logo'])) <img src="{{ $inv['logobase64'] }}" width="100"> @endif
        </td>
        
      <tr>
    </table>

    <table style="width: 100%; margin-top: 30px;">
      <tr>
        <td style="vertical-align:top;">
            <p>
              <strong><i>Client:</i></strong><br>
              <strong>{{ $inv['client_name'] }}</strong><br>
                {{ $inv['client_email'] }}<br>
                @if (!empty($inv['client_phone'])) {{ $inv['client_phone'] }}<br> @endif
                <!-- @if (!empty($inv['client_address'])) {!! nl2br(e($inv['client_address'])) !!}  @endif -->
            </p>
        </td>
        <td style="vertical-align:top;">
            <p>
              <strong><i>From:</i></strong><br>
              <strong>{{ $inv['invoice_name'] }}</strong><br>
                {{ $inv['invoice_email'] }}<br>
                @if (!empty($inv['invoice_phone'])) {{ $inv['invoice_phone'] }}<br> @endif
                {{ $inv['invoice_address'] }}<br>
            </p>
        </td>
        <td style="vertical-align:top; ">
            <p>
            <b>Invoice Number:</b> {{ 'INV-'.str_pad($inv['invoice_number'], 6, '0', STR_PAD_LEFT) }}<br>
            <b>Invoice Date:</b> {{ $inv['invoice_date'] }}<br>
            @if (!empty($inv['invoice_due_date'])) <b>Due Date:</b> {{ $inv['invoice_due_date'] }}<br> @endif
            </p>
        </td>
    </tr>
    </table>


    <table class="table table-striped" style="margin: 30px 0px;">
      <thead>
        <tr>
          <th>Item</th>
          <th>Qty</th>
          <th>Description</th>
          <th>Unit price</th>
          <th>Tax rate</th>
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

    <table>
      <tbody>
        <tr>
          <td style="width: 50%">&nbsp;</td>
          <td>
            <table class="table-bordered table--summary">
              <tbody>
                <tr>
                  <td>Subtotal (excl GST)</td>
                  <td style="text-align: center;">{{'$'.number_format($inv['invoice_sub_total'], 2, ".", ",") }}</td>
                </tr>
                <tr>
                  <td>Total GST</td>
                  <td style="text-align: center">{{'$'.number_format($inv['invoice_total_gst'], 2, ".", ",") }}</td>
                </tr>
                <tr>
                  <td><strong>Total:</strong></td>
                  <td style="text-align: center"><strong>{{'$'.number_format($inv['invoice_total_amount'], 2, ".", ",") }}</strong></td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
      </tbody>
    </table>

    @if (!empty($inv['invoice_due_date']))
    <div class="due--footer">
      <strong> Due Date: {{ $inv['invoice_due_date'] }} </strong>
    </div>
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
