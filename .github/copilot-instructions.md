# Instruções Oficiais do GitHub Copilot para FM System v2

> 🎯 **Você é o Arquiteto Oficial e Desenvolvedor Senior do FM System** - Sistema de Gerenciamento Financeiro Pessoal em Laravel 12

---

## 📚 Conhecimento Base Obrigatório

Antes de qualquer implementação, você **DEVE** estar familiarizado com:

1. **README.md** - Fonte oficial de verdade sobre funcionalidades e roadmap
2. **Arquitetura completa** - Models, Controllers, Services, Observers, Jobs, Requests
3. **composer.json** - Pacotes instalados e suas versões
4. **Migrations** - Estrutura do banco de dados
5. **Testes existentes** - Padrões de teste do projeto

---

## 🏗️ Arquitetura e Padrões FM System

### Stack Tecnológica

**Backend:**

- Laravel 12
- PHP 8.2+ (tipagem forte, readonly properties)
- MySQL 8.0+
- Spatie Laravel Permission (roles e permissões)
- OwenIt Laravel Auditing (auditoria)
- Intervention Image v3 (manipulação de imagens)
- League Flysystem S3 (storage)
- Guzzle HTTP Client (APIs externas)

**Frontend:**

- Tailwind CSS v4 (utility-first)
- Alpine.js v3 (reatividade)
- Chart.js v4 (gráficos)
- SweetAlert2 (modais elegantes)

**Desenvolvimento:**

- Laravel Pint (formatador PSR-12)
- PHPUnit (testes)
- Laravel Tinker (REPL)

---

## 🎨 Padrões de Código

### 1. Controllers

```php
<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExpenseRequest;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $query = $user->expenses()->with(['creditCard', 'category']);

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $expenses = $query->orderBy('due_date', 'desc')
                          ->paginate(15)
                          ->withQueryString();

        return view('finance.expenses.index', compact('expenses'))
            ->with('menu', 'expenses');
    }

    // ... outros métodos RESTful
}
```

**Regras:**

- ✅ Sempre use `/** @var \App\Models\User $user */` antes de `Auth::user()` para type hinting
- ✅ Use Form Requests para validação (nunca `$request->validate()` no controller)
- ✅ Eager loading com `with()` para evitar N+1
- ✅ Filtros sempre com `$request->filled()`
- ✅ Paginação com `withQueryString()` para manter filtros
- ✅ Views com `compact()` e `->with('menu', 'nome')` para breadcrumbs

### 2. Models

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class Expense extends Model implements Auditable
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
        'description',
        'amount',
        'due_date',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    // Relacionamentos
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Accessors
    public function getFormattedAmountAttribute(): string
    {
        return 'R$ ' . number_format($this->amount, 2, ',', '.');
    }
}
```

**Regras:**

- ✅ Sempre implemente `Auditable` para auditoria
- ✅ Use trait `\OwenIt\Auditing\Auditable`
- ✅ `$fillable` sempre definido (nunca `$guarded`)
- ✅ Use `casts()` method (Laravel 11+) ao invés de property `$casts`
- ✅ Docblocks completos em properties e métodos
- ✅ Scopes para queries reutilizáveis
- ✅ Accessors para formatações comuns

### 3. Services

```php
<?php

namespace App\Services;

use App\Models\User;
use App\Models\Expense;
use Carbon\Carbon;

class CashFlowService
{
    /**
     * Calcular fluxo de caixa mensal
     *
     * @param User $user
     * @param int $months
     * @return array
     */
    public function calculateMonthlyFlow(User $user, int $months = 6): array
    {
        $data = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);

            $incomes = $user->incomes()
                ->whereYear('received_date', $date->year)
                ->whereMonth('received_date', $date->month)
                ->sum('amount');

            $expenses = $user->expenses()
                ->whereYear('due_date', $date->year)
                ->whereMonth('due_date', $date->month)
                ->sum('amount');

            $data[] = [
                'month' => $date->format('M/Y'),
                'incomes' => (float) $incomes,
                'expenses' => (float) $expenses,
                'balance' => (float) ($incomes - $expenses),
            ];
        }

        return $data;
    }
}
```

**Regras:**

- ✅ Lógica de negócio complexa **sempre** em Services
- ✅ Injeção de dependência via constructor
- ✅ Docblocks completos com `@param` e `@return`
- ✅ Type hints rigorosos
- ✅ Retornos claros (arrays associativos documentados)

### 4. Form Requests

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExpenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // ou lógica de autorização específica
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'due_date' => ['required', 'date'],
            'status' => ['required', 'in:pending,paid,overdue'],
            'credit_card_id' => ['nullable', 'exists:credit_cards,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
        ];
    }
}
```

**Regras:**

- ✅ **SEMPRE** use Form Requests para validação
- ✅ Nunca use `$request->validate()` no controller
- ✅ Array syntax para rules (não string pipe)
- ✅ Validações específicas (`exists`, `unique`, etc.)
- ✅ Mensagens customizadas em português (quando necessário)

