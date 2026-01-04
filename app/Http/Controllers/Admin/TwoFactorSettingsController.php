<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TwoFactorAuthSetting;
use App\Models\EmailSmsSetting;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TwoFactorSettingsController extends Controller
{
    protected SmsService $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Mostrar configurações de 2FA
     */
    public function index()
    {
        $settings = TwoFactorAuthSetting::getSettings();
        $emailSmsSettings = EmailSmsSetting::getSettings();
        
        return view('admin.two-factor-settings', [
            'settings' => $settings,
            'emailSmsSettings' => $emailSmsSettings,
            'smsAvailable' => $emailSmsSettings->sms_enabled && $emailSmsSettings->hasCompleteSmsConfig(),
            'availableProviders' => $this->getAvailableProviders(),
            'menu' => 'two-factor-settings'
        ]);
    }

    /**
     * Atualizar configurações gerais de 2FA
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'enabled' => 'boolean',
            'default_method' => 'required|in:email,sms',
            'force_for_admins' => 'boolean',
            'allow_user_choice' => 'boolean',
            'code_expiry_minutes' => 'required|integer|min:1|max:60',
            'max_attempts' => 'required|integer|min:1|max:10'
        ], [
            'default_method.required' => 'O método padrão é obrigatório.',
            'default_method.in' => 'Método padrão deve ser email ou SMS.',
            'code_expiry_minutes.required' => 'Tempo de expiração é obrigatório.',
            'code_expiry_minutes.min' => 'Tempo mínimo é 1 minuto.',
            'code_expiry_minutes.max' => 'Tempo máximo é 60 minutos.',
            'max_attempts.required' => 'Máximo de tentativas é obrigatório.',
            'max_attempts.min' => 'Mínimo 1 tentativa.',
            'max_attempts.max' => 'Máximo 10 tentativas.'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $settings = TwoFactorAuthSetting::getSettings();
        
        $settings->update([
            'enabled' => $request->boolean('enabled'),
            'default_method' => $request->default_method,
            'force_for_admins' => $request->boolean('force_for_admins'),
            'allow_user_choice' => $request->boolean('allow_user_choice'),
            'code_expiry_minutes' => $request->code_expiry_minutes,
            'max_attempts' => $request->max_attempts
        ]);

        return back()->with('success', 'Configurações atualizadas com sucesso!');
    }


    /**
     * Validar configuração específica do provedor
     */
    protected function validateProviderConfig(string $provider, array $config): \Illuminate\Validation\Validator
    {
        $rules = [];
        $messages = [];

        switch ($provider) {
            case 'twilio':
                $rules = [
                    'sms_config.account_sid' => 'required|string',
                    'sms_config.auth_token' => 'required|string',
                    'sms_config.from_number' => 'required|string'
                ];
                $messages = [
                    'sms_config.account_sid.required' => 'Account SID do Twilio é obrigatório.',
                    'sms_config.auth_token.required' => 'Auth Token do Twilio é obrigatório.',
                    'sms_config.from_number.required' => 'Número remetente do Twilio é obrigatório.'
                ];
                break;

            case 'nexmo':
                $rules = [
                    'sms_config.api_key' => 'required|string',
                    'sms_config.api_secret' => 'required|string',
                    'sms_config.from_name' => 'nullable|string|max:11'
                ];
                $messages = [
                    'sms_config.api_key.required' => 'API Key do Nexmo é obrigatório.',
                    'sms_config.api_secret.required' => 'API Secret do Nexmo é obrigatório.',
                    'sms_config.from_name.max' => 'Nome remetente deve ter no máximo 11 caracteres.'
                ];
                break;

            case 'mock':
                // Mock não precisa de configuração específica
                break;

            case 'custom':
                $validation = $this->smsService->validateCustomProviderConfig($config);
                if (!$validation['valid']) {
                    $rules = ['sms_config' => 'required']; // Regra genérica
                    $messages = ['sms_config.required' => implode(', ', $validation['errors'])];
                }
                break;
        }

        return Validator::make(['sms_config' => $config], $rules, $messages);
    }

    /**
     * Obter lista de provedores SMS disponíveis
     */
    protected function getSmsProviders(): array
    {
        return [
            'twilio' => [
                'name' => 'Twilio',
                'description' => 'Provedor SMS confiável com cobertura global',
                'fields' => [
                    'account_sid' => 'Account SID',
                    'auth_token' => 'Auth Token',
                    'from_number' => 'Número Remetente'
                ]
            ],
            'nexmo' => [
                'name' => 'Nexmo/Vonage',
                'description' => 'Plataforma de comunicação da Vonage',
                'fields' => [
                    'api_key' => 'API Key',
                    'api_secret' => 'API Secret',
                    'from_name' => 'Nome Remetente (opcional)'
                ]
            ],
            'mock' => [
                'name' => 'Mock (Desenvolvimento)',
                'description' => 'Simulação para desenvolvimento e testes',
                'fields' => []
            ],
            'custom' => [
                'name' => 'Provedor Customizado',
                'description' => 'Configure qualquer provedor SMS via API REST',
                'fields' => $this->smsService->getCustomProviderFields()
            ]
        ];
    }

    /**
     * Obter estatísticas de uso do 2FA
     */
    public function statistics()
    {
        $stats = [
            'total_users' => \App\Models\User::count(),
            'users_with_2fa' => \App\Models\User::where('two_factor_enabled', true)->count(),
            'users_email_2fa' => \App\Models\User::where('two_factor_enabled', true)
                ->where('two_factor_method', 'email')->count(),
            'users_sms_2fa' => \App\Models\User::where('two_factor_enabled', true)
                ->where('two_factor_method', 'sms')->count(),
            'codes_sent_today' => \App\Models\TwoFactorCode::whereDate('created_at', today())->count(),
            'codes_used_today' => \App\Models\TwoFactorCode::whereDate('used_at', today())->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Obter lista de provedores disponíveis (apenas nomes)
     */
    protected function getAvailableProviders(): array
    {
        $emailSmsSettings = EmailSmsSetting::getSettings();
        
        $providers = [
            'email' => [
                'name' => 'Email',
                'available' => $emailSmsSettings->hasCompleteEmailConfig(),
                'description' => 'Envio via email configurado'
            ]
        ];
        
        if ($emailSmsSettings->sms_enabled && $emailSmsSettings->hasCompleteSmsConfig()) {
            $providerName = 'SMS';
            if ($emailSmsSettings->sms_provider) {
                $smsProviders = $this->getSmsProviders();
                $providerName = $smsProviders[$emailSmsSettings->sms_provider]['name'] ?? 'SMS';
            }
            
            $providers['sms'] = [
                'name' => $providerName,
                'available' => true,
                'description' => 'Provedor SMS configurado'
            ];
        } else {
            $providers['sms'] = [
                'name' => 'SMS',
                'available' => false,
                'description' => 'Configure SMS em Email e SMS'
            ];
        }
        
        return $providers;
    }
}