# 🏗️ Guia de Arquitetura - FM System v2

> **Arquitetura MVC com Services Pattern**

---

## 📚 Stack Tecnológica

**Backend:**
- Laravel 12 + PHP 8.2+
- MySQL 8.0+
- Spatie Laravel Permission v6.21
- OwenIt Laravel Auditing v14.0
- Intervention Image v3.11
- League Flysystem S3
- Guzzle HTTP Client

**Frontend:**
- Tailwind CSS v4
- Alpine.js v3
- Chart.js v4
- SweetAlert2

---

## 🔄 Estrutura de Código

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

---

## 📝 Padrões de Código

### Controllers

```php
namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExpenseRequest;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index(Request \$request)
    {
        /** @var \App\Models\User \$user */
        \$user = Auth::user();

        \$query = \$user->expenses()->with(['creditCard', 'category']);

        if (\$request->filled('status')) {
            \$query->where('status', \$request->status);
        }

        \$expenses = \$query->orderBy('due_date', 'desc')
                          ->paginate(15)
                          ->withQueryString();

        return view('finance.expenses.index', compact('expenses'))
            ->with('menu', 'expenses');
    }
}
```

**Regras:**
- ✅ Type hint `Auth::user()` com docblock
- ✅ Use Form Requests (nunca `\$request->validate()`)
- ✅ Eager loading com `with()`
- ✅ Paginação com `withQueryString()`
- ✅ Filtros com `\$request->filled()`

---

### Models

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Expense extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected \$fillable = [
        'user_id', 'description', 'amount', 'due_date', 'status',
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
        return \$this->belongsTo(User::class);
    }

    public function scopePending(\$query)
    {
        return \$query->where('status', 'pending');
    }
}
```

**Regras:**
- ✅ Sempre implemente `Auditable`
- ✅ `\$fillable` sempre definido
- ✅ Use `casts()` method
- ✅ Scopes para queries reutilizáveis

---

### Services

```php
namespace App\Services;

use App\Models\User;

class CashFlowService
{
    /**
     * Calcular fluxo de caixa mensal
     *
     * @param User \$user
     * @param int \$months
     * @return array
     */
    public function calculateMonthlyFlow(User \$user, int \$months = 6): array
    {
        // Lógica complexa isolada
        return [
            'incomes' => \$this->calculateIncomes(\$user, \$months),
            'expenses' => \$this->calculateExpenses(\$user, \$months),
        ];
    }

    private function calculateIncomes(User \$user, int \$months): float
    {
        // ...
    }
}
```

**Regras:**
- ✅ Lógica de negócio complexa em Services
- ✅ Injeção de dependência
- ✅ Docblocks completos
- ✅ Type hints rigorosos

---

### Form Requests

```php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'status' => ['required', 'in:pending,paid,overdue'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.min' => 'O valor deve ser maior que zero.',
        ];
    }
}
```

**Regras:**
- ✅ **SEMPRE** use Form Requests
- ✅ Array syntax para rules
- ✅ Mensagens em português

---

### Observers

```php
namespace App\Observers;

use App\Models\Expense;
use App\Models\CreditCard;

class ExpenseObserver
{
    public function created(Expense \$expense): void
    {
        \$this->updateCreditCardLimit(\$expense);
    }

    public function updated(Expense \$expense): void
    {
        \$this->updateCreditCardLimit(\$expense);
    }

    private function updateCreditCardLimit(Expense \$expense): void
    {
        if (!\$expense->credit_card_id) {
            return;
        }

        \$creditCard = CreditCard::find(\$expense->credit_card_id);

        if (!\$creditCard || !\$creditCard->auto_calculate_limit) {
            return;
        }

        \$totalUsed = \$creditCard->expenses()
            ->where('status', 'pending')
            ->sum('amount');

        \$creditCard->update([
            'available_limit' => \$creditCard->card_limit - \$totalUsed,
        ]);
    }
}
```

**Regras:**
- ✅ Use Observers para efeitos colaterais
- ✅ Métodos privados para lógica reutilizável
- ✅ Return early

---

### Jobs

```php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CheckOverdueExpenses implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        \$today = now()->toDateString();

        \$updated = Expense::where('status', 'pending')
            ->where('due_date', '<', \$today)
            ->update(['status' => 'overdue']);

        Log::info('CheckOverdueExpenses executed', [
            'updated' => \$updated,
            'date' => \$today,
        ]);
    }
}
```

**Regras:**
- ✅ Implements `ShouldQueue`
- ✅ Logs informativos

---

## ❌ O QUE NÃO USAR

- ❌ **Actions** - Pasta não existe, use Services
- ❌ **Policies** - Pasta não existe, use Spatie middleware
- ❌ **DTOs** - Ainda não implementados
- ❌ **Enums** - Ainda não implementados

## ✅ O QUE USAR

- ✅ **Services** - Lógica de negócio complexa
- ✅ **Form Requests** - Validação obrigatória
- ✅ **Observers** - Efeitos colaterais
- ✅ **Jobs** - Processamento assíncrono
- ✅ **Scopes** - Queries reutilizáveis

---

## 📋 Nomenclatura

### Arquivos
```
Controllers/  →  NomeController.php
Models/       →  Nome.php (singular)
Services/     →  NomeService.php
Requests/     →  NomeRequest.php
Observers/    →  NomeObserver.php
Jobs/         →  NomeJob.php
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
// RESTful
Route::resource('expenses', ExpenseController::class);

// Nomes
expenses.index, expenses.create, expenses.store
```

---

**[Ver Guia de Segurança →](security.md)**
