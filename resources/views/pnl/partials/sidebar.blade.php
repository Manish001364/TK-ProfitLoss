{{-- P&L Module Internal Navigation - TicketKart Theme --}}
<div class="pnl-sidebar">
    <div class="pnl-sidebar-header">
        <i class="fas fa-chart-line me-2"></i>
        <span>P&L Module</span>
    </div>
    <nav class="pnl-nav">
        <a href="{{ route('pnl.dashboard') }}" class="pnl-nav-link {{ request()->routeIs('pnl.dashboard') || request()->routeIs('pnl.dashboard.index') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('pnl.dashboard.cashflow') }}" class="pnl-nav-link {{ request()->routeIs('pnl.dashboard.cashflow') ? 'active' : '' }}">
            <i class="fas fa-chart-area"></i>
            <span>Cash Flow</span>
        </a>
        <a href="{{ route('pnl.events.index') }}" class="pnl-nav-link {{ request()->routeIs('pnl.events.*') ? 'active' : '' }}">
            <i class="fas fa-calendar-alt"></i>
            <span>Events</span>
        </a>
        <a href="{{ route('pnl.vendors.index') }}" class="pnl-nav-link {{ request()->routeIs('pnl.vendors.*') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            <span>Vendors & Artists</span>
        </a>
        <a href="{{ route('pnl.expenses.index') }}" class="pnl-nav-link {{ request()->routeIs('pnl.expenses.*') ? 'active' : '' }}">
            <i class="fas fa-receipt"></i>
            <span>Expenses</span>
        </a>
        <a href="{{ route('pnl.revenues.index') }}" class="pnl-nav-link {{ request()->routeIs('pnl.revenues.*') ? 'active' : '' }}">
            <i class="fas fa-pound-sign"></i>
            <span>Revenue</span>
        </a>
        <a href="{{ route('pnl.payments.index') }}" class="pnl-nav-link {{ request()->routeIs('pnl.payments.*') ? 'active' : '' }}">
            <i class="fas fa-credit-card"></i>
            <span>Payments</span>
        </a>
        
        <div class="pnl-nav-divider"></div>
        <small class="pnl-nav-label">Configuration</small>
        
        <a href="{{ route('pnl.categories.index') }}" class="pnl-nav-link {{ request()->routeIs('pnl.categories.*') ? 'active' : '' }}">
            <i class="fas fa-tags"></i>
            <span>Expense Categories</span>
        </a>
        <a href="{{ route('pnl.service-types.index') }}" class="pnl-nav-link {{ request()->routeIs('pnl.service-types.*') ? 'active' : '' }}">
            <i class="fas fa-user-tag"></i>
            <span>Service Types</span>
        </a>
        <a href="{{ route('pnl.settings.index') }}" class="pnl-nav-link {{ request()->routeIs('pnl.settings.*') ? 'active' : '' }}">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>
    </nav>
</div>

<style>
/* Light Theme Sidebar - TicketKart Style */
.pnl-sidebar {
    background: #ffffff;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    overflow: hidden;
    margin-bottom: 20px;
    border: 1px solid #e9ecef;
}

.pnl-sidebar-header {
    background: #dc3545;
    color: white;
    padding: 15px 20px;
    font-weight: 600;
    font-size: 14px;
    display: flex;
    align-items: center;
}

.pnl-nav {
    padding: 10px 0;
}

.pnl-nav-link {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: #495057;
    text-decoration: none;
    transition: all 0.2s ease;
    border-left: 3px solid transparent;
}

.pnl-nav-link:hover {
    background: rgba(220, 53, 69, 0.08);
    color: #dc3545;
    border-left-color: #dc3545;
    text-decoration: none;
}

.pnl-nav-link.active {
    background: rgba(220, 53, 69, 0.12);
    color: #dc3545;
    border-left-color: #dc3545;
    font-weight: 500;
}

.pnl-nav-link i {
    width: 20px;
    margin-right: 12px;
    font-size: 14px;
    color: #6c757d;
}

.pnl-nav-link:hover i,
.pnl-nav-link.active i {
    color: #dc3545;
}

.pnl-nav-link span {
    font-size: 14px;
}

.pnl-nav-divider {
    height: 1px;
    background: #e9ecef;
    margin: 10px 15px;
}

.pnl-nav-label {
    display: block;
    padding: 5px 20px;
    color: #6c757d;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Mobile - Horizontal scrollable menu */
@media (max-width: 991px) {
    .pnl-sidebar {
        margin-bottom: 15px;
        border-radius: 8px;
    }
    .pnl-sidebar-header {
        padding: 12px 15px;
        font-size: 13px;
    }
    .pnl-nav {
        display: flex;
        overflow-x: auto;
        padding: 8px;
        gap: 5px;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }
    .pnl-nav::-webkit-scrollbar {
        display: none;
    }
    .pnl-nav-link {
        padding: 8px 12px;
        border-left: none;
        border-radius: 6px;
        white-space: nowrap;
        flex-shrink: 0;
        border: 1px solid #e9ecef;
    }
    .pnl-nav-link.active {
        border-color: #dc3545;
        background: rgba(220, 53, 69, 0.1);
    }
    .pnl-nav-link i {
        margin-right: 6px;
        font-size: 12px;
    }
    .pnl-nav-link span {
        font-size: 12px;
    }
}

/* Very small screens - icon only */
@media (max-width: 576px) {
    .pnl-nav-link span {
        display: none;
    }
    .pnl-nav-link i {
        margin-right: 0;
        font-size: 16px;
    }
    .pnl-nav-link {
        padding: 10px 14px;
    }
}
</style>
