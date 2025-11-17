**FM System v2** - Sistema de Gerenciamento Financeiro Pessoal com Laravel 12.

> 💡 **Sobre as Versões**: Esta é a versão 2 do FM System, completamente reescrita com Laravel 12. A versão 1 foi desenvolvida em PHP puro e não está disponível publicamente.

## Requisitos

* PHP 8.2 ou superior - Conferir a versão: php -v
* MySQL 8.0 ou superior - Conferir a versão: mysql --version
* Composer - Conferir a instalação: composer --version
* Node.js 22 ou superior - Conferir a versão: node -v
* NPM ou Yarn - Para gerenciar dependências do Node.js e compilar assets
* GIT - Conferir se está instalado o GIT: git -v

**Frontend:**
* Tailwind CSS v4 - Incluído como dependência do projeto (instalado via npm)

## Como rodar o projeto baixado

Primeiro, baixe o projeto do repositório GitHub:
```
git clone https://github.com/robson-luiz/fm-system-v2-laravel.git
cd fm-system-v2-laravel
```

- Duplicar o arquivo ".env.example" e renomear para ".env".
- Alterar as credenciais do banco de dados.
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fm_system_v2
DB_USERNAME=root
DB_PASSWORD=
```

- Para a funcionalidade enviar e-mail funcionar, necessário alterar as credenciais do servidor de envio de e-mail no arquivo .env.
- Utilizar o servidor fake durante o desenvolvimento: [Acessar envio gratuito de e-mail](https://mailtrap.io)
```
MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=nome-do-usuario-na-mailtrap
MAIL_PASSWORD=senha-do-usuario-na-mailtrap
MAIL_FROM_ADDRESS="colocar-email-remetente@meu-dominio.com.br"
MAIL_FROM_NAME="${APP_NAME}"
```

Instalar as dependências do PHP.
```
composer install
```

Instalar as dependências do Node.js.
```
npm install
```

Gerar a chave no arquivo .env.
```
php artisan key:generate
```

Executar as migrations para criar as tabelas e as colunas.
```
php artisan migrate
```

Executar seed com php artisan para cadastrar registros de testes.
```
php artisan db:seed
```

Iniciar o projeto criado com Laravel.
```
php artisan serve
```

Iniciar o projeto criado com Laravel na porta específica.
```
php artisan serve --port=8082
```

Executar as bibliotecas Node.js.
```
npm run dev
```

Executar os Jobs no PC local.
```
php artisan queue:work
```

Acessar a página criada com Laravel.
```
http://127.0.0.1:8000
```

## Armazenamento de Arquivos

O sistema utiliza armazenamento local para imagens de usuários e comprovantes em desenvolvimento e produção.

## Deploy em Produção

### Preparação do Servidor

Criar chave SSH (chave pública e privada).
```
ssh-keygen -t rsa -b 4096 -C "seu-email@exemplo.com"
```

Acessar o servidor com SSH.
```
ssh usuario-do-servidor@ip-do-servidor-vps
```

### Deploy do Projeto

Baixar os arquivos do GitHub para o servidor.
```
git clone -b main <repository_url> .
```

Duplicar o arquivo ".env.example" e renomear para ".env".
```
cp .env.example .env
```

Editar as variáveis de ambiente.
```
nano .env
```

Configurar variáveis principais:
```
APP_NAME="FM System"
APP_ENV=production
APP_DEBUG=false
APP_TIMEZONE=America/Sao_Paulo
APP_URL=https://seu-dominio.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fm_system_v2
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha

SESSION_DRIVER=database
```

Instalar dependências do PHP.
```
composer install --optimize-autoloader --no-dev
```

Instalar dependências do Node.js e gerar build.
```
npm install
npm run build
```

Gerar chave da aplicação.
```
php artisan key:generate
```

Executar migrations e seeds.
```
php artisan migrate --force
php artisan db:seed --force
```

Limpar e otimizar cache.
```
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Configurar Queue Worker com Supervisor

