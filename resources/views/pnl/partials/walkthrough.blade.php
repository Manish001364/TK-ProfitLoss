{{-- 
    P&L Module Walkthrough / Onboarding Guide
    
    Shows automatically on first visit when:
    - User has no events created yet
    - User hasn't dismissed the walkthrough before
    
    Can be triggered manually via "Show Guide" button
--}}

{{-- Always render the modal HTML so it's available for manual trigger --}}
<div class="modal fade" id="walkthroughModal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="walkthroughModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title" id="walkthroughModalLabel">
                    <i class="fas fa-chart-line me-2"></i>Welcome to P&L Module
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Walkthrough Slides Carousel -->
                <div id="walkthroughCarousel" class="carousel slide" data-bs-ride="false">
                    <div class="carousel-inner">
                        
                        <!-- Slide 1: Welcome & Overview -->
                        <div class="carousel-item active">
                            <div class="p-4 text-center">
                                <div class="mb-4">
                                    <i class="fas fa-chart-pie fa-4x text-danger"></i>
                                </div>
                                <h4>Track Your Event Profitability</h4>
                                <p class="text-muted">
                                    The P&L (Profit & Loss) module helps you manage expenses, 
                                    track revenue, and understand your event's financial performance.
                                </p>
                                <div class="row g-3 mt-4 text-start">
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-start">
                                            <i class="fas fa-calendar-alt text-primary me-2 mt-1"></i>
                                            <div>
                                                <strong>Events</strong>
                                                <p class="small text-muted mb-0">Create P&L for each event</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-start">
                                            <i class="fas fa-receipt text-danger me-2 mt-1"></i>
                                            <div>
                                                <strong>Expenses</strong>
                                                <p class="small text-muted mb-0">Track costs with tax & invoicing</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-start">
                                            <i class="fas fa-pound-sign text-success me-2 mt-1"></i>
                                            <div>
                                                <strong>Revenue</strong>
                                                <p class="small text-muted mb-0">Track ticket sales & income</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 2: Key Features -->
                        <div class="carousel-item">
                            <div class="p-4">
                                <h4 class="text-center mb-4">
                                    <i class="fas fa-star text-warning me-2"></i>Key Features
                                </h4>
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
                                                    <li>Category-wise breakdown</li>
                                                    <li>Export to Excel & PDF</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 3: Quick Start Guide -->
                        <div class="carousel-item">
                            <div class="p-4">
                                <h4 class="text-center mb-4">
                                    <i class="fas fa-rocket text-primary me-2"></i>Quick Start Guide
                                </h4>
                                <div class="d-flex flex-column gap-3">
                                    <div class="d-flex align-items-start p-3 bg-light rounded">
                                        <span class="badge bg-danger rounded-circle me-3 fs-6">1</span>
                                        <div>
                                            <strong>Configure Settings</strong>
                                            <p class="small text-muted mb-0">
                                                Set your default currency, VAT rate, and invoice preferences in 
                                                <strong>Settings</strong>.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-start p-3 bg-light rounded">
                                        <span class="badge bg-danger rounded-circle me-3 fs-6">2</span>
                                        <div>
                                            <strong>Create an Event</strong>
                                            <p class="small text-muted mb-0">
                                                Go to <strong>Events</strong> and create a new event to track its P&L.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-start p-3 bg-light rounded">
                                        <span class="badge bg-danger rounded-circle me-3 fs-6">3</span>
                                        <div>
                                            <strong>Add Vendors</strong>
                                            <p class="small text-muted mb-0">
                                                Add your suppliers, artists, DJs in <strong>Vendors & Artists</strong>.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-start p-3 bg-light rounded">
                                        <span class="badge bg-danger rounded-circle me-3 fs-6">4</span>
                                        <div>
                                            <strong>Track Expenses & Revenue</strong>
                                            <p class="small text-muted mb-0">
                                                Add expenses and revenue entries to see your P&L analysis.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Carousel Indicators -->
                    <div class="d-flex justify-content-center pb-3">
                        <button type="button" data-bs-target="#walkthroughCarousel" data-bs-slide-to="0" 
                                class="btn btn-sm btn-outline-secondary mx-1 active" aria-current="true"></button>
                        <button type="button" data-bs-target="#walkthroughCarousel" data-bs-slide-to="1" 
                                class="btn btn-sm btn-outline-secondary mx-1"></button>
                        <button type="button" data-bs-target="#walkthroughCarousel" data-bs-slide-to="2" 
                                class="btn btn-sm btn-outline-secondary mx-1"></button>
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
                    <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">
                        Get Started <i class="fas fa-check"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
/**
 * P&L Walkthrough Controller
 * Manages the first-time user walkthrough modal
 */
(function() {
    'use strict';
    
    var walkthroughModal = null;
    var modalElement = document.getElementById('walkthroughModal');
    
    if (!modalElement) return;
    
    // Initialize Bootstrap modal
    walkthroughModal = new bootstrap.Modal(modalElement);
    
    // Check if should auto-show (first visit, no events)
    var hasEvents = {{ isset($events) && $events->count() > 0 ? 'true' : 'false' }};
    var dismissed = localStorage.getItem('pnl_walkthrough_dismissed') === 'true';
    var forceShow = {{ request()->has('show_walkthrough') ? 'true' : 'false' }};
    
    // Auto-show on first visit if no events and not dismissed
    if (forceShow || (!hasEvents && !dismissed)) {
        setTimeout(function() {
            walkthroughModal.show();
        }, 500); // Small delay for page to fully load
    }
    
    // Handle modal close - save dismissal preference
    modalElement.addEventListener('hidden.bs.modal', function() {
        var dontShow = document.getElementById('dontShowAgain');
        if (dontShow && dontShow.checked) {
            localStorage.setItem('pnl_walkthrough_dismissed', 'true');
            // Also save to server if endpoint exists
            if (typeof fetch !== 'undefined') {
                fetch('{{ route("pnl.settings.dismiss-walkthrough") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ dont_show: true })
                }).catch(function() {}); // Silently fail
            }
        }
    });
    
    // Global function to show walkthrough manually
    window.showWalkthroughAgain = function() {
        if (walkthroughModal) {
            walkthroughModal.show();
        }
    };
})();
</script>
