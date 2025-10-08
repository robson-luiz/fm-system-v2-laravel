**FM System v2** - Sistema de Gerenciamento Financeiro Pessoal com Laravel 12.

> 💡 **Sobre as Versões**: Esta é a versão 2 do FM System, completamente reescrita com Laravel 12. A versão 1 foi desenvolvida em PHP puro e não está disponível publicamente.

## Requisitos

* PHP 8.2 ou superior - Conferir a versão: php -v
* MySQL 8.0 ou superior - Conferir a versão: mysql --version
* Composer - Conferir a instalação: composer --version
* Node.js 22 ou superior - Conferir a versão: node -v
* GIT - Conferir se está instalado o GIT: git -v

## Como rodar o projeto baixado

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

O sistema utiliza armazenamento local para imagens de usuários e comprovantes.

Para uso em produção, é possível configurar S3 ou outros serviços de armazenamento cloud editando o arquivo `config/filesystems.php`.

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
git clone -b <branch_name> <repository_url> .
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

## Sequência para criar o projeto

Criar o projeto com Laravel
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

Acessar o conteúdo padrão do Laravel
```
http://127.0.0.1:8000
```

Criar Controller com php artisan.
```
php artisan make:controller NomeController
```
```
php artisan make:controller CoursesController
```

Criar View com php artisan.
```
php artisan make:view diretorio.nome-view
```
```
php artisan make:view courses.index
```

Criar migration com php artisan.
```
php artisan make:migration create_nome_table
```
```
php artisan make:migration create_courses_table
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
php artisan make:seeder UserSeeder
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
php artisan make:component alert --view
```

Criar o arquivo de Request com validações para o formulário.
```
php artisan make:request NomeRequest
```
```
php artisan make:request UserRequest
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

Instalar a biblioteca para apresentar o alerta personalizado.
```
npm install sweetalert2
```

Instalar a biblioteca para gerar PDF.
```
composer require barryvdh/laravel-dompdf
```

Instalar a biblioteca para gerar gráfico.
```
npm install chart.js
```

Instalar o pacote de manipulação de imagens.
```
composer require intervention/image
```

Como criar o arquivo de rotas para API no Laravel 11
```
php artisan install:api
```

## Como enviar e baixar os arquivos do GitHub

- Criar o repositório **"fm-system-v2-laravel"** no GitHub.
- Criar o branch **"main"** no repositório.

Baixar os arquivos do Git.
```
git clone -b <branch_name> <repository_url> .
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
git push origin develop
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
git checkout -b main
```

Mudar de branch.
```
git switch <branch>
```
```
git switch main
```

Mesclar o histórico de commits de uma branch em outra branch.
```
git merge <branch_name>
```
```
git merge develop
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

- Gerenciamento de despesas e receitas
- Controle de cartões de crédito
- Wishlist com análise de viabilidade
- Dashboard financeiro com gráficos
- Alertas inteligentes de pagamentos
- Sistema completo de permissões e auditoria

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

**Fase 2 - Cartões de Crédito** 📋 Planejada
- [ ] CRUD de cartões de crédito
- [ ] Vinculação de despesas com cartões
- [ ] Controle de limite e fatura
- [ ] Alerta de melhor dia de compra

**Fase 3 - Receitas e Dashboard** 📋 Planejada
- [ ] CRUD de receitas
- [ ] Dashboard financeiro com gráficos
- [ ] Relatórios de fluxo de caixa

**Fase 4 - Wishlist e Análises** 📋 Planejada
- [ ] Wishlist inteligente
- [ ] Análise de viabilidade de compras
- [ ] Verificação automática de pagamentos
- [ ] Alertas inteligentes

**Fase 5 - Recursos Avançados** 📋 Futuro
- [ ] Integração com IA para análises
- [ ] Open Banking
- [ ] Notificações por e-mail/SMS
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

**Relacionamento:** 1 Expense → N Installments (cascade delete)

---

## Contribuindo

Contribuições são bem-vindas! Sinta-se à vontade para abrir issues ou enviar pull requests.

## Licença

Este projeto está licenciado sob a licença MIT.