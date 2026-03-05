-- PostgreSQL Schema for Subway Pay
-- Converted from MySQL
-- Database: subway-pay

-- Table: admlogin
CREATE TABLE IF NOT EXISTS "admlogin" (
  "email" VARCHAR(255) NOT NULL,
  "senha" VARCHAR(255) NOT NULL
);

CREATE INDEX "idx_email_admlogin" ON "admlogin" ("email");

-- Table: app
CREATE TABLE IF NOT EXISTS "app" (
  "token" VARCHAR(255) NOT NULL PRIMARY KEY,
  "depositos" VARCHAR(255) NOT NULL,
  "saques" VARCHAR(255) NOT NULL,
  "usuarios" VARCHAR(255) NOT NULL,
  "faturamento_total" VARCHAR(255) NOT NULL,
  "cadastros" VARCHAR(255) NOT NULL,
  "saques_valor" VARCHAR(255) NOT NULL,
  "deposito_min" VARCHAR(255) NOT NULL,
  "saques_min" VARCHAR(255) NOT NULL,
  "aposta_max" VARCHAR(255) NOT NULL,
  "dificuldade_jogo" VARCHAR(255) NOT NULL,
  "aposta_min" VARCHAR(255) NOT NULL,
  "rollover_saque" VARCHAR(255) NOT NULL,
  "taxa_saque" VARCHAR(255) NOT NULL,
  "google_ads_tag" VARCHAR(255) NOT NULL,
  "facebook_ads_tag" VARCHAR(255) NOT NULL,
  "cpa" VARCHAR(255) NOT NULL,
  "deposito_min_cpa" VARCHAR(255) NOT NULL,
  "revenue_share_falso" VARCHAR(255) NOT NULL,
  "max_saque_cpa" VARCHAR(255) NOT NULL,
  "max_por_saque_cpa" VARCHAR(255) NOT NULL,
  "revenue_share" VARCHAR(255) NOT NULL,
  "chance_afiliado" VARCHAR(255) NOT NULL,
  "nome_unico" VARCHAR(255) NOT NULL,
  "nome_um" VARCHAR(255) NOT NULL,
  "nome_dois" VARCHAR(255) NOT NULL
);

-- Table: appconfig
CREATE TABLE IF NOT EXISTS "appconfig" (
  "id" VARCHAR(255) NOT NULL PRIMARY KEY,
  "nome" VARCHAR(255),
  "email" VARCHAR(255) NOT NULL,
  "senha" VARCHAR(255) NOT NULL,
  "cpf" VARCHAR(255),
  "telefone" VARCHAR(255) NOT NULL,
  "saldo" VARCHAR(255) NOT NULL,
  "jogoteste" VARCHAR(255),
  "linkafiliado" VARCHAR(255) NOT NULL,
  "depositou" VARCHAR(255) DEFAULT '0',
  "lead_aff" VARCHAR(255),
  "leads_ativos" VARCHAR(255) DEFAULT '0',
  "rollover1" VARCHAR(255) DEFAULT '0',
  "plano" VARCHAR(255) NOT NULL,
  "demo" VARCHAR(255) DEFAULT '0',
  "bloc" VARCHAR(255) DEFAULT '0',
  "sacou" VARCHAR(255) DEFAULT '0',
  "indicados" VARCHAR(255) NOT NULL DEFAULT '0',
  "saldo_comissao" VARCHAR(255) DEFAULT '0',
  "percas" VARCHAR(255) DEFAULT '0',
  "ganhos" VARCHAR(255) DEFAULT '0',
  "cpa" VARCHAR(255) NOT NULL,
  "cpafake" VARCHAR(255) DEFAULT '0',
  "jogo_demo" VARCHAR(255) DEFAULT '0',
  "comissaofake" VARCHAR(255) DEFAULT '0',
  "saldo_cpa" VARCHAR(255) DEFAULT '0',
  "primeiro_deposito" VARCHAR(255) DEFAULT '0',
  "status_primeiro_deposito" VARCHAR(255) DEFAULT '0',
  "cont_cpa" VARCHAR(255) DEFAULT '0',
  "data_cadastro" VARCHAR(255) NOT NULL,
  "afiliado" VARCHAR(255) NOT NULL
);

CREATE INDEX "idx_lead_aff" ON "appconfig" ("lead_aff");

-- Table: confirmar_deposito
CREATE TABLE IF NOT EXISTS "confirmar_deposito" (
  "email" VARCHAR(255) NOT NULL,
  "externalreference" VARCHAR(255) NOT NULL,
  "valor" VARCHAR(255) NOT NULL,
  "status" VARCHAR(255) NOT NULL,
  "data" VARCHAR(255) NOT NULL
);

CREATE INDEX "idx_email_confirmar" ON "confirmar_deposito" ("email");
CREATE INDEX "idx_externalreference_confirmar" ON "confirmar_deposito" ("externalreference");

-- Table: game
CREATE TABLE IF NOT EXISTS "game" (
  "email" VARCHAR(255) NOT NULL,
  "entry_value" VARCHAR(255) NOT NULL,
  "out_value" VARCHAR(255) NOT NULL
);

CREATE INDEX "idx_email_game" ON "game" ("email");

-- Table: gateway
CREATE TABLE IF NOT EXISTS "gateway" (
  "id" SERIAL PRIMARY KEY,
  "client_id" VARCHAR(255) NOT NULL,
  "client_secret" VARCHAR(255) NOT NULL
);

