<?php

namespace App\Http\Controllers;

use App\Models\SecurityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class SecurityLogController extends Controller
{
    /**
     * Display a listing of security logs
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('security_log_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $query = SecurityLog::query();

        // Filter by event type
        if ($request->has('event_type') && $request->event_type) {
            $query->where('event_type', $request->event_type);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by IP address
        if ($request->has('ip_address') && $request->ip_address) {
            $query->where('ip_address', 'like', '%' . $request->ip_address . '%');
        }

        // Filter by email
        if ($request->has('email') && $request->email) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $securityLogs = $query->orderBy('created_at', 'desc')->paginate(50);

        return view('security-logs.index', compact('securityLogs'));
    }

    /**
     * Show security log details
     */
    public function show(SecurityLog $securityLog)
    {
        abort_if(Gate::denies('security_log_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('security-logs.show', compact('securityLog'));
    }

    /**
     * Get security statistics
     */
    public function statistics()
    {
        abort_if(Gate::denies('security_log_statistics'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $stats = [
            'total_events' => SecurityLog::count(),
            'failed_logins' => SecurityLog::where('event_type', 'login_failed')->count(),
            'failed_registrations' => SecurityLog::where('event_type', 'register_failed')->count(),
            'blocked_ips' => SecurityLog::where('status', 'blocked')->count(),
            'recent_events' => SecurityLog::where('created_at', '>=', now()->subHours(24))->count(),
        ];

        return view('security-logs.statistics', compact('stats'));
    }

    /**
     * Clear old security logs
     */
    public function clearOldLogs(Request $request)
    {
        abort_if(Gate::denies('security_log_clear'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $days = $request->input('days', 30);
        $deleted = SecurityLog::where('created_at', '<', now()->subDays($days))->delete();

        return back()->with('success', "Berhasil menghapus {$deleted} log keamanan yang lebih dari {$days} hari.");
    }
}
