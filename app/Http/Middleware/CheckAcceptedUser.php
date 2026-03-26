<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAcceptedUser
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && ! $user->approved) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => "Votre compte n'est pas encore active par l'administrateur.",
            ]);
        }

        return $next($request);
    }
}
