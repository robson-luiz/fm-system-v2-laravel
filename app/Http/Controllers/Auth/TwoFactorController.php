<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\TwoFactorAuthService;
use App\Http\Middleware\TwoFactorAuthMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TwoFactorController extends Controller
{
    protected TwoFactorAuthService $twoFactorService;

    public function __construct(TwoFactorAuthService $twoFactorService)
    {
        $this->twoFactorService = $twoFactorService;
    }

    /**
     * Mostrar formulário de verificação 2FA
     */
    public function show()
    {
        $user = Auth::user();

        // Se o usuário não precisa de 2FA, redireciona
        if (!$user->requiresTwoFactor()) {
            return redirect()->intended('/dashboard');
        }

        // Se está bloqueado, faz logout
        if ($user->isTwoFactorLocked()) {
            Auth::logout();
            return redirect()->route('login')->with('error', 
                'Conta temporariamente bloqueada. Tente novamente mais tarde.'
            );
        }

        // Enviar código automaticamente se não houver um código recente
        $lastCodeSent = session('two_factor_code_sent_at');
        if (!$lastCodeSent || now()->diffInMinutes($lastCodeSent) > 2) {
            $result = $this->twoFactorService->sendCode($user);
            
            if ($result['success']) {
                session(['two_factor_code_sent_at' => now()]);
                session()->flash('status', 'Código enviado para ' . $result['destination_masked']);
            } else {
                session()->flash('error', $result['message']);
            }
        }

        return view('auth.two-factor', [
            'user' => $user,
            'method' => $user->two_factor_method ?: 'email',
            'destination_masked' => $this->getMaskedDestination($user)
        ]);
    }

    /**
     * Enviar código 2FA
     */
    public function sendCode(Request $request)
    {
        $user = Auth::user();

        // Validar se pode enviar código
        if (!$user->requiresTwoFactor()) {
            return response()->json([
                'success' => false,
                'message' => '2FA não é necessário para este usuário.'
            ], 400);
        }

        if ($user->isTwoFactorLocked()) {
            return response()->json([
                'success' => false,
                'message' => 'Conta temporariamente bloqueada.'
            ], 429);
        }

        // Enviar código
        $result = $this->twoFactorService->sendCode($user);

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Verificar código 2FA
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6|regex:/^[0-9]+$/'
        ], [
            'code.required' => 'O código é obrigatório.',
            'code.size' => 'O código deve ter 6 dígitos.',
            'code.regex' => 'O código deve conter apenas números.'
        ]);

        $user = Auth::user();

        // Validações básicas
        if (!$user->requiresTwoFactor()) {
            return redirect()->intended('/dashboard');
        }

        if ($user->isTwoFactorLocked()) {
            Auth::logout();
            return redirect()->route('login')->with('error', 
                'Conta temporariamente bloqueada.'
            );
        }

        // Verificar código
        $result = $this->twoFactorService->verifyCode($user, $request->code);

        if ($result['success']) {
            // Marcar como verificado na sessão
            TwoFactorAuthMiddleware::markTwoFactorVerified();
            
            return redirect()->intended('/dashboard')->with('success', 
                'Verificação realizada com sucesso!'
            );
        } else {
            // Se o usuário foi bloqueado após esta tentativa
            if ($user->fresh()->isTwoFactorLocked()) {
                Auth::logout();
                return redirect()->route('login')->with('error', 
                    'Muitas tentativas incorretas. Conta temporariamente bloqueada.'
                );
            }

            throw ValidationException::withMessages([
                'code' => [$result['message']]
            ]);
        }
    }

    /**
     * Reenviar código
     */
    public function resend(Request $request)
    {
        $user = Auth::user();

        if (!$user->requiresTwoFactor()) {
            return response()->json([
                'success' => false,
                'message' => '2FA não é necessário.'
            ], 400);
        }

        if ($user->isTwoFactorLocked()) {
            return response()->json([
                'success' => false,
                'message' => 'Conta bloqueada.'
            ], 429);
        }

        $result = $this->twoFactorService->sendCode($user);

        return response()->json($result);
    }

    /**
     * Obter destino mascarado para exibição
     */
    protected function getMaskedDestination($user): string
    {
        $destination = $user->getTwoFactorDestination();
        $method = $user->two_factor_method;

        if ($method === 'sms') {
            // Mascarar telefone
            $cleaned = preg_replace('/\D/', '', $destination);
            if (strlen($cleaned) >= 4) {
                return '***-***-' . substr($cleaned, -4);
            }
            return $destination;
        }

        // Mascarar email
        $parts = explode('@', $destination);
        if (count($parts) === 2) {
            $username = $parts[0];
            $domain = $parts[1];
            $maskedUsername = substr($username, 0, 2) . str_repeat('*', max(0, strlen($username) - 4)) . substr($username, -2);
            return $maskedUsername . '@' . $domain;
        }

        return $destination;
    }
}