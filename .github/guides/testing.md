# 🧪 Guia Completo de Testes - FM System v2

> **REGRA #1: Testes são OBRIGATÓRIOS e INEGOCIÁVEIS**

---

## ⚠️ TODA implementação/alteração DEVE incluir:

1. ✅ **Código da implementação**
2. ✅ **Testes completos** (Unit + Feature)
3. ✅ **Comando exato** para rodar os testes

---

## 📋 Cobertura Mínima de Testes

**Para CADA feature/alteração, crie testes para:**

- ✅ **Cenário Feliz** - Fluxo principal funciona corretamente
- ✅ **Casos de Borda** - Valores limites, null, vazios, strings longas
- ✅ **Validações** - Dados inválidos retornam erro apropriado
- ✅ **Permissões (Spatie)** - Usuários sem permissão recebem 403
- ✅ **Isolamento de Dados** - Usuário A não vê dados do Usuário B
- ✅ **Relacionamentos** - Foreign keys, cascades, updates

---

## 🧪 Template de Teste Obrigatório

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NomeTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // OBRIGATÓRIO para testes com Spatie Permission
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)
            ->forgetCachedPermissions();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    #[Test]
    public function it_can_do_something_successfully()
    {
        // Arrange
        $data = [
            'description' => 'Test Description',
            'amount' => 100.50,
        ];

        // Act
        $response = $this->post(route('resource.store'), $data);

        // Assert
        $response->assertRedirect(route('resource.index'));
        $this->assertDatabaseHas('table_name', [
            'description' => 'Test Description',
            'user_id' => $this->user->id,
        ]);
    }

    #[Test]
    public function it_fails_with_invalid_data()
    {
        // Arrange
        $data = ['description' => '']; // Inválido

        // Act
        $response = $this->post(route('resource.store'), $data);

        // Assert
        $response->assertSessionHasErrors('description');
        $this->assertDatabaseCount('table_name', 0);
    }

    #[Test]
    public function user_cannot_access_other_users_data()
    {
        // Arrange
        $otherUser = User::factory()->create();
        $item = Model::factory()->create(['user_id' => $otherUser->id]);

        // Act
        $response = $this->get(route('resource.show', $item));

        // Assert
        $response->assertStatus(403); // Ou 404 se preferir ocultar
    }
}
```

---

## 📦 Comandos de Teste

```bash
# Teste específico
php artisan test --filter=NomeDoTeste

# Teste de uma classe
php artisan test --filter=ExpenseTest

# Teste de pasta
php artisan test tests/Feature/

# Teste de um método específico
php artisan test --filter=it_can_create_expense

# Todos os testes
php artisan test

# Com coverage (requer xdebug)
php artisan test --coverage

