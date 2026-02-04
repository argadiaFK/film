<?php

namespace App\Listeners;

use App\Models\LoginHistory;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

class RecordLoginHistory
{
    public function handleLogin(Login $event): void
    {
        if ($event->user) {
            LoginHistory::recordLogin($event->user, true);
        }
    }

    public function handleLogout(Logout $event): void
    {
        if ($event->user) {
            // Update the last login record with logout time
            LoginHistory::where('user_id', $event->user->getAuthIdentifier())
                ->whereNull('logout_at')
                ->latest('login_at')
                ->first()
                    ?->update(['logout_at' => now()]);
        }
    }
}
