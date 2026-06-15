<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use App\Models\LoginHistory;
use Carbon\Carbon;

class LogSuccessfulLogout
{
    public function handle(Logout $event): void
    {
        if ($event->user) {
            $lastLogin = LoginHistory::where('user_id', $event->user->id)
                ->orderBy('login_datetime', 'desc')
                ->first();

            if ($lastLogin && is_null($lastLogin->logout_datetime)) {
                $lastLogin->update([
                    'logout_datetime' => Carbon::now()
                ]);
            }
        }
    }
}
