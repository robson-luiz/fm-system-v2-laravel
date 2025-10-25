@extends('layouts.admin')

@section('content')
    <!-- Meta tags para rotas JavaScript -->
    <meta name="email-sms-test-email-route" content="{{ route('admin.email-sms.test-email') }}">
    <meta name="email-sms-test-sms-route" content="{{ route('admin.email-sms.test-sms') }}">

    <!-- Título e Trilha de Navegação -->
    <div class="content-wrapper">
        <div class="content-header">
            <h2 class="content-title">Configurações</h2>
            <nav class="breadcrumb">
                <a href="{{ route('dashboard.index') }}" class="breadcrumb-link">Dashboard</a>
                <span>/</span>
                <span>Email e SMS</span>
            </nav>
        </div>
    </div>

    <div class="content-box">
        <div class="content-box-header">
            <h3 class="content-box-title">Configurações de Email e SMS</h3>
            <div class="content-box-btn">
                <a href="{{ route('admin.two-factor.index') }}" class="btn-info-md align-icon-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.623 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                    <span>Configurações 2FA</span>
                </a>
            </div>
        </div>

        <x-alert />

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Configurações de Email -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                        Configurações de Email
                    </h4>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.email-sms.update-email') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="form-label">Driver de Email</label>
                            <select name="mail_mailer" class="form-select" required>
                                <option value="smtp" {{ $settings->mail_mailer === 'smtp' ? 'selected' : '' }}>SMTP</option>
                                <option value="sendmail" {{ $settings->mail_mailer === 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                                <option value="mailgun" {{ $settings->mail_mailer === 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                <option value="ses" {{ $settings->mail_mailer === 'ses' ? 'selected' : '' }}>Amazon SES</option>
                                <option value="postmark" {{ $settings->mail_mailer === 'postmark' ? 'selected' : '' }}>Postmark</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div class="mb-4">
                                <label class="form-label">Servidor SMTP</label>
                                <input type="text" name="mail_host" class="form-input" 
                                       value="{{ old('mail_host', $settings->mail_host) }}" 
                                       placeholder="smtp.gmail.com">
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Porta SMTP</label>
                                <input type="number" name="mail_port" class="form-input" 
                                       value="{{ old('mail_port', $settings->mail_port) }}" 
                                       placeholder="587">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Usuário SMTP</label>
                            <input type="text" name="mail_username" class="form-input" 
                                   value="{{ old('mail_username', $settings->mail_username) }}" 
                                   placeholder="seu-email@gmail.com">
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Senha SMTP</label>
                            <input type="password" name="mail_password" class="form-input" 
                                   placeholder="Digite a senha (deixe em branco para manter a atual)">
                            @if($settings->mail_password)
                                <p class="text-sm text-green-600 dark:text-green-400 mt-1">✓ Senha configurada</p>
                            @endif
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Criptografia</label>
                            <select name="mail_encryption" class="form-select" required>
                                <option value="tls" {{ $settings->mail_encryption === 'tls' ? 'selected' : '' }}>TLS</option>
                                <option value="ssl" {{ $settings->mail_encryption === 'ssl' ? 'selected' : '' }}>SSL</option>
                                <option value="none" {{ $settings->mail_encryption === 'none' ? 'selected' : '' }}>Nenhuma</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div class="mb-4">
                                <label class="form-label">Email Remetente</label>
                                <input type="email" name="mail_from_address" class="form-input" 
                                       value="{{ old('mail_from_address', $settings->mail_from_address) }}" 
                                       placeholder="noreply@fmsystem.com" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Nome Remetente</label>
                                <input type="text" name="mail_from_name" class="form-input" 
                                       value="{{ old('mail_from_name', $settings->mail_from_name) }}" 
                                       placeholder="FM System" required>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="form-label">Email para Testes</label>
                            <input type="email" name="test_email" class="form-input" 
                                   value="{{ old('test_email', $settings->test_email) }}" 
                                   placeholder="seu-email@exemplo.com">
                        </div>

                        <div class="flex gap-2">
                            <button type="button" class="btn-info-md align-icon-btn" onclick="testEmail()">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                                </svg>
                                <span>Testar Email</span>
                            </button>
                            <button type="submit" class="btn-success-md align-icon-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Salvar Email</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Configurações de SMS -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Configurações de SMS
                    </h4>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.email-sms.update-sms') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="form-label flex items-center">
                                <input type="checkbox" name="sms_enabled" value="1" class="form-checkbox mr-2" 
                                       {{ $settings->sms_enabled ? 'checked' : '' }}>
                                Habilitar SMS
                            </label>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Ativa o envio de SMS no sistema</p>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Provedor SMS</label>
                            <select name="sms_provider" class="form-select" id="sms-provider" required>
                                <option value="">Selecione um provedor</option>
                                @foreach($smsProviders as $key => $provider)
                                    <option value="{{ $key }}" {{ $settings->sms_provider === $key ? 'selected' : '' }}>
                                        {{ $provider['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @foreach($smsProviders as $key => $provider)
                            @if($key === 'custom')
                                <!-- Configuração do Provedor Customizado -->
                                <div class="provider-config" id="config-{{ $key }}" style="{{ $settings->sms_provider === $key ? '' : 'display: none;' }}">
                                    <h5 class="text-lg font-semibold mb-2 text-gray-900 dark:text-white">{{ $provider['name'] }}</h5>
                                    <p class="text-gray-600 dark:text-gray-400 mb-4">{{ $provider['description'] }}</p>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <div class="mb-4">
                                            <label class="form-label">Nome do Provedor</label>
                                            <input type="text" name="custom_sms_provider_name" class="form-input"
                                                   value="{{ old('custom_sms_provider_name', $settings->custom_sms_provider_name) }}"
                                                   placeholder="Ex: Iagente, ZenviaNow, TotalVoice">
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label">Método HTTP</label>
                                            <select name="custom_sms_method" class="form-select">
                                                <option value="POST" {{ ($settings->custom_sms_method ?? 'POST') === 'POST' ? 'selected' : '' }}>POST</option>
                                                <option value="GET" {{ $settings->custom_sms_method === 'GET' ? 'selected' : '' }}>GET</option>
                                                <option value="PUT" {{ $settings->custom_sms_method === 'PUT' ? 'selected' : '' }}>PUT</option>
                                                <option value="PATCH" {{ $settings->custom_sms_method === 'PATCH' ? 'selected' : '' }}>PATCH</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label">URL da API</label>
                                        <input type="url" name="custom_sms_api_url" class="form-input"
                                               value="{{ old('custom_sms_api_url', $settings->custom_sms_api_url) }}"
                                               placeholder="https://api.provedor.com/v1/sms">
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <div class="mb-4">
                                            <label class="form-label">Campo do Telefone</label>
                                            <input type="text" name="custom_sms_phone_field" class="form-input"
                                                   value="{{ old('custom_sms_phone_field', $settings->custom_sms_phone_field) }}"
                                                   placeholder="to, phone, numero_destino">
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label">Campo da Mensagem</label>
                                            <input type="text" name="custom_sms_message_field" class="form-input"
                                                   value="{{ old('custom_sms_message_field', $settings->custom_sms_message_field) }}"
                                                   placeholder="message, text, mensagem">
                                        </div>
                                    </div>

                                    <!-- Headers HTTP -->
                                    <div class="mb-4">
                                        <label class="form-label">Headers HTTP</label>
                                        <div id="custom-headers-container">
                                            @php
                                                $headers = old('custom_sms_headers', $settings->custom_sms_headers ?? []);
                                                if (empty($headers)) {
                                                    $headers = [['key' => '', 'value' => '']];
                                                } else {
                                                    $formattedHeaders = [];
                                                    foreach ($headers as $key => $value) {
                                                        $formattedHeaders[] = ['key' => $key, 'value' => $value];
                                                    }
                                                    $headers = $formattedHeaders;
                                                }
                                            @endphp
                                            @foreach($headers as $index => $header)
                                                <div class="header-pair flex gap-2 mb-2">
                                                    <input type="text" name="custom_sms_headers[{{ $index }}][key]" 
                                                           class="form-input flex-1" placeholder="Chave (Ex: Authorization)"
                                                           value="{{ $header['key'] ?? '' }}">
                                                    <input type="text" name="custom_sms_headers[{{ $index }}][value]" 
                                                           class="form-input flex-1" placeholder="Valor (Ex: Bearer TOKEN)"
                                                           value="{{ $header['value'] ?? '' }}">
                                                    <button type="button" class="bg-red-500 hover:bg-red-600 text-white text-sm px-2 py-1 rounded" onclick="removeHeaderPair(this)">×</button>
                                                </div>
                                            @endforeach
                                        </div>
                                        <button type="button" class="bg-gray-500 hover:bg-gray-600 text-white text-sm px-3 py-1 rounded mt-2" onclick="addHeaderPair()">+ Adicionar Header</button>
                                    </div>

                                    <!-- Campos Adicionais -->
                                    <div class="mb-4">
                                        <label class="form-label">Campos Adicionais</label>
                                        <div id="custom-fields-container">
                                            @php
                                                $fields = old('custom_sms_additional_fields', $settings->custom_sms_additional_fields ?? []);
                                                if (empty($fields)) {
                                                    $fields = [['key' => '', 'value' => '']];
                                                } else {
                                                    $formattedFields = [];
                                                    foreach ($fields as $key => $value) {
                                                        $formattedFields[] = ['key' => $key, 'value' => $value];
                                                    }
                                                    $fields = $formattedFields;
                                                }
                                            @endphp
                                            @foreach($fields as $index => $field)
                                                <div class="field-pair flex gap-2 mb-2">
                                                    <input type="text" name="custom_sms_additional_fields[{{ $index }}][key]" 
                                                           class="form-input flex-1" placeholder="Campo (Ex: from, api_key)"
                                                           value="{{ $field['key'] ?? '' }}">
                                                    <input type="text" name="custom_sms_additional_fields[{{ $index }}][value]" 
                                                           class="form-input flex-1" placeholder="Valor (Ex: FM System)"
                                                           value="{{ $field['value'] ?? '' }}">
                                                    <button type="button" class="bg-red-500 hover:bg-red-600 text-white text-sm px-2 py-1 rounded" onclick="removeFieldPair(this)">×</button>
                                                </div>
                                            @endforeach
                                        </div>
                                        <button type="button" class="bg-gray-500 hover:bg-gray-600 text-white text-sm px-3 py-1 rounded mt-2" onclick="addFieldPair()">+ Adicionar Campo</button>
                                    </div>

                                    <!-- Indicadores de Sucesso -->
                                    <div class="mb-4">
                                        <label class="form-label">Indicadores de Sucesso</label>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Como identificar que o SMS foi enviado com sucesso</p>
                                        <div id="custom-success-container">
                                            @php
                                                $indicators = old('custom_sms_success_indicators', $settings->custom_sms_success_indicators ?? []);
                                                if (empty($indicators)) {
                                                    $indicators = [['key' => '', 'value' => '']];
                                                } else {
                                                    $formattedIndicators = [];
                                                    foreach ($indicators as $key => $value) {
                                                        $formattedIndicators[] = ['key' => $key, 'value' => $value];
                                                    }
                                                    $indicators = $formattedIndicators;
                                                }
                                            @endphp
                                            @foreach($indicators as $index => $indicator)
                                                <div class="success-pair flex gap-2 mb-2">
                                                    <select name="custom_sms_success_indicators[{{ $index }}][key]" class="form-select flex-1">
                                                        <option value="">Selecione...</option>
                                                        <option value="status_code" {{ ($indicator['key'] ?? '') === 'status_code' ? 'selected' : '' }}>Status Code</option>
                                                        <option value="status" {{ ($indicator['key'] ?? '') === 'status' ? 'selected' : '' }}>Campo "status"</option>
                                                        <option value="success" {{ ($indicator['key'] ?? '') === 'success' ? 'selected' : '' }}>Campo "success"</option>
                                                        <option value="response_contains" {{ ($indicator['key'] ?? '') === 'response_contains' ? 'selected' : '' }}>Resposta contém</option>
                                                        <option value="error" {{ ($indicator['key'] ?? '') === 'error' ? 'selected' : '' }}>Campo "error"</option>
                                                    </select>
                                                    <input type="text" name="custom_sms_success_indicators[{{ $index }}][value]" 
                                                           class="form-input flex-1" placeholder="Valor esperado (Ex: 200, success, true)"
                                                           value="{{ $indicator['value'] ?? '' }}">
                                                    <button type="button" class="bg-red-500 hover:bg-red-600 text-white text-sm px-2 py-1 rounded" onclick="removeSuccessPair(this)">×</button>
                                                </div>
                                            @endforeach
                                        </div>
                                        <button type="button" class="bg-gray-500 hover:bg-gray-600 text-white text-sm px-3 py-1 rounded mt-2" onclick="addSuccessPair()">+ Adicionar Indicador</button>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label">Número para Teste</label>
                                        <input type="text" name="custom_sms_test_number" class="form-input"
                                               value="{{ old('custom_sms_test_number', $settings->custom_sms_test_number) }}"
                                               placeholder="+5511999999999">
                                    </div>
                                </div>
                            @else
                                <!-- Configuração dos Outros Provedores -->
                                <div class="provider-config" id="config-{{ $key }}" style="{{ $settings->sms_provider === $key ? '' : 'display: none;' }}">
                                    <h5 class="text-lg font-semibold mb-2 text-gray-900 dark:text-white">{{ $provider['name'] }}</h5>
                                    <p class="text-gray-600 dark:text-gray-400 mb-4">{{ $provider['description'] }}</p>
                                    
                                    @foreach($provider['fields'] as $field => $label)
                                        <div class="mb-4">
                                            <label class="form-label">{{ $label }}</label>
                                            <input type="{{ str_contains($field, 'token') || str_contains($field, 'secret') ? 'password' : 'text' }}" 
                                                   name="sms_config[{{ $field }}]" 
                                                   class="form-input"
                                                   value="{{ $settings->sms_config[$field] ?? '' }}"
                                                   {{ !str_contains($label, 'opcional') ? 'required' : '' }}>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endforeach

                        <div class="mb-6">
                            <label class="form-label">Telefone para Testes</label>
                            <input type="text" name="test_phone" class="form-input" 
                                   value="{{ old('test_phone', $settings->test_phone) }}" 
                                   placeholder="+5511999999999">
                        </div>

                        <div class="flex gap-2">
                            <button type="button" class="btn-info-md align-icon-btn" onclick="testSmsEmailSettings()">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                                </svg>
                                <span>Testar SMS</span>
                            </button>
                            <button type="submit" class="btn-success-md align-icon-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Salvar SMS</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Status das Configurações -->
        <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 mr-2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                    </svg>
                    Status das Configurações
                </h4>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">Email Configurado</span>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $settings->hasCompleteEmailConfig() ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                            {{ $settings->hasCompleteEmailConfig() ? 'Sim' : 'Não' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">SMS Configurado</span>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $settings->hasCompleteSmsConfig() ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                            {{ $settings->hasCompleteSmsConfig() ? 'Sim' : 'Não' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
