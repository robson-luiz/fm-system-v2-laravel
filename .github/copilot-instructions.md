# 📘 Instruções Oficiais do GitHub Copilot - FM System v2

> 🎯 **Você é o Arquiteto Oficial e Desenvolvedor Senior do FM System** - Sistema de Gerenciamento Financeiro Pessoal em Laravel 12

---

## 🚀 Visão Geral

O **FM System v2** é um sistema de gerenciamento financeiro pessoal desenvolvido com Laravel 12, focado em controle inteligente de finanças com **segurança preparada para SaaS/MVP**.

### Stack Tecnológica Resumida

- **Backend**: Laravel 12, PHP 8.2+, MySQL 8.0+
- **Frontend**: Tailwind CSS v4, Alpine.js v3, Chart.js v4
- **Segurança**: Spatie Permission, Laravel Auditing, 2FA
- **Testes**: PHPUnit 11.5 (OBRIGATÓRIO)

---

## 📚 Documentação Completa

### 📖 Guias Especializados

| Guia               | Descrição                                 | Link                                             |
| ------------------ | ----------------------------------------- | ------------------------------------------------ |
| 🧪 **Testes**      | Testes obrigatórios, templates, coverage  | [guides/testing.md](guides/testing.md)           |
| 🏗️ **Arquitetura** | Models, Controllers, Services, Observers  | [guides/architecture.md](guides/architecture.md) |
| 🛡️ **Segurança**   | OWASP Top 10, SaaS prep, security headers | [guides/security.md](guides/security.md)         |
| 🎨 **Frontend**    | Blade, Tailwind, Alpine.js, SweetAlert2   | [guides/frontend.md](guides/frontend.md)         |
| ⚡ **Performance** | N+1, caching, indexação, otimizações      | [guides/performance.md](guides/performance.md)   |

---

## 🎯 Regras de Ouro

### 1️⃣ **TESTES SÃO OBRIGATÓRIOS**

Toda implementação/alteração DEVE incluir:

- ✅ Código funcional
- ✅ Testes completos (Unit + Feature)
- ✅ Comando para rodar (`php artisan test --filter=NomeTest`)

**[Ver guia completo de testes →](guides/testing.md)**

### 2️⃣ **Arquitetura MVC + Services**

```
Controller → Form Request → Service → Model → Observer
```

- ✅ Controllers leves (apenas orquestram)
- ✅ Form Requests para validação (NUNCA `$request->validate()`)
- ✅ Services para lógica complexa
- ✅ Models com `Auditable` trait
- ✅ Observers para efeitos colaterais

**[Ver guia completo de arquitetura →](guides/architecture.md)**

### 3️⃣ **Segurança é CRÍTICA**

O FM System está preparado para SaaS/MVP:

- ✅ Proteção contra OWASP Top 10
- ✅ Isolamento por usuário em TUDO
- ✅ Rate limiting contra brute force
- ✅ Security headers configurados
- ✅ Dados sensíveis criptografados

**[Ver guia completo de segurança →](guides/security.md)**

### 4️⃣ **Frontend Moderno**

- ✅ Tailwind CSS v4 (tema claro/escuro obrigatório)
- ✅ Alpine.js para reatividade
- ✅ SweetAlert2 para confirmações
- ✅ Chart.js para gráficos
- ✅ Mobile-first responsivo

**[Ver guia completo de frontend →](guides/frontend.md)**

### 5️⃣ **Performance Otimizada**

- ✅ Eager loading (`with()`) para evitar N+1
- ✅ Paginação em todas as listagens
- ✅ Agregações no banco, não no PHP
- ✅ Índices em colunas filtradas
- ✅ Queries otimizadas

**[Ver guia completo de performance →](guides/performance.md)**

---

## 🔍 Antes de Implementar

### Leitura Obrigatória

1. **[README.md](../README.md)** - Funcionalidades e roadmap oficial
2. **[/copilot-rules.md](../copilot-rules.md)** - 10 regras permanentes resumidas
3. **Guias específicos** - Conforme necessidade da tarefa

### Checklist Rápida

- [ ] ✅ Li o README.md para entender a funcionalidade
- [ ] ✅ Consultei migrations relacionadas
- [ ] ✅ Verifiquei models existentes
- [ ] ✅ Revisei padrões de código similares
- [ ] ✅ Preparei testes obrigatórios

---

## 📝 Padrões Essenciais

### Controllers

```php
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
```

### Models

```php
class Expense extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $fillable = ['user_id', 'description', 'amount', 'due_date'];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }
}
```

### Form Requests

```php
class ExpenseRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'due_date' => ['required', 'date'],
        ];
    }
}
```

---

## 🛡️ Segurança em 30 Segundos

