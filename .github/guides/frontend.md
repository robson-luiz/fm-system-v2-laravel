# 🎨 Guia de Frontend - FM System v2

> **Blade + Tailwind CSS v4 + Alpine.js v3**

---

## 📚 Stack Frontend

- **Tailwind CSS v4** - Utility-first CSS
- **Alpine.js v3** - Reatividade leve
- **Chart.js v4** - Gráficos interativos
- **SweetAlert2** - Modais elegantes
- **Tema Claro/Escuro** - Obrigatório

---

## 📝 Estrutura de Views

### Template Padrão

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
<script src="{{ asset('js/custom-script.js') }}"></script>
@endpush
```

---

## 🎨 Tailwind CSS - Tema Claro/Escuro

### Classes Obrigatórias

```html
<!-- Backgrounds -->
<div class="bg-white dark:bg-gray-800">
  <!-- Textos -->
  <p class="text-gray-900 dark:text-gray-100">
    <span class="text-gray-600 dark:text-gray-400">
      <!-- Borders -->
      <div class="border border-gray-200 dark:border-gray-700">
        <!-- Cards -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
          <!-- Inputs -->
          <input
            class="bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
          />

          <!-- Botões -->
          <button class="bg-blue-500 hover:bg-blue-600 text-white"></button>
        </div></div
    ></span>
  </p>
</div>
```

### Cards de Estatísticas

```blade
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Receitas do Mês
                </p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                    R$ {{ number_format($totalIncomes, 2, ',', '.') }}
                </p>
            </div>
            <div class="text-4xl">💰</div>
        </div>
    </div>
</div>
```

### Tabelas Responsivas

```blade
<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                    Descrição
                </th>
                <th class="px-6 py-3 text-right">Ações</th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @foreach($expenses as $expense)
            <tr>
                <td class="px-6 py-4 text-gray-900 dark:text-gray-100">
                    {{ $expense->description }}
                </td>
                <td class="px-6 py-4 text-right">
                    <a href="{{ route('expenses.edit', $expense) }}"
                       class="text-blue-600 hover:text-blue-900 dark:text-blue-400">
                        Editar
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
```

---

## 🔔 SweetAlert2 - Confirmações

### Exclusão Elegante

```javascript
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
```

**No Blade:**

```blade
<form id="delete-form-{{ $expense->id }}"
      action="{{ route('expenses.destroy', $expense) }}"
      method="POST"
      class="inline">
    @csrf
    @method('DELETE')
</form>

<button onclick="confirmDelete({{ $expense->id }}, '{{ $expense->description }}')"
        class="text-red-600 hover:text-red-900">
    Excluir
</button>
```

### Notificações de Sucesso

```javascript
@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Sucesso!',
        text: '{{ session('success') }}',
        timer: 3000,
        showConfirmButton: false
    });
@endif
```

---

## ⚡ Alpine.js - Reatividade

### Componentes Interativos

```blade
<div x-data="{ open: false }">
    <button @click="open = !open"
            class="bg-blue-500 text-white px-4 py-2 rounded">
        Toggle Menu
    </button>

    <div x-show="open"
         x-transition
         class="mt-4 bg-white dark:bg-gray-800 rounded shadow p-4">
        Menu Content
    </div>
</div>
```

### Máscaras de Dinheiro

```javascript
// public/js/money-mask.js
document.addEventListener("DOMContentLoaded", function () {
  const moneyInputs = document.querySelectorAll(".money-mask");

  moneyInputs.forEach((input) => {
    input.addEventListener("input", function (e) {
      let value = e.target.value.replace(/\D/g, "");
      value = (value / 100).toFixed(2);
      value = value.replace(".", ",");
      value = value.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
      e.target.value = "R$ " + value;
    });
  });
});
```

**No Blade:**

```blade
<input type="text"
       name="amount_display"
       class="money-mask bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
       placeholder="R$ 0,00">
```

---

## 📊 Chart.js - Gráficos

### Receitas vs Despesas

```blade
<canvas id="cashFlowChart"></canvas>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
const ctx = document.getElementById('cashFlowChart').getContext('2d');
const chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode($months) !!},
        datasets: [
            {
                label: 'Receitas',
                data: {!! json_encode($incomes) !!},
                borderColor: '#10B981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)'
            },
            {
                label: 'Despesas',
                data: {!! json_encode($expenses) !!},
                borderColor: '#EF4444',
                backgroundColor: 'rgba(239, 68, 68, 0.1)'
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                labels: {
                    color: isDarkMode ? '#F3F4F6' : '#1F2937'
                }
            }
        },
        scales: {
            y: {
                ticks: {
                    color: isDarkMode ? '#F3F4F6' : '#1F2937',
                    callback: value => 'R$ ' + value.toLocaleString('pt-BR')
                }
            }
        }
    }
});
</script>
@endpush
```

---

## 🎯 Badges e Status

### Status de Despesas

```blade
@php
    $statusColors = [
        'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        'paid' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        'overdue' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    ];

    $statusLabels = [
        'pending' => 'Pendente',
        'paid' => 'Paga',
        'overdue' => 'Vencida',
    ];
@endphp

<span class="px-2 py-1 text-xs font-semibold rounded {{ $statusColors[$expense->status] }}">
    {{ $statusLabels[$expense->status] }}
</span>
```

---

## 📱 Responsividade

### Mobile-First

```blade
<!-- Stack em mobile, grid em desktop -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    <!-- Cards -->
</div>

<!-- Ocultar em mobile -->
<div class="hidden md:block">
    <!-- Desktop only -->
</div>

<!-- Mostrar apenas em mobile -->
<div class="md:hidden">
    <!-- Mobile only -->
</div>

<!-- Texto responsivo -->
<h1 class="text-xl md:text-2xl lg:text-3xl">
    Título Responsivo
</h1>
```

---

## ✅ Checklist Frontend

- [ ] ✅ Tema claro/escuro em todos os elementos
- [ ] ✅ Breadcrumbs em todas as páginas
- [ ] ✅ Emojis nos títulos
- [ ] ✅ SweetAlert2 para confirmações
- [ ] ✅ Design responsivo (mobile-first)
- [ ] ✅ Máscaras de dinheiro funcionando
- [ ] ✅ Gráficos adaptados ao tema
- [ ] ✅ Badges coloridos para status
- [ ] ✅ Formulários com validação visual
- [ ] ✅ UX consistente em todo o sistema

---

**[Ver Guia de Performance →](performance.md) | [Ver Guia de Arquitetura →](architecture.md)**
