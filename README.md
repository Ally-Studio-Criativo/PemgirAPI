# PEMGIR - Sistema de Gestão de Catálogo de Produtos

Sistema web integrado para gerenciamento de catálogo de produtos da Pemgir Malhas, composto por landing page pública e painel administrativo.

## Tecnologias Utilizadas

### Backend
- **Laravel 11** - Framework PHP
- **MySQL 8.0** - Banco de dados
- **Laravel Sail** - Ambiente Docker para desenvolvimento
- **Sanctum** - Autenticação API

### Frontend
- **Quasar Framework 2.x** - Framework Vue.js 3 com Composition API
- **Vue Router** - Gerenciamento de rotas
- **Vue I18n** - Sistema de tradução (PT, EN, ES)
- **Axios** - Cliente HTTP para API

### Infraestrutura
- **Docker** - Containerização (ambiente de desenvolvimento)
- **Vite** - Build tool e bundler

## Estrutura do Projeto

```
PemgirAPI/
├── app/                    # Código Laravel (Controllers, Models)
├── database/              # Migrations e Seeders
├── routes/                # Rotas da API
├── resources/
│   └── app/              # Aplicação Quasar (Frontend)
│       ├── src/
│       │   ├── pages/   # Páginas Vue
│       │   ├── layouts/ # Layouts
│       │   └── router/  # Configuração de rotas
│       └── dist/        # Build de produção (gerado)
└── public/              # Assets públicos
```

## Rotas do Sistema

### Landing Page (Público)

| Rota | Componente | Descrição |
|------|-----------|-----------|
| `/` | IndexPage.vue | Página inicial com produtos em destaque |
| `/produtos` | Products.vue | Listagem de produtos por categoria |
| `/produtos/:productId` | Products.vue | Detalhes de um produto específico |

### Painel Administrativo (Autenticado)

| Rota | Componente | Descrição |
|------|-----------|-----------|
| `/acessar` | LoginPage.vue | Autenticação de usuários |
| `/admin` | DashboardPage.vue | Dashboard principal |
| `/admin/usuarios` | UsersPage.vue | Gestão de usuários |
| `/admin/categorias` | CategoriesPage.vue | Gestão de categorias |
| `/admin/produtos` | ProductsPage.vue | Gestão de produtos e imagens |
| `/admin/cores` | ColorsPage.vue | Gestão de cores |
| `/admin/paletas` | PalettesPage.vue | Gestão de paletas de cores |
| `/admin/campanhas` | CampaignsPage.vue | Gestão de campanhas de lançamento |

### API Endpoints

#### Públicos (sem autenticação)

```
GET  /api/v1/categories              # Lista categorias
GET  /api/v1/categories/{id}         # Detalhes de categoria
GET  /api/v1/products                # Lista produtos (filtro por category_id)
GET  /api/v1/products/{id}           # Detalhes de produto com imagens
GET  /api/v1/palettes                # Lista paletas de cores
GET  /api/v1/colors                  # Lista cores (filtro por palette_id)
GET  /api/v1/campaign/active         # Campanha ativa para landing page
POST /api/v1/login                   # Autenticação
```

#### Protegidos (requer Bearer Token)

```
# Usuários
GET    /api/v1/users
POST   /api/v1/users
PUT    /api/v1/users/{id}
DELETE /api/v1/users/{id}

# Categorias
POST   /api/v1/categories
PUT    /api/v1/categories/{id}
DELETE /api/v1/categories/{id}

# Produtos
POST   /api/v1/products
PUT    /api/v1/products/{id}
DELETE /api/v1/products/{id}

# Imagens de Produtos
GET    /api/v1/products/{id}/images
POST   /api/v1/products/{id}/images        # Upload de imagem
PUT    /api/v1/products/{id}/images/{img}  # Atualizar metadados
POST   /api/v1/products/{id}/images/reorder # Reordenar imagens
DELETE /api/v1/products/{id}/images/{img}

# Paletas e Cores
POST   /api/v1/palettes
PUT    /api/v1/palettes/{id}
DELETE /api/v1/palettes/{id}
POST   /api/v1/colors
PUT    /api/v1/colors/{id}
DELETE /api/v1/colors/{id}

# Campanhas
GET    /api/v1/campaigns               # Lista todas
POST   /api/v1/campaigns               # Criar nova
PUT    /api/v1/campaigns/{id}          # Atualizar
DELETE /api/v1/campaigns/{id}          # Deletar
```

## Comandos de Migração e População

Execute os comandos na ordem especificada:

### 1. Criar estrutura do banco de dados

```bash
php artisan migrate
```

### 2. Criar usuário administrador

```bash
php artisan db:seed --class=UserSeeder
```

Credenciais padrão: `admin@pemgir.com` / `password`

### 3. Popular paletas de cores (antes de cores)

```bash
php artisan db:seed --class=PaletteSeeder
```

### 4. Popular categorias (antes de produtos)

```bash
php artisan db:seed --class=CategorySeeder
```

### 5. Migrar cores do JSON para banco de dados

```bash
php artisan migrate:colors
```

Este comando:
- Lê cores do arquivo JSON em `/public/paleta/`
- Move imagens para `/storage/app/public/images/colors/`
- Cria registros na tabela `colors`

### 6. Migrar produtos do JSON para banco de dados

```bash
php artisan migrate:products
```

Este comando:
- Lê produtos do arquivo JSON
- Move imagens para `/storage/app/public/images/products/`
- Cria registros nas tabelas `products` e `product_images`
- **Requer que categorias já existam no banco**