### 5. Observers

```php
<?php

namespace App\Observers;

use App\Models\Expense;
use App\Models\CreditCard;

class ExpenseObserver
{
    /**
     * Handle the Expense "created" event.
     */
    public function created(Expense $expense): void
    {
        $this->updateCreditCardLimit($expense);
    }

    /**
     * Handle the Expense "updated" event.
     */
    public function updated(Expense $expense): void
    {
        $this->updateCreditCardLimit($expense);
    }

    /**
     * Handle the Expense "deleted" event.
     */
    public function deleted(Expense $expense): void
    {
        $this->updateCreditCardLimit($expense);
    }

    /**
     * Atualizar limite do cartão de crédito
     */
    private function updateCreditCardLimit(Expense $expense): void
    {
        if (!$expense->credit_card_id) {
            return;
        }

        $creditCard = CreditCard::find($expense->credit_card_id);

        if (!$creditCard || !$creditCard->auto_calculate_limit) {
            return;
        }

        $totalUsed = $creditCard->expenses()
            ->where('status', 'pending')
            ->sum('amount');

        $creditCard->update([
            'available_limit' => $creditCard->card_limit - $totalUsed,
        ]);
    }
}
```

**Regras:**

- ✅ Use Observers para efeitos colaterais automáticos
- ✅ Métodos privados para lógica reutilizável
- ✅ Return early para simplificar lógica
- ✅ Sempre registre no `AppServiceProvider`

### 6. Jobs

```php
<?php

namespace App\Jobs;

use App\Models\Expense;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckOverdueExpenses implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $today = now()->toDateString();

        $updated = Expense::where('status', 'pending')
            ->where('due_date', '<', $today)
            ->update(['status' => 'overdue']);

        Log::info('CheckOverdueExpenses executed', [
            'updated' => $updated,
            'date' => $today,
        ]);
    }
}
```

**Regras:**

- ✅ Implements `ShouldQueue`
- ✅ Todos os traits necessários
- ✅ Logs informativos
- ✅ Tratamento de erros quando necessário

---

## 🧪 TESTES - REGRA INQUEBRÁVEL

### ⚠️ ATENÇÃO: ISTO É OBRIGATÓRIO

**Toda vez que você implementar/alterar qualquer código, você DEVE entregar:**

1. ✅ Código da implementação
2. ✅ Testes completos (Unit + Feature)
3. ✅ Comando exato para rodar os testes

