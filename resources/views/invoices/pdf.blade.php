<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->no }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
        }
        .container {
            padding: 40px;
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            border-bottom: 3px solid #059669;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #059669;
            font-size: 32px;
            margin-bottom: 5px;
        }
        .header .company-info {
            color: #666;
            font-size: 11px;
        }
        .invoice-meta {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .invoice-meta .left,
        .invoice-meta .right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .invoice-meta .right {
            text-align: right;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #059669;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .info-block {
            margin-bottom: 20px;
        }
        .info-block p {
            margin: 3px 0;
        }
        .info-block .label {
            color: #666;
            font-weight: normal;
        }
        .info-block .value {
            font-weight: bold;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th {
            background-color: #059669;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }
        table td {
            padding: 10px 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        table tr:last-child td {
            border-bottom: 2px solid #059669;
        }
        .text-right {
            text-align: right;
        }
        .totals {
            margin-top: 30px;
            float: right;
            width: 300px;
        }
        .totals table {
            margin: 0;
        }
        .totals table td {
            padding: 8px 12px;
        }
        .totals .total-row {
            background-color: #f3f4f6;
            font-weight: bold;
            font-size: 14px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-paid {
            background-color: #d1fae5;
            color: #065f46;
        }
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        .footer {
            clear: both;
            margin-top: 60px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>INVOICE</h1>
            <div class="company-info">
                <strong>Estatia Property Management</strong><br>
                Your trusted real estate partner
            </div>
        </div>

        <!-- Invoice Meta Information -->
        <div class="invoice-meta">
            <div class="left">
                <div class="section-title">Bill To</div>
                <div class="info-block">
                    @if($invoice->order && $invoice->order->customer)
                        <p><strong>{{ $invoice->order->customer->name }}</strong></p>
                        @if($invoice->order->customer->email)
                            <p>{{ $invoice->order->customer->email }}</p>
                        @endif
                        @if($invoice->order->customer->phone)
                            <p>{{ $invoice->order->customer->phone }}</p>
                        @endif
                        @if($invoice->order->customer->address)
                            <p>{{ $invoice->order->customer->address }}</p>
                        @endif
                    @else
                        <p>N/A</p>
                    @endif
                </div>
            </div>
            <div class="right">
                <div class="info-block">
                    <p><span class="label">Invoice No:</span> <span class="value">{{ $invoice->no }}</span></p>
                    <p><span class="label">Invoice Date:</span> <span class="value">{{ $invoice->dt?->format('d M Y') ?? 'N/A' }}</span></p>
                    <p><span class="label">Order No:</span> <span class="value">{{ $invoice->order->no ?? 'N/A' }}</span></p>
                    <p><span class="label">Order Date:</span> <span class="value">{{ $invoice->order->dt?->format('d M Y') ?? 'N/A' }}</span></p>
                    <p>
                        <span class="label">Status:</span>
                        @if($invoice->payment_status === 'paid')
                            <span class="status-badge status-paid">Paid</span>
                        @else
                            <span class="status-badge status-pending">Pending</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Property Information -->
        @if($invoice->order)
        <div class="section-title">Property Details</div>
        <table>
            <thead>
                <tr>
                    <th>Project</th>
                    <th>Cluster</th>
                    <th>Unit No</th>
                    <th>Product Type</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $invoice->order->project->name ?? 'N/A' }}</td>
                    <td>{{ $invoice->order->cluster->name ?? 'N/A' }}</td>
                    <td>{{ $invoice->order->unit->no ?? 'N/A' }}</td>
                    <td>{{ $invoice->order->unit->product->type->name ?? 'N/A' }}</td>
                </tr>
            </tbody>
        </table>
        @endif

        <!-- Order Items (if applicable) -->
        <div class="section-title">Order Summary</div>
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>Property Purchase</strong><br>
                        @if($invoice->order)
                            {{ $invoice->order->project->name ?? '' }} 
                            {{ $invoice->order->cluster->name ?? '' }} 
                            - Unit {{ $invoice->order->unit->no ?? 'N/A' }}
                        @endif
                    </td>
                    <td class="text-right">Rp {{ number_format($invoice->order->total ?? 0, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals">
            <table>
                <tr>
                    <td>Subtotal:</td>
                    <td class="text-right">Rp {{ number_format($invoice->order->total ?? 0, 0, ',', '.') }}</td>
                </tr>
                <tr class="total-row">
                    <td>Total Amount:</td>
                    <td class="text-right">Rp {{ number_format($invoice->order->total ?? 0, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Amount Paid:</td>
                    <td class="text-right" style="color: #059669;">Rp {{ number_format($invoice->total_paid, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Amount Due:</td>
                    <td class="text-right" style="color: #dc2626;">Rp {{ number_format(($invoice->order->total ?? 0) - $invoice->total_paid, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <div class="clearfix"></div>

        <!-- Payment Information -->
        @if($invoice->payments && $invoice->payments->isNotEmpty())
        <div class="section-title" style="margin-top: 30px;">Payment History</div>
        <table>
            <thead>
                <tr>
                    <th>Payment No</th>
                    <th>Date</th>
                    <th>Method</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->payments as $payment)
                <tr>
                    <td>{{ $payment->no ?? 'N/A' }}</td>
                    <td>{{ $payment->dt?->format('d M Y') ?? 'N/A' }}</td>
                    <td>{{ $payment->method ?? 'N/A' }}</td>
                    <td class="text-right">Rp {{ number_format($payment->amount ?? 0, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>Thank you for your business!</p>
            <p>This is a computer-generated document. No signature is required.</p>
            <p style="margin-top: 10px;">Estatia Property Management &copy; {{ date('Y') }}</p>
        </div>
    </div>
</body>
</html>
