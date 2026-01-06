<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>P&L Summary Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #667eea; padding-bottom: 20px; }
        .header h1 { color: #667eea; margin: 0; }
        .header p { color: #6c757d; margin: 5px 0; }
        .summary-box { background: #f8f9fa; padding: 15px; margin: 20px 0; border-radius: 5px; }
        .summary-row { display: flex; justify-content: space-between; margin: 10px 0; }
        .profit { color: #28a745; font-weight: bold; }
        .loss { color: #dc3545; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #dee2e6; padding: 8px; text-align: left; }
        th { background: #667eea; color: white; }
        tr:nth-child(even) { background: #f8f9fa; }
        .text-right { text-align: right; }
        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #dee2e6; color: #6c757d; }
    </style>
</head>
<body>
    <div class="header">
        <h1>TicketKart P&L Report</h1>
        <p>Generated: {{ $generatedAt->format('d M Y, h:i A') }}</p>
        @if($filters['date_from'] || $filters['date_to'])
            <p>Period: {{ $filters['date_from'] ?? 'Start' }} to {{ $filters['date_to'] ?? 'Present' }}</p>
        @endif
    </div>

    <div class="summary-box">
        <h3>Financial Summary</h3>
        <table>
            <tr>
                <td>Total Revenue</td>
                <td class="text-right profit">₹{{ number_format($totalRevenue, 2) }}</td>
            </tr>
            <tr>
                <td>Total Expenses</td>
                <td class="text-right loss">₹{{ number_format($totalExpenses, 2) }}</td>
            </tr>
            <tr style="font-weight: bold; font-size: 14px;">
                <td>Net {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}</td>
                <td class="text-right {{ $netProfit >= 0 ? 'profit' : 'loss' }}">
                    {{ $netProfit >= 0 ? '' : '-' }}₹{{ number_format(abs($netProfit), 2) }}
                </td>
            </tr>
            <tr>
                <td>Total Tickets Sold</td>
                <td class="text-right">{{ number_format($totalTicketsSold) }}</td>
            </tr>
        </table>
    </div>

    <h3>Event-wise Breakdown</h3>
    <table>
        <thead>
            <tr>
                <th>Event</th>
                <th>Date</th>
                <th>Status</th>
                <th class="text-right">Revenue</th>
                <th class="text-right">Expenses</th>
                <th class="text-right">Profit/Loss</th>
            </tr>
        </thead>
        <tbody>
            @foreach($events as $event)
                <tr>
                    <td>{{ $event->name }}</td>
                    <td>{{ $event->event_date->format('d M Y') }}</td>
                    <td>{{ ucfirst($event->status) }}</td>
                    <td class="text-right">₹{{ number_format($event->total_revenue, 2) }}</td>
                    <td class="text-right">₹{{ number_format($event->total_expenses, 2) }}</td>
                    <td class="text-right {{ $event->net_profit >= 0 ? 'profit' : 'loss' }}">
                        {{ $event->net_profit >= 0 ? '' : '-' }}₹{{ number_format(abs($event->net_profit), 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p><strong>TicketKart</strong> - Event Management Platform</p>
        <p>www.ticketkart.com</p>
    </div>
</body>
</html>