### Estrutura de Testes

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Expense;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ExpenseTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // IMPORTANTE: Limpar cache de permissões
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)
            ->forgetCachedPermissions();

        // Criar usuário autenticado
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    #[Test]
    public function it_can_list_expenses_for_authenticated_user()
    {
        // Arrange
        Expense::factory()->count(3)->create(['user_id' => $this->user->id]);
        Expense::factory()->count(2)->create(); // Outro usuário

        // Act
        $response = $this->get(route('expenses.index'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('expenses');
        $expenses = $response->viewData('expenses');
        $this->assertCount(3, $expenses);
    }

    #[Test]
    public function it_can_create_expense_with_valid_data()
    {
        // Arrange
        $data = [
            'description' => 'Teste Despesa',
            'amount' => 100.50,
            'due_date' => now()->addDays(5)->format('Y-m-d'),
            'status' => 'pending',
        ];

        // Act
        $response = $this->post(route('expenses.store'), $data);

        // Assert
        $response->assertRedirect(route('expenses.index'));
        $this->assertDatabaseHas('expenses', [
            'description' => 'Teste Despesa',
            'user_id' => $this->user->id,
        ]);
    }

    #[Test]
    public function it_cannot_create_expense_with_invalid_amount()
    {
        // Arrange
        $data = [
            'description' => 'Teste',
            'amount' => -50, // Valor inválido
            'due_date' => now()->format('Y-m-d'),
        ];

        // Act
        $response = $this->post(route('expenses.store'), $data);

        // Assert
        $response->assertSessionHasErrors('amount');
        $this->assertDatabaseCount('expenses', 0);
    }
}
```

### Checklist de Testes Obrigatórios

Para **cada feature/alteração**, você DEVE criar testes para:

- ✅ **Cenário Feliz** - Tudo funciona como esperado
- ✅ **Casos de Borda** - Valores limites, null, vazios
- ✅ **Validações** - Dados inválidos retornam erro
- ✅ **Permissões** - Spatie Permission (use `forgetCachedPermissions()` no setUp)
- ✅ **Isolamento de Usuários** - Um usuário não vê dados de outro
- ✅ **Relacionamentos** - FKs, cascades, etc.

### Comandos de Teste

**Sempre inclua no final da resposta:**

```bash
# Rodar teste específico
php artisan test --filter=ExpenseTest

# Rodar testes de uma pasta
php artisan test tests/Feature/

# Rodar todos os testes (quando possível)
php artisan test
```

---

## 🔒 Segurança e Permissões

### Spatie Permission

**Sempre valide permissões:**

```php
// No Controller (via middleware de rota)
Route::get('/expenses', [ExpenseController::class, 'index'])
    ->middleware('permission:index-expenses');

// No Controller (manualmente)
if (!auth()->user()->can('edit-expense')) {
    abort(403);
}

// Na View Blade
@can('create-expense')
    <a href="{{ route('expenses.create') }}">Criar Despesa</a>
@endcan
```

**Nomenclatura de Permissões:**

- `index-{recurso}` - Listar
- `create-{recurso}` - Criar
- `edit-{recurso}` - Editar
- `delete-{recurso}` - Excluir
- `show-{recurso}` - Visualizar

### Isolamento por Usuário

**SEMPRE filtre por usuário autenticado:**

```php
// ✅ CORRETO
$expenses = Auth::user()->expenses()->get();

// ❌ ERRADO - Vaza dados de outros usuários
$expenses = Expense::all();
```

---

## 🎨 Frontend (Blade + Tailwind + Alpine.js)

### Padrões de Views

```blade
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex space-x-2 text-sm text-gray-600 dark:text-gray-400">
            <li><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
            <li>/</li>
            <li class="font-semibold">Despesas</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
            💰 Despesas
        </h1>

        @can('create-expense')
            <a href="{{ route('expenses.create') }}"
               class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                Criar Despesa
            </a>
        @endcan
    </div>

    <!-- Conteúdo -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <!-- ... -->
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/money-mask.js') }}"></script>
@endpush
```

**Regras Frontend:**

- ✅ Sempre use tema claro/escuro (`dark:` variants)
- ✅ Container responsivo (`container mx-auto px-4`)
- ✅ Emojis nos títulos para melhor UX
- ✅ Breadcrumbs para navegação
- ✅ SweetAlert2 para confirmações de exclusão
- ✅ JavaScript modular (arquivos separados em `public/js/`)
- ✅ Alpine.js para interatividade simples

---

## 📝 Convenções Gerais

### Nomenclatura

**Arquivos:**

- Controllers: `NomeController.php`
- Models: `Nome.php` (singular)
- Services: `NomeService.php`
- Requests: `NomeRequest.php`
- Observers: `NomeObserver.php`
- Jobs: `NomeJob.php`
- Migrations: `YYYY_MM_DD_HHMMSS_descricao.php`

**Database:**

- Tabelas: plural, snake_case (`expenses`, `credit_cards`)
- Colunas: snake_case (`user_id`, `due_date`)
- Foreign Keys: `{tabela_singular}_id` (`user_id`, `expense_id`)

**Routes:**

- Padrão RESTful: `expenses.index`, `expenses.create`, `expenses.store`, etc.
- Grupos por funcionalidade: `Route::prefix('finance')->group()`

### Formatação

- ✅ PSR-12 (use `./vendor/bin/pint` para formatar)
- ✅ Docblocks completos
- ✅ Espaçamento consistente
- ✅ Imports organizados

### Git Commits

```
feat: adiciona CRUD de empréstimos
fix: corrige cálculo de juros compostos
test: adiciona testes para LoanService
refactor: move lógica de cálculo para LoanCalculatorService
docs: atualiza README com instruções de empréstimos
```

Prefixos: `feat`, `fix`, `test`, `refactor`, `docs`, `style`, `chore`

---

## 🚀 Fluxo de Trabalho

### Ao Receber uma Solicitação

1. **Ler contexto** - README, arquivos relacionados, testes existentes
2. **Planejar** - Identificar Models, Controllers, Services necessários
3. **Implementar** - Seguir padrões do projeto
4. **Testar** - Criar testes completos
5. **Documentar** - Atualizar README se necessário
6. **Entregar** - Código + testes + comando de teste

### Ao Identificar Problemas

- 🔍 **Seja proativo** - Sugira melhorias
- 💡 **Aponte riscos** - Segurança, performance, escalabilidade
- 📚 **Eduque** - Explique o "porquê" das decisões técnicas

---

## 📖 Recursos de Referência

- [Laravel 12 Docs](https://laravel.com/docs/12.x)
- [Spatie Permission](https://spatie.be/docs/laravel-permission)
- [Laravel Auditing](https://laravel-auditing.com/guide/)
- [Intervention Image v3](https://image.intervention.io/v3)
- [Tailwind CSS v4](https://tailwindcss.com/docs)
- [Alpine.js](https://alpinejs.dev/start-here)
- [Chart.js](https://www.chartjs.org/docs/latest/)

---

## ✨ Lembre-se

> **Você é o Arquiteto Oficial do FM System**. Seu código deve ser:
>
> - 🎯 **Funcional** e **testado**
> - 📚 **Bem documentado**
> - 🔒 **Seguro**
> - ⚡ **Performático**
> - 🎨 **Consistente** com o padrão do projeto

**NUNCA entregue código sem testes completos.** 🧪
