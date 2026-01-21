<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: #dc3545;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border: 1px solid #ddd;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            margin: 10px 0;
        }
        .status-created { background: #cce5ff; color: #004085; }
        .status-paid { background: #d4edda; color: #155724; }
        .status-scheduled { background: #fff3cd; color: #856404; }
        .details-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }
        .details-table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .details-table td:first-child {
            color: #666;
            width: 40%;
        }
        .amount {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
            text-align: center;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 12px;
        }
        .note {
            background: #fff;
            border-left: 4px solid #17a2b8;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>TicketKart</h1>
    </div>
    
    <div class="content">
        <p>Dear {{ $expense->vendor->display_name ?? 'Vendor' }},</p>

        @if($action === 'created')
            <p>A new invoice has been created for your services:</p>
            <div class="status-badge status-created">New Invoice</div>
        @elseif($action === 'paid')
            <p>Great news! Your payment has been processed:</p>
            <div class="status-badge status-paid">Payment Confirmed</div>
        @elseif($action === 'scheduled')
            <p>Your payment has been scheduled:</p>
            <div class="status-badge status-scheduled">Payment Scheduled</div>
        @else
            <p>Please find your invoice details below:</p>
        @endif

        <table class="details-table">
            <tr>
                <td>Invoice Number:</td>
                <td><strong>{{ $expense->invoice_number ?? 'N/A' }}</strong></td>
            </tr>
            <tr>
                <td>Event:</td>
                <td>{{ $expense->event->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Description:</td>
                <td>{{ $expense->title }}</td>
            </tr>
            <tr>
                <td>Category:</td>
                <td>{{ $expense->category->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Invoice Date:</td>
                <td>{{ $expense->expense_date->format('d M Y') }}</td>
            </tr>
            @if($expense->payment && $expense->payment->scheduled_date)
            <tr>
                <td>Due Date:</td>
                <td>{{ $expense->payment->scheduled_date->format('d M Y') }}</td>
            </tr>
            @endif
            @if($action === 'paid' && $expense->payment && $expense->payment->actual_paid_date)
            <tr>
                <td>Paid On:</td>
                <td>{{ $expense->payment->actual_paid_date->format('d M Y') }}</td>
            </tr>
            @endif
        </table>

        <div class="amount">
            Total: £{{ number_format($expense->total_amount, 2) }}
        </div>

        @if($action === 'scheduled' && $expense->payment && $expense->payment->scheduled_date)
        <div class="note">
            <strong>Note:</strong> Your payment is scheduled for <strong>{{ $expense->payment->scheduled_date->format('d M Y') }}</strong>. 
            You will receive a confirmation email once the payment is processed.
        </div>
        @endif

        <p>Please find the attached PDF invoice for your records.</p>

        <p>If you have any questions, please don't hesitate to contact us.</p>

        <p>Best regards,<br>
        <strong>TicketKart Team</strong></p>
    </div>

    <div class="footer">
        <p>This is an automated email from TicketKart P&L Module.</p>
        <p>Please do not reply to this email.</p>
    </div>
</body>
</html>
