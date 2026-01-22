{{--
    P&L Module Layout
    
    Extends the main TicketKart organiser layout and adds:
    - P&L specific sidebar navigation
    - Main content area for P&L pages
    
    Usage: @extends('pnl.layouts.app')
--}}
@extends('layouts.organiser_layout')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        {{-- P&L Sidebar Navigation --}}
        <div class="col-lg-2 col-md-3 mb-4 mb-md-0">
            @include('pnl.partials.sidebar')
        </div>
        
        {{-- Main Content Area --}}
        <div class="col-lg-10 col-md-9">
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show mb-3" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            {{-- Page Content --}}
            @yield('pnl_content')
        </div>
    </div>
</div>
@endsection
