<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailSmsSetting;
use App\Mail\TestEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class EmailSmsSettingsController extends Controller
{
    /**
     * Mostrar configurações de Email e SMS
     */
    public function index()
    {
        $settings = EmailSmsSetting::getSettings();
        
        return view('admin.email-sms-settings', [
            'settings' => $settings,
            'smsProviders' => $this->getSmsProviders(),
            'menu' => 'email-sms-settings'
        ]);
    }

    /**
     * Atualizar configurações de Email
     */
    public function updateEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mail_mailer' => 'required|in:smtp,sendmail,mailgun,ses,postmark',
            'mail_host' => 'required_if:mail_mailer,smtp|nullable|string',
            'mail_port' => 'required_if:mail_mailer,smtp|nullable|integer|min:1|max:65535',
            'mail_username' => 'required_if:mail_mailer,smtp|nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'required|in:tls,ssl,none',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string|max:255',
            'test_email' => 'nullable|email',
        ], [
            'mail_mailer.required' => 'O driver de email é obrigatório.',
            'mail_host.required_if' => 'O servidor SMTP é obrigatório para SMTP.',
            'mail_port.required_if' => 'A porta SMTP é obrigatória para SMTP.',
            'mail_username.required_if' => 'O usuário SMTP é obrigatório para SMTP.',
            'mail_from_address.required' => 'O email remetente é obrigatório.',
            'mail_from_address.email' => 'O email remetente deve ser válido.',
            'mail_from_name.required' => 'O nome remetente é obrigatório.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $settings = EmailSmsSetting::getSettings();
        
        $updateData = $request->only([
            'mail_mailer', 'mail_host', 'mail_port', 'mail_username', 
            'mail_encryption', 'mail_from_address', 'mail_from_name', 'test_email'
        ]);

        // Só atualizar senha se foi fornecida
        if ($request->filled('mail_password')) {
            $updateData['mail_password'] = $request->mail_password;
        }

        $settings->update($updateData);

        return back()->with('success', 'Configurações de email atualizadas com sucesso!');
    }

    /**
     * Atualizar configurações de SMS
     */
    public function updateSms(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sms_provider' => 'required|in:twilio,nexmo,mock,custom',
            'sms_config' => 'required_unless:sms_provider,custom|array',
            'sms_enabled' => 'boolean',
            'test_phone' => 'nullable|string',
            // Campos para provedor customizado
            'custom_sms_provider_name' => 'required_if:sms_provider,custom|nullable|string|max:255',
            'custom_sms_api_url' => 'required_if:sms_provider,custom|nullable|url',
            'custom_sms_method' => 'required_if:sms_provider,custom|nullable|in:GET,POST,PUT,PATCH',
            'custom_sms_phone_field' => 'required_if:sms_provider,custom|nullable|string|max:255',
            'custom_sms_message_field' => 'required_if:sms_provider,custom|nullable|string|max:255',
            'custom_sms_headers' => 'nullable|array',
            'custom_sms_additional_fields' => 'nullable|array',
            'custom_sms_success_indicators' => 'nullable|array',
            'custom_sms_test_number' => 'nullable|string',
        ], [
            'custom_sms_provider_name.required_if' => 'Nome do provedor é obrigatório para provedor customizado.',
            'custom_sms_api_url.required_if' => 'URL da API é obrigatória para provedor customizado.',
            'custom_sms_api_url.url' => 'URL da API deve ser válida.',
            'custom_sms_method.required_if' => 'Método HTTP é obrigatório para provedor customizado.',
            'custom_sms_phone_field.required_if' => 'Campo do telefone é obrigatório para provedor customizado.',
            'custom_sms_message_field.required_if' => 'Campo da mensagem é obrigatório para provedor customizado.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $provider = $request->sms_provider;
        $config = $request->sms_config ?? [];

        // Validar configuração específica do provedor (apenas se não for custom)
        if ($provider !== 'custom') {
            $providerValidator = $this->validateProviderConfig($provider, $config);
            if ($providerValidator->fails()) {
                return back()->withErrors($providerValidator)->withInput();
            }
        }

        $settings = EmailSmsSetting::getSettings();
        
        $updateData = [
            'sms_provider' => $provider,
            'sms_config' => $config,
            'sms_enabled' => $request->boolean('sms_enabled'),
            'test_phone' => $request->test_phone,
        ];

        // Adicionar campos customizados se for provedor custom
        if ($provider === 'custom') {
            $updateData = array_merge($updateData, [
                'custom_sms_provider_name' => $request->custom_sms_provider_name,
                'custom_sms_api_url' => $request->custom_sms_api_url,
                'custom_sms_method' => $request->custom_sms_method ?? 'POST',
                'custom_sms_phone_field' => $request->custom_sms_phone_field,
                'custom_sms_message_field' => $request->custom_sms_message_field,
                'custom_sms_headers' => $this->parseKeyValuePairs($request->custom_sms_headers ?? []),
                'custom_sms_additional_fields' => $this->parseKeyValuePairs($request->custom_sms_additional_fields ?? []),
                'custom_sms_success_indicators' => $this->parseKeyValuePairs($request->custom_sms_success_indicators ?? []),
                'custom_sms_test_number' => $request->custom_sms_test_number,
            ]);
        } else {
            // Limpar campos customizados se não for custom
            $updateData = array_merge($updateData, [
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

        $settings->update($updateData);

        return back()->with('success', 'Configurações de SMS atualizadas com sucesso!');
    }

    /**
     * Testar configuração de Email
     */
    public function testEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email'
        ], [
            'test_email.required' => 'Email de teste é obrigatório.',
            'test_email.email' => 'Email deve ser válido.'
        ]);

        $settings = EmailSmsSetting::getSettings();
        
        if (!$settings->hasCompleteEmailConfig()) {
            return response()->json([
                'success' => false,
                'message' => 'Configurações de email incompletas. Preencha todos os campos obrigatórios.'
            ]);
        }

        try {
            // Aplicar configurações temporariamente
            $settings->applyEmailConfig();

            // Enviar email de teste usando a classe TestEmail
            Mail::to($request->test_email)->send(new TestEmail());

            return response()->json([
                'success' => true,
                'message' => 'Email de teste enviado com sucesso!'
            ]);

        } catch (\Exception $e) {
            // Log do erro para debug
            Log::error('Erro ao testar email: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar email: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Testar configuração de SMS
     */
    public function testSms(Request $request)
    {
        $request->validate([
            'test_phone' => 'required|string'
        ], [
            'test_phone.required' => 'Número de teste é obrigatório.'
        ]);

        $settings = EmailSmsSetting::getSettings();
        
        if (!$settings->hasCompleteSmsConfig()) {
            return response()->json([
                'success' => false,
                'message' => 'Configurações de SMS incompletas ou SMS não habilitado.'
            ]);
        }

        try {
            $phone = $request->test_phone;
            $message = 'Teste do FM System: ' . now()->format('d/m/Y H:i:s');

            if ($settings->sms_provider === 'custom') {
                // Usar provedor customizado
                $result = $settings->sendCustomSms($phone, $message);
                
                return response()->json([
                    'success' => $result['success'],
                    'message' => $result['message'],
                    'details' => $result
                ]);
            } else {
                // Outros provedores (mock, twilio, nexmo)
                Log::info('Teste SMS enviado via ' . $settings->sms_provider, [
                    'phone' => $phone,
                    'message' => $message
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'SMS de teste simulado com sucesso! (Provedor: ' . $settings->sms_provider . ')'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Erro ao testar SMS: ' . $e->getMessage(), [
                'phone' => $request->test_phone,
                'provider' => $settings->sms_provider
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar SMS: ' . $e->getMessage()
            ]);
        }
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
                'description' => 'Configure qualquer provedor SMS com API REST',
                'fields' => []
            ]
        ];
    }

    /**
     * Converter array de pares chave-valor do formulário para array associativo
     */
    protected function parseKeyValuePairs(array $pairs): array
    {
        $result = [];
        
        foreach ($pairs as $pair) {
            if (isset($pair['key']) && isset($pair['value']) && !empty($pair['key'])) {
                $result[$pair['key']] = $pair['value'];
            }
        }
        
        return $result;
    }
}