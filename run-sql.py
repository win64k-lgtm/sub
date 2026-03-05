import psycopg2
import sys

# Credenciais do Railway
conn_params = {
    'host': 'tramway.proxy.rlwy.net',
    'port': 55414,
    'user': 'postgres',
    'password': 'fkAuDOJNrSGZAwkLbtDoURsmgkiVFIYC',
    'database': 'railway'
}

try:
    # Conectar ao banco
    print("Conectando ao banco de dados...")
    conn = psycopg2.connect(**conn_params)
    cursor = conn.cursor()
    
    # Ler o arquivo SQL
    print("Lendo arquivo SQL...")
    with open('migration-tools/sql_subway_pg.sql', 'r', encoding='utf-8') as f:
        sql = f.read()
    
    # Executar o SQL
    print("Executando SQL...")
    cursor.execute(sql)
    conn.commit()
    
    print("✓ SQL executado com sucesso!")
    print("✓ Todas as tabelas foram criadas!")
    
except Exception as e:
    print(f"✗ Erro: {e}")
    sys.exit(1)
    
finally:
    if 'cursor' in locals():
        cursor.close()
    if 'conn' in locals():
        conn.close()
