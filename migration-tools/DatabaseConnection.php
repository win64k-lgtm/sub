<?php

/**
 * Gerenciador de Conexão com Banco de Dados
 * Feature: deploy-and-supabase-migration
 */
class DatabaseConnection
{
    private ?PDO $connection = null;
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Estabelece conexão com PostgreSQL (Supabase)
     */
    public function connect(): PDO
    {
        if ($this->connection !== null) {
            return $this->connection;
        }

        try {
            $dsn = sprintf(
                "pgsql:host=%s;port=%s;dbname=%s;sslmode=require",
                $this->config['host'],
                $this->config['port'],
                $this->config['database']
            );

            $this->connection = new PDO(
                $dsn,
                $this->config['user'],
                $this->config['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );

            return $this->connection;
        } catch (PDOException $e) {
            throw new Exception("Erro ao conectar ao banco: " . $e->getMessage());
        }
    }

    /**
     * Testa conectividade com o banco
     */
    public function testConnection(): bool
    {
        try {
            $pdo = $this->connect();
            $stmt = $pdo->query("SELECT 1");
            return $stmt !== false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Executa query preparada
     */
    public function execute(string $query, array $params = []): PDOStatement
    {
        $pdo = $this->connect();
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Executa múltiplas queries (para schema)
     */
    public function executeMultiple(string $sql): array
    {
        $pdo = $this->connect();
        $results = [];
        
        // Dividir por ponto-e-vírgula, mas ignorar dentro de strings
        $statements = $this->splitSqlStatements($sql);
        
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (empty($statement)) {
                continue;
            }

            try {
                $pdo->exec($statement);
                $results[] = [
                    'success' => true,
                    'statement' => substr($statement, 0, 100) . '...'
                ];
            } catch (PDOException $e) {
                $results[] = [
                    'success' => false,
                    'statement' => substr($statement, 0, 100) . '...',
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * Divide SQL em statements individuais
     */
    private function splitSqlStatements(string $sql): array
    {
        $statements = [];
        $current = '';
        $inString = false;
        $stringChar = '';
        
        for ($i = 0; $i < strlen($sql); $i++) {
            $char = $sql[$i];
            
            // Detectar strings
            if (($char === "'" || $char === '"') && ($i === 0 || $sql[$i-1] !== '\\')) {
                if (!$inString) {
                    $inString = true;
                    $stringChar = $char;
                } elseif ($char === $stringChar) {
                    $inString = false;
                }
            }
            
            // Detectar fim de statement
            if ($char === ';' && !$inString) {
                $statements[] = $current;
                $current = '';
                continue;
            }
            
            $current .= $char;
        }
        
        if (!empty(trim($current))) {
            $statements[] = $current;
        }
        
        return $statements;
    }

    /**
     * Obtém lista de tabelas
     */
    public function getTables(): array
    {
        $pdo = $this->connect();
        $stmt = $pdo->query("
            SELECT table_name 
            FROM information_schema.tables 
            WHERE table_schema = 'public' 
            AND table_type = 'BASE TABLE'
            ORDER BY table_name
        ");
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Conta registros em uma tabela
     */
    public function countRecords(string $table): int
    {
        $pdo = $this->connect();
        $stmt = $pdo->query("SELECT COUNT(*) FROM \"$table\"");
        return (int) $stmt->fetchColumn();
    }

    /**
     * Fecha conexão
     */
    public function close(): void
    {
        $this->connection = null;
    }
}
