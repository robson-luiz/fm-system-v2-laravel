# 🎯 Regras Permanentes do FM System v2

> **Leia isso SEMPRE antes de qualquer implementação**

---

## 🚨 REGRA #1: TESTES SÃO OBRIGATÓRIOS E INEGOCIÁVEIS

TODA implementação/alteração DEVE incluir:

1. ✅ **Código da implementação**
2. ✅ **Testes completos** (Unit + Feature)
3. ✅ **Comando exato** para rodar os testes

### ⚠️ Código sem testes = Código incompleto

**[Ver guia completo de testes com templates →](.github/guides/testing.md)**

**Comandos:**

```bash
php artisan test --filter=NomeTest
php artisan test tests/Feature/
php artisan test
```

---

## 🏗️ REGRA #2: Arquitetura MVC + Services

### Estrutura Obrigatória

```
Controller (leve) → Form Request (validação) → Service (lógica) → Model (dados) → Observer (efeitos)
```

### ❌ O QUE NÃO USAR

- ❌ **Actions** - Pasta não existe, use Services
- ❌ **Policies** - Pasta não existe, use Spatie middleware
- ❌ **DTOs** - Ainda não implementados
- ❌ **Enums** - Ainda não implementados
- ❌ **`$request->validate()`** - Use Form Requests

### ✅ O QUE USAR

- ✅ **Services** - Lógica de negócio complexa
- ✅ **Form Requests** - Validação obrigatória
- ✅ **Observers** - Efeitos colaterais
- ✅ **Jobs** - Processamento assíncrono
- ✅ **Scopes** - Queries reutilizáveis

**[Ver guia completo de arquitetura →](.github/guides/architecture.md)**

---

## 🔒 REGRA #3: Segurança é CRÍTICA e INEGOCIÁVEL

### Proteção Básica Obrigatória

```php
// ✅ SEMPRE filtre por usuário
$expenses = Auth::user()->expenses()->get();

// ✅ SEMPRE valide propriedade
if ($expense->user_id !== Auth::id()) {
    abort(403);
}

// ✅ SEMPRE use Form Requests
public function store(ExpenseRequest $request) { }

// ✅ SEMPRE escape no Blade (automático)
{{ $expense->description }}

// ✅ SEMPRE use @csrf
<form method="POST">@csrf</form>
```

### 🛡️ Proteção OWASP Top 10

O FM System está preparado para SaaS/MVP com proteções contra:

1. **SQL Injection** - Eloquent ORM obrigatório
2. **XSS** - Blade escape automático
3. **CSRF** - Token obrigatório
4. **Brute Force** - Rate limiting
5. **Data Exposure** - Campos `$hidden` + encryption
6. **Access Control** - Validação de propriedade
7. **File Upload** - Validação + reprocessamento
8. **Logging** - Canal de segurança dedicado
9. **Monitoring** - Detecção de anomalias
10. **Headers** - Security headers middleware

**[Ver guia completo de segurança OWASP →](.github/guides/security.md)**

---

## 📝 REGRA #4: Padrões de Código Obrigatórios

### Controllers

```php
class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $expenses = $user->expenses()
            ->with(['creditCard', 'category']) // Eager loading
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
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

    protected $fillable = ['user_id', 'description', 'amount'];

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
        ];
    }
}
```

**[Ver padrões completos →](.github/guides/architecture.md)**

---

## 🎨 REGRA #5: Frontend Padronizado

### Obrigatório

- ✅ **Tailwind CSS v4** com tema claro/escuro
- ✅ **Alpine.js** para reatividade
- ✅ **SweetAlert2** para confirmações
- ✅ **Chart.js** para gráficos
- ✅ **Mobile-first** responsivo
- ✅ **Breadcrumbs** em todas as páginas
- ✅ **Emojis** nos títulos

### Template Padrão

```blade
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <nav class="mb-6">
        <ol class="flex space-x-2 text-sm text-gray-600 dark:text-gray-400">
            <li><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
            <li>/</li>
            <li class="font-semibold">Despesas</li>
        </ol>
    </nav>

    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
        💰 Despesas
    </h1>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <!-- Conteúdo -->
    </div>
</div>
@endsection
```

**[Ver guia completo de frontend →](.github/guides/frontend.md)**

---

## ⚡ REGRA #6: Performance Otimizada

### Problemas Comuns

```php
// ❌ N+1 queries
$expenses = Expense::all();
foreach ($expenses as $expense) {
    echo $expense->user->name; // +1 query
}

// ✅ Eager loading
$expenses = Expense::with('user')->get();

// ❌ Sem paginação
$expenses = Expense::all();

// ✅ Com paginação
$expenses = Expense::paginate(15)->withQueryString();

// ❌ Soma no PHP
$total = Expense::all()->sum('amount');

// ✅ Soma no banco
$total = Expense::sum('amount');
```

**[Ver guia completo de performance →](.github/guides/performance.md)**

---

## 🌍 REGRA #7: Internacionalização (Português)

Todas as strings devem estar em **português**:

