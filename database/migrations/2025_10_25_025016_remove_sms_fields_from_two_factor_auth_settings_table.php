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
        Schema::table('two_factor_auth_settings', function (Blueprint $table) {
            // Remover campos SMS que agora estão na tabela email_sms_settings
            $table->dropColumn(['sms_provider', 'sms_config']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('two_factor_auth_settings', function (Blueprint $table) {
            // Restaurar campos SMS caso precise fazer rollback
            $table->string('sms_provider')->nullable();
            $table->json('sms_config')->nullable();
        });
    }
};