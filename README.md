# Gerenciador de Boletos

Aplicação CRUD para gerenciamento de clientes e boletos, desenvolvida como teste técnico.

## Funcionalidades

- Cadastro, edição, listagem e exclusão de clientes.
- Cadastro, edição, listagem e exclusão de boletos.
- Um cliente pode possuir vários boletos; cada boleto pertence a um cliente.
- Busca de clientes, busca de boletos e filtros por status e vencimento.
- Alteração rápida entre os status `pendente` e `pago`.
- Validações para CPF/CNPJ, valor decimal maior que zero e vencimento igual ou posterior à data atual.
- Confirmação antes de excluir clientes ou boletos.

## Tecnologias

- Backend: PHP 8.2 e Laravel 12
- Frontend: Vue 3, TypeScript e Vite
- Banco de dados: SQLite (desenvolvimento) ou MySQL

## Estrutura do projeto

```text
backend/   # API Laravel
Frontend/  # Interface Vue
```

## Como executar

### Backend

Entre na pasta do backend:

```bash
cd backend
```

Instale as dependências e configure o ambiente:

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

A API estará disponível em `http://127.0.0.1:8000`.

### Frontend

Em outro terminal, entre na pasta do frontend:

```bash
cd Frontend
npm install
npm run dev
```

O frontend será iniciado no endereço informado pelo Vite, normalmente `http://localhost:5173`.


## Rotas principais da API

| Método | Rota | Descrição |
| --- | --- | --- |
| GET | `/api/clientes` | Lista clientes |
| POST | `/api/clientes` | Cria cliente |
| PUT | `/api/clientes/{id}` | Atualiza cliente |
| DELETE | `/api/clientes/{id}` | Exclui cliente |
| GET | `/api/boletos` | Lista boletos |
| POST | `/api/boletos` | Cria boleto |
| PUT | `/api/boletos/{id}` | Atualiza boleto |
| DELETE | `/api/boletos/{id}` | Exclui boleto |
