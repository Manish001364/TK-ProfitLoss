{{--
    P&L Module Sidebar Navigation
    
    Self-contained navigation menu for the P&L module.
    Styled to match TicketKart theme with red accent color.
    
    Sections:
    - Main: Dashboard, Cash Flow, Events, Vendors, Expenses, Revenue, Payments
    - Configuration: Categories & Services, Settings
--}}

<style>
/* P&L Sidebar Styles */
.pnl-sidebar {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    overflow: hidden;
}
.pnl-sidebar-header {
    background: linear-gradient(135deg, #dc3545 0%, #b02a37 100%);
    color: #fff;
    padding: 1rem;
    font-weight: 600;
    font-size: 0.95rem;
}
.pnl-nav {
    padding: 0.5rem 0;
}
.pnl-nav-link {
    display: flex;
    align-items: center;
    padding: 0.6rem 1rem;
    color: #333;
    text-decoration: none;
    font-size: 0.875rem;
    transition: all 0.2s;
    border-left: 3px solid transparent;
}
.pnl-nav-link:hover {
    background: #f8f9fa;
    color: #dc3545;
}
.pnl-nav-link.active {
    background: #fff5f5;
    color: #dc3545;
    border-left-color: #dc3545;
    font-weight: 500;
}
.pnl-nav-link i {
    width: 20px;
    margin-right: 0.75rem;
    text-align: center;
    font-size: 0.9rem;
}
.pnl-nav-divider {
    height: 1px;
    background: #eee;
    margin: 0.5rem 1rem;
}
.pnl-nav-label {
    display: block;
    padding: 0.5rem 1rem 0.25rem;
    color: #999;
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
</style>

<div class="pnl-sidebar">
    {{-- Header --}}
    <div class="pnl-sidebar-header">
        <i class="fas fa-chart-line me-2"></i>
        <span>P&L Module</span>
    </div>
    
    {{-- Navigation Links --}}
    <nav class="pnl-nav">
        {{-- Main Section --}}
        <a href="{{ route('pnl.dashboard') }}" 
           class="pnl-nav-link {{ request()->routeIs('pnl.dashboard') && !request()->routeIs('pnl.dashboard.cashflow') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
        
        <a href="{{ route('pnl.dashboard.cashflow') }}" 
           class="pnl-nav-link {{ request()->routeIs('pnl.dashboard.cashflow') ? 'active' : '' }}">
            <i class="fas fa-chart-area"></i>
            <span>Cash Flow</span>
        </a>
        
        <a href="{{ route('pnl.events.index') }}" 
           class="pnl-nav-link {{ request()->routeIs('pnl.events.*') ? 'active' : '' }}">
            <i class="fas fa-calendar-alt"></i>
            <span>Events</span>
        </a>
        
        <a href="{{ route('pnl.vendors.index') }}" 
           class="pnl-nav-link {{ request()->routeIs('pnl.vendors.*') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            <span>Vendors & Artists</span>
        </a>
        
        <a href="{{ route('pnl.expenses.index') }}" 
           class="pnl-nav-link {{ request()->routeIs('pnl.expenses.*') ? 'active' : '' }}">
            <i class="fas fa-receipt"></i>
            <span>Expenses</span>
        </a>
        
        <a href="{{ route('pnl.revenues.index') }}" 
           class="pnl-nav-link {{ request()->routeIs('pnl.revenues.*') ? 'active' : '' }}">
            <i class="fas fa-pound-sign"></i>
            <span>Revenue</span>
        </a>
        
        <a href="{{ route('pnl.payments.index') }}" 
           class="pnl-nav-link {{ request()->routeIs('pnl.payments.*') ? 'active' : '' }}">
            <i class="fas fa-credit-card"></i>
            <span>Payments</span>
        </a>
        
        {{-- Configuration Section --}}
        <div class="pnl-nav-divider"></div>
        <small class="pnl-nav-label">Configuration</small>
        
        <a href="{{ route('pnl.configuration.index') }}" 
           class="pnl-nav-link {{ request()->routeIs('pnl.configuration.*') || request()->routeIs('pnl.categories.*') || request()->routeIs('pnl.service-types.*') ? 'active' : '' }}">
            <i class="fas fa-tags"></i>
            <span>Categories & Services</span>
        </a>
        
        <a href="{{ route('pnl.settings.index') }}" 
           class="pnl-nav-link {{ request()->routeIs('pnl.settings.*') ? 'active' : '' }}">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>
    </nav>
</div>
