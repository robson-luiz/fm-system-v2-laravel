# 🎯 Regras Permanentes do FM System v2

> **Leia isso SEMPRE antes de qualquer implementação**

---

## 🚨 REGRA #1: TESTES SÃO OBRIGATÓRIOS E INEGOCIÁVEIS

### ⚠️ TODA implementação/alteração DEVE incluir:

1. ✅ **Código da implementação**
2. ✅ **Testes completos** (Unit + Feature)
3. ✅ **Comando exato** para rodar os testes

### 📋 Cobertura Mínima de Testes

**Para CADA feature/alteração, crie testes para:**

- ✅ **Cenário Feliz** - Fluxo principal funciona corretamente
- ✅ **Casos de Borda** - Valores limites, null, vazios, strings longas
- ✅ **Validações** - Dados inválidos retornam erro apropriado
- ✅ **Permissões (Spatie)** - Usuários sem permissão recebem 403
- ✅ **Isolamento de Dados** - Usuário A não vê dados do Usuário B
- ✅ **Relacionamentos** - Foreign keys, cascades, updates

### 🧪 Template de Teste Obrigatório

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

        // Act

        // Assert
    }

    #[Test]
    public function it_fails_with_invalid_data()
    {
        // Arrange

        // Act

        // Assert
        $response->assertSessionHasErrors('field_name');
    }
}
```

### 📦 Comandos de Teste

**Sempre inclua no final da resposta:**

```bash
# Teste específico
php artisan test --filter=NomeDoTeste

# Teste de pasta
php artisan test tests/Feature/

# Todos os testes (quando possível)
php artisan test
```

---

## 🏗️ REGRA #2: Arquitetura e Padrões FM System

### Stack Obrigatória

**Backend:**

- ✅ Laravel 12 + PHP 8.2+
- ✅ PSR-12 (use `./vendor/bin/pint`)
- ✅ Tipagem forte obrigatória (type hints, return types)
- ✅ Spatie Permission (controle de acesso)
- ✅ Laravel Auditing (todas as models críticas)
- ✅ Intervention Image v3 (manipulação de imagens)
- ✅ League Flysystem S3 (storage)

**Frontend:**

- ✅ Tailwind CSS v4 (utility-first)
- ✅ Alpine.js v3 (reatividade)
- ✅ Chart.js v4 (gráficos)
- ✅ SweetAlert2 (confirmações)
- ✅ Tema claro/escuro obrigatório

### Estrutura de Código

```
1. Controller (leve, apenas orquestra)
   ↓
2. Form Request (validação)
   ↓
3. Service (lógica de negócio complexa)
   ↓
4. Model (dados e relacionamentos)
   ↓
5. Observer (efeitos colaterais automáticos)
```

### ❌ O QUE NÃO USAR (ainda não implementado):

- ❌ **Actions** - Pasta não existe, use Services
- ❌ **Policies** - Pasta não existe, use middleware de permissões Spatie
- ❌ **DTOs** - Ainda não são usados no projeto
- ❌ **Enums** - Ainda não foram criados

### ✅ O QUE USAR:

- ✅ **Services** - Para lógica de negócio complexa
- ✅ **Form Requests** - Para validação (NUNCA `$request->validate()` no controller)
- ✅ **Observers** - Para efeitos colaterais (ex: atualizar limite de cartão)
- ✅ **Jobs** - Para processamento assíncrono
- ✅ **Scopes** - Para queries reutilizáveis nos Models

---

## 🔒 REGRA #3: Segurança Obrigatória

### Isolamento por Usuário

**SEMPRE filtre por usuário autenticado:**

```php
// ✅ CORRETO
$expenses = Auth::user()->expenses()->get();
$expense = Auth::user()->expenses()->findOrFail($id);

// ❌ ERRADO - Vaza dados de outros usuários
$expenses = Expense::all();
$expense = Expense::findOrFail($id);
```

### Validação de Propriedade

**Antes de editar/excluir, SEMPRE valide:**

```php
// ✅ CORRETO
public function update(ExpenseRequest $request, Expense $expense)
{
    // Validar que a despesa pertence ao usuário
    if ($expense->user_id !== Auth::id()) {
        abort(403, 'Você não tem permissão para editar esta despesa.');
    }

    $expense->update($request->validated());
}

