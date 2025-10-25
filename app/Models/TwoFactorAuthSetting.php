<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TwoFactorAuthSetting extends Model
{
    protected $fillable = [
        'enabled',
        'default_method',
        'force_for_admins',
        'allow_user_choice',
        'code_expiry_minutes',
        'max_attempts',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'force_for_admins' => 'boolean',
        'allow_user_choice' => 'boolean',
    ];

    /**
     * Obter as configurações do sistema (singleton)
     */
    public static function getSettings()
    {
        return static::first() ?? static::create([
            'enabled' => false,
            'default_method' => 'email',
            'force_for_admins' => false,
            'allow_user_choice' => true,
            'code_expiry_minutes' => 5,
            'max_attempts' => 3,
        ]);
    }

    /**
     * Verificar se o 2FA está habilitado no sistema
     */
    public static function isEnabled(): bool
    {
        return static::getSettings()->enabled;
    }

    /**
     * Verificar se é obrigatório para administradores
     */
    public static function isRequiredForAdmins(): bool
    {
        return static::getSettings()->force_for_admins;
    }

    /**
     * Obter método padrão
     */
    public static function getDefaultMethod(): string
    {
        return static::getSettings()->default_method;
    }

    /**
     * Verificar se usuários podem escolher o método
     */
    public static function allowsUserChoice(): bool
    {
        return static::getSettings()->allow_user_choice;
    }

    /**
     * Obter tempo de expiração em minutos
     */
    public static function getCodeExpiryMinutes(): int
    {
        return static::getSettings()->code_expiry_minutes;
    }

    /**
     * Obter máximo de tentativas
     */
    public static function getMaxAttempts(): int
    {
        return static::getSettings()->max_attempts;
    }

    /**
     * Obter configurações SMS do EmailSmsSetting
     */
    public static function getSmsProvider(): ?string
    {
        $emailSmsSettings = \App\Models\EmailSmsSetting::getSettings();
        return $emailSmsSettings->sms_enabled ? $emailSmsSettings->sms_provider : null;
    }

    /**
     * Obter configuração SMS do EmailSmsSetting
     */
    public static function getSmsConfig(): ?array
    {
        $emailSmsSettings = \App\Models\EmailSmsSetting::getSettings();
        return $emailSmsSettings->sms_enabled ? $emailSmsSettings->sms_config : null;
    }

    /**
     * Verificar se SMS está disponível
     */
    public static function isSmsAvailable(): bool
    {
        $emailSmsSettings = \App\Models\EmailSmsSetting::getSettings();
        return $emailSmsSettings->sms_enabled && $emailSmsSettings->hasCompleteSmsConfig();
    }
}