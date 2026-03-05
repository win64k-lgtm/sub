<?php

/**
 * Conversor de Schema MySQL para PostgreSQL
 * Feature: deploy-and-supabase-migration
 */
class SchemaConverter
{
    private array $conversionLog = [];

    /**
     * Converte schema MySQL completo para PostgreSQL
     */
    public function convertSchema(string $mysqlSchema): string
    {
        $lines = explode("\n", $mysqlSchema);
        $pgSchema = [];
        $inTableDefinition = false;
        $tableName = '';

        foreach ($lines as $line) {
            $trimmed = trim($line);

            // Pular comentários e comandos SET
            if (empty($trimmed) || 
                str_starts_with($trimmed, '--') || 
                str_starts_with($trimmed, '/*') ||
                str_starts_with($trimmed, 'SET ') ||
                str_starts_with($trimmed, 'START ') ||
                str_starts_with($trimmed, 'COMMIT') ||
                str_starts_with($trimmed, '/*!')) {
                continue;
            }

            // Detectar início de CREATE TABLE
            if (preg_match('/CREATE TABLE `?(\w+)`?/i', $trimmed, $matches)) {
                $inTableDefinition = true;
                $tableName = $matches[1];
                $pgSchema[] = "CREATE TABLE IF NOT EXISTS \"$tableName\" (";
                continue;
            }

            // Processar definições de colunas
            if ($inTableDefinition && !str_starts_with($trimmed, ')')) {
                $converted = $this->convertColumnDefinition($trimmed, $tableName);
                if (!empty($converted)) {
                    $pgSchema[] = "  " . $converted;
                }
                continue;
            }

            // Fim da definição da tabela
            if ($inTableDefinition && str_starts_with($trimmed, ')')) {
                // Remover última vírgula se existir
                $lastIndex = count($pgSchema) - 1;
                $pgSchema[$lastIndex] = rtrim($pgSchema[$lastIndex], ',');
                $pgSchema[] = ");";
                $pgSchema[] = "";
                $inTableDefinition = false;
                continue;
            }

            // Processar ALTER TABLE para índices
            if (preg_match('/ALTER TABLE `?(\w+)`?/i', $trimmed, $matches)) {
                $tableName = $matches[1];
                $indexStatements = $this->convertAlterTable($trimmed, $tableName);
                $pgSchema = array_merge($pgSchema, $indexStatements);
            }
        }

        return implode("\n", $pgSchema);
    }

    /**
     * Converte definição de coluna MySQL para PostgreSQL
     */
    private function convertColumnDefinition(string $definition, string $tableName): string
    {
        $definition = trim($definition, " \t\n\r\0\x0B,");

        // Pular linhas vazias
        if (empty($definition)) {
            return '';
        }

        // Remover backticks
        $definition = str_replace('`', '"', $definition);

        // Converter tipos de dados
        $definition = $this->convertDataTypes($definition);

        // Remover COLLATE
        $definition = preg_replace('/COLLATE\s+\w+/i', '', $definition);

        // Remover CHARACTER SET
        $definition = preg_replace('/CHARACTER SET\s+\w+/i', '', $definition);

        // Processar PRIMARY KEY inline
        if (preg_match('/"(\w+)"\s+(.+)\s+PRIMARY KEY/i', $definition, $matches)) {
            $columnName = $matches[1];
            $type = $matches[2];
            
            // Se for INT com PRIMARY KEY, usar SERIAL
            if (preg_match('/INT/i', $type)) {
                $this->log("Converted PRIMARY KEY column $columnName to SERIAL", $tableName);
                return "\"$columnName\" SERIAL PRIMARY KEY,";
            }
        }

        // Processar KEY/INDEX inline (remover, será adicionado depois)
        if (preg_match('/^(KEY|INDEX)\s+/i', $definition)) {
            return '';
        }

        // Adicionar vírgula se não tiver
        if (!str_ends_with($definition, ',')) {
            $definition .= ',';
        }

        return $definition;
    }

    /**
     * Converte tipos de dados MySQL para PostgreSQL
     */
    private function convertDataTypes(string $definition): string
    {
        // INT AUTO_INCREMENT -> SERIAL
        if (preg_match('/"(\w+)"\s+INT.*AUTO_INCREMENT/i', $definition, $matches)) {
            $columnName = $matches[1];
            $this->log("Converted INT AUTO_INCREMENT to SERIAL for column: $columnName");
            return str_replace(
                preg_match('/INT.*AUTO_INCREMENT/i', $definition, $m) ? $m[0] : '',
                'SERIAL',
                $definition
            );
        }

        // VARCHAR permanece igual
        // TEXT permanece igual
        
        // DATETIME -> TIMESTAMP
        $definition = preg_replace_callback(
            '/DATETIME/i',
            function($matches) {
                $this->log("Converted DATETIME to TIMESTAMP");
                return 'TIMESTAMP';
            },
            $definition
        );

        return $definition;
    }

    /**
     * Converte ALTER TABLE para criar índices
     */
    private function convertAlterTable(string $statement, string $tableName): array
    {
        $result = [];

        // PRIMARY KEY
        if (preg_match('/ADD PRIMARY KEY \(`?(\w+)`?\)/i', $statement, $matches)) {
            $column = $matches[1];
            $result[] = "ALTER TABLE \"$tableName\" ADD PRIMARY KEY (\"$column\");";
            $this->log("Added PRIMARY KEY on column: $column", $tableName);
        }

        // KEY/INDEX
        if (preg_match('/ADD KEY `?(\w+)`? \(`?(\w+)`?\)/i', $statement, $matches)) {
            $indexName = $matches[1];
            $column = $matches[2];
            $result[] = "CREATE INDEX \"$indexName\" ON \"$tableName\" (\"$column\");";
            $this->log("Created INDEX $indexName on column: $column", $tableName);
        }

        // AUTO_INCREMENT
        if (preg_match('/MODIFY `?(\w+)`? INT NOT NULL AUTO_INCREMENT/i', $statement, $matches)) {
            // Já tratado na conversão de tipos
            $this->log("AUTO_INCREMENT handled in type conversion", $tableName);
        }

        return $result;
    }

    /**
     * Registra conversão no log
     */
    private function log(string $message, string $context = ''): void
    {
        $this->conversionLog[] = [
            'timestamp' => date('Y-m-d H:i:s'),
            'context' => $context,
            'message' => $message
        ];
    }

    /**
     * Retorna log de conversões
     */
    public function getConversionLog(): array
    {
        return $this->conversionLog;
    }

    /**
     * Salva log em arquivo
     */
    public function saveLog(string $filename): void
    {
        $content = "Schema Conversion Log\n";
        $content .= "=====================\n\n";
        
        foreach ($this->conversionLog as $entry) {
            $content .= "[{$entry['timestamp']}]";
            if (!empty($entry['context'])) {
                $content .= " [{$entry['context']}]";
            }
            $content .= " {$entry['message']}\n";
        }

        file_put_contents($filename, $content);
    }
}
