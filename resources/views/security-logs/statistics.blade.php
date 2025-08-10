@extends('layouts.main')
@php
    $menuName = 'Security Statistics';
    $currentRoute = ucwords(str_replace('.', ' / ', Route::currentRouteName()));
@endphp
@section('content')

<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $stats['total_events'] }}</h3>
                <p>Total Security Events</p>
            </div>
            <div class="icon">
                <i class="fas fa-shield-alt"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $stats['failed_logins'] }}</h3>
                <p>Failed Login Attempts</p>
            </div>
            <div class="icon">
                <i class="fas fa-times-circle"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $stats['failed_registrations'] }}</h3>
                <p>Failed Registration Attempts</p>
            </div>
            <div class="icon">
                <i class="fas fa-user-times"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $stats['blocked_ips'] }}</h3>
                <p>Blocked IP Addresses</p>
            </div>
            <div class="icon">
                <i class="fas fa-ban"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Security Events (Last 24 Hours)</h3>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <h2 class="text-info">{{ $stats['recent_events'] }}</h2>
                    <p>Events in the last 24 hours</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Clear Old Logs</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('app.security-logs.clear') }}">
                    @csrf
                    <div class="form-group">
                        <label>Delete logs older than (days)</label>
                        <select name="days" class="form-control">
                            <option value="7">7 days</option>
                            <option value="30" selected>30 days</option>
                            <option value="60">60 days</option>
                            <option value="90">90 days</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete old logs?')">
                        <i class="fas fa-trash"></i> Clear Old Logs
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Security Recommendations</h3>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <h5><i class="fas fa-info-circle"></i> Security Tips</h5>
            <ul>
                <li>Monitor failed login attempts regularly</li>
                <li>Review blocked IP addresses for false positives</li>
                <li>Keep security logs for at least 30 days</li>
                <li>Set up alerts for unusual activity patterns</li>
                <li>Regularly update security policies</li>
            </ul>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Quick Actions</h3>
    </div>
    <div class="card-body">
        <a href="{{ route('app.security-logs.index') }}" class="btn btn-primary">
            <i class="fas fa-list"></i> View All Security Logs
        </a>
        <a href="{{ route('app.users.index') }}" class="btn btn-secondary">
            <i class="fas fa-users"></i> Manage Users
        </a>
    </div>
</div>

@endsection
