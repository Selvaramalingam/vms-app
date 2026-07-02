<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use App\Models\LoginHistory;
use Carbon\Carbon;

class LogSuccessfulLogin
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle(Login $event): void
    {
        $user = $event->user;

        // Do not record Vehicle Login sessions
        if ($user->hasRole('Vehicle')) {
            return;
        }

        // Store new login
        LoginHistory::create([
            'user_id' => $user->id,
            'role' => $user->roles->first()->name ?? 'User',
            'login_datetime' => Carbon::now(),
            'ip_address' => $this->request->ip(),
            'device' => $this->request->header('User-Agent'),
            'browser' => $this->request->header('User-Agent'), // Simplification for user-agent string
        ]);

        // Keep only the last 5 records per user
        $records = LoginHistory::where('user_id', $user->id)->orderBy('login_datetime', 'desc')->get();
        if ($records->count() > 50) {
            $idsToDelete = $records->slice(50)->pluck('id');
            LoginHistory::whereIn('id', $idsToDelete)->delete();
        }
    }
}
