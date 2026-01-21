@extends('layouts.organiser_layout')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- P&L Sidebar Navigation -->
        <div class="col-lg-2 col-md-3">
            @include('pnl.partials.sidebar')
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-10 col-md-9">
            @yield('pnl_content')
        </div>
    </div>
</div>
@endsection
