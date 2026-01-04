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
        Schema::create('two_factor_auth_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('enabled')->default(false)->comment('Se o 2FA está habilitado no sistema');
            $table->enum('default_method', ['email', 'sms'])->default('email')->comment('Método padrão para novos usuários');
            $table->boolean('force_for_admins')->default(false)->comment('Forçar 2FA para administradores');
            $table->boolean('allow_user_choice')->default(true)->comment('Permitir que usuários escolham o método');
            $table->integer('code_expiry_minutes')->default(5)->comment('Tempo de expiração do código em minutos');
            $table->integer('max_attempts')->default(3)->comment('Máximo de tentativas antes de bloquear');
            $table->string('sms_provider')->nullable()->comment('Provedor de SMS (twilio, nexmo, etc)');
            $table->json('sms_config')->nullable()->comment('Configurações do provedor SMS');
            $table->timestamps();
        });

        // Inserir configuração padrão
        DB::table('two_factor_auth_settings')->insert([
            'enabled' => false,
            'default_method' => 'email',
            'force_for_admins' => false,
            'allow_user_choice' => true,
            'code_expiry_minutes' => 5,
            'max_attempts' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('two_factor_auth_settings');
    }
};