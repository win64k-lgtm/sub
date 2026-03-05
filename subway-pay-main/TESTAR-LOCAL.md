# Como testar o projeto localmente

## 1. Ter PHP e extensão PostgreSQL

- **PHP** 7.4 ou superior instalado.
- Extensão **pdo_pgsql** habilitada.

Para verificar no terminal:

```bash
php -v
php -m | findstr pdo_pgsql
```

Se `pdo_pgsql` não aparecer, ative no `php.ini` (remova o `;` da linha):

```ini
;extension=pdo_pgsql
extension=pdo_pgsql
```

---

## 2. Usar o banco do Railway (mais simples)

Você não precisa instalar PostgreSQL na sua máquina. O código já está configurado para o Railway.

1. Entre na pasta do projeto:
   ```bash
   cd subway-pay-main
   ```

2. (Opcional) Crie um `.env` com a conexão do Railway (senha já está no fallback do código):
   ```env
   PGHOST=tramway.proxy.rlwy.net
   PGPORT=55414
   PGUSER=postgres
   PGPASSWORD=fkAuDOJNrSGZAwkLbtDoURsmgkiVFIYC
   PGDATABASE=railway
   ```

3. Suba o servidor embutido do PHP:
   ```bash
   php -S localhost:8000
   ```

4. No navegador: **http://localhost:8000**

Assim você testa localmente usando o mesmo banco do Railway.

---

## 3. Usar PostgreSQL na sua máquina (opcional)

Se quiser um banco só para desenvolvimento local:

1. Instale o PostgreSQL (ex.: [postgresql.org](https://www.postgresql.org/download/windows/) ou via Chocolatey: `choco install postgresql`).

2. Crie um banco (ex.: `railway`) e importe a estrutura/dados que você usa no Railway.

3. Crie o arquivo `.env` na pasta `subway-pay-main`:
   ```env
   PGHOST=127.0.0.1
   PGPORT=5432
   PGUSER=postgres
   PGPASSWORD=sua_senha_do_postgres_local
   PGDATABASE=railway
   PGSSLMODE=disable
   ```
   (`PGSSLMODE=disable` é necessário para PostgreSQL local sem SSL.)

4. Rode o PHP:
   ```bash
   cd subway-pay-main
   php -S localhost:8000
   ```

5. Acesse **http://localhost:8000**.

---

## 4. Resumo rápido

| O que você quer              | O que fazer |
|-----------------------------|-------------|
| Só testar sem instalar DB   | Usar banco do Railway + `php -S localhost:8000` |
| Testar com banco local      | Instalar PostgreSQL, criar banco, configurar `.env` com `PGHOST=127.0.0.1` e rodar `php -S localhost:8000` |

O `conectarbanco.php` lê as variáveis do `.env`; se não existir `.env`, usa os valores padrão do Railway.
