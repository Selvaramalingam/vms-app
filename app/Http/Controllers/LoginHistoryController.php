<?php

namespace App\Http\Controllers;

use App\Models\LoginHistory;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LoginHistoryController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'Admin');
        $userId = $request->get('user_id', '');
        $period = $request->get('period', '');

        // Base query filtered by role tab
        $query = LoginHistory::with('user')
            ->where('role', $tab)
            ->orderBy('login_datetime', 'desc');

        // Filter by user ID
        if (!empty($userId)) {
            $query->where('user_id', $userId);
        }

        // Period filter
        if ($period === 'today') {
            $query->whereDate('login_datetime', Carbon::today());
        } elseif ($period === '7days') {
            $query->where('login_datetime', '>=', Carbon::now()->subDays(7));
        } elseif ($period === '30days') {
            $query->where('login_datetime', '>=', Carbon::now()->subDays(30));
        }

        $histories = $query->paginate(20)->appends($request->query());
        
        $users = \App\Models\User::role($tab)->orderBy('name')->get();

        // Last login for Admin
        $lastAdminLogin = LoginHistory::with('user')
            ->where('role', 'Admin')
            ->orderBy('login_datetime', 'desc')
            ->first();

        // Last login for Vehicle
        $lastVehicleLogin = LoginHistory::with('user')
            ->where('role', 'Vehicle')
            ->orderBy('login_datetime', 'desc')
            ->first();

        return view('login-history.index', compact(
            'histories', 'tab', 'userId', 'period', 'users',
            'lastAdminLogin', 'lastVehicleLogin'
        ));
    }
}