### 7. Definir produtos em destaque por categoria

```bash
php artisan categories:set-featured
```

Define o primeiro produto de cada categoria como destaque na landing page.

### 8. Popular campanha de lançamento

```bash
php artisan db:seed --class=CampaignSeeder
```

### 9. Criar link simbólico do storage

```bash
php artisan storage:link
```

## Deploy em Hospedagem Compartilhada

### Pré-requisitos

- PHP 8.2 ou superior
- MySQL 8.0 ou superior
- Composer
- Node.js 18+ e NPM (para build do frontend)
- Acesso SSH (recomendado) ou painel de controle com terminal

### Passo 1: Build do Frontend

No ambiente de desenvolvimento:

```bash
cd resources/app
npm install
npm run build
```

Isso gera os arquivos otimizados em `resources/app/dist/`.

### Passo 2: Estrutura de Diretórios no Servidor

Configure a estrutura conforme o padrão da hospedagem:

```
/home/usuario/
├── public_html/              # Raiz pública do domínio
│   └── (arquivos do frontend)
└── pemgir-api/              # Fora da pasta pública (segurança)
    ├── app/
    ├── bootstrap/
    ├── config/
    ├── database/
    ├── routes/
    ├── storage/
    └── vendor/
```

### Passo 3: Upload dos Arquivos

1. **Backend Laravel**: Envie todo o projeto Laravel para `/home/usuario/pemgir-api/`
2. **Frontend Build**: Copie o conteúdo de `resources/app/dist/` para `/home/usuario/public_html/`
3. **Assets do Backend**: Copie `public/` do Laravel para `/home/usuario/public_html/api/`

### Passo 4: Configurar .htaccess na Raiz Pública

Crie `/home/usuario/public_html/.htaccess`:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On

    # Redirecionar requisições da API para o index.php do Laravel
    RewriteCond %{REQUEST_URI} ^/api/
    RewriteRule ^api/(.*)$ /api/index.php [L]

    # Redirecionar todas as outras rotas para o index.html do Quasar
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ /index.html [L]
</IfModule>
```

### Passo 5: Configurar .htaccess para a API

Crie `/home/usuario/public_html/api/.htaccess`:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On

    # Redirecionar para o public do Laravel (fora da pasta pública)
    RewriteRule ^(.*)$ /home/usuario/pemgir-api/public/index.php [L]
</IfModule>
```

### Passo 6: Configurar Variáveis de Ambiente

Edite `/home/usuario/pemgir-api/.env`:

```env
APP_NAME="Pemgir Textil"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seudominio.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=nome_do_banco
DB_USERNAME=usuario_banco
DB_PASSWORD=senha_banco

# URL base da API para o frontend
API_URL=https://seudominio.com/api/v1
API_URL_IMG=https://seudominio.com/api
```

### Passo 7: Instalar Dependências e Otimizar

Via SSH:

```bash
cd /home/usuario/pemgir-api

# Instalar dependências do Composer
composer install --optimize-autoloader --no-dev

# Gerar chave da aplicação
php artisan key:generate

# Executar migrations
php artisan migrate --force

# Popular banco de dados (seguir ordem da seção "Comandos de Migração")
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=PaletteSeeder
php artisan db:seed --class=CategorySeeder
php artisan migrate:colors
php artisan migrate:products
php artisan categories:set-featured
php artisan db:seed --class=CampaignSeeder

# Criar link simbólico do storage
php artisan storage:link

# Cachear configurações
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Passo 8: Ajustar Permissões

```bash
chmod -R 755 /home/usuario/pemgir-api
chmod -R 775 /home/usuario/pemgir-api/storage
chmod -R 775 /home/usuario/pemgir-api/bootstrap/cache
```

### Passo 9: Configurar Variáveis no Frontend

Se necessário, edite `/home/usuario/public_html/index.html` e ajuste as variáveis de ambiente embutidas no build.

Alternativamente, reconstrua o frontend com as variáveis corretas antes do upload:

```bash
# No ambiente de desenvolvimento
cd resources/app
echo "API_URL=https://seudominio.com/api/v1" > .env.production
echo "API_URL_IMG=https://seudominio.com/api" >> .env.production
npm run build
```

### Limpar Cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Logs

Logs da aplicação: `/home/usuario/pemgir-api/storage/logs/laravel.log`

## Solução de Problemas

### Erro 500 ao acessar API

- Verifique permissões das pastas `storage/` e `bootstrap/cache/`
- Confirme que o `.env` está configurado corretamente
- Verifique logs em `storage/logs/laravel.log`

### Imagens não aparecem

- Confirme que `php artisan storage:link` foi executado
- Verifique se o caminho no `.env` (`API_URL_IMG`) está correto
- Confirme permissões da pasta `storage/app/public/`

### Rotas do frontend retornam 404

- Verifique se o `.htaccess` está configurado corretamente
- Confirme que `mod_rewrite` está habilitado no Apache
- Teste se `index.html` está na raiz de `public_html/`

### API retorna HTML ao invés de JSON

- Confirme que as requisições incluem o header `Accept: application/json`
- Verifique se o `.htaccess` da API está redirecionando corretamente
- Confirme que o caminho no RewriteRule aponta para o `index.php` correto

## Suporte Técnico

### Para entrar em contato com o desenvolvedor:
 - WhatsApp: 47 9 9196-0964 (João)
 - WhatsApp  47 8807-2794 (Aline)
