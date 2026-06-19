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
        $user  = \Illuminate\Support\Facades\Auth::guard('web')->user();
        //$user = filament()->auth()->user();

        if ($user instanceof Utilisateur && !$user->statut) {
            // Déconnecter l'utilisateur
            Auth::guard('web')->logout();

            // Vider la session
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Message flash via l'objet Request
            $request->session()->flash(
                'error',
                'Votre compte est inactif. Veuillez contacter un administrateur !'
            );

            //return redirect()->to(filament()->getLoginUrl());
            // Rediriger vers login
            return redirect()->to('/admin/login');
        }

        return $next($request);
    }
}