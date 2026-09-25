<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SingleSessionService
{
    public const ALREADY_ACTIVE_MESSAGE = 'Ce compte est déjà connecté sur un autre appareil ou navigateur. Veuillez fermer votre session précédente avant de vous reconnecter.';

    public function claim(Request $request, User $user, string $field = 'email'): void
    {
        $session = $request->session();
        $sessionId = $session->getId();
        $sessionTable = config('session.table', 'sessions');
        $lifetime = max(1, (int) config('session.lifetime', 120));
        $cutoff = now()->subMinutes($lifetime)->timestamp;
        $claimed = false;

        DB::transaction(function () use ($user, $session, $sessionId, $sessionTable, $cutoff, &$claimed): void {
            User::query()
                ->whereKey($user->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            DB::table($sessionTable)
                ->where('user_id', $user->getKey())
                ->where('last_activity', '<', $cutoff)
                ->delete();

            $hasAnotherActiveSession = DB::table($sessionTable)
                ->where('user_id', $user->getKey())
                ->where('last_activity', '>=', $cutoff)
                ->where('id', '<>', $sessionId)
                ->exists();

            if ($hasAnotherActiveSession) {
                return;
            }

            $session->save();
            $claimed = true;
        });

        if ($claimed) {
            return;
        }

        Auth::guard('web')->logout();
        $session->invalidate();
        $session->regenerateToken();

        $rejectedSessionId = $session->getId();
        app()->terminating(function () use ($sessionTable, $rejectedSessionId): void {
            DB::table($sessionTable)->where('id', $rejectedSessionId)->delete();
        });

        throw ValidationException::withMessages([
            $field => self::ALREADY_ACTIVE_MESSAGE,
        ]);
    }
}
