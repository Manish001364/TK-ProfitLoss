{{-- P&L Module Internal Navigation --}}
<div class="pnl-sidebar">
    <div class="pnl-sidebar-header">
        <h6 class="mb-0">
            <i class="fas fa-chart-line me-2"></i>P&L Module
        </h6>
    </div>
    <nav class="pnl-nav">
        <a href="{{ route('pnl.dashboard') }}" class="pnl-nav-link {{ request()->routeIs('pnl.dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
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
        <a href="{{ route('pnl.categories.index') }}" class="pnl-nav-link {{ request()->routeIs('pnl.categories.*') ? 'active' : '' }}">
            <i class="fas fa-tags"></i>
            <span>Categories</span>
        </a>
        <a href="{{ route('pnl.settings.index') }}" class="pnl-nav-link {{ request()->routeIs('pnl.settings.*') ? 'active' : '' }}">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>
    </nav>
</div>

<style>
.pnl-sidebar {
    background: #1a1a2e;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 20px;
}

.pnl-sidebar-header {
    background: #dc3545;
    color: white;
    padding: 15px 20px;
    font-weight: 600;
}

.pnl-nav {
    padding: 10px 0;
}

.pnl-nav-link {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: #a0a0a0;
    text-decoration: none;
    transition: all 0.2s ease;
    border-left: 3px solid transparent;
}

.pnl-nav-link:hover {
    background: rgba(220, 53, 69, 0.1);
    color: #fff;
    border-left-color: #dc3545;
}

.pnl-nav-link.active {
    background: rgba(220, 53, 69, 0.2);
    color: #fff;
    border-left-color: #dc3545;
}

.pnl-nav-link i {
    width: 20px;
    margin-right: 12px;
    font-size: 14px;
}

.pnl-nav-link span {
    font-size: 14px;
}

/* For horizontal layout on mobile */
@media (max-width: 991px) {
    .pnl-sidebar {
        margin-bottom: 15px;
    }
    .pnl-nav {
        display: flex;
        flex-wrap: wrap;
        padding: 5px;
    }
    .pnl-nav-link {
        padding: 8px 12px;
        border-left: none;
        border-radius: 5px;
        margin: 3px;
    }
    .pnl-nav-link i {
        margin-right: 6px;
    }
    .pnl-nav-link span {
        font-size: 12px;
    }
}
</style>
