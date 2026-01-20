# Audiophile Laravel API

API em Laravel 12 com autenticação via Laravel Sanctum (modo cookie/sessão), focada em servir um frontend (SPA) com endpoints JSON.

## Stack

- PHP 8.2+
- Laravel 12
- Laravel Sanctum
- MySQL por padrão (`DB_CONNECTION=mysql`)
- Session/Queue/Cache em `database`

## Setup

Pré-requisitos: PHP 8.2+, Composer, Node.js/npm.

1. Instale dependências e gere o `.env`:
   - `composer install`
   - `copy .env.example .env` (Windows) ou `cp .env.example .env`
2. Gere a key e rode as migrations:
   - `php artisan key:generate`
   - `php artisan migrate`
3. Suba o servidor:
   - `php artisan serve`

## Base URL

- API: `http://127.0.0.1:8000/api`

## Autenticação (Sanctum cookie/sessão)

Os endpoints protegidos usam `auth:sanctum` e dependem de sessão/cookies.

Para testar no Postman:
- `Headers`: `Accept: application/json` e `Content-Type: application/json`
- `Body`: `raw` → `JSON`
- Mantenha os cookies habilitados (o Postman guarda automaticamente por host)

Se você receber erro de CSRF em ambiente SPA, faça antes:
- `GET http://127.0.0.1:8000/sanctum/csrf-cookie`
e então repita o `POST` enviando o header `X-XSRF-TOKEN` (o Postman consegue ler do cookie `XSRF-TOKEN`).

## Endpoints atuais

### Health check

- `GET /api` → `{ "name": "Audiophile API", "status": "ok" }`
- `GET /api/health` → `{ "status": "ok", "timestamp": "..." }`

### Auth

- `POST /api/auth/register` (rate limit: `throttle:register`)
  - Body:
    - `name` (required)
    - `email` (required)
    - `password` (required, min 8)
    - `password_confirmation` (required)
    - `phone` (optional)
  - Observação: o usuário é autenticado automaticamente após o cadastro.

- `POST /api/auth/login` (rate limit: `throttle:login`)
  - Body:
    - `email` (required)
    - `password` (required)

- `GET /api/auth/me` (middleware: `auth:sanctum`)
  - Retorna o usuário autenticado.

- `POST /api/auth/logout` (middleware: `auth:sanctum`)
  - Encerra a sessão atual.

## Exemplos (curl)

Register:

`curl -i http://127.0.0.1:8000/api/auth/register -H "Accept: application/json" -H "Content-Type: application/json" -d "{\"name\":\"Joao\",\"email\":\"Joao@teste.com\",\"password\":\"Senha@1234\",\"password_confirmation\":\"Senha@1234\"}"`

Login:

`curl -i http://127.0.0.1:8000/api/auth/login -H "Accept: application/json" -H "Content-Type: application/json" -c cookies.txt -b cookies.txt -d "{\"email\":\"joao@teste.com\",\"password\":\"Senha@1234\"}"`

Me (após login, reutilizando cookies):

`curl -i http://127.0.0.1:8000/api/auth/me -H "Accept: application/json" -c cookies.txt -b cookies.txt`

## Rate limiting

Configurado em `app/Providers/AppServiceProvider.php`:
- `api`: 60 req/min (por user id autenticado ou IP)
- `login`: 5 req/min (por IP + email)
- `register`: 3 req/min (por IP)

## Modelagem de usuário

Migration em `database/migrations/0001_01_01_000000_create_users_table.php`:
- `users.role`: `customer` | `admin` (default: `customer`)
- `users.is_active`: boolean (default: `true`)
- `users.phone`: nullable

## Testes

- Rodar testes: `composer test` ou `php artisan test`

## Documentação da API (recomendado)

- Laravel Scribe (`knuckleswtf/scribe`): gera docs HTML + Postman/OpenAPI a partir de rotas/Requests/PHPDoc.
- OpenAPI/Swagger: `DarkaOnLine/L5-Swagger` (bom se você quer contrato OpenAPI primeiro).
- Postman: mantenha uma Collection versionada em `docs/postman/` (simples e útil em entrevista).

## Próximos passos sugeridos

- CRUD de produtos/usuários/pedidos com validação via Form Requests e autorização via Policies.
- Testes de feature para fluxos principais (register/login/me/logout, criação de recurso protegida).
