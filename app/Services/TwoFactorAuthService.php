<?php

namespace App\Services;

use App\Models\User;
use App\Models\TwoFactorCode;
use App\Models\TwoFactorAuthSetting;
use App\Models\EmailSmsSetting;
use App\Mail\TwoFactorCodeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;

class TwoFactorAuthService
{
    protected SmsService $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Gerar e enviar código 2FA para o usuário
     */
    public function sendCode(User $user, string $method = null): array
    {
        try {
            // Verificar se o usuário está bloqueado
            if ($user->isTwoFactorLocked()) {
                return [
                    'success' => false,
                    'message' => 'Usuário temporariamente bloqueado devido a muitas tentativas. Tente novamente mais tarde.'
                ];
            }

            // Determinar método de envio
            $method = $method ?: $user->two_factor_method ?: TwoFactorAuthSetting::getDefaultMethod();
            
            // Validar método
            if (!in_array($method, ['email', 'sms'])) {
                return [
                    'success' => false,
                    'message' => 'Método de envio inválido.'
                ];
            }

            // Validar destino
            $destination = $this->getDestination($user, $method);
            if (!$destination) {
                return [
                    'success' => false,
                    'message' => $method === 'sms' 
                        ? 'Número de telefone não cadastrado ou não verificado.'
                        : 'Email não disponível.'
                ];
            }

            // Criar código
            $code = TwoFactorCode::createForUser($user, $method, $destination);

            // Enviar código
            $sent = $this->deliverCode($code);

            if ($sent) {
                Log::info('Código 2FA enviado', [
                    'user_id' => $user->id,
                    'method' => $method,
                    'destination' => $this->maskDestination($destination, $method)
                ]);

                return [
                    'success' => true,
                    'message' => $method === 'sms' 
                        ? 'Código enviado via SMS para ' . $this->maskDestination($destination, $method)
                        : 'Código enviado para ' . $this->maskDestination($destination, $method),
                    'method' => $method,
                    'destination_masked' => $this->maskDestination($destination, $method)
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Erro ao enviar código. Tente novamente.'
                ];
            }

        } catch (Exception $e) {
            Log::error('Erro ao enviar código 2FA', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Erro interno. Tente novamente mais tarde.'
            ];
        }
    }

    /**
     * Verificar código 2FA
     */
    public function verifyCode(User $user, string $inputCode): array
    {
        try {
            // Verificar se o usuário está bloqueado
            if ($user->isTwoFactorLocked()) {
                return [
                    'success' => false,
                    'message' => 'Usuário temporariamente bloqueado. Tente novamente mais tarde.'
                ];
            }

            // Buscar código válido
            $code = TwoFactorCode::forUser($user)
                ->valid()
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$code) {
                return [
                    'success' => false,
                    'message' => 'Nenhum código válido encontrado. Solicite um novo código.'
                ];
            }

            // Incrementar tentativas
            $code->incrementAttempts();

            // Verificar código
            if ($code->code === $inputCode) {
                // Código correto
                $code->markAsUsed();
                $user->resetTwoFactorFailedAttempts();

                Log::info('Código 2FA verificado com sucesso', [
                    'user_id' => $user->id,
                    'method' => $code->method
                ]);

                return [
                    'success' => true,
                    'message' => 'Código verificado com sucesso!'
                ];
            } else {
                // Código incorreto
                $user->incrementTwoFactorFailedAttempts();

                Log::warning('Código 2FA incorreto', [
                    'user_id' => $user->id,
                    'attempts' => $user->two_factor_failed_attempts
                ]);

                return [
                    'success' => false,
                    'message' => 'Código incorreto. Tente novamente.'
                ];
            }

        } catch (Exception $e) {
            Log::error('Erro ao verificar código 2FA', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Erro interno. Tente novamente mais tarde.'
            ];
        }
    }

    /**
     * Obter destino para envio do código
     */
    protected function getDestination(User $user, string $method): ?string
    {
        if ($method === 'sms') {
            return $user->hasVerifiedPhone() ? $user->phone_number : null;
        }

        return $user->email;
    }

    /**
     * Entregar código via método especificado
     */
    protected function deliverCode(TwoFactorCode $code): bool
    {
        if ($code->method === 'sms') {
            return $this->sendSms($code);
        }

        return $this->sendEmail($code);
    }

    /**
     * Enviar código via email
     */
    protected function sendEmail(TwoFactorCode $code): bool
    {
        try {
            // Aplicar configurações de email do sistema
            $emailSettings = EmailSmsSetting::getSettings();
            if ($emailSettings->hasCompleteEmailConfig()) {
                $emailSettings->applyEmailConfig();
            }

            // Enviar email usando a classe TwoFactorCodeMail
            Mail::to($code->destination)->send(
                new TwoFactorCodeMail(
                    $code->user,
                    $code->code,
                    TwoFactorAuthSetting::getCodeExpiryMinutes(),
                    request()->ip()
                )
            );

            return true;
        } catch (Exception $e) {
            Log::error('Erro ao enviar email 2FA', [
                'code_id' => $code->id,
                'destination' => $code->destination,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Enviar código via SMS
     */
    protected function sendSms(TwoFactorCode $code): bool
    {
        try {
            $message = "Seu código de verificação FM System: {$code->code}. Válido por " . 
                      TwoFactorAuthSetting::getCodeExpiryMinutes() . " minutos.";

            return $this->smsService->send($code->destination, $message);
        } catch (Exception $e) {
            Log::error('Erro ao enviar SMS 2FA', [
                'code_id' => $code->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Mascarar destino para exibição
     */
    protected function maskDestination(string $destination, string $method): string
    {
        if ($method === 'sms') {
            // Mascarar telefone: +55 (11) 9****-1234
            $cleaned = preg_replace('/\D/', '', $destination);
            if (strlen($cleaned) >= 10) {
                return substr($cleaned, 0, -4) . str_repeat('*', 4) . substr($cleaned, -4);
            }
            return $destination;
        }

        // Mascarar email: u****@example.com
        $parts = explode('@', $destination);
        if (count($parts) === 2) {
            $username = $parts[0];
            $domain = $parts[1];
            $maskedUsername = substr($username, 0, 1) . str_repeat('*', max(0, strlen($username) - 2)) . substr($username, -1);
            return $maskedUsername . '@' . $domain;
        }

        return $destination;
    }

    /**
     * Limpar códigos expirados
     */
    public function cleanupExpiredCodes(): int
    {
        return TwoFactorCode::where('expires_at', '<', now())->delete();
    }
}