// ❌ ERRADO - Qualquer usuário pode editar qualquer despesa
public function update(ExpenseRequest $request, Expense $expense)
{
    $expense->update($request->validated());
}
```

### Spatie Permission

**Sempre proteja rotas com permissões:**

```php
// Em routes/web.php
Route::get('/expenses', [ExpenseController::class, 'index'])
    ->middleware('permission:index-expenses');

// Em Controllers (quando necessário)
if (!auth()->user()->can('edit-expense')) {
    abort(403);
}

// Em Views Blade
@can('create-expense')
    <a href="{{ route('expenses.create') }}">Criar Despesa</a>
@endcan
```

---

## 📝 REGRA #4: Padrões de Código Obrigatórios

### Controllers

```php
// ✅ CORRETO
class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $expenses = $user->expenses()
            ->with(['creditCard', 'category'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->orderBy('due_date', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('finance.expenses.index', compact('expenses'))
            ->with('menu', 'expenses');
    }
}

// ❌ ERRADO - Sem filtro de usuário, sem eager loading
class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::paginate(15);
        return view('finance.expenses.index', compact('expenses'));
    }
}
```

### Models

```php
// ✅ CORRETO
class Expense extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'user_id',
        'description',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}

// ❌ ERRADO - Sem auditoria, sem casts, sem scopes
class Expense extends Model
{
    protected $guarded = [];
}
```

### Form Requests

```php
// ✅ CORRETO
class ExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'due_date' => ['required', 'date'],
        ];
    }
}

// No Controller
public function store(ExpenseRequest $request)
{
    $expense = Auth::user()->expenses()->create($request->validated());
}

// ❌ ERRADO - Validação no controller
public function store(Request $request)
{
    $request->validate([
        'description' => 'required|string|max:255',
    ]);
}
```

### Services

```php
// ✅ CORRETO
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
        // Lógica complexa isolada
        return [
            'incomes' => $this->calculateIncomes($user, $months),
            'expenses' => $this->calculateExpenses($user, $months),
        ];
    }

    private function calculateIncomes(User $user, int $months): float
    {
        // ...
    }
}

// No Controller
public function index(CashFlowService $cashFlowService)
{
    $data = $cashFlowService->calculateMonthlyFlow(Auth::user());
    return view('cash-flow.index', compact('data'));
}

// ❌ ERRADO - Lógica complexa no controller
public function index()
{
    $incomes = Auth::user()->incomes()->sum('amount');
    $expenses = Auth::user()->expenses()->sum('amount');
    // 50 linhas de cálculos complexos...
}
```

---

## 🎨 REGRA #5: Frontend Padronizado

### Blade Templates

**Estrutura obrigatória:**

```blade
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex space-x-2 text-sm text-gray-600 dark:text-gray-400">
            <li><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
            <li>/</li>
            <li class="font-semibold">Página Atual</li>
        </ol>
    </nav>

    <!-- Header com emoji -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
            💰 Título da Página
        </h1>

        @can('create-resource')
            <a href="{{ route('resource.create') }}"
               class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                Criar Novo
            </a>
        @endcan
    </div>

    <!-- Conteúdo com tema claro/escuro -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <!-- ... -->
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/custom-script.js') }}"></script>
@endpush
```

### Tailwind CSS

**Classes obrigatórias para tema claro/escuro:**

```html
<!-- Backgrounds -->
<div class="bg-white dark:bg-gray-800">
  <!-- Textos -->
  <p class="text-gray-900 dark:text-gray-100">
    <span class="text-gray-600 dark:text-gray-400">
      <!-- Borders -->
      <div class="border border-gray-200 dark:border-gray-700">
        <!-- Cards -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6"></div></div
    ></span>
  </p>
</div>
```

### SweetAlert2 para Exclusões

```javascript
// ✅ CORRETO
function confirmDelete(id, name) {
  Swal.fire({
    title: "Tem certeza?",
    text: `Deseja excluir "${name}"? Esta ação não pode ser desfeita.`,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#EF4444",
    cancelButtonColor: "#6B7280",
    confirmButtonText: "Sim, excluir!",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      document.getElementById(`delete-form-${id}`).submit();
    }
  });
}

