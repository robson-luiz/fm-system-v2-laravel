# Changelog

Todas as mudanças notáveis neste projeto serão documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/),
e este projeto adere ao [Semantic Versioning](https://semver.org/lang/pt-BR/).

> 💡 **Nota sobre Versões**: Este é o **FM System v2**, reescrito com Laravel 12. A v1 (PHP puro) não está disponível publicamente.

---

## [0.1.0] - 2025-10-07

### 🎉 Lançamento Inicial - FM System v2 - Fase 1: Sistema de Despesas

### ✨ Adicionado

#### Sistema Base
- Sistema de autenticação (Laravel Breeze)
- Sistema de permissões (Spatie)
- Auditoria de ações (OwenIt)
- Interface responsiva com Tailwind CSS v4
- Suporte a tema claro/escuro
- Gerenciamento de usuários

#### CRUD Completo de Despesas
- Listagem de despesas com paginação
- Cadastro de despesas com validações
- Visualização detalhada de cada despesa
- Edição e exclusão de despesas
- Filtros avançados (status, periodicidade, cartão, mês)
- Estatísticas em cards (pendentes, pagas, vencidas)
- Alertas visuais (vencidas, próximas ao vencimento)

#### Sistema de Parcelas Inteligente
**Parcelas Fixas (Automático)**
- Divisão automática de valores
- Cálculo de datas mensais com Carbon
- Ajuste de arredondamento na última parcela
- Preview em tempo real

**Parcelas Flexíveis (Personalizado)** ⭐ NOVO
- Toggle intuitivo: "Parcelas Iguais" ↔ "Parcelas Personalizadas"
- Gerador dinâmico de campos para cada parcela
- Valores e datas personalizados por parcela
- Validação em tempo real da soma das parcelas
- Feedback visual (✓ confere | ⚠ diferença)
- Suporte a entrada maior + parcelas menores
- Máscaras de dinheiro (R$ 1.000,00)
- Conversão automática no submit

**Gerenciamento Individual de Parcelas**
- Tabela separada `installments` no banco
- Relacionamento `1 Expense → N Installments`
- Estatísticas: Total, Pagas, Pendentes, Vencidas
- Marcação individual de parcela como paga (via AJAX)
- Desfazer pagamento de parcela
- Histórico completo de pagamentos
- Modais interativos (SweetAlert2)

#### Banco de Dados
- Migration `expenses` (description, amount, due_date, etc)
- Migration `installments` (parcelas separadas)
- Migration de limpeza (remove campos antigos)
- Seeders com dados de teste
- 37 permissões para módulos financeiros
- Índices para otimização de queries

#### Interface e UX
- Design moderno e responsivo
- Dark mode completo
- Modais interativos com SweetAlert2
- Máscaras de input (dinheiro, data)
- Badges coloridos por status
- Formulários com validação em tempo real
- Feedback visual instantâneo

#### Documentação
- README.md completo e profissional
- CHANGELOG.md com versionamento semântico
- LICENSE (MIT)
- .gitignore configurado
- Documentação da estrutura do banco
- Roadmap detalhado por fases

### 🔧 Recursos Técnicos
- Transações DB para atomicidade
- Eager loading para evitar N+1 queries
- AJAX para ações sem reload de página
- Validações: Frontend (JavaScript) + Backend (Laravel)
- Auditoria completa (OwenIt)
- Permissões granulares (Spatie)
- JavaScript consolidado em `app.js` com contexto
- Código organizado e bem documentado

### 🎯 Arquitetura
- Models: `Expense`, `Installment`, `CreditCard` (estrutura)
- Controllers: `ExpenseController` com CRUD completo
- Relacionamentos: `hasMany`, `belongsTo`
- Scopes: `pending()`, `paid()`, `overdue()`, `dueSoon()`
- Accessors: `amount_formatted`, `status_badge`, `installments_summary`
- Services: Lógica de negócio separada

### 📊 Desempenho
- Paginação (15 itens/página)
- Índices no banco de dados
- Queries otimizadas
- Eager loading implementado
- Cache de relacionamentos

---

## Tipos de Mudanças

- ✨ **Adicionado** para novas funcionalidades.
- 🔄 **Mudado** para mudanças em funcionalidades existentes.
- ❌ **Descontinuado** para funcionalidades que serão removidas.
- 🗑️ **Removido** para funcionalidades removidas.
- 🐛 **Corrigido** para correções de bugs.
- 🔒 **Segurança** para vulnerabilidades corrigidas.

---

## [Não Lançado]

### 🚧 Próxima Fase (Fase 2)
- CRUD de Cartões de Crédito
- Vinculação de despesas com cartões
- Análise de melhor dia de compra
- Controle de limite de cartão
- Alertas de aproximação do limite

### 📋 Fases Futuras

**Fase 3 - Receitas e Dashboard**
- CRUD de Receitas
- Dashboard financeiro com gráficos
- Relatórios de fluxo de caixa
- Análise Receitas vs Despesas

**Fase 4 - Wishlist e Análises**
- Wishlist inteligente
- Análise de viabilidade de compras
- Verificação automática de pagamentos
- Alertas inteligentes

**Fase 5 - Recursos Avançados**
- Integração com IA para análises
- Open Banking
- Relatórios em PDF/Excel
- Notificações por e-mail/SMS
- Multi-moeda

---

**Legenda de Versões:**
- **Maior** (X.0.0): Mudanças incompatíveis na API
- **Menor** (0.X.0): Novas funcionalidades compatíveis
- **Correção** (0.0.X): Correções de bugs

