<?php

namespace App\Services;

use App\Models\EmailSmsSetting;
use App\Services\SmsProviders\CustomSmsProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Exception;

class SmsService
{
    /**
     * Enviar SMS usando o provedor configurado
     */
    public function send(string $phoneNumber, string $message): bool
    {
        $settings = EmailSmsSetting::getSettings();
        
        if (!$settings->sms_enabled || !$settings->sms_provider || !$settings->sms_config) {
            Log::error('SMS provider não configurado ou não habilitado');
            return false;
        }

        $provider = $settings->sms_provider;
        $config = $settings->sms_config;

        try {
            switch ($provider) {
                case 'twilio':
                    return $this->sendViaTwilio($phoneNumber, $message, $config);
                
                case 'nexmo':
                    return $this->sendViaNexmo($phoneNumber, $message, $config);
                
                case 'mock':
                    return $this->sendViaMock($phoneNumber, $message, $config);
                
                case 'custom':
                    return $this->sendViaCustomProvider($phoneNumber, $message, $config);
                
                default:
                    Log::error('Provedor SMS não suportado: ' . $provider);
                    return false;
            }
        } catch (Exception $e) {
            Log::error('Erro ao enviar SMS', [
                'provider' => $provider,
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Enviar SMS via Twilio
     */
    protected function sendViaTwilio(string $phoneNumber, string $message, array $config): bool
    {
        $accountSid = $config['account_sid'] ?? null;
        $authToken = $config['auth_token'] ?? null;
        $fromNumber = $config['from_number'] ?? null;

        if (!$accountSid || !$authToken || !$fromNumber) {
            Log::error('Configuração Twilio incompleta');
            return false;
        }

        $url = "https://api.twilio.com/2010-04-01/Accounts/{$accountSid}/Messages.json";

        $response = Http::asForm()
            ->withBasicAuth($accountSid, $authToken)
            ->post($url, [
                'From' => $fromNumber,
                'To' => $phoneNumber,
                'Body' => $message
            ]);

        if ($response->successful()) {
            Log::info('SMS enviado via Twilio', [
                'phone' => $phoneNumber,
                'sid' => $response->json('sid')
            ]);
            return true;
        }

        Log::error('Erro ao enviar SMS via Twilio', [
            'phone' => $phoneNumber,
            'status' => $response->status(),
            'response' => $response->body()
        ]);

        return false;
    }

    /**
     * Enviar SMS via Nexmo/Vonage
     */
    protected function sendViaNexmo(string $phoneNumber, string $message, array $config): bool
    {
        $apiKey = $config['api_key'] ?? null;
        $apiSecret = $config['api_secret'] ?? null;
        $fromName = $config['from_name'] ?? 'FM System';

        if (!$apiKey || !$apiSecret) {
            Log::error('Configuração Nexmo incompleta');
            return false;
        }

        $url = 'https://rest.nexmo.com/sms/json';

        $response = Http::post($url, [
            'api_key' => $apiKey,
            'api_secret' => $apiSecret,
            'from' => $fromName,
            'to' => $phoneNumber,
            'text' => $message
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $status = $data['messages'][0]['status'] ?? '1';
            
            if ($status === '0') {
                Log::info('SMS enviado via Nexmo', [
                    'phone' => $phoneNumber,
                    'message_id' => $data['messages'][0]['message-id'] ?? null
                ]);
                return true;
            }
        }

        Log::error('Erro ao enviar SMS via Nexmo', [
            'phone' => $phoneNumber,
            'response' => $response->body()
        ]);

        return false;
    }

    /**
     * Enviar SMS via Mock (para desenvolvimento/testes)
     */
    protected function sendViaMock(string $phoneNumber, string $message, array $config): bool
    {
        // Em ambiente de desenvolvimento, apenas loga o SMS
        if (app()->environment(['local', 'testing'])) {
            Log::info('SMS Mock enviado', [
                'phone' => $phoneNumber,
                'message' => $message
            ]);
            return true;
        }

        Log::warning('Tentativa de usar SMS Mock em produção');
        return false;
    }

    /**
     * Validar número de telefone
     */
    public function validatePhoneNumber(string $phoneNumber): bool
    {
        // Remove todos os caracteres não numéricos
        $cleaned = preg_replace('/\D/', '', $phoneNumber);
        
        // Verifica se tem pelo menos 10 dígitos (formato brasileiro mínimo)
        if (strlen($cleaned) < 10) {
            return false;
        }

        // Verifica se é um número brasileiro válido
        if (strlen($cleaned) === 11 && substr($cleaned, 0, 2) === '55') {
            // Formato: 5511999999999 (com código do país)
            return true;
        }

        if (strlen($cleaned) === 11 && in_array(substr($cleaned, 2, 1), ['9'])) {
            // Formato: 11999999999 (celular com 9º dígito)
            return true;
        }

        if (strlen($cleaned) === 10) {
            // Formato: 1199999999 (telefone fixo)
            return true;
        }

        return false;
    }

    /**
     * Formatar número de telefone para envio
     */
    public function formatPhoneNumber(string $phoneNumber): string
    {
        $cleaned = preg_replace('/\D/', '', $phoneNumber);
        
        // Se não tem código do país, adiciona +55
        if (strlen($cleaned) <= 11 && !str_starts_with($cleaned, '55')) {
            $cleaned = '55' . $cleaned;
        }

        return '+' . $cleaned;
    }

    /**
     * Testar configuração do provedor SMS
     */
    public function testConfiguration(): array
    {
        $settings = EmailSmsSetting::getSettings();
        
        if (!$settings->sms_enabled || !$settings->sms_provider || !$settings->sms_config) {
            return [
                'success' => false,
                'message' => 'Provedor SMS não configurado ou não habilitado'
            ];
        }

        $provider = $settings->sms_provider;
        $config = $settings->sms_config;

        try {
            switch ($provider) {
                case 'twilio':
                    return $this->testTwilioConfig($config);
                
                case 'nexmo':
                    return $this->testNexmoConfig($config);
                
                case 'mock':
                    return ['success' => true, 'message' => 'Mock SMS configurado'];
                
                case 'custom':
                    return $this->testCustomProvider($config);
                
                default:
                    return [
                        'success' => false,
                        'message' => 'Provedor não suportado: ' . $provider
                    ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao testar configuração: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Testar configuração Twilio
     */
    protected function testTwilioConfig(array $config): array
    {
        $accountSid = $config['account_sid'] ?? null;
        $authToken = $config['auth_token'] ?? null;

        if (!$accountSid || !$authToken) {
            return [
                'success' => false,
                'message' => 'Configuração Twilio incompleta'
            ];
        }

        $url = "https://api.twilio.com/2010-04-01/Accounts/{$accountSid}.json";

        $response = Http::withBasicAuth($accountSid, $authToken)->get($url);

        if ($response->successful()) {
            return [
                'success' => true,
                'message' => 'Configuração Twilio válida'
            ];
        }

        return [
            'success' => false,
            'message' => 'Credenciais Twilio inválidas'
        ];
    }

    /**
     * Testar configuração Nexmo
     */
    protected function testNexmoConfig(array $config): array
    {
        $apiKey = $config['api_key'] ?? null;
        $apiSecret = $config['api_secret'] ?? null;

        if (!$apiKey || !$apiSecret) {
            return [
                'success' => false,
                'message' => 'Configuração Nexmo incompleta'
            ];
        }

        $url = 'https://rest.nexmo.com/account/get-balance';

        $response = Http::get($url, [
            'api_key' => $apiKey,
            'api_secret' => $apiSecret
        ]);

        if ($response->successful()) {
            return [
                'success' => true,
                'message' => 'Configuração Nexmo válida'
            ];
        }

        return [
            'success' => false,
            'message' => 'Credenciais Nexmo inválidas'
        ];
    }

    /**
     * Enviar SMS via provedor customizado
     */
    protected function sendViaCustomProvider(string $phoneNumber, string $message, array $config): bool
    {
        $provider = new CustomSmsProvider($config);
        return $provider->send($phoneNumber, $message);
    }

    /**
     * Testar provedor customizado
     */
    protected function testCustomProvider(array $config): array
    {
        $provider = new CustomSmsProvider($config);
        return $provider->testConnection();
    }

    /**
     * Obter campos de configuração para provedor customizado
     */
    public function getCustomProviderFields(): array
    {
        $provider = new CustomSmsProvider();
        return $provider->getConfigFields();
    }

    /**
     * Validar configuração do provedor customizado
     */
    public function validateCustomProviderConfig(array $config): array
    {
        $provider = new CustomSmsProvider($config);
        return $provider->validateConfig($config);
    }
}
