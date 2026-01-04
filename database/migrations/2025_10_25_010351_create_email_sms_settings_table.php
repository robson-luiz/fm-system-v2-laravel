<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('email_sms_settings', function (Blueprint $table) {
            $table->id();
            
            // Configurações de Email
            $table->string('mail_mailer')->default('smtp')->comment('Driver de email (smtp, sendmail, etc)');
            $table->string('mail_host')->nullable()->comment('Servidor SMTP');
            $table->integer('mail_port')->default(587)->comment('Porta SMTP');
            $table->string('mail_username')->nullable()->comment('Usuário SMTP');
            $table->string('mail_password')->nullable()->comment('Senha SMTP');
            $table->enum('mail_encryption', ['tls', 'ssl', 'none'])->default('tls')->comment('Criptografia SMTP');
            $table->string('mail_from_address')->nullable()->comment('Email remetente');
            $table->string('mail_from_name')->default('FM System')->comment('Nome remetente');
            
            // Configurações de SMS
            $table->string('sms_provider')->nullable()->comment('Provedor SMS (twilio, nexmo, etc)');
            $table->json('sms_config')->nullable()->comment('Configurações do provedor SMS');
            $table->boolean('sms_enabled')->default(false)->comment('SMS habilitado');
            
            // Configurações de Teste
            $table->string('test_email')->nullable()->comment('Email para testes');
            $table->string('test_phone')->nullable()->comment('Telefone para testes');
            
            $table->timestamps();
        });

        // Inserir configuração padrão
        DB::table('email_sms_settings')->insert([
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
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_sms_settings');
    }
};