<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Se não há usuário autenticado, deixa passar (será tratado por outros middlewares)
        if (!$user) {
            return $next($request);
        }

        // Se o usuário não precisa de 2FA, deixa passar
        if (!$user->requiresTwoFactor()) {
            return $next($request);
        }

        // Se o usuário está bloqueado por tentativas excessivas
        if ($user->isTwoFactorLocked()) {
            Auth::logout();
            return redirect()->route('login')->with('error', 
                'Conta temporariamente bloqueada devido a muitas tentativas de verificação. Tente novamente mais tarde.'
            );
        }

        // Verificar se já foi verificado nesta sessão
        if ($this->isTwoFactorVerifiedInSession($request)) {
            return $next($request);
        }

        // Se chegou até aqui, precisa verificar 2FA
        return redirect()->route('two-factor.show');
    }

    /**
     * Verificar se o 2FA foi verificado nesta sessão
     */
    protected function isTwoFactorVerifiedInSession(Request $request): bool
    {
        $sessionKey = 'two_factor_verified_' . Auth::id();
        $lastVerification = session($sessionKey);

        if (!$lastVerification) {
            return false;
        }

        // Verificar se a verificação ainda é válida (ex: 24 horas)
        $maxAge = config('auth.two_factor_session_lifetime', 1440); // minutos
        $verificationTime = \Carbon\Carbon::parse($lastVerification);
        
        if ($verificationTime->addMinutes($maxAge)->isPast()) {
            // Verificação expirou
            session()->forget($sessionKey);
            return false;
        }

        return true;
    }

    /**
     * Marcar 2FA como verificado na sessão
     */
    public static function markTwoFactorVerified(): void
    {
        $sessionKey = 'two_factor_verified_' . Auth::id();
        session([$sessionKey => now()->toISOString()]);
    }

    /**
     * Limpar verificação 2FA da sessão
     */
    public static function clearTwoFactorVerification(): void
    {
        $sessionKey = 'two_factor_verified_' . Auth::id();
        session()->forget($sessionKey);
    }
}