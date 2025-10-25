<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autenticação 2FA Ativada - FM System</title>
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
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        .logo svg {
            width: 40px;
            height: 40px;
            color: white;
        }
        h1 {
            color: #1f2937;
            margin: 0 0 10px 0;
            font-size: 28px;
            font-weight: 700;
        }
        .subtitle {
            color: #6b7280;
            margin: 0 0 30px 0;
            font-size: 16px;
        }
        .success-badge {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 25px;
            display: inline-block;
            margin: 20px 0;
            font-weight: 600;
        }
        .feature-list {
            background-color: #f8fafc;
            border-radius: 8px;
            padding: 25px;
            margin: 25px 0;
        }
        .feature-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        .feature-item:last-child {
            margin-bottom: 0;
        }
        .feature-icon {
            width: 24px;
            height: 24px;
            background-color: #10b981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
        }
        .feature-icon svg {
            width: 14px;
            height: 14px;
            color: white;
        }
        .feature-text {
            color: #374151;
            font-size: 14px;
        }
        .info-box {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-left: 4px solid #3b82f6;
            padding: 20px;
            margin: 25px 0;
            border-radius: 0 8px 8px 0;
        }
        .info-box h3 {
            color: #1e40af;
            margin: 0 0 10px 0;
            font-size: 16px;
            font-weight: 600;
        }
        .info-box p {
            margin: 0;
            color: #1e40af;
            font-size: 14px;
        }
        .method-badge {
            background-color: #3b82f6;
            color: white;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 25px;
            border-top: 2px solid #e5e7eb;
        }
        .footer p {
            color: #6b7280;
            font-size: 12px;
            margin: 5px 0;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            padding: 14px 28px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 20px 0;
            transition: transform 0.2s;
        }
        .cta-button:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <h1>🎉 Parabéns!</h1>
            <p class="subtitle">Sua conta agora está mais segura</p>
        </div>

        <div class="success-badge">
            ✅ Autenticação de Duas Etapas Ativada
        </div>

        <p>Olá, <strong>{{ $user->name }}</strong>!</p>
        
        <p>Você ativou com sucesso a <strong>Autenticação de Duas Etapas (2FA)</strong> em sua conta do FM System. Sua segurança é nossa prioridade!</p>

        <div class="info-box">
            <h3>📱 Método Configurado</h3>
            <p>
                Você escolheu receber códigos via: 
                <span class="method-badge">
                    {{ $method === 'sms' ? '📱 SMS' : '📧 Email' }}
                </span>
            </p>
        </div>

        <div class="feature-list">
            <h3 style="color: #1f2937; margin: 0 0 20px 0; font-size: 18px;">🔐 O que isso significa:</h3>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div class="feature-text">
                    <strong>Segurança Extra:</strong> Mesmo que alguém descubra sua senha, não conseguirá acessar sua conta sem o código 2FA.
                </div>
            </div>

            <div class="feature-item">
                <div class="feature-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div class="feature-text">
                    <strong>Proteção de Dados:</strong> Seus dados financeiros estão protegidos com tecnologia de ponta.
                </div>
            </div>

            <div class="feature-item">
                <div class="feature-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="feature-text">
                    <strong>Notificações:</strong> Você será notificado sobre todas as tentativas de login em sua conta.
                </div>
            </div>
        </div>

        <h3 style="color: #1f2937; margin: 25px 0 15px 0;">🚀 Próximos Passos:</h3>
        <ol style="color: #374151; padding-left: 20px;">
            <li style="margin-bottom: 8px;">Na próxima vez que fizer login, você receberá um código de 6 dígitos</li>
            <li style="margin-bottom: 8px;">Digite o código na tela de verificação para acessar sua conta</li>
            <li style="margin-bottom: 8px;">Mantenha seu {{ $method === 'sms' ? 'telefone' : 'email' }} sempre atualizado</li>
        </ol>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ config('app.url') }}" class="cta-button">
                🏠 Acessar FM System
            </a>
        </div>

        <div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin: 25px 0; border-radius: 0 8px 8px 0;">
            <p style="margin: 0; color: #92400e; font-size: 14px;">
                <strong>💡 Dica:</strong> Guarde este email como referência. Se precisar de ajuda, nossa equipe de suporte está sempre disponível!
            </p>
        </div>

        <div class="footer">
            <p><strong>FM System</strong></p>
            <p>Sistema de Gestão Financeira Pessoal</p>
            <p>Obrigado por confiar em nós! 💙</p>
            <p>Data: {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>
</body>
</html>
