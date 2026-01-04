<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de Verificação - FM System</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 40px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            width: 60px;
            height: 60px;
            background-color: #3b82f6;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        .logo svg {
            width: 30px;
            height: 30px;
            color: white;
        }
        h1 {
            color: #1f2937;
            margin: 0 0 10px 0;
            font-size: 24px;
            font-weight: 600;
        }
        .subtitle {
            color: #6b7280;
            margin: 0 0 30px 0;
            font-size: 16px;
        }
        .code-container {
            background-color: #f3f4f6;
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
        }
        .code {
            font-size: 36px;
            font-weight: bold;
            color: #1f2937;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
            margin: 0;
        }
        .code-label {
            color: #6b7280;
            font-size: 14px;
            margin-top: 10px;
        }
        .info-box {
            background-color: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 0 4px 4px 0;
        }
        .info-box p {
            margin: 0;
            color: #1e40af;
            font-size: 14px;
        }
        .warning-box {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 0 4px 4px 0;
        }
        .warning-box p {
            margin: 0;
            color: #92400e;
            font-size: 14px;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            color: #6b7280;
            font-size: 12px;
            margin: 5px 0;
        }
        .button {
            display: inline-block;
            background-color: #3b82f6;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            margin: 20px 0;
        }
        .button:hover {
            background-color: #2563eb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h1>Código de Verificação</h1>
            <p class="subtitle">FM System - Autenticação de Duas Etapas</p>
        </div>

        <p>Olá, <strong>{{ $user->name }}</strong>!</p>
        
        <p>Você solicitou acesso ao FM System. Para completar o login, utilize o código de verificação abaixo:</p>

        <div class="code-container">
            <div class="code">{{ $code }}</div>
            <div class="code-label">Código de 6 dígitos</div>
        </div>

        <div class="info-box">
            <p><strong>Informações importantes:</strong></p>
            <p>• Este código expira em {{ $expiresInMinutes }} minutos</p>
            <p>• Use apenas no site oficial do FM System</p>
            <p>• Não compartilhe este código com ninguém</p>
        </div>

        <div class="warning-box">
            <p><strong>Não solicitou este código?</strong></p>
            <p>Se você não tentou fazer login no FM System, ignore este email. Sua conta permanece segura.</p>
        </div>

        <p>Se você está tendo problemas para inserir o código, você pode tentar:</p>
        <ul>
            <li>Verificar se não há espaços extras</li>
            <li>Solicitar um novo código</li>
            <li>Entrar em contato com o suporte</li>
        </ul>

        <div class="footer">
            <p><strong>FM System</strong></p>
            <p>Sistema de Gestão Financeira</p>
            <p>Este é um email automático, não responda.</p>
            <p>Data: {{ now()->format('d/m/Y H:i') }}</p>
            <p>IP: {{ $ipAddress }}</p>
        </div>
    </div>
</body>
</html>
