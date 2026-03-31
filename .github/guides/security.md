# 🛡️ Guia Completo de Segurança - FM System v2

> **Sistema financeiro preparado para SaaS/MVP - Segurança é CRÍTICA e INEGOCIÁVEL**

---

## 🔒 Proteção Contra OWASP Top 10

### 1. SQL Injection Prevention

```php
// ✅ CORRETO - Laravel protege automaticamente
$users = User::where('email', $request->email)->get();
$expenses = DB::table('expenses')->where('user_id', Auth::id())->get();

// ❌ PERIGO - SQL Injection vulnerável
$users = DB::select("SELECT * FROM users WHERE email = '{$request->email}'");
```

**Com raw SQL, use bindings:**

```php
// ✅ CORRETO
$users = DB::select('SELECT * FROM users WHERE email = ?', [$request->email]);

// ❌ ERRADO
$users = DB::select("SELECT * FROM users WHERE email = '{$request->email}'");
```

---

### 2. Cross-Site Scripting (XSS)

```blade
{{-- ✅ CORRETO - Blade escapa automaticamente --}}
<h1>{{ $expense->description }}</h1>

{{-- ❌ PERIGO - Não faz escape --}}
<div>{!! $untrustedContent !!}</div>
```

**Sanitize inputs:**

```php
public function validated($key = null, $default = null)
{
    $validated = parent::validated($key, $default);

    if (isset($validated['description'])) {
        $validated['description'] = strip_tags($validated['description']);
    }

    return $validated;
}
```

---

### 3. CSRF Protection

```blade
{{-- SEMPRE inclua @csrf --}}
<form method="POST" action="{{ route('expenses.store') }}">
    @csrf
    <input type="text" name="description">
</form>

{{-- DELETE/PUT --}}
<form method="POST" action="{{ route('expenses.destroy', $expense) }}">
    @csrf
    @method('DELETE')
</form>
```

**AJAX:**

```javascript
fetch("/api/endpoint", {
  method: "POST",
  headers: {
    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
  },
  body: JSON.stringify(data),
});
```

---

### 4. Rate Limiting (Brute Force Protection)

```php
// Em routes/web.php
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1'); // 5 tentativas/minuto

Route::post('/2fa/verify', [TwoFactorController::class, 'verify'])
    ->middleware('throttle:3,1');

Route::post('/expenses', [ExpenseController::class, 'store'])
    ->middleware('throttle:60,1');
```

---

### 5. Security Misconfiguration

**.env PRODUÇÃO:**

```env
# ✅ OBRIGATÓRIO
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seu-dominio.com
FORCE_HTTPS=true

# ❌ NUNCA em produção
APP_DEBUG=true
APP_ENV=local
```

**Force HTTPS:**

```php
// app/Providers/AppServiceProvider.php
public function boot(): void
{
    if ($this->app->environment('production')) {
        URL::forceScheme('https');
    }
}
```

---

### 6. Sensitive Data Exposure

```php
// ✅ Oculte campos sensíveis
class User extends Model
{
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];
}

// ✅ Criptografe dados sensíveis
protected function casts(): array
{
    return [
        'credit_card_number' => 'encrypted',
        'ssn' => 'encrypted',
    ];
}

// ❌ NUNCA logue senhas
Log::info('User login', [
    'user_id' => $user->id,
    // ❌ 'password' => $password, // NUNCA!
]);
```

---

### 7. Broken Access Control

```php
// ✅ SEMPRE valide propriedade
public function update(ExpenseRequest $request, Expense $expense)
{
    // Método 1: Manual
    if ($expense->user_id !== Auth::id()) {
        abort(403);
    }

    $expense->update($request->validated());
}

// ✅ Método 2: Escopo automático
$expense = Auth::user()->expenses()->findOrFail($id);
$expense->update($request->validated());

// ❌ ERRADO - Não valida
public function update(ExpenseRequest $request, Expense $expense)
{
    $expense->update($request->validated());
}
```

---

### 8. Insecure File Upload

