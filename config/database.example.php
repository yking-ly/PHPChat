<?php
/**
 * Database Configuration
 * 
 * SETUP INSTRUCTIONS:
 * 1. Copy this file to 'database.php' in the same directory
 * 2. Update the credentials below with your MySQL settings
 * 3. Never commit database.php to version control
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'php_chat');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_CHARSET', 'utf8mb4');

/**
 * Get database connection
 * @return PDO|null Database connection or null on failure
 */
function getDBConnection()
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            return null;
        }
    }

    return $pdo;
}

/**
 * Execute a prepared statement
 * @param string $sql SQL query
 * @param array $params Parameters for prepared statement
 * @return PDOStatement|false
 */
function executeQuery($sql, $params = [])
{
    $pdo = getDBConnection();
    if (!$pdo)
        return false;

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        error_log("Query failed: " . $e->getMessage());
        return false;
    }
}
?>