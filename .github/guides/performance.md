# ⚡ Guia de Performance - FM System v2

> **Otimização e Boas Práticas de Performance**

---

## 🚫 N+1 Queries - O Problema Mais Comum

### ❌ Problema

```php
// ERRADO - Gera 1 + N queries
$expenses = Expense::all(); // 1 query

foreach ($expenses as $expense) {
    echo $expense->user->name; // +1 query por despesa
    echo $expense->creditCard->name; // +1 query por despesa
}
// Total: 1 + (N * 2) queries
```

### ✅ Solução

```php
// CORRETO - Apenas 1 query
$expenses = Expense::with(['user', 'creditCard'])->get();

foreach ($expenses as $expense) {
    echo $expense->user->name; // Sem query adicional
    echo $expense->creditCard->name; // Sem query adicional
}
// Total: 1 query
```

### 🎯 Eager Loading Avançado

```php
// Carregar múltiplos níveis
$expenses = Expense::with([
    'user',
    'creditCard',
    'category',
    'installments' => function ($query) {
        $query->where('status', 'pending');
    }
])->get();

// Carregar relacionamentos condicionais
$expenses = Expense::with([
    'user:id,name,email', // Apenas campos específicos
    'creditCard' => function ($query) {
        $query->select('id', 'name', 'card_limit');
    }
])->get();
```

---

## 📄 Paginação Obrigatória

### ❌ Problema

```php
// ERRADO - Carrega TODOS os registros
$expenses = Expense::all();
return view('expenses.index', compact('expenses'));
// Com 10.000 registros = 💥 Timeout
```

### ✅ Solução

```php
// CORRETO - Apenas 15 por página
$expenses = Expense::paginate(15)->withQueryString();
return view('expenses.index', compact('expenses'));
```

### 📖 Tipos de Paginação

```php
// Paginação normal (mostra total de páginas)
$expenses = Expense::paginate(15);

// Simple pagination (apenas "Anterior/Próximo")
$expenses = Expense::simplePaginate(15);

// Cursor pagination (melhor performance em datasets grandes)
$expenses = Expense::cursorPaginate(15);

// Custom page size
$perPage = request('per_page', 15); // Default 15
$expenses = Expense::paginate($perPage);
```

---

## 📊 Agregações no Banco

### ❌ Problema

```php
// ERRADO - Traz TODOS para PHP e soma
$expenses = Expense::where('status', 'pending')->get();
$total = $expenses->sum('amount'); // Soma no PHP
```

### ✅ Solução

```php
// CORRETO - Soma direto no banco
$total = Expense::where('status', 'pending')->sum('amount');

// Outras agregações
$count = Expense::where('status', 'paid')->count();
$average = Expense::avg('amount');
$max = Expense::max('amount');
$min = Expense::min('amount');
```

### 🎯 Queries Otimizadas

```php
// Múltiplas agregações de uma vez
$stats = Expense::selectRaw('
        SUM(CASE WHEN status = "pending" THEN amount ELSE 0 END) as pending_total,
        SUM(CASE WHEN status = "paid" THEN amount ELSE 0 END) as paid_total,
        COUNT(*) as total_count
    ')
    ->where('user_id', Auth::id())
    ->first();

// Agrupa e conta
$expensesByCategory = Expense::select('category_id')
    ->selectRaw('COUNT(*) as count')
    ->selectRaw('SUM(amount) as total')
    ->groupBy('category_id')
    ->get();
```

---

## 🗄️ Indexação de Banco de Dados

### Índices Críticos

```php
// Em migrations
Schema::create('expenses', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->date('due_date');
    $table->string('status');

    // ÍNDICES OBRIGATÓRIOS
    $table->index('user_id'); // Filtro por usuário (mais comum)
    $table->index('status'); // Filtro por status
    $table->index(['user_id', 'status']); // Índice composto
    $table->index(['user_id', 'due_date']); // Para ordenação por data

    $table->timestamps();
});
```

### Quando Usar Índices

✅ **USE índices em:**

- Colunas de foreign keys (`user_id`, `category_id`)
- Colunas usadas em WHERE (`status`, `due_date`)
- Colunas usadas em ORDER BY (`created_at`, `due_date`)
- Colunas usadas em JOIN

❌ **NÃO use índices em:**

- Colunas raramente filtradas
- Tabelas muito pequenas (<1000 registros)
- Colunas com poucos valores únicos (boolean)

---

## 🚀 Query Scopes para Reutilização

