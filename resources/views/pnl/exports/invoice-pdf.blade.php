<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $expense->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            padding: 40px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            border-bottom: 3px solid #dc3545;
            padding-bottom: 20px;
        }
        .company-info {
            text-align: left;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #dc3545;
            margin-bottom: 5px;
        }
        .invoice-title {
            text-align: right;
        }
        .invoice-title h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 5px;
        }
        .invoice-number {
            font-size: 14px;
            color: #666;
        }
        .details-section {
            margin-bottom: 30px;
        }
        .details-row {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .details-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .details-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-right: 10px;
        }
        .details-box h3 {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 8px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .details-box p {
            margin-bottom: 3px;
        }
        .details-box .name {
            font-weight: bold;
            font-size: 14px;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .invoice-table th {
            background: #dc3545;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
        }
        .invoice-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #eee;
        }
        .invoice-table .text-right {
            text-align: right;
        }
        .totals-section {
            width: 300px;
            float: right;
            margin-top: 10px;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .totals-row.grand-total {
            font-size: 16px;
            font-weight: bold;
            background: #f8f9fa;
            padding: 12px;
            border: none;
            margin-top: 5px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        .status-scheduled {
            background: #cce5ff;
            color: #004085;
        }
        .status-paid {
            background: #d4edda;
            color: #155724;
        }
        .footer {
            clear: both;
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
        .payment-info {
            margin-top: 30px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .payment-info h3 {
            font-size: 12px;
            color: #333;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <table style="width: 100%; margin-bottom: 30px;">
        <tr>
            <td style="vertical-align: top;">
                <div class="company-name">TicketKart</div>
                <div style="color: #666; font-size: 11px;">Event Management & Ticketing</div>
            </td>
            <td style="text-align: right; vertical-align: top;">
                <h1 style="font-size: 28px; margin: 0;">INVOICE</h1>
                <div style="color: #666; margin-top: 5px;">{{ $expense->invoice_number ?? 'N/A' }}</div>
            </td>
        </tr>
    </table>

    <table style="width: 100%; margin-bottom: 30px;">
        <tr>
            <td style="width: 50%; vertical-align: top; padding-right: 15px;">
                <div class="details-box">
                    <h3>Bill To</h3>
                    @if($expense->vendor)
                        <p class="name">{{ $expense->vendor->display_name }}</p>
                        @if($expense->vendor->business_name && $expense->vendor->business_name != $expense->vendor->full_name)
                            <p>{{ $expense->vendor->business_name }}</p>
                        @endif
                        @if($expense->vendor->email)
                            <p>{{ $expense->vendor->email }}</p>
                        @endif
                        @if($expense->vendor->phone)
                            <p>{{ $expense->vendor->phone }}</p>
                        @endif
                        @if($expense->vendor->business_address)
                            <p>{{ $expense->vendor->business_address }}</p>
                        @endif
                    @else
                        <p>N/A</p>
                    @endif
                </div>
            </td>
            <td style="width: 50%; vertical-align: top;">
                <div class="details-box">
                    <h3>Invoice Details</h3>
                    <table style="width: 100%; font-size: 12px;">
                        <tr>
                            <td style="padding: 3px 0; color: #666;">Invoice Date:</td>
                            <td style="padding: 3px 0; text-align: right;">{{ $expense->expense_date->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 3px 0; color: #666;">Event:</td>
                            <td style="padding: 3px 0; text-align: right;">{{ $expense->event->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 3px 0; color: #666;">Event Date:</td>
                            <td style="padding: 3px 0; text-align: right;">{{ $expense->event->event_date->format('d M Y') ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 3px 0; color: #666;">Payment Status:</td>
                            <td style="padding: 3px 0; text-align: right;">
                                <span class="status-badge status-{{ $expense->payment->status ?? 'pending' }}">
                                    {{ ucfirst($expense->payment->status ?? 'Pending') }}
                                </span>
                            </td>
                        </tr>
                        @if($expense->payment && $expense->payment->status === 'paid' && $expense->payment->actual_paid_date)
                        <tr>
                            <td style="padding: 3px 0; color: #155724; font-weight: bold;">Paid Date:</td>
                            <td style="padding: 3px 0; text-align: right; color: #155724; font-weight: bold;">{{ $expense->payment->actual_paid_date->format('d M Y') }}</td>
                        </tr>
                        @elseif($expense->payment && $expense->payment->scheduled_date)
                        <tr>
                            <td style="padding: 3px 0; color: #666;">Due Date:</td>
                            <td style="padding: 3px 0; text-align: right;">{{ $expense->payment->scheduled_date->format('d M Y') }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <table class="invoice-table">
        <thead>
            <tr>
                <th style="width: 50%;">Description</th>
                <th>Category</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>{{ $expense->title }}</strong>
                    @if($expense->description)
                        <br><small style="color: #666;">{{ $expense->description }}</small>
                    @endif
                </td>
                <td>{{ $expense->category->name ?? 'N/A' }}</td>
                <td class="text-right">£{{ number_format($expense->amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="totals-section">
        <div class="totals-row">
            <span>Subtotal</span>
            <span>£{{ number_format($expense->amount, 2) }}</span>
        </div>
        @if($expense->is_taxable && $expense->tax_amount > 0)
        <div class="totals-row">
            <span>VAT ({{ $expense->tax_rate ?? 20 }}%)</span>
            <span>£{{ number_format($expense->tax_amount, 2) }}</span>
        </div>
        @else
        <div class="totals-row" style="color: #666; font-style: italic;">
            <span>VAT</span>
            <span>Non-Taxable</span>
        </div>
        @endif
        <div class="totals-row grand-total">
            <span>Total</span>
            <span>£{{ number_format($expense->total_amount, 2) }}</span>
        </div>
    </div>

    <div style="clear: both;"></div>

    @if($expense->payment && ($expense->payment->payment_method || $expense->payment->transaction_reference))
    <div class="payment-info">
        <h3>Payment Information</h3>
        <table style="font-size: 12px;">
            @if($expense->payment->payment_method)
            <tr>
                <td style="padding: 3px 0; color: #666; width: 150px;">Payment Method:</td>
                <td style="padding: 3px 0;">{{ ucfirst(str_replace('_', ' ', $expense->payment->payment_method)) }}</td>
            </tr>
            @endif
            @if($expense->payment->transaction_reference)
            <tr>
                <td style="padding: 3px 0; color: #666;">Reference:</td>
                <td style="padding: 3px 0;">{{ $expense->payment->transaction_reference }}</td>
            </tr>
            @endif
            @if($expense->payment->actual_paid_date)
            <tr>
                <td style="padding: 3px 0; color: #666;">Paid On:</td>
                <td style="padding: 3px 0;">{{ $expense->payment->actual_paid_date->format('d M Y') }}</td>
            </tr>
            @endif
        </table>
    </div>
    @endif

    <div class="footer">
        <p>Thank you for your business!</p>
        <p style="margin-top: 5px;">Generated by TicketKart P&L Module on {{ now()->format('d M Y H:i') }}</p>
    </div>
</body>
</html>
