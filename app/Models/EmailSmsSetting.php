<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class EmailSmsSetting extends Model
{
    protected $fillable = [
        'mail_mailer',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'mail_from_address',
        'mail_from_name',
        'sms_provider',
        'sms_config',
        'sms_enabled',
        'test_email',
        'test_phone',
        // Campos para provedor customizado
        'custom_sms_provider_name',
        'custom_sms_api_url',
        'custom_sms_method',
        'custom_sms_phone_field',
        'custom_sms_message_field',
        'custom_sms_headers',
        'custom_sms_additional_fields',
        'custom_sms_success_indicators',
        'custom_sms_test_number',
    ];

    protected $casts = [
        'sms_config' => 'array',
        'sms_enabled' => 'boolean',
        'custom_sms_headers' => 'array',
        'custom_sms_additional_fields' => 'array',
        'custom_sms_success_indicators' => 'array',
    ];

    /**
     * Obter as configurações do sistema (singleton)
     */
    public static function getSettings()
    {
        return static::first() ?? static::create([
            'mail_mailer' => 'smtp',
            'mail_host' => null,
            'mail_port' => 587,
            'mail_username' => null,
            'mail_password' => null,
            'mail_encryption' => 'tls',
            'mail_from_address' => null,
            'mail_from_name' => 'FM System',
            'sms_provider' => null,
            'sms_config' => null,
            'sms_enabled' => false,
            'test_email' => null,
            'test_phone' => null,
            // Campos para provedor customizado
            'custom_sms_provider_name' => null,
            'custom_sms_api_url' => null,
            'custom_sms_method' => 'POST',
            'custom_sms_phone_field' => null,
            'custom_sms_message_field' => null,
            'custom_sms_headers' => null,
            'custom_sms_additional_fields' => null,
            'custom_sms_success_indicators' => null,
            'custom_sms_test_number' => null,
        ]);
    }

    /**
     * Obter configurações de email para o Laravel
     */
    public function getMailConfig(): array
    {
        return [
            'default' => $this->mail_mailer,
            'mailers' => [
                'smtp' => [
                    'transport' => 'smtp',
                    'host' => $this->mail_host,
                    'port' => (int) $this->mail_port,
                    'encryption' => $this->mail_encryption === 'none' ? null : $this->mail_encryption,
                    'username' => $this->mail_username,
                    'password' => $this->mail_password,
                    'timeout' => null,
                    'local_domain' => env('MAIL_EHLO_DOMAIN'),
                ],
                'sendmail' => [
                    'transport' => 'sendmail',
                    'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
                ],
                'log' => [
                    'transport' => 'log',
                    'channel' => env('MAIL_LOG_CHANNEL'),
                ],
                'array' => [
                    'transport' => 'array',
                ],
                'failover' => [
                    'transport' => 'failover',
                    'mailers' => [
                        'smtp',
                        'log',
                    ],
                ],
            ],
            'from' => [
                'address' => $this->mail_from_address,
                'name' => $this->mail_from_name,
            ],
        ];
    }

    /**
     * Verificar se as configurações de email estão completas
     */
    public function hasCompleteEmailConfig(): bool
    {
        return !empty($this->mail_host) && 
               !empty($this->mail_username) && 
               !empty($this->mail_password) && 
               !empty($this->mail_from_address);
    }

    /**
     * Verificar se as configurações de SMS estão completas
     */
    public function hasCompleteSmsConfig(): bool
    {
        if ($this->sms_provider === 'custom') {
            return $this->hasCompleteCustomSmsConfig();
        }

        return !empty($this->sms_provider) && 
               !empty($this->sms_config) && 
               $this->sms_enabled;
    }

    /**
     * Verificar se as configurações de SMS customizado estão completas
     */
    public function hasCompleteCustomSmsConfig(): bool
    {
        return !empty($this->custom_sms_provider_name) &&
               !empty($this->custom_sms_api_url) &&
               !empty($this->custom_sms_phone_field) &&
               !empty($this->custom_sms_message_field) &&
               $this->sms_enabled;
    }

    /**
     * Aplicar configurações de email ao Laravel
     */
    public function applyEmailConfig(): void
    {
        if ($this->hasCompleteEmailConfig()) {
            // Aplicar configurações ao config do Laravel
            $mailConfig = $this->getMailConfig();
            config(['mail' => $mailConfig]);
            
            // Recriar o mailer com as novas configurações
            app()->forgetInstance('mail.manager');
            app()->forgetInstance('mailer');
        }
    }

    /**
     * Enviar SMS via provedor customizado
     */
    public function sendCustomSms(string $phone, string $message): array
    {
        if (!$this->hasCompleteCustomSmsConfig()) {
            throw new \Exception('Configurações de SMS customizado incompletas');
        }

        try {
            // Preparar dados para envio
            $data = [];
            $data[$this->custom_sms_phone_field] = $phone;
            $data[$this->custom_sms_message_field] = $message;

            // Adicionar campos extras se existirem
            if ($this->custom_sms_additional_fields) {
                $data = array_merge($data, $this->custom_sms_additional_fields);
            }

            // Preparar headers
            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ];
            
            if ($this->custom_sms_headers) {
                $headers = array_merge($headers, $this->custom_sms_headers);
            }

            // Fazer requisição HTTP
            $client = new \GuzzleHttp\Client();
            $options = [
                'headers' => $headers,
                'timeout' => 30,
            ];

            // Definir método e dados baseado no método HTTP
            if (in_array($this->custom_sms_method, ['POST', 'PUT', 'PATCH'])) {
                $options['json'] = $data;
            } else {
                $options['query'] = $data;
            }

            $response = $client->request($this->custom_sms_method, $this->custom_sms_api_url, $options);
            
            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody()->getContents();
            $responseData = json_decode($responseBody, true);

            // Verificar indicadores de sucesso
            $success = $this->checkSmsSuccess($statusCode, $responseData, $responseBody);

            return [
                'success' => $success,
                'status_code' => $statusCode,
                'response' => $responseData ?? $responseBody,
                'message' => $success ? 'SMS enviado com sucesso!' : 'Falha ao enviar SMS'
            ];

        } catch (\Exception $e) {
            Log::error('Erro ao enviar SMS customizado: ' . $e->getMessage(), [
                'provider' => $this->custom_sms_provider_name,
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Erro ao enviar SMS: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verificar se a resposta indica sucesso
     */
    private function checkSmsSuccess(int $statusCode, ?array $responseData, string $responseBody): bool
    {
        // Se não há indicadores customizados, usar status code padrão
        if (!$this->custom_sms_success_indicators) {
            return $statusCode >= 200 && $statusCode < 300;
        }

        foreach ($this->custom_sms_success_indicators as $key => $expectedValue) {
            switch ($key) {
                case 'status_code':
                    if ($statusCode != $expectedValue) {
                        return false;
                    }
                    break;
                
                case 'response_contains':
                    if (strpos($responseBody, $expectedValue) === false) {
                        return false;
                    }
                    break;
                
                default:
                    // Verificar campos na resposta JSON
                    if (!isset($responseData[$key]) || $responseData[$key] != $expectedValue) {
                        return false;
                    }
                    break;
            }
        }

        return true;
    }
}