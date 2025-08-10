@extends('layouts.main')
@php
    $menuName = 'Security Logs';
    $currentRoute = ucwords(str_replace('.', ' / ', Route::currentRouteName()));
@endphp
@section('content')

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Security Logs</h3>
        <div class="card-tools">
            @can('security_log_statistics')
            <a href="{{ route('app.security-logs.statistics') }}" class="btn btn-info btn-sm">
                <i class="fas fa-chart-bar"></i> Statistics
            </a>
            @endcan
        </div>
    </div>

    <div class="card-body">
        <!-- Filter Form -->
        <form method="GET" action="{{ route('app.security-logs.index') }}" class="mb-4">
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Event Type</label>
                        <select name="event_type" class="form-control">
                            <option value="">All Events</option>
                            <option value="login_failed" {{ request('event_type') == 'login_failed' ? 'selected' : '' }}>Login Failed</option>
                            <option value="login_success" {{ request('event_type') == 'login_success' ? 'selected' : '' }}>Login Success</option>
                            <option value="register_failed" {{ request('event_type') == 'register_failed' ? 'selected' : '' }}>Register Failed</option>
                            <option value="register_success" {{ request('event_type') == 'register_success' ? 'selected' : '' }}>Register Success</option>
                            <option value="login_blocked" {{ request('event_type') == 'login_blocked' ? 'selected' : '' }}>Login Blocked</option>
                            <option value="register_blocked" {{ request('event_type') == 'register_blocked' ? 'selected' : '' }}>Register Blocked</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Success</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="blocked" {{ request('status') == 'blocked' ? 'selected' : '' }}>Blocked</option>
                            <option value="info" {{ request('status') == 'info' ? 'selected' : '' }}>Info</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>IP Address</label>
                        <input type="text" name="ip_address" class="form-control" value="{{ request('ip_address') }}" placeholder="Search IP">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="text" name="email" class="form-control" value="{{ request('email') }}" placeholder="Search Email">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Date From</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Date To</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('app.security-logs.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </div>
        </form>

        <!-- Security Logs Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Event Type</th>
                        <th>IP Address</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Attempt Count</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($securityLogs as $log)
                    <tr>
                        <td>{{ $log->id }}</td>
                        <td>
                            <span class="badge badge-{{ $log->event_type == 'login_failed' || $log->event_type == 'register_failed' ? 'danger' : ($log->event_type == 'login_success' || $log->event_type == 'register_success' ? 'success' : 'warning') }}">
                                {{ ucwords(str_replace('_', ' ', $log->event_type)) }}
                            </span>
                        </td>
                        <td>{{ $log->ip_address }}</td>
                        <td>{{ $log->email ?? '-' }}</td>
                        <td>
                            <span class="badge badge-{{ $log->status == 'success' ? 'success' : ($log->status == 'failed' ? 'danger' : ($log->status == 'blocked' ? 'warning' : 'info')) }}">
                                {{ ucfirst($log->status) }}
                            </span>
                        </td>
                        <td>{{ $log->attempt_count }}</td>
                        <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        <td>
                            <a href="{{ route('app.security-logs.show', $log->id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">No security logs found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $securityLogs->appends(request()->query())->links() }}
        </div>
    </div>
</div>

@endsection
