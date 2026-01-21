<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .email-container {
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #28a745 0%, #218838 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .header .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .content {
            padding: 30px 20px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .payment-details {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            color: #6c757d;
            font-size: 14px;
        }
        .detail-value {
            font-weight: 600;
            color: #333;
        }
        .amount-row {
            background: #d4edda;
            margin: -20px;
            margin-top: 15px;
            padding: 15px 20px;
            border-radius: 0 0 8px 8px;
        }
        .amount-row .detail-value {
            color: #155724;
            font-size: 20px;
        }
        .message-box {
            background: #e8f5e9;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 20px 0;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            margin-top: 15px;
        }
        @media (max-width: 480px) {
            .detail-row {
                flex-direction: column;
            }
            .detail-value {
                margin-top: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="icon">✓</div>
            <h1>Payment Confirmed</h1>
        </div>
        
        <div class="content">
            @if($recipientType === 'vendor')
                <p class="greeting">Dear {{ $payment->vendor->name ?? 'Vendor' }},</p>
                <p>We're pleased to inform you that your payment has been processed. Below are the payment details:</p>
            @else
                <p class="greeting">Payment Confirmation</p>
                <p>This is to confirm that the following payment has been marked as paid:</p>
            @endif

            <div class="payment-details">
                <div class="detail-row">
                    <span class="detail-label">Invoice/Expense</span>
                    <span class="detail-value">{{ $payment->expense->title ?? 'N/A' }}</span>
                </div>
                
                @if($payment->expense && $payment->expense->invoice_number)
                <div class="detail-row">
                    <span class="detail-label">Invoice Number</span>
                    <span class="detail-value">{{ $payment->expense->invoice_number }}</span>
                </div>
                @endif
                
                <div class="detail-row">
                    <span class="detail-label">Event</span>
                    <span class="detail-value">{{ $payment->expense->event->name ?? 'N/A' }}</span>
                </div>
                
                @if($recipientType === 'organiser' && $payment->vendor)
                <div class="detail-row">
                    <span class="detail-label">Paid To (Vendor)</span>
                    <span class="detail-value">{{ $payment->vendor->name }}</span>
                </div>
                @endif
                
                <div class="detail-row">
                    <span class="detail-label">Payment Date</span>
                    <span class="detail-value">{{ $payment->actual_paid_date ? \Carbon\Carbon::parse($payment->actual_paid_date)->format('d M Y') : now()->format('d M Y') }}</span>
                </div>
                
                @if($payment->payment_method)
                <div class="detail-row">
                    <span class="detail-label">Payment Method</span>
                    <span class="detail-value">{{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}</span>
                </div>
                @endif
                
                @if($payment->transaction_reference)
                <div class="detail-row">
                    <span class="detail-label">Reference</span>
                    <span class="detail-value">{{ $payment->transaction_reference }}</span>
                </div>
                @endif
                
                <div class="detail-row amount-row">
                    <span class="detail-label">Amount Paid</span>
                    <span class="detail-value">
                        @php
                            $currency = $payment->expense->currency ?? 'GBP';
                            $symbol = match($currency) {
                                'GBP' => '£',
                                'USD' => '$',
                                'EUR' => '€',
                                default => $currency . ' '
                            };
                        @endphp
                        {{ $symbol }}{{ number_format($payment->amount, 2) }}
                    </span>
                </div>
            </div>

            @if($recipientType === 'vendor')
                <div class="message-box">
                    <strong>Thank you for your services!</strong><br>
                    If you have any questions about this payment, please contact the event organiser.
                </div>
            @else
                <div class="message-box">
                    <strong>Payment Complete</strong><br>
                    This payment has been recorded in your P&L system. Keep this email for your records.
                </div>
            @endif
        </div>
        
        <div class="footer">
            <p>This is an automated message from the TicketKart P&L Module.</p>
            <p>© {{ date('Y') }} TicketKart. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