Instalar o Supervisor.
```
sudo apt install supervisor
```

Criar arquivo de configuração.
```
sudo nano /etc/supervisor/conf.d/fm-system-worker.conf
```

Configuração do supervisor:
```
[program:fm-system-worker]
process_name=%(program_name)s_%(process_num)02d
command=/usr/bin/php /caminho/completo/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=seu-usuario
numprocs=1
redirect_stderr=true
stdout_logfile=/caminho/completo/storage/logs/worker.log
```

Aplicar configurações.
```
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start fm-system-worker:*
```

## Comandos Artisan Úteis para Desenvolvimento

Criar o projeto com Laravel (comando usado inicialmente pelo professor)
```
composer create-project laravel/laravel .
```

Iniciar o projeto criado com Laravel.
```
php artisan serve
```

Instalar as dependências do Node.js.
```
npm install
```

Executar as bibliotecas Node.js.
```
npm run dev
```

Acessar o sistema FM System
```
http://127.0.0.1:8000
```

Criar Controller com php artisan.
```
php artisan make:controller NomeController
```
```
php artisan make:controller ExpenseController
```

Criar View com php artisan.
```
php artisan make:view diretorio.nome-view
```
```
php artisan make:view expenses.index
```

Criar migration com php artisan.
```
php artisan make:migration create_nome_table
```
```
php artisan make:migration create_expenses_table
php artisan make:migration create_installments_table
```

Executar as migrations para criar a base de dados e as tabelas.
```
php artisan migrate
```

Criar seed com php artisan para cadastrar registros de testes.
```
php artisan make:seeder NomeSeeder
```
```
php artisan make:seeder ExpenseSeeder
```

Executar seed com php artisan para cadastrar registros de testes.
```
php artisan db:seed
```

Desfazer todas as migrations e executá-las novamente.
```
php artisan migrate:fresh
```

Desfazer todas as migrations, executá-las novamente e rodar as seeds.
```
php artisan migrate:fresh --seed
```

Criar componente
```
php artisan make:component nome --view
```
```
php artisan make:component expense-card --view
```

Criar o arquivo de Request com validações para o formulário.
```
php artisan make:request NomeRequest
```
```
php artisan make:request ExpenseRequest
```

