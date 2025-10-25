<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Teste de Email - FM System</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;">
        <h1>🎉 Teste de Email</h1>
        <p>FM System - Configuração de Email</p>
    </div>
    
    <div style="background: #f8f9fa; padding: 30px; border-radius: 0 0 8px 8px;">
        <div style="background: #28a745; color: white; padding: 8px 16px; border-radius: 20px; display: inline-block; margin: 10px 0;">
            ✅ Configuração Funcionando!
        </div>
        
        <p>Este é um email de teste do FM System. Se você recebeu esta mensagem, as configurações de email estão funcionando corretamente!</p>
        
        <hr style="margin: 20px 0; border: none; border-top: 1px solid #ddd;">
        
        <p><strong>Informações do Teste:</strong></p>
        <ul>
            <li><strong>Data/Hora:</strong> {{ now()->format('d/m/Y H:i:s') }}</li>
            <li><strong>Sistema:</strong> FM System v2</li>
        </ul>
        
        <p style="margin-top: 30px;">
            <em>Este é um email automático de teste. Se você recebeu esta mensagem, significa que as configurações de email do seu sistema estão funcionando perfeitamente!</em>
        </p>
    </div>
    
    <div style="text-align: center; margin-top: 20px; color: #666; font-size: 12px;">
        <p>© {{ date('Y') }} FM System. Todos os direitos reservados.</p>
    </div>
</body>
</html>