<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')
            ->orderBy('created_at', 'desc');

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by model type
        if ($request->filled('model_type')) {
            $query->where('model_type', 'like', '%'.$request->model_type.'%');
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $auditLogs = $query->paginate(50);

        // Get unique model types for filter
        $modelTypes = AuditLog::select('model_type')
            ->distinct()
            ->pluck('model_type')
            ->map(function ($type) {
                return class_basename($type);
            })
            ->sort()
            ->values();

        // Get users who have audit logs
        $users = \App\Models\User::whereIn('id', function ($query) {
            $query->select('user_id')
                ->from('audit_logs')
                ->whereNotNull('user_id')
                ->distinct();
        })->orderBy('name')->get();

        return view('audit-logs.index', compact('auditLogs', 'modelTypes', 'users'));
    }

    public function show($id)
    {
        $auditLog = AuditLog::with('user')->findOrFail($id);

        return view('audit-logs.show', compact('auditLog'));
    }
}