Traduzir para português [Módulo pt-BR](https://github.com/lucascudo/laravel-pt-BR-localization)

Instalar o pacote de auditoria do Laravel.
```
composer require owen-it/laravel-auditing
```

Publicar a configuração e as migration para auditoria.
```
php artisan vendor:publish --provider "OwenIt\Auditing\AuditingServiceProvider" --tag="config"
```
```
php artisan vendor:publish --provider "OwenIt\Auditing\AuditingServiceProvider" --tag="migrations"
```

Limpar cache de configuração.
```
php artisan config:clear
```

Instalar a dependência de permissão.
```
composer require spatie/laravel-permission
```

Criar as migrations para o sistema de permissão.
```
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

Limpar cache de configuração.
```
php artisan config:clear
```

Executar as migrations do sistema de permissão.
```
php artisan migrate
```

## Bibliotecas e Dependências Incluídas

O projeto já vem com as seguintes bibliotecas pré-instaladas:

**Frontend:**
- **Tailwind CSS v4** - Framework CSS utilitário
- **Alpine.js v3** - Framework JavaScript reativo
- **Chart.js v4** - Biblioteca para gráficos
- **SweetAlert2** - Alertas personalizados elegantes

**Backend:**
- **Spatie Laravel Permission** - Sistema de permissões e roles
- **OwenIt Laravel Auditing** - Auditoria de ações do sistema
- **Intervention Image** - Manipulação de imagens
- **Laravel Tinker** - REPL interativo do Laravel

**Desenvolvimento:**
- **Laravel Pint** - Formatador de código PHP
- **Laravel Sail** - Ambiente Docker (opcional)
- **Faker** - Geração de dados fake para testes

Para instalar todas as dependências após clonar o projeto:
```
composer install
npm install
```

## Como baixar e trabalhar com o projeto do GitHub

O repositório **"fm-system-v2-laravel"** já está criado no GitHub com a branch **"main"**.

Baixar os arquivos do Git.
```
git clone -b main <repository_url> .
```

- Colocar o código fonte do projeto no diretório que está trabalhando.

Alterar o Usuário Globalmente (para todos os repositórios).
```
git config --global user.name "SeuNomeDeUsuario"
git config --global user.email "seuemail@exemplo.com"
```

Verificar em qual está branch.
```
git branch 
```

Baixar as atualizações do GitHub.
```
git pull
```

Adicionar todos os arquivos modificados no staging area - área de preparação.
```
git add .
```

commit representa um conjunto de alterações e um ponto específico da história do seu projeto, registra apenas as alterações adicionadas ao índice de preparação.
O comando -m permite que insira a mensagem de commit diretamente na linha de comando.
```
git commit -m "Base projeto"
```

Enviar os commits locais, para um repositório remoto.
```
git push <remote> <branch>
git push origin main
```

Voltar um ou mais commits. Usar HEAD~2 para voltar dois commits, e assim por diante.
```
git reset --hard HEAD~1
```

Criar nova branch no PC.
```
git checkout -b <branch>
```
```
git checkout -b feature/nova-funcionalidade
```

Mudar de branch.
```
git switch <branch>
```
```
git switch feature/nova-funcionalidade
```

Mesclar o histórico de commits de uma branch em outra branch.
```
git merge <branch_name>
```
```
git merge feature/nova-funcionalidade
```

Fazer o push das alterações.
```
git push origin <branch_name>
```
```
git push origin main
```

## Sobre o Projeto

### FM System v2

**FM System v2** é um sistema de gerenciamento financeiro pessoal desenvolvido com Laravel 12, focado em ajudar usuários a controlarem suas finanças de forma inteligente e proativa.

**Por que versão 2?**

Este projeto é a **segunda versão** do FM System. A versão 1 foi desenvolvida inteiramente em PHP puro como parte do aprendizado inicial de desenvolvimento web. Com o avanço dos estudos e a adoção de frameworks modernos, o sistema foi completamente reescrito utilizando Laravel 12, trazendo:

- 🏗️ Arquitetura MVC robusta
- 🔒 Sistema de autenticação e permissões integrado
- 🎨 Interface moderna com Tailwind CSS v4
- ⚡ Performance otimizada
- 📝 Código organizado e escalável
- 🧪 Facilidade para testes

A versão 1 (PHP puro) permanece como projeto pessoal de aprendizado e não está disponível publicamente.

### Funcionalidades Principais

- 🔐 **Sistema de autenticação robusto** com login de dois fatores (2FA)
- 💰 **Gerenciamento inteligente de despesas** com sistema de parcelas flexíveis
- 💳 **Controle de cartões de crédito** com análise de melhor data de compra
- 📊 **Dashboard financeiro** com gráficos e relatórios detalhados
- 🎯 **Wishlist inteligente** com análise de viabilidade financeira
- 🔔 **Alertas proativos** de pagamentos e vencimentos
- 👥 **Sistema completo de permissões** e auditoria de ações
- 🎨 **Interface moderna** com tema claro/escuro e design responsivo

### Roadmap

**Base Inicial do Sistema** ✅ Concluída
- [x] Sistema de autenticação e permissões (Spatie)
- [x] Gerenciamento de usuários com roles
- [x] Sistema de auditoria (OwenIt/laravel-auditing)
- [x] Interface responsiva com Tailwind CSS v4
- [x] Suporte a tema claro/escuro

**Fase 1 - Gestão de Despesas** ✅ Concluída (07/10/2025)
- [x] CRUD completo de despesas
- [x] Sistema de parcelas com tabela separada
- [x] Parcelas fixas (valores iguais)
- [x] Parcelas flexíveis (valores personalizados)
- [x] Validação em tempo real de valores
- [x] Marcação individual de parcelas pagas
- [x] Histórico de pagamentos

**Fase 2 - Login com 2 Fatores** ✅ Concluída (25/10/2025)
- [x] Implementação de autenticação de dois fatores (2FA)
- [x] Configuração administrativa para escolha do método de envio
- [x] Envio de código via e-mail
- [x] Envio de código via SMS
- [x] Interface de configuração no painel administrativo
- [x] Validação e verificação de códigos temporários
- [x] Backup codes para recuperação de acesso
- [x] Logs de segurança para tentativas de login
- [x] **Provedores SMS Customizados**: Configure qualquer provedor SMS (Iagente, ZenviaNow, TotalVoice, etc)
- [x] **Verificação completa do sistema 2FA** - (09/11/2025)

**Fase 3 - Cartões de Crédito** ✅ Concluída (09/11/2025)
- [x] CRUD de cartões de crédito
- [x] Vinculação de despesas com cartões
- [x] Controle de limite e fatura
- [x] Alerta de melhor dia de compra
- [x] Cálculo automático de limite disponível
- [x] Observer para atualização em tempo real
- [x] Ajustes de UI/UX e responsividade móvel

**Fase 4 - Sistema de Receitas** ✅ Concluída (16/11/2025)
- [x] CRUD completo de receitas
- [x] Sistema de categorização (Salário, Freelance, Vendas, Investimentos, Aluguel, Comissões, Outros)
- [x] Tipos de receita (Fixa e Variável)
- [x] Status de recebimento (Pendente e Recebida)
- [x] Interface tabular padronizada seguindo padrão de despesas
- [x] Estatísticas em tempo real (pendentes, recebidas no mês, total, receitas fixas)
- [x] Filtros avançados por status, tipo, categoria e período mensal
- [x] Sistema de fontes/origem das receitas
- [x] Observações personalizadas
- [x] Seeder com dados realísticos para testes
- [x] Validações completas (frontend e backend)
- [x] **Refinamentos (16/11/2025)**: Correções no formulário de edição, máscaras centralizadas, SweetAlert2 e ajustes de tema

**Fase 5 - Dashboard e Relatórios** 📋 Planejada
- [ ] Wishlist inteligente
- [ ] Análise de viabilidade de compras
- [ ] Verificação automática de pagamentos
- [ ] Alertas inteligentes

**Fase 6 - Recursos Avançados** 📋 Futuro
- [ ] Integração com IA para análises
- [ ] Open Banking
- [ ] Notificações por e-mail/SMS avançadas
- [ ] Multi-moeda

---

## Funcionalidades Implementadas

### 📊 Sistema de Despesas (Fase 1 - Concluída em 07/10/2025)

#### **CRUD Completo**
- ✅ Listagem com filtros (status, periodicidade, cartão, mês)
- ✅ Cadastro com validações
- ✅ Visualização detalhada
- ✅ Edição de despesas
- ✅ Exclusão com confirmação (SweetAlert2)

#### **Sistema de Parcelas Inteligente**

**1. Arquitetura Refatorada**
- Tabela separada `installments` para gerenciar parcelas
- Cada despesa pode ter múltiplas parcelas independentes
- Relacionamento `hasMany` entre Expense e Installment

**2. Tipos de Parcelamento**

**Parcelas Fixas (Automático)**
```
Valor: R$ 3.000,00 | Parcelas: 3
Resultado: 3x de R$ 1.000,00
```
- Sistema divide automaticamente
- Última parcela ajusta arredondamento
- Datas calculadas mensalmente

**Parcelas Flexíveis (Personalizado)**
```
Exemplo: Entrada + Parcelas diferentes
- Entrada: R$ 500,00 (Nov/2025)
- Parcela 2: R$ 300,00 (Dez/2025)
- Parcela 3: R$ 400,00 (Jan/2026)
- Parcela 4: R$ 300,00 (Fev/2026)
```
- Valores personalizados para cada parcela
- Datas de vencimento individuais
- Validação em tempo real da soma
- Feedback visual: ✓ (confere) | ⚠ (diferença)

**3. Gerenciamento Individual de Parcelas**
- Visualização em tabela na página de detalhes
- Estatísticas: Total, Pagas, Pendentes, Vencidas
- Marcar parcela individual como paga (via AJAX)
- Desfazer pagamento de parcela
- Modais interativos com SweetAlert2

**4. Interface e UX**
- Toggle intuitivo: "Parcelas Iguais" ↔ "Parcelas Personalizadas"
- Gerador dinâmico de campos
- Máscara de dinheiro (R$ 1.000,00)
- Conversão automática no submit
- Suporte a tema claro/escuro
- Responsivo (mobile-first)

**5. Recursos Técnicos**
- **Transações DB**: Atomicidade garantida
- **Eager Loading**: Performance otimizada
- **AJAX**: Ações sem reload de página
- **Validações**: Frontend (JavaScript) + Backend (Laravel)
- **Auditoria**: Todas as ações registradas
- **Permissões**: Controle granular por ação

#### **Alertas e Feedback**
- Despesas vencidas (badge vermelho)
- Vencimento próximo (7 dias - badge laranja)
- Status visual por cores
- Mensagens de sucesso/erro com SweetAlert2

#### **Filtros e Pesquisa**
- Filtro por status (pendente, paga)
- Filtro por periodicidade
- Filtro por cartão de crédito
- Filtro por mês/ano
- Estatísticas em cards

### 🔐 Sistema de Autenticação 2FA (Fase 2 - Concluída em 25/10/2025)

#### **Autenticação de Dois Fatores**
- ✅ **Verificação por E-mail**: Códigos de 6 dígitos via SMTP
- ✅ **Verificação por SMS**: Integração com provedores SMS
- ✅ **Backup Codes**: Códigos de recuperação para emergências
- ✅ **Configuração Flexível**: Admin escolhe método padrão por usuário

#### **Painel Administrativo Completo**
- ✅ **Configurações de E-mail**: SMTP configurável via interface
- ✅ **Configurações de SMS**: Múltiplos provedores suportados
- ✅ **Teste Integrado**: Teste de envio direto no painel
- ✅ **Estatísticas**: Monitoramento de códigos enviados/validados

#### **Provedores SMS Customizados** 🇧🇷
**Sistema revolucionário que permite configurar QUALQUER provedor SMS**

**Características:**
- ✅ **Flexibilidade Total**: Configure qualquer API REST
- ✅ **Provedores Brasileiros**: Iagente, ZenviaNow, TotalVoice
- ✅ **Provedores Internacionais**: Twilio, Nexmo, etc
- ✅ **Interface Amigável**: Configure sem tocar no código
- ✅ **Teste em Tempo Real**: Validação antes de ativar

**Configuração Simples:**
```
Nome: Iagente
URL: https://api.iagente.com.br/v1/sms/send
Método: POST
Campo Telefone: to
Campo Mensagem: message
Headers: Authorization: Bearer TOKEN
Indicadores: status: success
```

**Benefícios:**
- 🚫 **Sem Vendor Lock-in**: Mude de provedor quando quiser
- 🇧🇷 **Suporte Nacional**: Use empresas brasileiras
- 💰 **Economia**: Escolha o provedor mais barato
- 🔧 **Manutenção Zero**: Configure uma vez, funciona sempre
- 📊 **Logs Detalhados**: Monitore todos os envios

#### **Recursos Técnicos 2FA**
- **Guzzle HTTP**: Cliente HTTP robusto para APIs SMS
- **Validação Dinâmica**: Headers e campos personalizáveis
- **Rate Limiting**: Proteção contra spam de códigos
- **Auditoria Completa**: Log de todas as tentativas
- **Segurança Avançada**: Códigos com tempo de expiração

### 💳 Sistema de Cartões de Crédito (Fase 3 - Concluída em 02/11/2025)

#### **CRUD Completo de Cartões**
- ✅ **Listagem Inteligente**: Cards visuais com estatísticas em tempo real
- ✅ **Cadastro Avançado**: Validações, máscaras de dinheiro e cálculos automáticos
- ✅ **Visualização Detalhada**: Interface tipo "cartão físico" com informações completas
- ✅ **Edição Flexível**: Atualização com controle de limite automático/manual

#### **Controle de Limite Inteligente**
- ✅ **Cálculo Automático**: Observer atualiza limite em tempo real baseado nas despesas
- ✅ **Modo Manual**: Controle direto do usuário sobre limite disponível
- ✅ **Validações**: Previne limite disponível maior que limite total
- ✅ **Feedback Visual**: Gráficos circulares e barras de progresso do uso

#### **Integração com Despesas**
- ✅ **Vinculação Automática**: Despesas ligadas a cartões específicos
- ✅ **Atualização em Tempo Real**: Observer monitora criação/edição/exclusão de despesas
- ✅ **Histórico de Transações**: Visualização das despesas recentes por cartão
- ✅ **Estatísticas Detalhadas**: Total de despesas, valores pendentes e pagos

#### **Análise de Melhor Dia para Compra**
- ✅ **Cálculo Automático**: Sistema identifica melhor data baseado no fechamento
- ✅ **Configuração Manual**: Usuário pode definir dia preferido
- ✅ **Alertas Visuais**: Destaque do próximo vencimento e dias restantes
- ✅ **Planejamento Financeiro**: Informações para maximizar prazo de pagamento

#### **Interface e UX**
- ✅ **Design Responsivo**: Adaptado para mobile e desktop
- ✅ **Tema Claro/Escuro**: Suporte completo aos dois temas
- ✅ **Máscaras de Dinheiro**: Formatação automática de valores monetários
- ✅ **Alertas Inteligentes**: SweetAlert2 para confirmações e feedback
- ✅ **Navegação Intuitiva**: Breadcrumbs e botões de ação contextuais

#### **Recursos Técnicos**
- **Observer Pattern**: ExpenseObserver para atualização automática de limites
- **Eloquent Relationships**: Relacionamentos otimizados entre cartões e despesas
- **JavaScript Modular**: Máscaras de dinheiro e validações em tempo real
- **Migrations Versionadas**: Campo `auto_calculate_limit` para configuração
- ✅ **Comando Artisan**: `credit-cards:update-limits` para manutenção
- ✅ **Ajustes de Legibilidade (16/11/2025)**: Melhorias em cores de texto para tema escuro

> ⚠️ **Status**: Funcionalidade completa e operacional. Ajustes de legibilidade implementados em 16/11/2025.

### 💰 Sistema de Receitas (Fase 4 - Concluída em 16/11/2025)

#### **CRUD Completo de Receitas**
- ✅ **Listagem Inteligente**: Interface tabular padronizada seguindo o padrão de despesas
- ✅ **Cadastro Avançado**: Formulário com validações completas e máscara de dinheiro centralizada
- ✅ **Visualização Detalhada**: Cards informativos com todas as informações da receita
- ✅ **Edição Refinada**: Formulário corrigido com dados pré-preenchidos e categorias funcionais

#### **Sistema de Categorização**
- ✅ **Categorias Padrão**: Salário, Freelance, Vendas, Investimentos, Aluguel, Comissões, Outros
- ✅ **Tipos de Receita**: 
  - **Fixa**: Receitas regulares e previsíveis (salário, aluguel)
  - **Variável**: Receitas ocasionais e variáveis (freelance, vendas)
- ✅ **Fonte/Origem**: Campo opcional para identificar a fonte da receita
- ✅ **Status de Recebimento**: Pendente (laranja) e Recebida (verde)

#### **Estatísticas em Tempo Real**
- ✅ **Pendentes**: Valor total em receitas pendentes (R$)
- ✅ **Recebidas no Mês**: Total recebido no mês atual (R$)
- ✅ **Total de Receitas**: Contador total de receitas cadastradas
- ✅ **Receitas Fixas**: Contador de receitas do tipo fixa

#### **Sistema de Filtros e Pesquisa**
- ✅ **Filtro por Status**: Todos, Pendentes, Recebidas
- ✅ **Filtro por Tipo**: Todos, Receita Fixa, Receita Variável
- ✅ **Filtro por Categoria**: Todas as categorias disponíveis
- ✅ **Filtro por Período**: Seleção de mês/ano específico
- ✅ **Botões de Ação**: Pesquisar (azul) e Limpar filtros (amarelo)

#### **Interface e Experiência do Usuário**
- ✅ **Design Consistente**: Segue exatamente o padrão visual de despesas
- ✅ **Tabela Responsiva**: Colunas ocultas em mobile, informações adaptadas
- ✅ **Ações Contextuais**: Visualizar, Editar e Excluir com ícones intuitivos
- ✅ **Validações em Tempo Real**: Máscaras de dinheiro centralizadas via `money-mask.js`
- ✅ **Feedback Visual**: SweetAlert2 integrado para exclusão elegante
- ✅ **Tema Claro/Escuro**: Ajustes de legibilidade em cores de texto

#### **Recursos Técnicos**
- **Eloquent Scopes**: `forUser()`, `byStatus()`, `byCategory()`, `byType()`, `currentMonth()`
- **Request Validation**: `IncomeRequest` com validações completas
- **Seeder Inteligente**: `IncomeSeeder` com dados realísticos dos últimos 6 meses
- **Formatação Automática**: Accessors para valores monetários formatados
- **Relacionamentos**: Receitas vinculadas a usuários com controle de acesso
- **JavaScript Modular**: Scripts centralizados para máscaras de dinheiro

#### **Refinamentos Técnicos (16/11/2025)**
- ✅ **Formulário de Edição**: Corrigido problema com `getDefaultCategories()` substituído por `$categories`
- ✅ **Máscaras de Dinheiro**: Centralizadas no `money-mask.js`, removidos scripts duplicados
- ✅ **SweetAlert2**: Implementado para exclusão de receitas com modais elegantes
- ✅ **Legibilidade**: Ajustadas cores de texto para tema escuro em cartões
- ✅ **Testes**: Corrigidos 2 testes com falhas, todos os 9 testes agora passam

#### **Dados de Teste Realísticos**
- ✅ **Período Abrangente**: Receitas dos últimos 6 meses + próximos 3 meses
- ✅ **Variedade de Valores**: Baseados na categoria (salário: R$ 2.800-12.000)
- ✅ **Receitas Recorrentes**: Salário e aluguel fixos mensais
- ✅ **Observações Contextuais**: Notas específicas por categoria
- ✅ **Status Inteligente**: 85% das receitas passadas marcadas como recebidas

> ✅ **Status**: Sistema 100% funcional e refinado. Todos os ajustes técnicos implementados em 16/11/2025.

---

## Estrutura de Banco de Dados

### Tabela: `expenses`
```sql
- id, user_id, credit_card_id
- description, amount
- due_date, periodicity, status
- payment_date, num_installments
- reason_not_paid
- timestamps
```

### Tabela: `installments`
```sql
- id, expense_id
- installment_number
- amount, due_date, status
- payment_date, reason_not_paid
- timestamps
```

### Tabela: `incomes`
```sql
- id, user_id
- description, amount
- received_date, category, type
- status, source, notes
- timestamps
```

### Tabela: `credit_cards`
```sql
- id, user_id
- name, bank, last_four_digits
- card_limit, available_limit
- closing_day, due_day, best_purchase_day
- interest_rate, annual_fee
- is_active, auto_calculate_limit
- timestamps
```

**Relacionamentos:**
- 1 Expense → N Installments (cascade delete)
- 1 CreditCard → N Expenses (nullable foreign key)
- 1 User → N CreditCards (user ownership)
- 1 User → N Incomes (user ownership)

---

## Contribuindo

Contribuições são bem-vindas! Sinta-se à vontade para abrir issues ou enviar pull requests.

## Licença

Este projeto está licenciado sob a licença MIT.