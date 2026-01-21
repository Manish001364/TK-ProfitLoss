{{-- P&L Module Walkthrough / Onboarding --}}
@php
    // Check if user has seen walkthrough or has created events
    $hasSeenWalkthrough = session('pnl_walkthrough_seen', false);
    $hasEvents = isset($events) ? $events->count() > 0 : false;
    $showWalkthrough = !$hasSeenWalkthrough && !$hasEvents;
@endphp

@if($showWalkthrough || request()->has('show_walkthrough'))
<div class="modal fade" id="walkthroughModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title"><i class="fas fa-chart-line me-2"></i>Welcome to P&L Module</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" onclick="dismissWalkthrough()"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Walkthrough Slides -->
                <div id="walkthroughCarousel" class="carousel slide" data-bs-ride="false">
                    <div class="carousel-inner">
                        <!-- Slide 1: Welcome -->
                        <div class="carousel-item active">
                            <div class="p-4 text-center">
                                <div class="mb-4">
                                    <i class="fas fa-chart-pie fa-4x text-danger"></i>
                                </div>
                                <h4>Track Your Event Profitability</h4>
                                <p class="text-muted">The P&L (Profit & Loss) module helps you manage expenses, track revenue, and understand your event's financial performance.</p>
                                <div class="row g-3 mt-4 text-start">
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-start">
                                            <i class="fas fa-calendar-alt text-primary me-2 mt-1"></i>
                                            <div>
                                                <strong>Events</strong>
                                                <p class="small text-muted mb-0">Create P&L for each event or link to TicketKart events</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-start">
                                            <i class="fas fa-receipt text-danger me-2 mt-1"></i>
                                            <div>
                                                <strong>Expenses</strong>
                                                <p class="small text-muted mb-0">Track all event costs with tax & invoicing</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-start">
                                            <i class="fas fa-pound-sign text-success me-2 mt-1"></i>
                                            <div>
                                                <strong>Revenue</strong>
                                                <p class="small text-muted mb-0">Track ticket sales and other income</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 2: Features -->
                        <div class="carousel-item">
                            <div class="p-4">
                                <h4 class="text-center mb-4"><i class="fas fa-star text-warning me-2"></i>Key Features</h4>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="card border-0 bg-light h-100">
                                            <div class="card-body">
                                                <h6><i class="fas fa-users text-info me-2"></i>Vendor Management</h6>
                                                <ul class="small text-muted mb-0">
                                                    <li>Add artists, DJs, caterers, suppliers</li>
                                                    <li>Store contact & payment details</li>
                                                    <li>Track payment history per vendor</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border-0 bg-light h-100">
                                            <div class="card-body">
                                                <h6><i class="fas fa-file-invoice text-success me-2"></i>Invoicing & Tax</h6>
                                                <ul class="small text-muted mb-0">
                                                    <li>Auto-generated invoice numbers</li>
                                                    <li>VAT/Tax calculations</li>
                                                    <li>PDF invoice generation & email</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border-0 bg-light h-100">
                                            <div class="card-body">
                                                <h6><i class="fas fa-credit-card text-warning me-2"></i>Payment Tracking</h6>
                                                <ul class="small text-muted mb-0">
                                                    <li>Pending, scheduled & paid status</li>
                                                    <li>Overdue payment alerts</li>
                                                    <li>Payment reminders via email</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border-0 bg-light h-100">
                                            <div class="card-body">
                                                <h6><i class="fas fa-chart-bar text-danger me-2"></i>Analytics & Reports</h6>
                                                <ul class="small text-muted mb-0">
                                                    <li>Revenue vs expense trends</li>
                                                    <li>Budget utilisation tracking</li>
                                                    <li>Export to Excel & PDF</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 3: Quick Start -->
                        <div class="carousel-item">
                            <div class="p-4">
                                <h4 class="text-center mb-4"><i class="fas fa-rocket text-primary me-2"></i>Quick Start Guide</h4>
                                <div class="d-flex flex-column gap-3">
                                    <div class="d-flex align-items-start p-3 bg-light rounded">
                                        <span class="badge bg-danger rounded-circle me-3 fs-6">1</span>
                                        <div>
                                            <strong>Configure Settings</strong>
                                            <p class="small text-muted mb-0">Go to <strong>Settings</strong> to set your default VAT rate and invoice preferences.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-start p-3 bg-light rounded">
                                        <span class="badge bg-danger rounded-circle me-3 fs-6">2</span>
                                        <div>
                                            <strong>Create an Event</strong>
                                            <p class="small text-muted mb-0">Go to <strong>Events</strong> and create a new event (or link to your TicketKart event).</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-start p-3 bg-light rounded">
                                        <span class="badge bg-danger rounded-circle me-3 fs-6">3</span>
                                        <div>
                                            <strong>Add Vendors</strong>
                                            <p class="small text-muted mb-0">Go to <strong>Vendors & Artists</strong> to add your suppliers, DJs, artists, etc.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-start p-3 bg-light rounded">
                                        <span class="badge bg-danger rounded-circle me-3 fs-6">4</span>
                                        <div>
                                            <strong>Track Expenses & Revenue</strong>
                                            <p class="small text-muted mb-0">Add expenses and revenue entries to see your P&L analysis on the dashboard.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Carousel Indicators -->
                    <div class="d-flex justify-content-center pb-3">
                        <button type="button" data-bs-target="#walkthroughCarousel" data-bs-slide-to="0" class="btn btn-sm btn-outline-secondary mx-1 active" aria-current="true"></button>
                        <button type="button" data-bs-target="#walkthroughCarousel" data-bs-slide-to="1" class="btn btn-sm btn-outline-secondary mx-1"></button>
                        <button type="button" data-bs-target="#walkthroughCarousel" data-bs-slide-to="2" class="btn btn-sm btn-outline-secondary mx-1"></button>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="dontShowAgain">
                    <label class="form-check-label small" for="dontShowAgain">Don't show this again</label>
                </div>
                <div class="ms-auto">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-target="#walkthroughCarousel" data-bs-slide="prev">
                        <i class="fas fa-arrow-left"></i> Back
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-target="#walkthroughCarousel" data-bs-slide="next">
                        Next <i class="fas fa-arrow-right"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" onclick="dismissWalkthrough()">
                        Get Started <i class="fas fa-check"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show walkthrough modal on first visit
    @if($showWalkthrough && !request()->has('hide_walkthrough'))
    var walkthroughModal = new bootstrap.Modal(document.getElementById('walkthroughModal'));
    walkthroughModal.show();
    @endif
});

function dismissWalkthrough() {
    var dontShow = document.getElementById('dontShowAgain').checked;
    if (dontShow) {
        // Store in localStorage to persist across sessions
        localStorage.setItem('pnl_walkthrough_dismissed', 'true');
    }
    // Also set session via AJAX
    fetch('{{ route("pnl.settings.dismiss-walkthrough") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ dont_show: dontShow })
    });
    var modal = bootstrap.Modal.getInstance(document.getElementById('walkthroughModal'));
    if (modal) modal.hide();
}

// Check localStorage on load
if (localStorage.getItem('pnl_walkthrough_dismissed') === 'true') {
    document.getElementById('walkthroughModal')?.remove();
}
</script>
@endif
