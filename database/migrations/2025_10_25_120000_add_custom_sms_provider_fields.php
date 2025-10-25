<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('email_sms_settings', function (Blueprint $table) {
            // Adicionar campos para provedor customizado
            $table->string('custom_sms_provider_name')->nullable()->comment('Nome do provedor customizado');
            $table->string('custom_sms_api_url')->nullable()->comment('URL da API do provedor customizado');
            $table->enum('custom_sms_method', ['GET', 'POST', 'PUT', 'PATCH'])->default('POST')->comment('Método HTTP da API');
            $table->string('custom_sms_phone_field')->nullable()->comment('Nome do campo para telefone');
            $table->string('custom_sms_message_field')->nullable()->comment('Nome do campo para mensagem');
            $table->json('custom_sms_headers')->nullable()->comment('Headers HTTP customizados');
            $table->json('custom_sms_additional_fields')->nullable()->comment('Campos adicionais da API');
            $table->json('custom_sms_success_indicators')->nullable()->comment('Indicadores de sucesso da resposta');
            $table->string('custom_sms_test_number')->nullable()->comment('Número para teste do provedor customizado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_sms_settings', function (Blueprint $table) {
            $table->dropColumn([
                'custom_sms_provider_name',
                'custom_sms_api_url',
                'custom_sms_method',
                'custom_sms_phone_field',
                'custom_sms_message_field',
                'custom_sms_headers',
                'custom_sms_additional_fields',
                'custom_sms_success_indicators',
                'custom_sms_test_number'
            ]);
        });
    }
};