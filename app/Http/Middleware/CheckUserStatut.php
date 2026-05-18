<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Auth;

class CheckUserStatut
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Utilisateur|null $user */
        $user = filament()->auth()->user();

        if ($user instanceof Utilisateur && !$user->statut) {
            // Déconnecter l'utilisateur
            Auth::guard('web')->logout();

            // Vider la session
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Rediriger vers login avec message
            session()->flash('error', 'Votre compte est inactif. Contactez un administrateur.');

            return redirect()->to(filament()->getLoginUrl());
        }

        return $next($request);
    }
}
