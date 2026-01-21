{{-- 
    P&L Module - Sidebar Menu Code
    
    INSTRUCTIONS:
    1. Copy this entire code block
    2. Paste it in your sidebar file: resources/views/customer/sidemenu.blade.php
    3. Place it after your existing menu items (e.g., after Events, Tickets, etc.)
    
    CSS FILE:
    - Copy pnl-sidebar.css to: public/css/pnl-sidebar.css
    - Add this to your layout head: <link rel="stylesheet" href="{{ asset('css/pnl-sidebar.css') }}">
--}}

{{-- P&L Module Menu --}}
@php
    $pnlActive = request()->is('pnl/*') || request()->routeIs('pnl.*');
@endphp

<li class="pnl-menu-item {{ $pnlActive ? 'active' : '' }}">
    <a href="#pnlSubmenu" data-bs-toggle="collapse" class="{{ $pnlActive ? '' : 'collapsed' }}" aria-expanded="{{ $pnlActive ? 'true' : 'false' }}">
        <i class="fas fa-chart-line pnl-menu-icon"></i>
        <span class="pnl-menu-text">P&L</span>
        <i class="fas fa-chevron-down pnl-chevron"></i>
    </a>
    
    <ul class="collapse pnl-submenu {{ $pnlActive ? 'show' : '' }}" id="pnlSubmenu">
        <li class="{{ request()->routeIs('pnl.dashboard') ? 'active' : '' }}">
            <a href="{{ route('pnl.dashboard') }}">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="{{ request()->routeIs('pnl.events.*') ? 'active' : '' }}">
            <a href="{{ route('pnl.events.index') }}">
                <i class="fas fa-calendar-alt"></i>
                <span>Events</span>
            </a>
        </li>
        <li class="{{ request()->routeIs('pnl.vendors.*') ? 'active' : '' }}">
            <a href="{{ route('pnl.vendors.index') }}">
                <i class="fas fa-users"></i>
                <span>Vendors & Artists</span>
            </a>
        </li>
        <li class="{{ request()->routeIs('pnl.expenses.*') ? 'active' : '' }}">
            <a href="{{ route('pnl.expenses.index') }}">
                <i class="fas fa-receipt"></i>
                <span>Expenses</span>
            </a>
        </li>
        <li class="{{ request()->routeIs('pnl.revenues.*') ? 'active' : '' }}">
            <a href="{{ route('pnl.revenues.index') }}">
                <i class="fas fa-pound-sign"></i>
                <span>Revenue</span>
            </a>
        </li>
        <li class="{{ request()->routeIs('pnl.payments.*') ? 'active' : '' }}">
            <a href="{{ route('pnl.payments.index') }}">
                <i class="fas fa-credit-card"></i>
                <span>Payments</span>
            </a>
        </li>
        <li class="{{ request()->routeIs('pnl.categories.*') ? 'active' : '' }}">
            <a href="{{ route('pnl.categories.index') }}">
                <i class="fas fa-tags"></i>
                <span>Categories</span>
            </a>
        </li>
        <li class="{{ request()->routeIs('pnl.settings.*') ? 'active' : '' }}">
            <a href="{{ route('pnl.settings.index') }}">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
        </li>
    </ul>
</li>
