<?php

/**
 * Script para converter schema MySQL para PostgreSQL
 * Feature: deploy-and-supabase-migration
 */

require_once __DIR__ . '/SchemaConverter.php';

// Ler arquivo SQL do MySQL
$mysqlSchemaFile = __DIR__ . '/../subway-pay-main/sql_subway.sql';
if (!file_exists($mysqlSchemaFile)) {
    die("Erro: Arquivo sql_subway.sql não encontrado!\n");
}

echo "Lendo schema MySQL...\n";
$mysqlSchema = file_get_contents($mysqlSchemaFile);

// Converter schema
echo "Convertendo schema para PostgreSQL...\n";
$converter = new SchemaConverter();
$pgSchema = $converter->convertSchema($mysqlSchema);

// Salvar schema convertido
$outputFile = __DIR__ . '/sql_subway_pg.sql';
file_put_contents($outputFile, $pgSchema);
echo "Schema PostgreSQL salvo em: $outputFile\n";

// Salvar log de conversões
$logFile = __DIR__ . '/conversion.log';
$converter->saveLog($logFile);
echo "Log de conversões salvo em: $logFile\n";

// Exibir resumo
$log = $converter->getConversionLog();
echo "\nResumo da conversão:\n";
echo "- Total de conversões: " . count($log) . "\n";
echo "\nSchema PostgreSQL pronto para ser executado no Supabase!\n";
