<?php
/**
 * Conexão com Supabase PostgreSQL usando PDO
 * Feature: deploy-and-supabase-migration
 */

// Carregar variáveis de ambiente
function loadEnvFile($file) {
    if (!file_exists($file)) {
        die("Erro: Arquivo .env não encontrado!");
    }
    
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $_ENV[trim($name)] = trim($value);
        }
    }
}

// Carregar .env
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    loadEnvFile($envFile);
}

// Configuração do banco (Supabase PostgreSQL)
$config = array(
    'db_host' => $_ENV['SUPABASE_HOST'] ?? 'db.jaypkwnrqdgcbfklftou.supabase.co',
    'db_port' => $_ENV['SUPABASE_PORT'] ?? '5432',
    'db_user' => $_ENV['SUPABASE_USER'] ?? 'postgres',
    'db_pass' => $_ENV['SUPABASE_PASSWORD'] ?? 'Subwaypay12121',
    'db_name' => $_ENV['SUPABASE_DATABASE'] ?? 'postgres'
);

// Função para criar conexão PDO
function getConnection() {
    global $config;
    
    try {
        $dsn = sprintf(
            "pgsql:host=%s;port=%s;dbname=%s;sslmode=require",
            $config['db_host'],
            $config['db_port'],
            $config['db_name']
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
        die("Erro ao conectar ao banco de dados: " . $e->getMessage());
    }
}

// Criar conexão global
try {
    $conn = getConnection();
} catch (Exception $e) {
    die("Erro de conexão: " . $e->getMessage());
}

?>