```php
// Em Model Expense
public function scopeForUser($query, User $user)
{
    return $query->where('user_id', $user->id);
}

public function scopePending($query)
{
    return $query->where('status', 'pending');
}

public function scopeOverdue($query)
{
    return $query->where('status', 'overdue')
                 ->orWhere(function ($q) {
                     $q->where('status', 'pending')
                       ->where('due_date', '<', now());
                 });
}

public function scopeCurrentMonth($query)
{
    return $query->whereYear('due_date', now()->year)
                 ->whereMonth('due_date', now()->month);
}

// Uso
$expenses = Expense::forUser(Auth::user())
    ->pending()
    ->currentMonth()
    ->with('category')
    ->get();
```

---

## 💾 Caching Estratégico

### Cache de Queries Pesadas

```php
use Illuminate\Support\Facades\Cache;

// Cache por 1 hora
$stats = Cache::remember('dashboard_stats_' . Auth::id(), 3600, function () {
    return [
        'total_incomes' => Income::forUser(Auth::user())->sum('amount'),
        'total_expenses' => Expense::forUser(Auth::user())->sum('amount'),
        'balance' => Income::sum('amount') - Expense::sum('amount'),
    ];
});

// Limpar cache quando dados mudarem
// Em Observer
public function created(Expense $expense)
{
    Cache::forget('dashboard_stats_' . $expense->user_id);
}
```

### Cache de Views

```php
// Não disponível nativamente no Laravel 12, mas pode usar:
// - View Composers para dados compartilhados
// - Fragment caching em Blade com package terceiro
```

---

## 🔄 Lazy Loading vs Eager Loading

```php
// ❌ Lazy Loading (N+1)
$user = User::find(1);
$expenses = $user->expenses; // Query adicional

// ✅ Eager Loading
$user = User::with('expenses')->find(1);
$expenses = $user->expenses; // Sem query adicional

// ✅ Lazy Eager Loading (quando já tem o model)
$user = User::find(1);
$user->load('expenses'); // Carrega depois
```

---

## 📦 Chunk para Datasets Grandes

```php
// ❌ ERRADO - Carrega tudo na memória
Expense::all()->each(function ($expense) {
    // Processar
});

// ✅ CORRETO - Processa em blocos de 100
Expense::chunk(100, function ($expenses) {
    foreach ($expenses as $expense) {
        // Processar
    }
});

// ✅ Ainda melhor - Lazy collection
Expense::lazy()->each(function ($expense) {
    // Processa um por vez sem carregar tudo
});
```

---

## ⚠️ Select Apenas Campos Necessários

```php
// ❌ ERRADO - Traz TODAS as colunas
$expenses = Expense::all();

// ✅ CORRETO - Apenas campos necessários
$expenses = Expense::select('id', 'description', 'amount', 'due_date')->get();

// ✅ Com relacionamentos
$expenses = Expense::select('id', 'user_id', 'amount')
    ->with(['user:id,name']) // Apenas id e name do user
    ->get();
```

---

## 🧪 Debugging de Performance

### Laravel Debugbar

```bash
composer require barryvdh/laravel-debugbar --dev
```

### Telescope (Produção-ready)

```bash
composer require laravel/telescope
php artisan telescope:install
php artisan migrate
```

### Query Logging Manual

```php
// Habilitar
DB::enableQueryLog();

// Executar queries
$expenses = Expense::with('user')->get();

// Ver queries executadas
dd(DB::getQueryLog());
```

---

## ✅ Checklist de Performance

- [ ] ✅ Usar eager loading (`with()`) para evitar N+1
- [ ] ✅ Usar paginação em todas as listagens
- [ ] ✅ Agregar no banco, não no PHP
- [ ] ✅ Índices em colunas filtradas/ordenadas
- [ ] ✅ Select apenas campos necessários
- [ ] ✅ Cache para queries pesadas
- [ ] ✅ Chunk/Lazy para datasets grandes
- [ ] ✅ Query Scopes para reutilização
- [ ] ✅ Usar `withQueryString()` na paginação
- [ ] ✅ Testar com Debugbar/Telescope

---

## 🎯 Benchmarks

### Medir Performance

```php
use Illuminate\Support\Benchmarking\Benchmark;

Benchmark::measure([
    'Eager Loading' => fn () => Expense::with('user')->get(),
    'Lazy Loading' => fn () => Expense::all()->each->user,
], iterations: 10);

// Resultado:
// Eager Loading: 15ms
// Lazy Loading: 250ms ❌
```

---

**[Ver Guia de Segurança →](security.md) | [Ver Guia de Testes →](testing.md)**
