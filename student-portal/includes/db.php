<?php
/**
 * Database Connection & Query Helper
 * Government Scholarship Portal
 */

define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'government_scholarship_portal');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Log error internally and show clean message
            error_log("Database Connection Error: " . $e->getMessage());
            die("<div style='font-family: sans-serif; padding: 20px; background: #fff3f3; color: #d32f2f; border: 1px solid #ffcdd2; border-radius: 8px; max-width: 600px; margin: 40px auto;'>
                <h3>Database Connection Failed</h3>
                <p>Could not connect to the scholarship database. Please ensure XAMPP MySQL is running and the database <strong>" . htmlspecialchars(DB_NAME) . "</strong> is imported.</p>
                <small>Error: " . htmlspecialchars($e->getMessage()) . "</small>
            </div>");
        }
    }
    return $pdo;
}

/**
 * Execute a parameterized query and return statement
 */
function dbQuery($sql, $params = []) {
    $stmt = getDB()->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

/**
 * Fetch single row
 */
function dbFetchOne($sql, $params = []) {
    return dbQuery($sql, $params)->fetch();
}

/**
 * Fetch all matching rows
 */
function dbFetchAll($sql, $params = []) {
    return dbQuery($sql, $params)->fetchAll();
}

/**
 * Get last inserted ID
 */
function dbLastId() {
    return getDB()->lastInsertId();
}
