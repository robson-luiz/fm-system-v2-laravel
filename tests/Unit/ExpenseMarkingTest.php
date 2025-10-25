<?php

namespace Tests\Unit;

use App\Models\Expense;
use App\Models\Installment;
use App\Models\User;
use App\Models\UserStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ExpenseMarkingTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $expense;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar user_status necessário (SQLite não tem constraints por padrão, mas vamos ser explícitos)
        DB::table('user_statuses')->insert([
            'id' => 3,
            'name' => 'Ativo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Criar usuário de teste
        $this->user = User::factory()->create();
        
        // Criar despesa de teste com parcelas
        $this->expense = Expense::create([
            'user_id' => $this->user->id,
            'description' => 'Teste Despesa',
            'amount' => 300.00,
            'due_date' => now()->addDays(5),
            'periodicity' => 'one-time',
            'status' => 'pending',
            'num_installments' => 3,
        ]);

        // Criar parcelas
        for ($i = 1; $i <= 3; $i++) {
            Installment::create([
                'expense_id' => $this->expense->id,
                'installment_number' => $i,
                'amount' => 100.00,
                'due_date' => now()->addDays($i * 30),
                'status' => 'pending',
            ]);
        }
    }

    #[Test]
    public function it_can_mark_all_as_paid()
    {
        // Arrange
        $paymentDate = '2025-10-25';
        
        // Act
        $result = $this->expense->markAllAsPaid($paymentDate);
        
        // Assert
        $this->assertTrue($result);
        
        // Verificar se a despesa foi marcada como paga
        $this->expense->refresh();
        $this->assertEquals('paid', $this->expense->status);
        $this->assertEquals($paymentDate, $this->expense->payment_date->toDateString());
        $this->assertNull($this->expense->reason_not_paid);
        
        // Verificar se todas as parcelas foram marcadas como pagas
        $installments = $this->expense->installments;
        $this->assertEquals(3, $installments->count());
        
        foreach ($installments as $installment) {
            $this->assertEquals('paid', $installment->status);
            $this->assertEquals($paymentDate, $installment->payment_date->toDateString());
            $this->assertNull($installment->reason_not_paid);
        }
    }

    #[Test]
    public function it_can_mark_all_as_overdue()
    {
        // Arrange
        $reason = 'Não consegui pagar devido a problemas financeiros';
        
        // Act
        $result = $this->expense->markAllAsOverdue($reason);
        
        // Assert
        $this->assertTrue($result);
        
        // Verificar se a despesa foi marcada como atrasada
        $this->expense->refresh();
        $this->assertEquals('overdue', $this->expense->status);
        $this->assertNull($this->expense->payment_date);
        $this->assertEquals($reason, $this->expense->reason_not_paid);
        
        // Verificar se todas as parcelas foram marcadas como atrasadas
        $installments = $this->expense->installments;
        $this->assertEquals(3, $installments->count());
        
        foreach ($installments as $installment) {
            $this->assertEquals('overdue', $installment->status);
            $this->assertNull($installment->payment_date);
            $this->assertEquals($reason, $installment->reason_not_paid);
        }
    }

    #[Test]
    public function it_handles_expense_without_installments()
    {
        // Arrange - Criar despesa sem parcelas
        $singleExpense = Expense::create([
            'user_id' => $this->user->id,
            'description' => 'Despesa Única',
            'amount' => 150.00,
            'due_date' => now()->addDays(5),
            'periodicity' => 'one-time',
            'status' => 'pending',
            'num_installments' => 1,
        ]);
        
        $paymentDate = '2025-10-25';
        
        // Act
        $result = $singleExpense->markAllAsPaid($paymentDate);
        
        // Assert
        $this->assertTrue($result);
        
        $singleExpense->refresh();
        $this->assertEquals('paid', $singleExpense->status);
        $this->assertEquals($paymentDate, $singleExpense->payment_date->toDateString());
        $this->assertNull($singleExpense->reason_not_paid);
        
        // Verificar que não há parcelas
        $this->assertEquals(0, $singleExpense->installments->count());
    }

    #[Test]
    public function it_uses_current_date_when_no_payment_date_provided()
    {
        // Act
        $result = $this->expense->markAllAsPaid();
        
        // Assert
        $this->assertTrue($result);
        
        $this->expense->refresh();
        $this->assertEquals('paid', $this->expense->status);
        $this->assertEquals(now()->toDateString(), $this->expense->payment_date->toDateString());
    }

    #[Test]
    public function it_maintains_data_integrity_with_transactions()
    {
        // Arrange - Verificar estado inicial
        $this->assertEquals('pending', $this->expense->status);
        $this->assertNull($this->expense->payment_date);
        
        $initialInstallments = $this->expense->installments;
        $this->assertEquals(3, $initialInstallments->count());
        
        foreach ($initialInstallments as $installment) {
            $this->assertEquals('pending', $installment->status);
            $this->assertNull($installment->payment_date);
        }
        
        // Act - Marcar como pago
        $paymentDate = '2025-10-25';
        $result = $this->expense->markAllAsPaid($paymentDate);
        
        // Assert - Verificar que a transação foi bem-sucedida
        $this->assertTrue($result);
        
        // Verificar que TODOS os dados foram atualizados consistentemente
        $this->expense->refresh();
        $this->assertEquals('paid', $this->expense->status);
        $this->assertEquals($paymentDate, $this->expense->payment_date->toDateString());
        
        // Verificar que TODAS as parcelas foram atualizadas
        $updatedInstallments = $this->expense->installments;
        $this->assertEquals(3, $updatedInstallments->count());
        
        foreach ($updatedInstallments as $installment) {
            $this->assertEquals('paid', $installment->status);
            $this->assertEquals($paymentDate, $installment->payment_date->toDateString());
            $this->assertNull($installment->reason_not_paid);
        }
    }
}