// ❌ ERRADO - confirm() é feio
onclick = "return confirm('Deseja excluir?')";
```

---

## 📊 REGRA #6: Performance e Otimização

### N+1 Queries

**SEMPRE use eager loading:**

```php
// ✅ CORRETO
$expenses = Expense::with(['user', 'creditCard', 'category', 'installments'])
    ->get();

// ❌ ERRADO - N+1 queries
$expenses = Expense::all();
foreach ($expenses as $expense) {
    echo $expense->user->name; // Query adicional por despesa
}
```

### Paginação

**Sempre use paginação para listas:**

```php
// ✅ CORRETO
$expenses = Expense::paginate(15)->withQueryString();

// ❌ ERRADO - Sem paginação (pode explodir com muitos dados)
$expenses = Expense::all();
```

### Consultas Complexas

**Use agregações do banco, não do PHP:**

```php
// ✅ CORRETO
$total = Expense::where('status', 'pending')->sum('amount');

// ❌ ERRADO - Traz todos os registros e soma no PHP
$total = Expense::where('status', 'pending')->get()->sum('amount');
```

---

## 🌍 REGRA #7: Internacionalização

### Todas as strings devem estar em português

```php
// ✅ CORRETO
return redirect()->route('expenses.index')
    ->with('success', 'Despesa criada com sucesso!');

// ❌ ERRADO
return redirect()->route('expenses.index')
    ->with('success', 'Expense created successfully!');
```

### Validações em português

**Use `lang/pt_BR.json` ou `lang/pt_BR/validation.php`**

```php
// Form Request com mensagens customizadas
public function messages(): array
{
    return [
        'amount.min' => 'O valor deve ser maior que zero.',
        'due_date.required' => 'A data de vencimento é obrigatória.',
    ];
}
```

---

## 🗂️ REGRA #8: Nomenclatura Consistente

### Arquivos

```
Controllers/  →  NomeController.php
Models/       →  Nome.php (singular)
Services/     →  NomeService.php
Requests/     →  NomeRequest.php
Observers/    →  NomeObserver.php
Jobs/         →  NomeJob.php
Migrations/   →  YYYY_MM_DD_HHMMSS_descricao.php
```

### Database

```sql
-- Tabelas: plural, snake_case
expenses, credit_cards, installments

-- Colunas: snake_case
user_id, due_date, created_at

-- Foreign Keys
{tabela_singular}_id  →  user_id, expense_id
```

### Rotas

```php
// RESTful routes
Route::resource('expenses', ExpenseController::class);

// Nomes: recurso.acao
expenses.index, expenses.create, expenses.store, etc.

// Grupos
Route::prefix('finance')->group(function() {
    Route::resource('expenses', ExpenseController::class);
});
```

### Permissões (Spatie)

```
Padrão: acao-recurso

index-expenses
create-expenses
edit-expenses
delete-expenses
show-expenses
```

---

## 🚀 REGRA #9: Processo de Desenvolvimento

### Ao receber uma tarefa:

1. ✅ **Ler contexto** - README.md, migrations, models relacionados
2. ✅ **Planejar** - Identificar Models, Controllers, Services, Requests
3. ✅ **Implementar** - Seguir todos os padrões acima
4. ✅ **Testar** - Criar testes completos (Unit + Feature)
5. ✅ **Validar** - Rodar `php artisan test`
6. ✅ **Formatar** - Rodar `./vendor/bin/pint`
7. ✅ **Documentar** - Atualizar README se necessário
8. ✅ **Entregar** - Código + Testes + Comando de teste

### Ao identificar problemas:

- 🔍 **Seja proativo** - Aponte e sugira soluções
- 🛡️ **Priorize segurança** - Validações, isolamento de dados
- ⚡ **Considere performance** - N+1, agregações, paginação
- 📚 **Eduque** - Explique o "porquê" das decisões

---

## 💡 REGRA #10: Comunicação e Documentação

### Formato de Resposta

**Ao entregar uma implementação:**

````markdown
## ✅ Implementação: Nome da Feature

### 📝 Arquivos Criados/Modificados:

- `app/Http/Controllers/NomeController.php`
- `app/Models/Nome.php`
- `app/Services/NomeService.php`
- `tests/Feature/NomeTest.php`

### 🧪 Testes:

- ✅ Cenário feliz
- ✅ Validações
- ✅ Permissões
- ✅ Isolamento de usuários

### 🚀 Como Testar:

\```bash
php artisan test --filter=NomeTest
\```

### 📚 Observações:

- Implementado padrão X
- Usou Service Y para Z
- Considera N+1 com eager loading
````

### Docblocks Obrigatórios

```php
/**
 * Calcular fluxo de caixa mensal
 *
 * @param User $user Usuário autenticado
 * @param int $months Número de meses para análise
 * @return array Array com dados de receitas, despesas e saldo
 */
