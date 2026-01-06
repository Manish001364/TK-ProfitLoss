<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Reminder</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border: 1px solid #e9ecef;
        }
        .details-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
        }
        .amount {
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
            margin: 10px 0;
        }
        .label {
            color: #6c757d;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .value {
            font-size: 16px;
            color: #333;
            margin-bottom: 15px;
        }
        .due-date {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        .due-date.overdue {
            background: #f8d7da;
            border-color: #dc3545;
        }
        .footer {
            background: #343a40;
            color: #adb5bd;
            padding: 20px;
            text-align: center;
            border-radius: 0 0 10px 10px;
            font-size: 14px;
        }
        .footer a {
            color: #667eea;
        }
        .note {
            font-size: 13px;
            color: #6c757d;
            font-style: italic;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>💰 Payment Reminder</h1>
        <p>TicketKart Event Management</p>
    </div>
    
    <div class="content">
        <p>Dear <strong>{{ $vendor->display_name }}</strong>,</p>
        
        <p>This is a friendly reminder about an upcoming payment for the following event:</p>
        
        <div class="details-box">
            <div class="label">Event</div>
            <div class="value"><strong>{{ $event->name }}</strong></div>
            
            <div class="label">Event Date</div>
            <div class="value">{{ $event->event_date->format('d M Y') }}</div>
            
            @if($event->venue)
                <div class="label">Venue</div>
                <div class="value">{{ $event->venue }}</div>
            @endif
            
            <div class="label">Payment For</div>
            <div class="value">{{ $payment->expense->title }}</div>
            
            <div class="label">Amount Due</div>
            <div class="amount">₹{{ number_format($amount, 2) }}</div>
        </div>
        
        <div class="due-date {{ $daysUntilDue < 0 ? 'overdue' : '' }}">
            @if($daysUntilDue < 0)
                <strong>⚠️ Payment Overdue</strong><br>
                Was due on: <strong>{{ $scheduledDate->format('d M Y') }}</strong><br>
                <small>({{ abs($daysUntilDue) }} days overdue)</small>
            @elseif($daysUntilDue == 0)
                <strong>📅 Payment Due Today!</strong><br>
                <small>{{ $scheduledDate->format('d M Y') }}</small>
            @else
                <strong>📅 Scheduled Payment Date</strong><br>
                {{ $scheduledDate->format('d M Y') }}<br>
                <small>({{ $daysUntilDue }} {{ $daysUntilDue == 1 ? 'day' : 'days' }} remaining)</small>
            @endif
        </div>
        
        <p class="note">
            <strong>Note:</strong> This is an automated reminder from TicketKart. 
            Payments are processed manually. Please contact the event organizer for payment details and confirmation.
        </p>
    </div>
    
    <div class="footer">
        <p>Powered by <strong>TicketKart</strong></p>
        <p><a href="https://ticketkart.com">www.ticketkart.com</a></p>
        <p style="font-size: 12px; margin-top: 10px;">
            This is an automated message. Please do not reply directly to this email.
        </p>
    </div>
</body>
</html>
