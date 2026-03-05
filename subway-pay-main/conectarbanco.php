<?php

/**
 * Configuração de Conexão com Supabase PostgreSQL
 * Migrado de MySQL para PostgreSQL
 */

// Carregar variáveis de ambiente (evita redeclare quando o arquivo é incluído mais de uma vez)
if (!function_exists('loadEnvFile')) {
    function loadEnvFile($file) {
        if (!file_exists($file)) {
            return false;
        }
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            if (strpos($line, '=') !== false) {
                list($name, $value) = explode('=', $line, 2);
                $_ENV[trim($name)] = trim($value);
            }
        }
        return true;
    }
}

// Tentar carregar .env (só na primeira inclusão)
if (!isset($config)) {
    loadEnvFile(__DIR__ . '/.env');
}

// Configuração do Railway PostgreSQL (usar variáveis de ambiente se disponíveis)
if (!isset($config)) {
$config = array(
    'db_host' => $_ENV['PGHOST'] ?? 'tramway.proxy.rlwy.net',
    'db_port' => $_ENV['PGPORT'] ?? '55414',
    'db_user' => $_ENV['PGUSER'] ?? 'postgres',
    'db_pass' => $_ENV['PGPASSWORD'] ?? 'fkAuDOJNrSGZAwkLbtDoURsmgkiVFIYC',
    'db_name' => $_ENV['PGDATABASE'] ?? 'railway',
    'db_sslmode' => $_ENV['PGSSLMODE'] ?? 'require'
);
}

/**
 * Função para obter conexão PDO com PostgreSQL
 * Substitui mysqli por PDO
 */
if (!function_exists('getConnection')) {
function getConnection() {
    global $config;
    
    try {
        $dsn = sprintf(
            "pgsql:host=%s;port=%s;dbname=%s;sslmode=%s",
            $config['db_host'],
            $config['db_port'],
            $config['db_name'],
            $config['db_sslmode']
        );
        
        $pdo = new PDO(
            $dsn,
            $config['db_user'],
            $config['db_pass'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
        
        return $pdo;
    } catch (PDOException $e) {
        error_log("Erro de conexão: " . $e->getMessage());
        die("Erro ao conectar ao banco de dados. Tente novamente mais tarde.");
    }
}
}

/**
 * Classe de compatibilidade mysqli -> PDO
 * Permite usar código antigo com nova conexão
 */
if (!class_exists('MysqliCompatibility')) {
class MysqliCompatibility {
    private $pdo;
    public $connect_error = null;
    
    public function __construct($host, $user, $pass, $dbname, $port = null) {
        try {
            $this->pdo = getConnection();
        } catch (Exception $e) {
            $this->connect_error = $e->getMessage();
        }
    }
    
    public function query($sql) {
        try {
            // Adaptar query MySQL para PostgreSQL
            $sql = $this->adaptQuery($sql);
            $stmt = $this->pdo->query($sql);
            return new MysqliResultCompatibility($stmt);
        } catch (PDOException $e) {
            error_log("Query error: " . $e->getMessage());
            return false;
        }
    }
    
    public function prepare($sql) {
        $sql = $this->adaptQuery($sql);
        return $this->pdo->prepare($sql);
    }
    
    public function real_escape_string($string) {
        return addslashes($string);
    }
    
    public function close() {
        $this->pdo = null;
    }
    
    private function adaptQuery($sql) {
        // Converter aspas de identificadores se necessário
        // MySQL usa ` (backtick), PostgreSQL usa " (aspas duplas)
        $sql = str_replace('`', '"', $sql);
        
        // Adicionar mais conversões conforme necessário
        return $sql;
    }
}
}

if (!class_exists('MysqliResultCompatibility')) {
class MysqliResultCompatibility {
    private $stmt;
    public $num_rows = 0;
    
    public function __construct($stmt) {
        $this->stmt = $stmt;
        if ($stmt) {
            $this->num_rows = $stmt->rowCount();
        }
    }
    
    public function fetch_assoc() {
        return $this->stmt ? $this->stmt->fetch(PDO::FETCH_ASSOC) : null;
    }
    
    public function fetch_array() {
        return $this->stmt ? $this->stmt->fetch(PDO::FETCH_BOTH) : null;
    }
}
}

// Função auxiliar para criar conexão compatível com código antigo
if (!function_exists('new_mysqli')) {
function new_mysqli($host, $user, $pass, $dbname) {
    return new MysqliCompatibility($host, $user, $pass, $dbname);
}
}

?>