# Parallel execution (mais rápido)
php artisan test --parallel
```

---

## 🎯 Exemplos de Testes Específicos

### Teste de CRUD Completo

```php
class ExpenseTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_list_user_expenses()
    {
        Expense::factory()->count(3)->create(['user_id' => $this->user->id]);
        Expense::factory()->count(2)->create(); // Outro usuário

        $response = $this->get(route('expenses.index'));

        $response->assertStatus(200);
        $response->assertViewHas('expenses');
        $this->assertCount(3, $response->viewData('expenses'));
    }

    #[Test]
    public function it_can_create_expense()
    {
        $data = [
            'description' => 'Nova Despesa',
            'amount' => 150.00,
            'due_date' => now()->addDays(10)->format('Y-m-d'),
            'status' => 'pending',
        ];

        $response = $this->post(route('expenses.store'), $data);

        $response->assertRedirect(route('expenses.index'));
        $this->assertDatabaseHas('expenses', [
            'description' => 'Nova Despesa',
            'user_id' => $this->user->id,
        ]);
    }

    #[Test]
    public function it_can_update_own_expense()
    {
        $expense = Expense::factory()->create(['user_id' => $this->user->id]);

        $response = $this->put(route('expenses.update', $expense), [
            'description' => 'Atualizado',
            'amount' => 200,
            'due_date' => now()->format('Y-m-d'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'description' => 'Atualizado',
        ]);
    }

    #[Test]
    public function it_cannot_update_other_users_expense()
    {
        $otherExpense = Expense::factory()->create();

        $response = $this->put(route('expenses.update', $otherExpense), [
            'description' => 'Hacker',
        ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function it_can_delete_own_expense()
    {
        $expense = Expense::factory()->create(['user_id' => $this->user->id]);

        $response = $this->delete(route('expenses.destroy', $expense));

        $response->assertRedirect();
        $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);
    }
}
```

### Teste de Validações

```php
#[Test]
public function it_requires_description()
{
    $response = $this->post(route('expenses.store'), [
        'amount' => 100,
        'due_date' => now()->format('Y-m-d'),
    ]);

    $response->assertSessionHasErrors('description');
}

#[Test]
public function it_requires_positive_amount()
{
    $response = $this->post(route('expenses.store'), [
        'description' => 'Test',
        'amount' => -50, // Negativo
        'due_date' => now()->format('Y-m-d'),
    ]);

    $response->assertSessionHasErrors('amount');
}

#[Test]
public function it_validates_amount_format()
{
    $response = $this->post(route('expenses.store'), [
        'description' => 'Test',
        'amount' => 'abc', // Não numérico
        'due_date' => now()->format('Y-m-d'),
    ]);

    $response->assertSessionHasErrors('amount');
}

#[Test]
public function it_validates_date_format()
{
    $response = $this->post(route('expenses.store'), [
        'description' => 'Test',
        'amount' => 100,
        'due_date' => 'invalid-date',
    ]);

    $response->assertSessionHasErrors('due_date');
}
```

### Teste de Relacionamentos

```php
#[Test]
public function expense_belongs_to_user()
{
    $expense = Expense::factory()->create(['user_id' => $this->user->id]);

    $this->assertInstanceOf(User::class, $expense->user);
    $this->assertEquals($this->user->id, $expense->user->id);
}

#[Test]
public function expense_can_have_installments()
{
    $expense = Expense::factory()->create(['user_id' => $this->user->id]);
    Installment::factory()->count(3)->create(['expense_id' => $expense->id]);

    $this->assertCount(3, $expense->installments);
}

#[Test]
public function deleting_expense_deletes_installments()
{
    $expense = Expense::factory()->create(['user_id' => $this->user->id]);
    $installments = Installment::factory()->count(3)->create(['expense_id' => $expense->id]);

    $expense->delete();

    foreach ($installments as $installment) {
        $this->assertDatabaseMissing('installments', ['id' => $installment->id]);
    }
}
```

### Teste de Permissões (Spatie)

```php
#[Test]
public function user_with_permission_can_create_expense()
{
    $this->user->givePermissionTo('create-expenses');

    $response = $this->post(route('expenses.store'), [
        'description' => 'Test',
        'amount' => 100,
        'due_date' => now()->format('Y-m-d'),
    ]);

    $response->assertRedirect();
}

#[Test]
public function user_without_permission_cannot_create_expense()
{
    // Usuário sem permissão

    $response = $this->post(route('expenses.store'), [
        'description' => 'Test',
        'amount' => 100,
        'due_date' => now()->format('Y-m-d'),
    ]);

    $response->assertStatus(403);
}
```

### Teste de Services

```php
class CashFlowServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CashFlowService $service;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new CashFlowService();
        $this->user = User::factory()->create();
    }

    #[Test]
    public function it_calculates_monthly_flow_correctly()
    {
        // Arrange
        Income::factory()->create([
            'user_id' => $this->user->id,
            'amount' => 5000,
            'received_date' => now()->startOfMonth(),
        ]);

        Expense::factory()->create([
            'user_id' => $this->user->id,
            'amount' => 2000,
            'due_date' => now()->startOfMonth(),
        ]);

        // Act
        $result = $this->service->calculateMonthlyFlow($this->user, 1);

        // Assert
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals(5000, $result[0]['incomes']);
        $this->assertEquals(2000, $result[0]['expenses']);
        $this->assertEquals(3000, $result[0]['balance']);
    }
}
```

---

## 🔧 Dicas e Truques

### Factories

**Sempre crie factories para seus models:**

```php
// database/factories/ExpenseFactory.php
class ExpenseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'description' => $this->faker->sentence(),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'due_date' => $this->faker->dateTimeBetween('now', '+30 days'),
            'status' => 'pending',
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
            'payment_date' => now(),
        ]);
    }
}
```

### Seeders para Testes

**Não use seeders em testes, use factories diretamente**

```php
// ❌ ERRADO
$this->seed(ExpenseSeeder::class);

// ✅ CORRETO
Expense::factory()->count(5)->create(['user_id' => $this->user->id]);
```

### Assertions Úteis

```php
// Database
$this->assertDatabaseHas('expenses', ['id' => 1]);
$this->assertDatabaseMissing('expenses', ['id' => 1]);
$this->assertDatabaseCount('expenses', 5);

// Response
$response->assertStatus(200);
$response->assertRedirect(route('expenses.index'));
$response->assertSessionHasErrors('field');
$response->assertSessionDoesntHaveErrors();
$response->assertViewIs('expenses.index');
$response->assertViewHas('expenses');

// JSON
$response->assertJson(['success' => true]);
$response->assertJsonStructure(['data' => ['id', 'name']]);

// Auth
$this->assertAuthenticated();
$this->assertGuest();
```

---

## 🚫 Anti-Patterns

### ❌ Não faça:

```php
// Testar apenas cenário feliz
#[Test]
public function it_works()
{
    $response = $this->post('/endpoint', $validData);
    $response->assertStatus(200);
}

// Testes muito vagos
#[Test]
public function test()
{
    $this->assertTrue(true);
}

// Não usar factories
$user = new User();
$user->name = 'Test';
$user->save();
```

### ✅ Faça:

```php
// Testes descritivos e completos
#[Test]
public function it_creates_expense_with_valid_data()
{
    // Arrange, Act, Assert claros
}

#[Test]
public function it_rejects_invalid_amount()
{
    // Testa cenário de erro
}

// Use factories
$user = User::factory()->create();
$expense = Expense::factory()->create(['user_id' => $user->id]);
```

---

## ✅ Checklist de Testes

Antes de entregar, verifique:

- [ ] ✅ Todos os testes passam (`php artisan test`)
- [ ] ✅ Testa cenário feliz (happy path)
- [ ] ✅ Testa validações (dados inválidos)
- [ ] ✅ Testa permissões (authorized/unauthorized)
- [ ] ✅ Testa isolamento de usuários
- [ ] ✅ Testa relacionamentos
- [ ] ✅ Testa casos de borda (null, vazios, limites)
- [ ] ✅ Usa factories ao invés de criar dados manualmente
- [ ] ✅ Usa `RefreshDatabase` trait
- [ ] ✅ Limpa cache de permissões (`forgetCachedPermissions`)
- [ ] ✅ Nomes descritivos (`it_can_create_expense`)
- [ ] ✅ Arrange, Act, Assert estruturado

---

**Lembre-se: Código sem testes é código incompleto.** 🚫