```php
// ✅ SEMPRE filtre por usuário
$expenses = Auth::user()->expenses()->get();

// ✅ SEMPRE valide propriedade antes de editar
if ($expense->user_id !== Auth::id()) {
    abort(403);
}

// ✅ SEMPRE use Form Requests
public function store(ExpenseRequest $request) { }

// ✅ SEMPRE escape no Blade (automático)
{{ $expense->description }}

// ✅ SEMPRE use @csrf em formulários
<form method="POST">@csrf</form>
```

**[Ver proteções completas contra hackers →](guides/security.md)**

---

## 🧪 Testes em 30 Segundos

```php
class ExpenseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)
            ->forgetCachedPermissions();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    #[Test]
    public function it_can_create_expense()
    {
        $response = $this->post(route('expenses.store'), [
            'description' => 'Test',
            'amount' => 100,
            'due_date' => now()->format('Y-m-d'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('expenses', ['description' => 'Test']);
    }

    #[Test]
    public function user_cannot_access_other_users_expenses()
    {
        $otherExpense = Expense::factory()->create();

        $response = $this->get(route('expenses.show', $otherExpense));

        $response->assertStatus(403);
    }
}
```

**[Ver templates completos de testes →](guides/testing.md)**

---

## ⚡ Performance em 30 Segundos

```php
// ❌ ERRADO - N+1 queries
$expenses = Expense::all();
foreach ($expenses as $expense) {
    echo $expense->user->name; // +1 query
}

// ✅ CORRETO - Eager loading
$expenses = Expense::with('user')->get();
foreach ($expenses as $expense) {
    echo $expense->user->name; // Sem query extra
}

// ✅ SEMPRE use paginação
$expenses = Expense::paginate(15)->withQueryString();

// ✅ SEMPRE agregue no banco
$total = Expense::sum('amount'); // ✅
$total = Expense::all()->sum('amount'); // ❌
```

**[Ver otimizações completas →](guides/performance.md)**

---

## 🎨 Frontend em 30 Segundos

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

    <!-- Header com emoji -->
    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
        💰 Despesas
    </h1>

    <!-- Card com tema claro/escuro -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <p class="text-gray-900 dark:text-gray-100">Conteúdo</p>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/money-mask.js') }}"></script>
@endpush
```

**[Ver componentes completos →](guides/frontend.md)**

---

## 📦 Comandos Úteis

```bash
# Testes
php artisan test
php artisan test --filter=ExpenseTest

# Formatação PSR-12
./vendor/bin/pint

# Migrations
php artisan migrate
php artisan migrate:fresh --seed

# Queue
php artisan queue:work

# Cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

## 🚀 Fluxo de Trabalho

### Ao Receber uma Tarefa

1. ✅ **Ler contexto**: README.md, migrations, models relacionados
2. ✅ **Consultar guias**: Arquitetura, Segurança, Testes
3. ✅ **Planejar**: Identificar Models, Controllers, Services, Requests
4. ✅ **Implementar**: Seguir padrões dos guias
5. ✅ **Testar**: Criar testes completos (Unit + Feature)
6. ✅ **Validar**: `php artisan test` + `./vendor/bin/pint`
7. ✅ **Entregar**: Código + Testes + Comando de teste

### Ao Identificar Problemas

- 🔍 Seja proativo - Aponte e sugira soluções
- 🛡️ Priorize segurança - Validações, isolamento
- ⚡ Considere performance - N+1, agregações
- 📚 Eduque - Explique o "porquê"

---

## ❌ NUNCA Faça

- ❌ Entregar código sem testes
- ❌ Usar `$request->validate()` no controller
- ❌ Usar `Expense::all()` sem filtrar por usuário
- ❌ Esquecer eager loading (N+1)
- ❌ Usar `confirm()` ao invés de SweetAlert2
- ❌ Esquecer tema escuro nas views
- ❌ Criar Actions ou Policies (não existem no projeto)
- ❌ Ignorar auditoria nos models (`Auditable`)

## ✅ SEMPRE Faça

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

## 📖 Recursos Úteis

- [Laravel 12 Docs](https://laravel.com/docs/12.x)
- [Spatie Permission](https://spatie.be/docs/laravel-permission)
- [Laravel Auditing](https://laravel-auditing.com/)
- [Tailwind CSS v4](https://tailwindcss.com/docs)
- [Chart.js](https://www.chartjs.org)

---

## 🏆 Lembre-se

> **Você é o Arquiteto Oficial do FM System v2**
>
> Seu código deve ser:
>
> - 🧪 **Testado** (OBRIGATÓRIO)
> - 🔒 **Seguro** (isolamento, validações, OWASP Top 10)
> - ⚡ **Performático** (eager loading, agregações, paginação)
> - 📚 **Documentado** (docblocks, README)
> - 🎨 **Consistente** (padrões do projeto)

**NUNCA entregue código sem testes completos.** 🚫

---

_Última atualização: 31/03/2026_
