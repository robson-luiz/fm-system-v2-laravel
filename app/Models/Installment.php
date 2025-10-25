<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Installment extends Model implements AuditableContract
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'expense_id',
        'installment_number',
        'amount',
        'due_date',
        'status', // 'pending', 'paid', 'overdue'
        'payment_date',
        'reason_not_paid',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'payment_date' => 'date',
    ];

    /**
     * Relationship: Installment belongs to Expense
     */
    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    /**
     * Scope: Filter by status
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Filter by status
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope: Filter overdue installments
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    /**
     * Scope: Filter installments that should be overdue (pending + past due date)
     */
    public function scopeShouldBeOverdue($query)
    {
        return $query->where('status', 'pending')
                     ->where('due_date', '<', now()->toDateString());
    }

    /**
     * Scope: Filter installments due soon (next 7 days)
     */
    public function scopeDueSoon($query)
    {
        return $query->where('status', 'pending')
                     ->whereBetween('due_date', [
                         now()->toDateString(),
                         now()->addDays(7)->toDateString()
                     ]);
    }

    /**
     * Accessor: Get formatted amount
     */
    public function getAmountFormattedAttribute()
    {
        return 'R$ ' . number_format($this->amount, 2, ',', '.');
    }

    /**
     * Accessor: Get status badge HTML
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="badge badge-warning">Pendente</span>',
            'paid' => '<span class="badge badge-success">Pago</span>',
            'overdue' => '<span class="badge badge-danger">Atrasada</span>',
        ];
        return $badges[$this->status] ?? '<span class="badge badge-secondary">Desconhecido</span>';
    }

    /**
     * Accessor: Check if installment is overdue
     */
    public function getIsOverdueAttribute()
    {
    return ($this->status === 'pending' && $this->due_date < now()->toDateString()) || $this->status === 'overdue';
    }

    /**
     * Accessor: Check if installment is due soon (next 7 days)
     */
    public function getIsDueSoonAttribute()
    {
        return $this->status === 'pending' && 
               $this->due_date >= now()->toDateString() && 
               $this->due_date <= now()->addDays(7)->toDateString();
    }

    /**
     * Mark installment as paid
     */
    public function markAsPaid($paymentDate = null)
    {
        $this->update([
            'status' => 'paid',
            'payment_date' => $paymentDate ?? now()->toDateString(),
            'reason_not_paid' => null,
        ]);
    }

    /**
     * Mark installment as unpaid with reason
     */
    public function markAsUnpaid($reason = null)
    {
        $newStatus = ($this->due_date < now()->toDateString()) ? 'overdue' : 'pending';
        $this->update([
            'status' => $newStatus,
            'payment_date' => null,
            'reason_not_paid' => $reason,
        ]);
    }

    /**
     * Mark installment as overdue with reason
     */
    public function markAsOverdue($reason = null)
    {
        $this->update([
            'status' => 'overdue',
            'payment_date' => null,
            'reason_not_paid' => $reason,
        ]);
    }
}