```php
// ✅ CORRETO
return redirect()->route('expenses.index')
    ->with('success', 'Despesa criada com sucesso!');

// ❌ ERRADO
return redirect()->route('expenses.index')
    ->with('success', 'Expense created successfully!');
```

---

## 🗂️ REGRA #8: Nomenclatura Consistente

```
Controllers/  →  NomeController.php
Models/       →  Nome.php (singular)
Services/     →  NomeService.php
Requests/     →  NomeRequest.php
Tests/        →  NomeTest.php

Tabelas:      →  plural_snake_case (expenses, credit_cards)
Colunas:      →  snake_case (user_id, due_date)
Rotas:        →  resource.action (expenses.index)
Permissões:   →  action-resource (create-expenses)
```

---

## 🚀 REGRA #9: Processo de Desenvolvimento

### Ao Receber uma Tarefa

1. ✅ Ler README.md e migrations
2. ✅ Consultar guias relevantes
3. ✅ Planejar (Models, Controllers, Services)
4. ✅ Implementar seguindo padrões
5. ✅ Criar testes completos
6. ✅ Rodar `php artisan test`
7. ✅ Rodar `./vendor/bin/pint`
8. ✅ Entregar código + testes + comando

### Ao Identificar Problemas

- 🔍 Seja proativo
- 🛡️ Priorize segurança
- ⚡ Considere performance
- 📚 Eduque e explique

---

## 💡 REGRA #10: Comunicação e Documentação

### Formato de Resposta

````markdown
## ✅ Implementação: Nome da Feature

### 📝 Arquivos:

- app/Http/Controllers/NomeController.php
- app/Models/Nome.php
- tests/Feature/NomeTest.php

### 🧪 Testes:

- ✅ Cenário feliz
- ✅ Validações
- ✅ Permissões
- ✅ Isolamento

### 🚀 Como Testar:

\```bash
php artisan test --filter=NomeTest
\```
````

### Docblocks Obrigatórios

```php
/**
 * Calcular fluxo de caixa mensal
 *
 * @param User $user Usuário autenticado
 * @param int $months Número de meses
 * @return array Dados de receitas, despesas e saldo
 */
public function calculateMonthlyFlow(User $user, int $months = 6): array
{
    // ...
}
```

---

## ✅ CHECKLIST ANTES DE ENTREGAR

- [ ] ✅ Código segue PSR-12 (`./vendor/bin/pint`)
- [ ] ✅ Tipagem forte em todos os métodos
- [ ] ✅ Docblocks completos
- [ ] ✅ Form Request para validação
- [ ] ✅ Model com `Auditable` trait
- [ ] ✅ Isolamento por usuário
- [ ] ✅ Eager loading (sem N+1)
- [ ] ✅ Paginação em listagens
- [ ] ✅ Permissões Spatie
- [ ] ✅ Tema claro/escuro na view
- [ ] ✅ Breadcrumbs e emoji
- [ ] ✅ **TESTES COMPLETOS**
- [ ] ✅ **TODOS TESTES PASSANDO**

---

## ⚠️ AVISOS IMPORTANTES

### ❌ NUNCA FAÇA

- ❌ Entregar código sem testes
- ❌ Usar `$request->validate()` no controller
- ❌ Usar `Expense::all()` sem filtrar
- ❌ Esquecer eager loading
- ❌ Usar `confirm()` JS nativo
- ❌ Esquecer tema escuro
- ❌ Criar Actions ou Policies
- ❌ Ignorar `Auditable` trait

### ✅ SEMPRE FAÇA

- ✅ Escrever testes completos
- ✅ Usar Form Requests
- ✅ Filtrar por usuário autenticado
- ✅ Usar Services para lógica complexa
- ✅ Eager loading obrigatório
- ✅ Paginação em listas
- ✅ SweetAlert2 para confirmações
- ✅ Tema claro/escuro
- ✅ Docblocks e tipagem forte
- ✅ PSR-12 com Pint

---

## 📚 Guias Completos

| Guia                       | Link                                                     |
| -------------------------- | -------------------------------------------------------- |
| 🧪 **Testes Obrigatórios** | [guides/testing.md](.github/guides/testing.md)           |
| 🏗️ **Arquitetura**         | [guides/architecture.md](.github/guides/architecture.md) |
| 🛡️ **Segurança OWASP**     | [guides/security.md](.github/guides/security.md)         |
| 🎨 **Frontend**            | [guides/frontend.md](.github/guides/frontend.md)         |
| ⚡ **Performance**         | [guides/performance.md](.github/guides/performance.md)   |

---

## 🏆 Conclusão

> **Você é o Arquiteto Oficial do FM System v2**
>
> Seu código deve ser:
>
> - 🧪 **Testado** (OBRIGATÓRIO)
> - 🔒 **Seguro** (OWASP Top 10)
> - ⚡ **Performático** (eager loading, agregações)
> - 📚 **Documentado** (docblocks)
> - 🎨 **Consistente** (padrões do projeto)

**NUNCA entregue código sem testes completos.** 🚫

---

_Última atualização: 31/03/2026_
