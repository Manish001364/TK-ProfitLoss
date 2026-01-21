{{-- P&L Budget Tips & Analysis --}}
@php
    // Calculate metrics
    $totalBudget = isset($totalBudget) ? $totalBudget : 0;
    $totalExpenses = isset($totalExpenses) ? $totalExpenses : 0;
    $totalRevenue = isset($totalRevenue) ? $totalRevenue : 0;
    $netProfit = isset($netProfit) ? $netProfit : ($totalRevenue - $totalExpenses);
    $budgetUtilisation = $totalBudget > 0 ? ($totalExpenses / $totalBudget) * 100 : 0;
    
    // Determine status
    $isProfitable = $netProfit >= 0;
    $isOverBudget = $budgetUtilisation > 100;
    $isUnderBudget = $budgetUtilisation < 70;
    $hasLowRevenue = $totalRevenue < $totalExpenses;
@endphp

@if($totalExpenses > 0 || $totalRevenue > 0)
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-gradient text-white border-0 py-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <h6 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Smart Tips & Insights</h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            @if(!$isProfitable)
            <!-- Loss Warning & Tips -->
            <div class="col-12">
                <div class="alert alert-warning mb-0">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-exclamation-triangle fa-lg me-3 mt-1 text-warning"></i>
                        <div>
                            <strong>Event Currently at a Loss</strong>
                            <p class="small mb-2">Your expenses (£{{ number_format($totalExpenses, 0) }}) exceed revenue (£{{ number_format($totalRevenue, 0) }}). Here are some suggestions:</p>
                            <ul class="small mb-0">
                                <li><strong>Increase Ticket Sales:</strong> Consider promotions, early bird discounts, or social media marketing to boost ticket sales.</li>
                                <li><strong>Review Vendor Costs:</strong> Negotiate with vendors or look for alternative suppliers with better rates.</li>
                                <li><strong>Add Revenue Streams:</strong> Consider merchandise, VIP packages, food & beverage sales, or sponsorships.</li>
                                <li><strong>Cut Non-Essential Expenses:</strong> Review variable costs that can be reduced without affecting event quality.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <!-- Profit Celebration -->
            <div class="col-12">
                <div class="alert alert-success mb-0">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-trophy fa-lg me-3 text-success"></i>
                        <div>
                            <strong>Great Job! Event is Profitable</strong>
                            <p class="small mb-0">Net profit: <strong>£{{ number_format($netProfit, 0) }}</strong>. Keep monitoring expenses to maintain profitability.</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if($isOverBudget)
            <!-- Over Budget Warning -->
            <div class="col-md-6">
                <div class="alert alert-danger mb-0 h-100">
                    <h6 class="alert-heading"><i class="fas fa-chart-line me-2"></i>Over Budget ({{ number_format($budgetUtilisation, 0) }}%)</h6>
                    <p class="small mb-2">You've exceeded your allocated budget. Consider:</p>
                    <ul class="small mb-0">
                        <li>Reviewing and cutting non-essential expenses</li>
                        <li>Postponing discretionary spending</li>
                        <li>Negotiating payment terms with vendors</li>
                        <li>Increasing your budget allocation if revenue supports it</li>
                    </ul>
                </div>
            </div>
            @elseif($isUnderBudget && $budgetUtilisation > 0)
            <!-- Under Budget - Opportunity -->
            <div class="col-md-6">
                <div class="alert alert-info mb-0 h-100">
                    <h6 class="alert-heading"><i class="fas fa-piggy-bank me-2"></i>Under Budget ({{ number_format($budgetUtilisation, 0) }}%)</h6>
                    <p class="small mb-2">You have budget headroom! Consider investing in:</p>
                    <ul class="small mb-0">
                        <li><strong>Marketing:</strong> Boost ticket sales with paid advertising</li>
                        <li><strong>Production:</strong> Enhance stage, lighting, or sound</li>
                        <li><strong>Guest Experience:</strong> Add amenities or entertainment</li>
                        <li><strong>Reserve:</strong> Keep as contingency for unexpected costs</li>
                    </ul>
                </div>
            </div>
            @endif

            @if($hasLowRevenue && $totalRevenue > 0)
            <!-- Revenue Tips -->
            <div class="col-md-6">
                <div class="alert alert-light border mb-0 h-100">
                    <h6 class="alert-heading"><i class="fas fa-pound-sign me-2 text-success"></i>Revenue Optimisation</h6>
                    <ul class="small mb-0">
                        <li><strong>Tiered Pricing:</strong> Offer VIP, Early Bird, and Standard tickets</li>
                        <li><strong>Upsells:</strong> Parking, merchandise, meet & greet packages</li>
                        <li><strong>Partnerships:</strong> Seek sponsors for additional revenue</li>
                        <li><strong>Group Discounts:</strong> Encourage bulk purchases</li>
                    </ul>
                </div>
            </div>
            @endif

            <!-- General Tips -->
            <div class="col-md-{{ ($isOverBudget || $isUnderBudget || $hasLowRevenue) ? '6' : '12' }}">
                <div class="alert alert-light border mb-0 h-100">
                    <h6 class="alert-heading"><i class="fas fa-clipboard-list me-2 text-primary"></i>Best Practices</h6>
                    <div class="row">
                        <div class="col-6">
                            <ul class="small mb-0">
                                <li>Track expenses as they occur</li>
                                <li>Get multiple vendor quotes</li>
                                <li>Keep receipts for all purchases</li>
                            </ul>
                        </div>
                        <div class="col-6">
                            <ul class="small mb-0">
                                <li>Review P&L weekly</li>
                                <li>Set payment reminders</li>
                                <li>Categorise expenses accurately</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