```php
// ✅ Validação rigorosa
public function rules(): array
{
    return [
        'avatar' => [
            'required',
            'image',
            'mimes:jpeg,png,jpg',
            'max:2048', // 2MB
            'dimensions:max_width=2000,max_height=2000',
        ],
    ];
}

// ✅ Reprocesse imagens
use Intervention\Image\ImageManager;

$manager = new ImageManager(new Driver());
$image = $manager->read($request->file('avatar'));
$image->scale(width: 500);
$filename = uniqid() . '.jpg';
$image->save(storage_path("app/public/avatars/{$filename}"));
```

---

### 9. Logging & Monitoring

```php
// Configure canal de segurança em config/logging.php
'channels' => [
    'security' => [
        'driver' => 'daily',
        'path' => storage_path('logs/security.log'),
        'level' => 'info',
        'days' => 90,
    ],
],

// Logue ações críticas
Log::channel('security')->warning('Failed login attempt', [
    'email' => $request->email,
    'ip' => $request->ip(),
    'user_agent' => $request->userAgent(),
]);
```

---

### 10. Detecção de Anomalias

```php
namespace App\Services;

class SecurityMonitorService
{
    public function detectSuspiciousActivity(User $user): array
    {
        $alerts = [];

        // Múltiplos logins de IPs diferentes
        $recentLogins = DB::table('audit_logs')
            ->where('user_id', $user->id)
            ->where('event', 'login')
            ->where('created_at', '>', now()->subHours(24))
            ->distinct('ip_address')
            ->count();

        if ($recentLogins > 5) {
            $alerts[] = 'Múltiplos IPs detectados em 24h';
        }

        return $alerts;
    }
}
```

---

## 🔐 Preparação para SaaS/MVP

### Security Headers Middleware

```php
namespace App\Http\Middleware;

class SecurityHeaders
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Content Security Policy
        $response->headers->set('Content-Security-Policy',
            "default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net;"
        );

        // HSTS (force HTTPS)
        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload'
            );
        }

        return $response;
    }
}

// Registrar em app/Http/Kernel.php
protected $middleware = [
    \App\Http\Middleware\SecurityHeaders::class,
];
```

---

## 🧪 Security Testing

```php
class SecurityTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_cannot_access_other_users_expenses()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $expenseB = Expense::factory()->create(['user_id' => $userB->id]);

        $this->actingAs($userA);
        $response = $this->get(route('expenses.show', $expenseB));

        $response->assertStatus(403);
    }

    #[Test]
    public function sql_injection_is_prevented()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('expenses.index', [
            'search' => "'; DROP TABLE expenses; --"
        ]));

        $response->assertStatus(200);
        $this->assertDatabaseHas('expenses', []);
    }

    #[Test]
    public function xss_attack_is_prevented()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $xssPayload = '<script>alert("XSS")</script>';

        $response = $this->post(route('expenses.store'), [
            'description' => $xssPayload,
            'amount' => 100,
            'due_date' => now()->format('Y-m-d'),
        ]);

        $expense = Expense::first();
        $this->assertStringNotContainsString('<script>', $expense->description);
    }

    #[Test]
    public function rate_limiting_blocks_excessive_requests()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        for ($i = 0; $i < 61; $i++) {
            $response = $this->get(route('expenses.index'));

            if ($i < 60) {
                $response->assertStatus(200);
            } else {
                $response->assertStatus(429); // Too Many Requests
            }
        }
    }
}
```

---

## 📋 Production Security Checklist

**NUNCA faça deploy sem verificar:**

- [ ] ✅ `APP_DEBUG=false`
- [ ] ✅ `APP_ENV=production`
- [ ] ✅ HTTPS forçado
- [ ] ✅ Rate limiting ativo
- [ ] ✅ Headers de segurança configurados
- [ ] ✅ Validação de propriedade em TODOS os controllers
- [ ] ✅ Eloquent ORM usado (sem raw SQL vulnerável)
- [ ] ✅ Blade escape automático
- [ ] ✅ Upload de arquivos validado
- [ ] ✅ Dados sensíveis criptografados
- [ ] ✅ Auditoria ativa (Laravel Auditing)
- [ ] ✅ Logs de segurança implementados
- [ ] ✅ Testes de segurança passando
- [ ] ✅ Senhas fortes no .env
- [ ] ✅ Backups automatizados

---

**[Ver Guia de Testes →](testing.md) | [Ver Guia de Performance →](performance.md)**
