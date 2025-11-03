<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class CreditCard extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'bank',
        'last_four_digits',
        'card_limit',
        'available_limit',
        'closing_day',
        'due_day',
        'best_purchase_day',
        'interest_rate',
        'annual_fee',
        'is_active',
        'auto_calculate_limit',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'card_limit' => 'decimal:2',
            'available_limit' => 'decimal:2',
            'interest_rate' => 'decimal:2',
            'annual_fee' => 'decimal:2',
            'is_active' => 'boolean',
            'auto_calculate_limit' => 'boolean',
        ];
    }

    /**
     * Relacionamento com usuário
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com despesas
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * Scope para cartões ativos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Accessor para formatar limite do cartão
     */
    public function getCardLimitFormattedAttribute()
    {
        return 'R$ ' . number_format($this->card_limit, 2, ',', '.');
    }

    /**
     * Accessor para formatar limite disponível
     */
    public function getAvailableLimitFormattedAttribute()
    {
        return 'R$ ' . number_format($this->available_limit, 2, ',', '.');
    }

    /**
     * Accessor para formatar anuidade
     */
    public function getAnnualFeeFormattedAttribute()
    {
        return 'R$ ' . number_format($this->annual_fee, 2, ',', '.');
    }

    /**
     * Accessor para percentual de limite utilizado
     */
    public function getLimitUsagePercentageAttribute()
    {
        if ($this->card_limit <= 0) {
            return 0;
        }

        $used = $this->card_limit - $this->available_limit;
        return round(($used / $this->card_limit) * 100, 2);
    }

    /**
     * Accessor para valor utilizado
     */
    public function getUsedLimitAttribute()
    {
        return $this->card_limit - $this->available_limit;
    }

    /**
     * Accessor para valor utilizado formatado
     */
    public function getUsedLimitFormattedAttribute()
    {
        return 'R$ ' . number_format($this->used_limit, 2, ',', '.');
    }

    /**
     * Calcula melhor dia para compra se não estiver definido
     */
    public function getBestPurchaseDayCalculatedAttribute()
    {
        if ($this->best_purchase_day) {
            return $this->best_purchase_day;
        }

        // Melhor dia é logo após o fechamento
        return $this->closing_day < 28 ? $this->closing_day + 1 : 1;
    }
}
