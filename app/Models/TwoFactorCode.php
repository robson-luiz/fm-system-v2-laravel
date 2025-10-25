<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class TwoFactorCode extends Model
{
    protected $fillable = [
        'user_id',
        'code',
        'method',
        'destination',
        'expires_at',
        'used_at',
        'attempts',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    /**
     * Relacionamento com usuário
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Gerar um código de 6 dígitos
     */
    public static function generateCode(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Criar um novo código para o usuário
     */
    public static function createForUser(User $user, string $method, string $destination): self
    {
        // Invalidar códigos anteriores não utilizados
        static::where('user_id', $user->id)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->delete();

        $settings = TwoFactorAuthSetting::getSettings();
        
        return static::create([
            'user_id' => $user->id,
            'code' => static::generateCode(),
            'method' => $method,
            'destination' => $destination,
            'expires_at' => now()->addMinutes($settings->code_expiry_minutes),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Verificar se o código é válido
     */
    public function isValid(): bool
    {
        return $this->used_at === null && 
               $this->expires_at > now() && 
               $this->attempts < TwoFactorAuthSetting::getMaxAttempts();
    }

    /**
     * Verificar se o código expirou
     */
    public function isExpired(): bool
    {
        return $this->expires_at <= now();
    }

    /**
     * Verificar se o código foi usado
     */
    public function isUsed(): bool
    {
        return $this->used_at !== null;
    }

    /**
     * Marcar código como usado
     */
    public function markAsUsed(): void
    {
        $this->update(['used_at' => now()]);
    }

    /**
     * Incrementar tentativas
     */
    public function incrementAttempts(): void
    {
        $this->increment('attempts');
    }

    /**
     * Scope para códigos válidos
     */
    public function scopeValid($query)
    {
        return $query->whereNull('used_at')
                    ->where('expires_at', '>', now())
                    ->where('attempts', '<', TwoFactorAuthSetting::getMaxAttempts());
    }

    /**
     * Scope para códigos de um usuário específico
     */
    public function scopeForUser($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }
}