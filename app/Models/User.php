<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'cpf',
        'alias',
        'image',
        'two_factor_enabled',
        'two_factor_method',
        'phone_number',
        'phone_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_enabled' => 'boolean',
            'phone_verified_at' => 'datetime',
            'two_factor_verified_at' => 'datetime',
            'two_factor_locked_until' => 'datetime',
        ];
    }

    // Chave estrangeira - Criar relacionamento entre usuário e status
    public function userStatus()
    {
        return $this->belongsTo(UserStatus::class);
    }

    // Relacionamento com despesas
    /**
     * @return HasMany<Expense>
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    // Relacionamento com cartões de crédito
    /**
     * @return HasMany<CreditCard>
     */
    public function creditCards(): HasMany
    {
        return $this->hasMany(CreditCard::class);
    }

    // Relacionamento com receitas
    /**
     * @return HasMany<Income>
     */
    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }

    // Formatar o CPF para imprimir na VIEW
    public function getCpfFormattedAttribute()
    {
        if (!$this->cpf || strlen($this->cpf) !== 11) {
            return $this->cpf;
        }

        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $this->cpf);
    }

    // Accessor para retornar a imagem ou uma imagem padrão
    public function getImageUrlAttribute()
    {
        /** @var \Illuminate\Contracts\Filesystem\Cloud $disk */
        $disk = Storage::disk('public');

        // Se não tiver imagem, usa padrão
        if (!$this->image) {
            return asset('/images/users/user.png');
        }

        // Primeiro verifica se existe na pasta profile (perfil do usuário)
        $profilePath = "profile/{$this->id}/{$this->image}";
        if (Storage::disk('public')->exists($profilePath)) {
            return asset("storage/{$profilePath}");
        }

        // Depois verifica se existe na pasta users (admin editando)
        $usersPath = "users/{$this->id}/{$this->image}";
        if (Storage::disk('public')->exists($usersPath)) {
            return asset("storage/{$usersPath}");
        }

        // Verifica se ainda existe na pasta pública antiga (para compatibilidade)
        if (file_exists(public_path('images/users/' . $this->id . '/' . $this->image))) {
            return asset('images/users/' . $this->id . '/' . $this->image);
        }

        // Se não encontrar em lugar algum, retorna a imagem padrão
        return asset('/images/users/user.png');
    }

    /**
     * Relacionamento com códigos de 2FA
     */
    public function twoFactorCodes()
    {
        return $this->hasMany(TwoFactorCode::class);
    }

    /**
     * Verificar se o usuário tem 2FA habilitado
     */
    public function hasTwoFactorEnabled(): bool
    {
        return $this->two_factor_enabled;
    }

    /**
     * Verificar se o usuário deve usar 2FA obrigatoriamente
     */
    public function requiresTwoFactor(): bool
    {
        if (!TwoFactorAuthSetting::isEnabled()) {
            return false;
        }

        // Se é obrigatório para todos os usuários
        if (TwoFactorAuthSetting::isRequiredForAllUsers()) {
            return true;
        }

        // Se é obrigatório para admins e o usuário é admin
        if (TwoFactorAuthSetting::isRequiredForAdmins() && $this->hasRole('admin')) {
            return true;
        }

        // Se o usuário habilitou voluntariamente
        return $this->two_factor_enabled;
    }

    /**
     * Obter o destino para envio do código 2FA
     */
    public function getTwoFactorDestination(): string
    {
        return $this->two_factor_method === 'sms' 
            ? $this->phone_number 
            : $this->email;
    }

    /**
     * Verificar se o telefone está verificado (necessário para SMS)
     */
    public function hasVerifiedPhone(): bool
    {
        return $this->phone_verified_at !== null;
    }

    /**
     * Verificar se o usuário está bloqueado por tentativas de 2FA
     */
    public function isTwoFactorLocked(): bool
    {
        return $this->two_factor_locked_until && 
               $this->two_factor_locked_until > now();
    }

    /**
     * Bloquear usuário por tentativas excessivas de 2FA
     */
    public function lockTwoFactor(int $minutes = 15): void
    {
        $this->update([
            'two_factor_locked_until' => now()->addMinutes($minutes)
        ]);
    }

    /**
     * Desbloquear usuário do 2FA
     */
    public function unlockTwoFactor(): void
    {
        $this->update([
            'two_factor_locked_until' => null,
            'two_factor_failed_attempts' => 0
        ]);
    }

    /**
     * Incrementar tentativas falhadas de 2FA
     */
    public function incrementTwoFactorFailedAttempts(): void
    {
        $this->increment('two_factor_failed_attempts');
        
        $maxAttempts = TwoFactorAuthSetting::getMaxAttempts();
        if ($this->two_factor_failed_attempts >= $maxAttempts) {
            $this->lockTwoFactor();
        }
    }

    /**
     * Resetar tentativas falhadas de 2FA
     */
    public function resetTwoFactorFailedAttempts(): void
    {
        $this->update([
            'two_factor_failed_attempts' => 0,
            'two_factor_verified_at' => now()
        ]);
    }

    /**
     * Habilitar 2FA para o usuário
     */
    public function enableTwoFactor(string $method = null): void
    {
        $method = $method ?: TwoFactorAuthSetting::getDefaultMethod();
        
        $this->update([
            'two_factor_enabled' => true,
            'two_factor_method' => $method
        ]);
    }

    /**
     * Desabilitar 2FA para o usuário
     */
    public function disableTwoFactor(): void
    {
        $this->update([
            'two_factor_enabled' => false,
            'two_factor_verified_at' => null,
            'two_factor_failed_attempts' => 0,
            'two_factor_locked_until' => null
        ]);

        // Remover códigos pendentes
        $this->twoFactorCodes()->delete();
    }
}
