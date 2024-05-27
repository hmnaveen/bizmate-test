<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="X-UA-Compatible" content="ie=edge">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

		<style>


			table {
				width: 100%;
				font-size: 14px;
			}

			.header--table td {
				vertical-align: top;
			}


			.header--table td.company--logo img {
				max-height: 150px !important;
				padding: 20px;
				padding-left: 40px;
			}

			.header--table td.invoice--section {
				padding: 10px;
				padding-right: 20px;
			}

			td.invoice--section .invoice--title {
				font-size: 40px;
			}

			td.invoice--section .invoice--due,
			.due--date {	
				position: relative;
				background: #f9dddf;
				display: inline;
				padding: 5px 10px;
				top: 10px;
			}

			.due--date {
				top: 0px;
				display: inline-block;
				margin-bottom: 10px;
				font-size: 17px;
			}

			.address--table {
				margin-top: 30px;
				border-collapse: collapse;
			}

			.address--table td {
				padding: 10px 20px;
				vertical-align: top;
			}

			.address--title {
				font-size: 12px;
			}

			.address--name {
				font-size: 18px;
			}

			.particulars--table {
				margin-top: 40px;
			}

			.particulars--table thead th {
				border-top: 2px solid #000;
				border-bottom: 1px solid #e2e2e2;
				text-align: center;
				padding: 10px 15px;
				font-size: 16px;
			}

			.particulars--table tbody td {
				text-align: center;
				padding: 10px 15px;
			}

			.particulars--table tbody.items tr {
				border-bottom: 1px solid #e2e2e2;
			}

			.particulars--table tbody.items td:nth-child(3) {
				text-align: left;
			}

			.particulars--table tbody.items tr:last-child {
				border-bottom: 2px solid #000;
			}

			.particulars--table tbody.items tr:last-child td {
				padding-bottom: 20px;
			}

			.table--deets {
				margin-top: 30px;
			}

			.extra--deets {
				font-size: 12px;
			}

			.grand--total {
				font-size: 18px;
			}



		</style>

	</head>
	<body>
		<table class="header--table">
			<tr>
				<td width="70%" class="company--logo">
					@if (!empty($inv['logo'])) <img src="{{ $inv['logobase64'] }}">@endif
				</td>
				<td class="invoice--section">
					<div class="invoice--title"><strong>INVOICE</strong></div>
					<div class="invoice--number">Invoice Number: <b>{{ 'INV-'.str_pad($inv['invoice_number'], 6, '0', STR_PAD_LEFT) }}</b></div>	
					<div class="invoice--date">Invoice Date: <b>{{ $inv['invoice_date'] }}</b></div>	
					@if (!empty($inv['invoice_due_date'])) <div class="invoice--due"><b>Due Date: {{ $inv['invoice_due_date'] }}</b></div> @endif
					</p>
				</td>
			</tr>
		</table>

		<table class="address--table">
			<tr>
				<td width="50%">
					<div class="address--title">Client</div>
					<div class="address--name"><strong>{{ $inv['client_name'] }}</strong></div>
					<div class="address--email">{{ $inv['client_email'] }}</div>
					@if (!empty($inv['client_phone'])) <div class="address--number">{{ $inv['client_phone'] }}</div> @endif
                	<!-- @if (!empty($inv['client_address'])) <div class="address--address">{!! nl2br(e($inv['client_address'])) !!}</div>  @endif -->
				</td>
				<td>
					<div class="address--title">From</div>
					<div class="address--name"><strong>{{ $inv['invoice_name'] }}</strong></div>
					<div class="address--email">{{ $inv['invoice_email'] }}</div>
					@if (!empty($inv['invoice_phone'])) <div class="address--number">{{ $inv['invoice_phone'] }}</div> @endif
					<div class="address--address">{{ $inv['invoice_address'] }}</div>
				</td>
			</tr>
		</table>

		<table class="particulars--table">
			<thead>
				<tr>
					<th>Item</th>
					<th>Qty</th>
					<th style="text-align: left;">Description</th>
					<th>Unit price</th>
					<th>Tax rate</th>
					<th>Amount</th>
				</tr>
			</thead>
			<tbody class="items">
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

		<table class="table--deets">
			<tbody>
				<tr>
					<td width="60%" rowspan="4">
						<div class="extra--deets">

							@if (!empty($inv['invoice_due_date'])) <div class="due--date"><strong>Due Date: {{ $inv['invoice_due_date'] }} </strong></div>@endif
							
							<div class="invoice--terms">
								Additional notes and payment information (e.g Bank Account)
								<br>{!! nl2br(e($inv['invoice_terms'])) !!}
							</div>
							
							@if (!empty($inv['pdfpreview'])) <div class="invoice--link">Invoice link: <a href="/pdf/{{$inv['invoice_pdf']}}" target="_blank"> {{$inv['invoice_pdf']}}</a></div>  @endif
						</div>
					</td>
				</tr>
				
				<tr>
					<td colspan="2" style="text-align: right; padding: 0px;">Subtotal (excl GST)</td>
					<td style="text-align: center; padding: 0px;">{{'$'.number_format($inv['invoice_sub_total'], 2, ".", ",") }}</td>
				</tr>
				<tr>
					<td colspan="2" style="text-align: right; padding: 0px;">Total GST</td>
					<td style="text-align: center; padding: 0px;">{{'$'.number_format($inv['invoice_total_gst'], 2, ".", ",") }}</td>
				</tr>
				<tr class="grand--total">
					<td colspan="2" style="text-align: right;"><strong>Total</strong></td>
					<td style="text-align: center;"><strong>{{'$'.number_format($inv['invoice_total_amount'], 2, ".", ",") }}</strong></td>
				</tr>
			</tbody>
		</table>

		
	</body>
</html>
