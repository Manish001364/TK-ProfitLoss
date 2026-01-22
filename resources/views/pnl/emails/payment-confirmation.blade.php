<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f5f5f5;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <!-- Header -->
        <tr>
            <td style="background: linear-gradient(135deg, #28a745 0%, #218838 100%); color: white; padding: 30px 20px; text-align: center;">
                <div style="font-size: 48px; margin-bottom: 10px;">✓</div>
                <h1 style="margin: 0; font-size: 24px; font-weight: 600;">Payment Confirmed</h1>
            </td>
        </tr>
        
        <!-- Content -->
        <tr>
            <td style="padding: 30px 20px;">
                @if($recipientType === 'vendor')
                    <p style="font-size: 18px; margin-bottom: 20px;">Dear {{ $payment->vendor->display_name ?? $payment->vendor->name ?? 'Vendor' }},</p>
                    <p>We're pleased to inform you that your payment has been processed. Below are the payment details:</p>
                @else
                    <p style="font-size: 18px; margin-bottom: 20px;">Payment Confirmation</p>
                    <p>This is to confirm that the following payment has been marked as paid:</p>
                @endif

                <!-- Payment Details Table -->
                <table width="100%" cellpadding="0" cellspacing="0" style="background: #f8f9fa; border-radius: 8px; margin: 20px 0;">
                    <tr>
                        <td style="padding: 15px 20px; border-bottom: 1px solid #e9ecef;">
                            <span style="color: #6c757d; font-size: 14px;">Invoice/Expense:</span><br>
                            <strong style="color: #333;">{{ $payment->expense->title ?? 'N/A' }}</strong>
                        </td>
                    </tr>
                    
                    @if($payment->expense && $payment->expense->invoice_number)
                    <tr>
                        <td style="padding: 15px 20px; border-bottom: 1px solid #e9ecef;">
                            <span style="color: #6c757d; font-size: 14px;">Invoice Number:</span><br>
                            <strong style="color: #333;">{{ $payment->expense->invoice_number }}</strong>
                        </td>
                    </tr>
                    @endif
                    
                    <tr>
                        <td style="padding: 15px 20px; border-bottom: 1px solid #e9ecef;">
                            <span style="color: #6c757d; font-size: 14px;">Event:</span><br>
                            <strong style="color: #333;">{{ $payment->expense->event->name ?? 'N/A' }}</strong>
                        </td>
                    </tr>
                    
                    @if($recipientType === 'organiser' && $payment->vendor)
                    <tr>
                        <td style="padding: 15px 20px; border-bottom: 1px solid #e9ecef;">
                            <span style="color: #6c757d; font-size: 14px;">Paid To (Vendor):</span><br>
                            <strong style="color: #333;">{{ $payment->vendor->display_name ?? $payment->vendor->name ?? 'N/A' }}</strong>
                        </td>
                    </tr>
                    @endif
                    
                    <tr>
                        <td style="padding: 15px 20px; border-bottom: 1px solid #e9ecef;">
                            <span style="color: #6c757d; font-size: 14px;">Payment Date:</span><br>
                            <strong style="color: #333;">{{ $payment->actual_paid_date ? \Carbon\Carbon::parse($payment->actual_paid_date)->format('d M Y') : now()->format('d M Y') }}</strong>
                        </td>
                    </tr>
                    
                    @if($payment->payment_method)
                    <tr>
                        <td style="padding: 15px 20px; border-bottom: 1px solid #e9ecef;">
                            <span style="color: #6c757d; font-size: 14px;">Payment Method:</span><br>
                            <strong style="color: #333;">{{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}</strong>
                        </td>
                    </tr>
                    @endif
                    
                    @if($payment->transaction_reference)
                    <tr>
                        <td style="padding: 15px 20px; border-bottom: 1px solid #e9ecef;">
                            <span style="color: #6c757d; font-size: 14px;">Reference:</span><br>
                            <strong style="color: #333;">{{ $payment->transaction_reference }}</strong>
                        </td>
                    </tr>
                    @endif
                    
                    <!-- Amount Row -->
                    <tr>
                        <td style="padding: 20px; background: #d4edda; border-radius: 0 0 8px 8px;">
                            <span style="color: #6c757d; font-size: 14px;">Amount Paid:</span><br>
                            @php
                                $currency = $payment->expense->currency ?? 'GBP';
                                $symbol = match($currency) {
                                    'GBP' => '£',
                                    'USD' => '$',
                                    'EUR' => '€',
                                    'INR' => '₹',
                                    default => $currency . ' '
                                };
                            @endphp
                            <strong style="color: #155724; font-size: 24px;">{{ $symbol }}{{ number_format($payment->amount, 2) }}</strong>
                        </td>
                    </tr>
                </table>

                @if($recipientType === 'vendor')
                    <div style="background: #e8f5e9; border-left: 4px solid #28a745; padding: 15px; margin: 20px 0;">
                        <strong>Thank you for your services!</strong><br>
                        If you have any questions about this payment, please contact the event organiser.
                    </div>
                @else
                    <div style="background: #e8f5e9; border-left: 4px solid #28a745; padding: 15px; margin: 20px 0;">
                        <strong>Payment Complete</strong><br>
                        This payment has been recorded in your P&L system. Keep this email for your records.
                    </div>
                @endif
            </td>
        </tr>
        
        <!-- Footer -->
        <tr>
            <td style="background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #6c757d;">
                <p style="margin: 0;">This is an automated message from the TicketKart P&L Module.</p>
                <p style="margin: 5px 0 0 0;">© {{ date('Y') }} TicketKart. All rights reserved.</p>
            </td>
        </tr>
    </table>
</body>
</html>