-- Table: ggr
CREATE TABLE IF NOT EXISTS "ggr" (
  "token" VARCHAR(255) NOT NULL PRIMARY KEY,
  "ggr_taxa" VARCHAR(255) NOT NULL,
  "data" VARCHAR(255) NOT NULL,
  "situacao" VARCHAR(255) NOT NULL,
  "total_ganhos" VARCHAR(255) NOT NULL,
  "percas_24h" VARCHAR(255) NOT NULL,
  "percas_1m" VARCHAR(255) NOT NULL,
  "total_percas" VARCHAR(255) NOT NULL,
  "ggr_24h" VARCHAR(255) NOT NULL,
  "ggr_1m" VARCHAR(255) NOT NULL,
  "credito_ggr" VARCHAR(255) NOT NULL,
  "debito_ggr" VARCHAR(255) NOT NULL,
  "ggr_pago" VARCHAR(255) NOT NULL,
  "status_ggr" VARCHAR(255) NOT NULL,
  "ggr_total" VARCHAR(255) NOT NULL,
  "saldo_inserido" VARCHAR(255) NOT NULL,
  "senha" VARCHAR(255) NOT NULL
);

CREATE INDEX "idx_data_ggr" ON "ggr" ("data");

-- Table: pix
CREATE TABLE IF NOT EXISTS "pix" (
  "id" SERIAL PRIMARY KEY,
  "value" VARCHAR(255) NOT NULL,
  "email" VARCHAR(255) NOT NULL,
  "code" VARCHAR(255) NOT NULL,
  "status" VARCHAR(255) NOT NULL,
  "data" VARCHAR(255) NOT NULL
);

-- Table: pix_deposito
CREATE TABLE IF NOT EXISTS "pix_deposito" (
  "id" VARCHAR(255) NOT NULL PRIMARY KEY,
  "value" VARCHAR(255) NOT NULL,
  "email" VARCHAR(255) NOT NULL,
  "code" VARCHAR(255) NOT NULL,
  "status" VARCHAR(255) NOT NULL,
  "data" VARCHAR(255) NOT NULL
);

CREATE INDEX "idx_email_pix_deposito" ON "pix_deposito" ("email");

-- Table: planos
CREATE TABLE IF NOT EXISTS "planos" (
  "nome" VARCHAR(255) NOT NULL PRIMARY KEY,
  "cpa" VARCHAR(255) NOT NULL,
  "rev" VARCHAR(255) NOT NULL,
  "indicacao" VARCHAR(255) NOT NULL,
  "valor_saque_maximo" VARCHAR(255) NOT NULL,
  "saque_diario" VARCHAR(255) NOT NULL,
  "data" VARCHAR(255) NOT NULL,
  "situacao" VARCHAR(255) NOT NULL,
  "senha" VARCHAR(255) NOT NULL
);

CREATE INDEX "idx_data_planos" ON "planos" ("data");

-- Table: saques
CREATE TABLE IF NOT EXISTS "saques" (
  "email" VARCHAR(255),
  "externalreference" VARCHAR(255),
  "destino" VARCHAR(255),
  "chavepix" VARCHAR(255),
  "data" VARCHAR(255),
  "valor" VARCHAR(255),
  "status" VARCHAR(255)
);

CREATE INDEX "idx_email_saques" ON "saques" ("email");
CREATE INDEX "idx_externalreference_saques" ON "saques" ("externalreference");

-- Table: saque_afiliado
CREATE TABLE IF NOT EXISTS "saque_afiliado" (
  "email" VARCHAR(255) NOT NULL,
  "nome" VARCHAR(255) NOT NULL,
  "pix" VARCHAR(255) NOT NULL,
  "valor" VARCHAR(255) NOT NULL,
  "status" VARCHAR(255) NOT NULL
);

-- Table: token
CREATE TABLE IF NOT EXISTS "token" (
  "email" VARCHAR(255) NOT NULL,
  "value" VARCHAR(255) NOT NULL
);

CREATE INDEX "idx_email_token" ON "token" ("email");

-- Insert initial data
INSERT INTO "admlogin" ("email", "senha") VALUES ('admin@admin.com', 'admin123@');

INSERT INTO "app" ("token", "depositos", "saques", "usuarios", "faturamento_total", "cadastros", "saques_valor", "deposito_min", "saques_min", "aposta_max", "dificuldade_jogo", "aposta_min", "rollover_saque", "taxa_saque", "google_ads_tag", "facebook_ads_tag", "cpa", "deposito_min_cpa", "revenue_share_falso", "max_saque_cpa", "max_por_saque_cpa", "revenue_share", "chance_afiliado", "nome_unico", "nome_um", "nome_dois") 
VALUES ('', '', '', '', '', '', '', '20', '100', '1', 'medio', '1', '10', '5', '', '123213213', '5', '20', '20', '100', '100', '0', '80', 'SubwayTeste', 'SUBWAY', 'TESTE');

INSERT INTO "appconfig" ("id", "nome", "email", "senha", "cpf", "telefone", "saldo", "jogoteste", "linkafiliado", "depositou", "lead_aff", "leads_ativos", "rollover1", "plano", "demo", "bloc", "sacou", "indicados", "saldo_comissao", "percas", "ganhos", "cpa", "cpafake", "jogo_demo", "comissaofake", "saldo_cpa", "primeiro_deposito", "status_primeiro_deposito", "cont_cpa", "data_cadastro", "afiliado") 
VALUES ('1', NULL, 'contato@daanrox.com', 'Rox123456@', NULL, '(31) 99281-2273', '9', '1', 'https://subway-pay.test/cadastrar/?aff=1', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '1', '0', '5', '0', '0', '0', '0', '0', '0', '0', '07-11-2025 13:32', '');

INSERT INTO "gateway" ("client_id", "client_secret") VALUES ('', '');
