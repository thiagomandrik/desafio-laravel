# Desafio Backend — Places API

Uma API JSON simples para gerenciar **lugares** (CRUD), construída com Laravel 12 e PostgreSQL, como parte do desafio de desenvolvedor backend da SGBr.

## Stack

- PHP 8.4 / Laravel 12
- PostgreSQL 16
- Docker & Docker Compose
- L5-Swagger (documentação OpenAPI)
- PHPUnit (testes de feature)

## Requisitos

- Docker
- Docker Compose

Não é necessário instalar PHP/Composer/PostgreSQL localmente — tudo roda dentro dos containers.

## Como executar

```bash
git clone https://github.com/thiagomandrik/desafio-laravel.git
cd desafio-laravel
cp .env.example .env
docker compose up -d --build
```

Na primeira inicialização, o entrypoint do container `app` faz automaticamente:

1. Instala as dependências do Composer (se `vendor/` não existir);
2. Copia `.env.example` para `.env` (se não existir) e gera a `APP_KEY`;
3. Roda as migrations (`php artisan migrate --force`);
4. Gera a documentação Swagger/OpenAPI;
5. Sobe a API em `http://localhost:8000`.

O serviço `pgsql` cria dois bancos na primeira execução: `desafio_laravel` (aplicação) e `desafio_laravel_testing` (testes), via `docker/postgres/init-testing-db.sh`.

Para conferir se está tudo no ar:

```bash
curl http://localhost:8000/api/places
# {"data":[],"links":{...},"meta":{...}}
```

Para parar o ambiente:

```bash
docker compose down
```

## Rodando os testes

```bash
docker compose exec app php artisan test
```

A suíte usa `RefreshDatabase` contra o banco `desafio_laravel_testing` (ver `phpunit.xml`) e cobre criação, listagem/filtro, exibição, atualização e remoção — incluindo erros de validação e o comportamento de soft delete.

## Documentação da API (Swagger)

A documentação interativa OpenAPI fica disponível com a aplicação no ar:

```
http://localhost:8000/docs
```

## Endpoints

Todas as requisições/respostas usam `application/json`. URL base: `http://localhost:8000/api`.

Os filtros do `GET /places` também são validados (`name` até 255 caracteres, `page` inteiro ≥ 1); valores inválidos retornam `422`.

| Método | Endpoint            | Descrição                                              |
|--------|---------------------|----------------------------------------------------------|
| GET    | `/places`           | Lista lugares (paginado, 15 por página)                  |
| GET    | `/places?name=...`  | Lista lugares, filtrando por nome (parcial, case-insensitive) |
| GET    | `/places/{id}`      | Exibe um lugar específico                                |
| POST   | `/places`           | Cria um lugar                                            |
| PUT    | `/places/{id}`      | Atualiza um lugar                                        |
| DELETE | `/places/{id}`      | Remove um lugar (soft delete)                            |

### Campos de um lugar

| Campo        | Tipo   | Observações                                                 |
|--------------|--------|---------------------------------------------------------------|
| `id`         | int    | Gerado automaticamente                                        |
| `name`       | string | Obrigatório                                                   |
| `slug`       | string | Gerado automaticamente a partir do `name` (kebab-case, único; não é enviado pelo cliente). Se o nome já existir, recebe um sufixo incremental (`praia-mole`, `praia-mole-2`, ...). Não muda em atualizações posteriores do `name`. |
| `city`       | string | Obrigatório                                                   |
| `state`      | string | Obrigatório, sigla de UF com 2 letras (ex.: `SC`)              |
| `created_at` | string | Timestamp ISO 8601                                             |
| `updated_at` | string | Timestamp ISO 8601                                             |
| `deleted_at` | string | Timestamp ISO 8601, `null` a menos que tenha sido removido     |

### Exemplos

**Criar um lugar**

```bash
curl -X POST http://localhost:8000/api/places \
  -H "Content-Type: application/json" \
  -d '{"name":"Praia Mole","city":"Florianópolis","state":"SC"}'
```

```json
{
  "data": {
    "id": 1,
    "name": "Praia Mole",
    "slug": "praia-mole",
    "city": "Florianópolis",
    "state": "SC",
    "created_at": "2026-07-14T12:00:00.000000Z",
    "updated_at": "2026-07-14T12:00:00.000000Z",
    "deleted_at": null
  }
}
```

**Listar / filtrar por nome**

```bash
curl "http://localhost:8000/api/places?name=mole"
```

**Exibir um lugar**

```bash
curl http://localhost:8000/api/places/1
```

**Atualizar um lugar**

```bash
curl -X PUT http://localhost:8000/api/places/1 \
  -H "Content-Type: application/json" \
  -d '{"name":"Praia Mole","city":"Florianópolis","state":"SC"}'
```

**Remover um lugar**

```bash
curl -X DELETE http://localhost:8000/api/places/1
```

Um erro de validação (campos ausentes/inválidos, por exemplo) retorna `422` com um objeto `errors`:

```json
{
  "message": "The name field is required. (and 1 more error)",
  "errors": {
    "name": ["The name field is required."],
    "state": ["The selected state is invalid."]
  }
}
```

Um recurso inexistente, ou uma rota/id inválido (ex.: `/api/places/abc`), retorna `404` com uma mensagem limpa e consistente:

```json
{
  "message": "Recurso não encontrado."
}
```

## Arquitetura

- **`PlaceController`** só traduz HTTP: recebe a request, delega pro service, devolve o resource/status code, sem regra de negócio.
- **`PlaceService`** (`app/Services/PlaceService.php`) concentra a orquestração do CRUD
- **`Place` (model)** aplica a regra de geração do slug (evento `creating`), garantindo que ela valha em qualquer ponto de entrada (API, seeders, tinker), não só no controller.
- **`ApiExceptionRenderer`** (`app/Exceptions/ApiExceptionRenderer.php`), registrado em `bootstrap/app.php`, padroniza as respostas de erro da API: 404 limpo e um fallback genérico para qualquer exceção inesperada, sem stack trace.


