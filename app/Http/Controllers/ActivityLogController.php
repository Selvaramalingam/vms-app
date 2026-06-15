<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        $logs = $query->paginate(20)->withQueryString();
        $users = \App\Models\User::orderBy('name')->get();
        $modules = ActivityLog::select('module')->distinct()->pluck('module');
        $actions = ['created', 'updated', 'deleted', 'login', 'logout'];
        
        return view('activity_logs.index', compact('logs', 'users', 'modules', 'actions'));
    }
}
