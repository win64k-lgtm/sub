<?php

/**
 * Script para criar tabelas no Supabase
 * Feature: deploy-and-supabase-migration
 */

require_once __DIR__ . '/DatabaseConnection.php';

// Carregar variáveis de ambiente
function loadEnv($file) {
    if (!file_exists($file)) {
        die("Erro: Arquivo .env não encontrado!\n");
    }
    
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

loadEnv(__DIR__ . '/../.env');

// Configuração do Supabase
$config = [
    'host' => $_ENV['SUPABASE_HOST'],
    'port' => $_ENV['SUPABASE_PORT'],
    'database' => $_ENV['SUPABASE_DATABASE'],
    'user' => $_ENV['SUPABASE_USER'],
    'password' => $_ENV['SUPABASE_PASSWORD']
];

echo "=== Criação de Tabelas no Supabase ===\n\n";

// Testar conexão
echo "1. Testando conexão com Supabase...\n";
$db = new DatabaseConnection($config);

if (!$db->testConnection()) {
    die("❌ Erro: Não foi possível conectar ao Supabase!\n");
}
echo "✅ Conexão estabelecida com sucesso!\n\n";

// Ler schema PostgreSQL
echo "2. Lendo schema PostgreSQL...\n";
$schemaFile = __DIR__ . '/sql_subway_pg.sql';
if (!file_exists($schemaFile)) {
    die("❌ Erro: Arquivo sql_subway_pg.sql não encontrado!\n");
}

$schema = file_get_contents($schemaFile);
echo "✅ Schema carregado!\n\n";

// Executar schema
echo "3. Criando tabelas no Supabase...\n";
$results = $db->executeMultiple($schema);

$successCount = 0;
$errorCount = 0;

foreach ($results as $result) {
    if ($result['success']) {
        $successCount++;
        echo "✅ " . $result['statement'] . "\n";
    } else {
        $errorCount++;
        echo "❌ " . $result['statement'] . "\n";
        echo "   Erro: " . $result['error'] . "\n";
    }
}

echo "\n4. Validando tabelas criadas...\n";
$tables = $db->getTables();
echo "✅ Tabelas encontradas: " . count($tables) . "\n";

foreach ($tables as $table) {
    $count = $db->countRecords($table);
    echo "   - $table: $count registros\n";
}

echo "\n=== Resumo ===\n";
echo "✅ Statements executados com sucesso: $successCount\n";
if ($errorCount > 0) {
    echo "❌ Statements com erro: $errorCount\n";
}
echo "✅ Total de tabelas: " . count($tables) . "\n";

if ($errorCount === 0) {
    echo "\n🎉 Todas as tabelas foram criadas com sucesso!\n";
} else {
    echo "\n⚠️  Algumas operações falharam. Verifique os erros acima.\n";
}
