#!/usr/bin/env python3
"""
Script para criar tabelas no Supabase (alternativa Python)
Feature: deploy-and-supabase-migration
"""

import os
import psycopg2
from psycopg2 import sql

def load_env(file_path):
    """Carrega variáveis de ambiente do arquivo .env"""
    env_vars = {}
    with open(file_path, 'r') as f:
        for line in f:
            line = line.strip()
            if line and not line.startswith('#'):
                key, value = line.split('=', 1)
                env_vars[key.strip()] = value.strip()
    return env_vars

def main():
    print("=== Criação de Tabelas no Supabase ===\n")
    
    # Carregar credenciais
    env_file = os.path.join(os.path.dirname(__file__), '..', '.env')
    env = load_env(env_file)
    
    # Configuração de conexão
    conn_params = {
        'host': env['SUPABASE_HOST'],
        'port': env['SUPABASE_PORT'],
        'database': env['SUPABASE_DATABASE'],
        'user': env['SUPABASE_USER'],
        'password': env['SUPABASE_PASSWORD'],
        'sslmode': 'require'
    }
    
    # Testar conexão
    print("1. Testando conexão com Supabase...")
    try:
        conn = psycopg2.connect(**conn_params)
        print("✅ Conexão estabelecida com sucesso!\n")
    except Exception as e:
        print(f"❌ Erro ao conectar: {e}")
        return
    
    # Ler schema
    print("2. Lendo schema PostgreSQL...")
    schema_file = os.path.join(os.path.dirname(__file__), 'sql_subway_pg.sql')
    with open(schema_file, 'r', encoding='utf-8') as f:
        schema = f.read()
    print("✅ Schema carregado!\n")
    
    # Executar schema
    print("3. Criando tabelas no Supabase...")
    cursor = conn.cursor()
    
    # Dividir em statements
    statements = [s.strip() for s in schema.split(';') if s.strip()]
    
    success_count = 0
    error_count = 0
    
    for statement in statements:
        if not statement:
            continue
            
        try:
            cursor.execute(statement)
            conn.commit()
            success_count += 1
            preview = statement[:100].replace('\n', ' ')
            print(f"✅ {preview}...")
        except Exception as e:
            error_count += 1
            preview = statement[:100].replace('\n', ' ')
            print(f"❌ {preview}...")
            print(f"   Erro: {e}")
    
    # Validar tabelas
    print("\n4. Validando tabelas criadas...")
    cursor.execute("""
        SELECT table_name 
        FROM information_schema.tables 
        WHERE table_schema = 'public' 
        AND table_type = 'BASE TABLE'
        ORDER BY table_name
    """)
    
    tables = cursor.fetchall()
    print(f"✅ Tabelas encontradas: {len(tables)}\n")
    
    for (table,) in tables:
        cursor.execute(f'SELECT COUNT(*) FROM "{table}"')
        count = cursor.fetchone()[0]
        print(f"   - {table}: {count} registros")
    
    # Resumo
    print("\n=== Resumo ===")
    print(f"✅ Statements executados com sucesso: {success_count}")
    if error_count > 0:
        print(f"❌ Statements com erro: {error_count}")
    print(f"✅ Total de tabelas: {len(tables)}")
    
    if error_count == 0:
        print("\n🎉 Todas as tabelas foram criadas com sucesso!")
    else:
        print("\n⚠️  Algumas operações falharam. Verifique os erros acima.")
    
    cursor.close()
    conn.close()

if __name__ == '__main__':
    main()