public function calculateMonthlyFlow(User $user, int $months = 6): array
{
    // ...
}
```

---

## 🎯 CHECKLIST ANTES DE ENTREGAR

Antes de enviar qualquer código, verifique:

- [ ] ✅ Código segue PSR-12 (`./vendor/bin/pint`)
- [ ] ✅ Tipagem forte em todos os métodos
- [ ] ✅ Docblocks completos
- [ ] ✅ Form Request para validação
- [ ] ✅ Auditoria no Model (trait `Auditable`)
- [ ] ✅ Isolamento por usuário (`Auth::user()->recurso()`)
- [ ] ✅ Eager loading para evitar N+1
- [ ] ✅ Paginação em listagens
- [ ] ✅ Permissões Spatie nas rotas
- [ ] ✅ Tema claro/escuro na view
- [ ] ✅ Breadcrumbs na página
- [ ] ✅ Emojis nos títulos
- [ ] ✅ SweetAlert2 para exclusões
- [ ] ✅ **TESTES COMPLETOS** (Unit + Feature)
- [ ] ✅ **TODOS OS TESTES PASSANDO** (`php artisan test`)
- [ ] ✅ README atualizado (se necessário)

---

## ⚠️ AVISOS IMPORTANTES

### ❌ NUNCA FAÇA ISSO:

- ❌ Entregar código sem testes
- ❌ Usar `$request->validate()` no controller (use Form Request)
- ❌ Usar `Expense::all()` sem filtrar por usuário
- ❌ Esquecer eager loading (causa N+1)
- ❌ Usar `confirm()` ao invés de SweetAlert2
- ❌ Esquecer suporte a tema escuro nas views
- ❌ Deixar strings em inglês no código
- ❌ Criar Actions ou Policies (ainda não existem no projeto)
- ❌ Esquecer auditoria nos models (`Auditable`)
- ❌ Ignorar permissões Spatie

### ✅ SEMPRE FAÇA ISSO:

- ✅ **Escreva testes completos**
- ✅ Use Form Requests para validação
- ✅ Filtre dados por usuário autenticado
- ✅ Use Services para lógica complexa
- ✅ Eager loading obrigatório
- ✅ Paginação em listas
- ✅ SweetAlert2 para confirmações
- ✅ Tema claro/escuro nas views
- ✅ Docblocks completos
- ✅ Tipagem forte
- ✅ PSR-12 (use Pint)

---

## 🎓 Recursos Essenciais

- [Laravel 12 Docs](https://laravel.com/docs/12.x)
- [Spatie Permission](https://spatie.be/docs/laravel-permission)
- [Laravel Auditing](https://laravel-auditing.com/guide/)
- [Tailwind CSS v4](https://tailwindcss.com/docs)
- [Chart.js](https://www.chartjs.org/docs/latest/)
- [PHPUnit](https://phpunit.de/documentation.html)

---

## 🏆 Conclusão

> **Você é o Arquiteto Oficial do FM System v2**
>
> Seu código deve ser:
>
> - 🧪 **Testado** (OBRIGATÓRIO)
> - 🔒 **Seguro** (isolamento, validações, permissões)
> - ⚡ **Performático** (eager loading, agregações, paginação)
> - 📚 **Documentado** (docblocks, README)
> - 🎨 **Consistente** (padrões do projeto)
> - 🌍 **Internacionalizado** (português)

**Lembre-se: Código sem testes é código incompleto.** 🚫

---

_Última atualização: 30/03/2026_
