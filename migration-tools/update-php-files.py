#!/usr/bin/env python3
"""
Script para atualizar arquivos PHP de mysqli para PDO
Feature: deploy-and-supabase-migration
"""

import os
import re
import shutil
from pathlib import Path

def backup_file(filepath):
    """Cria backup do arquivo original"""
    backup_path = filepath + '.backup'
    shutil.copy2(filepath, backup_path)
    print(f"✅ Backup criado: {backup_path}")

def convert_connection(content):
    """Converte conexão mysqli para PDO"""
    # Substituir include do conectarbanco.php
    content = re.sub(
        r'include\s+["\']\.*/conectarbanco\.php["\'];?',
        'include __DIR__ . "/conectarbanco-pdo.php";',
        content
    )
    
    # Remover criação de conexão mysqli
    content = re.sub(
        r'\$conn\s*=\s*new\s+mysqli\([^)]+\);?',
        '// Conexão PDO já disponível via conectarbanco-pdo.php',
        content
    )
    
    return content

def convert_queries(content):
    """Converte queries mysqli para PDO"""
    
    # Converter query() para prepare() + execute()
    # Exemplo: $result = $conn->query($sql);
    # Para: $stmt = $conn->prepare($sql); $stmt->execute();
    
    # Converter fetch_assoc() para fetch()
    content = re.sub(
        r'->fetch_assoc\(\)',
        '->fetch(PDO::FETCH_ASSOC)',
        content
    )
    
    # Converter num_rows para rowCount()
    content = re.sub(
        r'->num_rows',
        '->rowCount()',
        content
    )
    
    # Converter affected_rows para rowCount()
    content = re.sub(
        r'->affected_rows',
        '->rowCount()',
        content
    )
    
    # Converter insert_id para lastInsertId()
    content = re.sub(
        r'->insert_id',
        '->lastInsertId()',
        content
    )
    
    # Converter close() para null
    content = re.sub(
        r'\$conn->close\(\);?',
        '$conn = null;',
        content
    )
    
    return content

def add_pdo_note(content):
    """Adiciona nota sobre conversão PDO"""
    note = """<?php
/**
 * NOTA: Este arquivo foi convertido de mysqli para PDO
 * Feature: deploy-and-supabase-migration
 * Data: """ + str(Path(__file__).stat().st_mtime) + """
 */
"""
    
    if content.startswith('<?php'):
        content = content.replace('<?php', note, 1)
    
    return content

def process_file(filepath):
    """Processa um arquivo PHP"""
    print(f"\n📄 Processando: {filepath}")
    
    try:
        # Ler conteúdo
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # Verificar se usa mysqli
        if 'mysqli' not in content:
            print("   ⏭️  Não usa mysqli, pulando...")
            return False
        
        # Criar backup
        backup_file(filepath)
        
        # Converter
        original_content = content
        content = convert_connection(content)
        content = convert_queries(content)
        content = add_pdo_note(content)
        
        # Salvar se houve mudanças
        if content != original_content:
            with open(filepath, 'w', encoding='utf-8') as f:
                f.write(content)
            print("   ✅ Arquivo atualizado!")
            return True
        else:
            print("   ℹ️  Nenhuma mudança necessária")
            return False
            
    except Exception as e:
        print(f"   ❌ Erro: {e}")
        return False

def main():
    print("=== Atualização de Arquivos PHP ===\n")
    print("⚠️  ATENÇÃO: Este script fará backup de todos os arquivos antes de modificá-los\n")
    
    # Diretório raiz
    root_dir = Path(__file__).parent.parent / 'subway-pay-main'
    
    if not root_dir.exists():
        print(f"❌ Diretório não encontrado: {root_dir}")
        return
    
    # Encontrar todos os arquivos PHP
    php_files = list(root_dir.rglob('*.php'))
    print(f"📊 Encontrados {len(php_files)} arquivos PHP\n")
    
    # Processar arquivos
    updated_count = 0
    for php_file in php_files:
        if process_file(php_file):
            updated_count += 1
    
    # Resumo
    print("\n=== Resumo ===")
    print(f"✅ Arquivos atualizados: {updated_count}")
    print(f"📁 Total de arquivos: {len(php_files)}")
    print(f"💾 Backups criados em: *.php.backup")
    
    print("\n🎉 Conversão concluída!")
    print("\n⚠️  IMPORTANTE: Teste a aplicação antes de deletar os backups!")

if __name__ == '__main__':
    main()
