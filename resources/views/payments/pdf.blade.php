<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt - {{ $payment->no }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.5;
        }
        .container {
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 3px solid #059669;
        }
        .header h1 {
            font-size: 24px;
            color: #059669;
            margin-bottom: 5px;
        }
        .header .subtitle {
            font-size: 14px;
            color: #666;
        }
        .payment-info {
            background-color: #f9fafb;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #e5e7eb;
        }
        .payment-info h2 {
            font-size: 16px;
            color: #059669;
            margin-bottom: 10px;
        }
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }
        .info-label {
            display: table-cell;
            width: 40%;
            font-weight: bold;
            color: #4b5563;
        }
        .info-value {
            display: table-cell;
            width: 60%;
            color: #111827;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #111827;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #d1d5db;
        }
        .two-column {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 10px;
        }
        .column:last-child {
            padding-right: 0;
            padding-left: 10px;
        }
        .field {
            margin-bottom: 8px;
        }
        .field-label {
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 2px;
        }
        .field-value {
            font-size: 11px;
            color: #111827;
        }
        .bank-info {
            background-color: #eff6ff;
            padding: 12px;
            border-radius: 5px;
            border: 1px solid #bfdbfe;
            margin-bottom: 20px;
        }
        .bank-info h3 {
            font-size: 13px;
            color: #1e40af;
            margin-bottom: 8px;
        }
        .summary-box {
            background-color: #f0fdf4;
            padding: 15px;
            border-radius: 5px;
            border: 2px solid #059669;
            margin-top: 20px;
        }
        .summary-row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }
        .summary-label {
            display: table-cell;
            width: 70%;
            font-size: 12px;
            color: #4b5563;
        }
        .summary-value {
            display: table-cell;
            width: 30%;
            text-align: right;
            font-size: 12px;
            font-weight: bold;
        }
        .summary-total {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid #059669;
        }
        .summary-total .summary-label {
            font-size: 14px;
            font-weight: bold;
            color: #111827;
        }
        .summary-total .summary-value {
            font-size: 16px;
            color: #059669;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        .status-paid {
            background-color: #d1fae5;
            color: #065f46;
        }
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        .status-transfer {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .status-cash {
            background-color: #d1fae5;
            color: #065f46;
        }
        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
        }
        .signature-section {
            margin-top: 40px;
            display: table;
            width: 100%;
        }
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
        }
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #333;
            padding-top: 5px;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>PAYMENT RECEIPT</h1>
            <div class="subtitle">{{ config('app.name', 'Estatia') }}</div>
        </div>

        <!-- Payment Information Box -->
        <div class="payment-info">
            <h2>Payment Information</h2>
            <div class="info-row">
                <div class="info-label">Payment Number:</div>
                <div class="info-value">{{ $payment->no }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Payment Date:</div>
                <div class="info-value">{{ $payment->dt?->format('d F Y') ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Paid At:</div>
                <div class="info-value">{{ $payment->paid_at?->format('d F Y H:i') ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Payment Method:</div>
                <div class="info-value">
                    @if($payment->payment_type === 'cash')
                        <span class="status-badge status-cash">CASH</span>
                    @elseif($payment->payment_type === 'transfer')
                        <span class="status-badge status-transfer">BANK TRANSFER</span>
                    @else
                        {{ strtoupper($payment->payment_type) }}
                    @endif
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Amount Paid:</div>
                <div class="info-value" style="font-size: 14px; font-weight: bold; color: #059669;">
                    Rp {{ number_format($payment->amount, 0, ',', '.') }}
                </div>
            </div>
        </div>

        <!-- Bank Transfer Details (if applicable) -->
        @if($payment->payment_type === 'transfer' && $payment->bank_account_id)
            <div class="bank-info">
                <h3>Bank Transfer Details</h3>
                <div class="two-column">
                    <div class="column">
                        <div class="field">
                            <div class="field-label">Account Number</div>
                            <div class="field-value" style="font-family: monospace; font-weight: bold;">{{ $payment->bank_account_id }}</div>
                        </div>
                    </div>
                    <div class="column">
                        <div class="field">
                            <div class="field-label">Bank Name</div>
                            <div class="field-value">{{ $payment->bank_account_type ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
                <div class="field">
                    <div class="field-label">Account Name</div>
                    <div class="field-value">{{ $payment->bank_account_name ?? 'N/A' }}</div>
                </div>
            </div>
        @endif

        <!-- Invoice & Order Details -->
        <div class="section">
            <div class="section-title">Invoice & Order Details</div>
            <div class="two-column">
                <div class="column">
                    <div class="field">
                        <div class="field-label">Invoice Number</div>
                        <div class="field-value">{{ $payment->invoice->no }}</div>
                    </div>
                    <div class="field">
                        <div class="field-label">Invoice Date</div>
                        <div class="field-value">{{ $payment->invoice->dt?->format('d F Y') ?? 'N/A' }}</div>
                    </div>
                </div>
                <div class="column">
                    <div class="field">
                        <div class="field-label">Order Number</div>
                        <div class="field-value">{{ $payment->invoice->order->no }}</div>
                    </div>
                    <div class="field">
                        <div class="field-label">Payment Status</div>
                        <div class="field-value">
                            @php
                                $totalPaid = $payment->invoice->payments->sum('amount');
                                $totalAmount = $payment->invoice->order->total;
                                $isPaid = $totalPaid >= $totalAmount;
                            @endphp
                            @if($isPaid)
                                <span class="status-badge status-paid">PAID</span>
                            @else
                                <span class="status-badge status-pending">PENDING</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        @if($payment->invoice->order->customer)
            <div class="section">
                <div class="section-title">Customer Information</div>
                <div class="two-column">
                    <div class="column">
                        <div class="field">
                            <div class="field-label">Customer Name</div>
                            <div class="field-value">{{ $payment->invoice->order->customer->name }}</div>
                        </div>
                        <div class="field">
                            <div class="field-label">Email</div>
                            <div class="field-value">{{ $payment->invoice->order->customer->email ?? 'N/A' }}</div>
                        </div>
                    </div>
                    <div class="column">
                        <div class="field">
                            <div class="field-label">Phone</div>
                            <div class="field-value">{{ $payment->invoice->order->customer->phone ?? 'N/A' }}</div>
                        </div>
                        <div class="field">
                            <div class="field-label">Address</div>
                            <div class="field-value">{{ $payment->invoice->order->customer->address ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Property Details -->
        <div class="section">
            <div class="section-title">Property Details</div>
            <div class="two-column">
                <div class="column">
                    @if($payment->invoice->order->project)
                        <div class="field">
                            <div class="field-label">Project</div>
                            <div class="field-value">{{ $payment->invoice->order->project->name }}</div>
                        </div>
                    @endif
                    @if($payment->invoice->order->cluster)
                        <div class="field">
                            <div class="field-label">Cluster</div>
                            <div class="field-value">{{ $payment->invoice->order->cluster->name }}</div>
                        </div>
                    @endif
                </div>
                <div class="column">
                    @if($payment->invoice->order->unit)
                        <div class="field">
                            <div class="field-label">Unit Number</div>
                            <div class="field-value">{{ $payment->invoice->order->unit->no }}</div>
                        </div>
                        @if($payment->invoice->order->unit->product)
                            <div class="field">
                                <div class="field-label">Product Type</div>
                                <div class="field-value">{{ $payment->invoice->order->unit->product->name }}</div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Payment Summary -->
        <div class="summary-box">
            <div class="summary-row">
                <div class="summary-label">Order Total</div>
                <div class="summary-value">Rp {{ number_format($payment->invoice->order->total, 0, ',', '.') }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-label">Total Paid (Including This Payment)</div>
                <div class="summary-value">Rp {{ number_format($totalPaid, 0, ',', '.') }}</div>
            </div>
            <div class="summary-row summary-total">
                <div class="summary-label">Remaining Balance</div>
                <div class="summary-value">
                    @php
                        $remaining = $payment->invoice->order->total - $totalPaid;
                    @endphp
                    Rp {{ number_format($remaining, 0, ',', '.') }}
                </div>
            </div>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div>Received by</div>
                <div class="signature-line">_____________________</div>
            </div>
            <div class="signature-box">
                <div>Authorized by</div>
                <div class="signature-line">_____________________</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>This is a computer-generated receipt and does not require a signature.</p>
            <p>Printed on: {{ now()->format('d F Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
