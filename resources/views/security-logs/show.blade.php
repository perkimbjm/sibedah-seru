@extends('layouts.main')
@php
    $menuName = 'Security Log Detail';
    $currentRoute = ucwords(str_replace('.', ' / ', Route::currentRouteName()));
@endphp
@section('content')

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Security Log Detail</h3>
        <div class="card-tools">
            <a href="{{ route('app.security-logs.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">ID</th>
                        <td>{{ $securityLog->id }}</td>
                    </tr>
                    <tr>
                        <th>Event Type</th>
                        <td>
                            <span class="badge badge-{{ $securityLog->event_type == 'login_failed' || $securityLog->event_type == 'register_failed' ? 'danger' : ($securityLog->event_type == 'login_success' || $securityLog->event_type == 'register_success' ? 'success' : 'warning') }}">
                                {{ ucwords(str_replace('_', ' ', $securityLog->event_type)) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>IP Address</th>
                        <td>{{ $securityLog->ip_address }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $securityLog->email ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="badge badge-{{ $securityLog->status == 'success' ? 'success' : ($securityLog->status == 'failed' ? 'danger' : ($securityLog->status == 'blocked' ? 'warning' : 'info')) }}">
                                {{ ucfirst($securityLog->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Attempt Count</th>
                        <td>{{ $securityLog->attempt_count }}</td>
                    </tr>
                    <tr>
                        <th>Created At</th>
                        <td>{{ $securityLog->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                    <tr>
                        <th>Updated At</th>
                        <td>{{ $securityLog->updated_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h5>User Agent</h5>
                <div class="p-3 rounded bg-light">
                    <code>{{ $securityLog->user_agent ?? 'N/A' }}</code>
                </div>

                @if($securityLog->details)
                <h5 class="mt-4">Additional Details</h5>
                <div class="p-3 rounded bg-light">
                    <pre><code>{{ json_encode($securityLog->details, JSON_PRETTY_PRINT) }}</code></pre>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
