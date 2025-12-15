<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Wishlist extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'target_amount',
        'current_amount',
        'priority',
        'target_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'target_date' => 'date',
    ];

    /**
     * Relacionamento com User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para filtrar por usuário
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope para filtrar por status
     */
    public function scopeByStatus($query, $status)
    {
        if ($status && $status !== 'todos') {
            return $query->where('status', $status);
        }
        return $query;
    }

    /**
     * Scope para filtrar por prioridade
     */
    public function scopeByPriority($query, $priority)
    {
        if ($priority && $priority !== 'todas') {
            return $query->where('priority', $priority);
        }
        return $query;
    }

    /**
     * Accessor para progresso percentual
     */
    public function getProgressPercentageAttribute()
    {
        if ($this->target_amount <= 0) {
            return 0;
        }
        
        $percentage = ($this->current_amount / $this->target_amount) * 100;
        return min(100, round($percentage, 2));
    }

    /**
     * Accessor para valor restante
     */
    public function getRemainingAmountAttribute()
    {
        return max(0, $this->target_amount - $this->current_amount);
    }

    /**
     * Accessor para valor formatado
     */
    public function getFormattedTargetAmountAttribute()
    {
        return 'R$ ' . number_format($this->target_amount, 2, ',', '.');
    }

    /**
     * Accessor para valor atual formatado
     */
    public function getFormattedCurrentAmountAttribute()
    {
        return 'R$ ' . number_format($this->current_amount, 2, ',', '.');
    }

    /**
     * Accessor para valor restante formatado
     */
    public function getFormattedRemainingAmountAttribute()
    {
        return 'R$ ' . number_format($this->remaining_amount, 2, ',', '.');
    }

    /**
     * Verifica se o item está concluído
     */
    public function isCompleted()
    {
        return $this->status === 'concluida' || $this->current_amount >= $this->target_amount;
    }

    /**
     * Marca item como concluído
     */
    public function markAsCompleted()
    {
        $this->status = 'concluida';
        $this->current_amount = $this->target_amount;
        $this->save();
    }

    /**
     * Lista de status disponíveis
     */
    public static function getStatuses()
    {
        return [
            'em_progresso' => 'Em Progresso',
            'concluida' => 'Concluída',
            'cancelada' => 'Cancelada',
        ];
    }

    /**
     * Lista de prioridades disponíveis
     */
    public static function getPriorities()
    {
        return [
            'baixa' => 'Baixa',
            'media' => 'Média',
            'alta' => 'Alta',
        ];
    }
}

