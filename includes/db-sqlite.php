<?php
/**
 * SQLite Database Configuration
 * Fallback when MySQL is unavailable
 */

// SQLite database file location
$db_file = __DIR__ . '/../data/skidiyog.db';

// Ensure data directory exists
if (!is_dir(dirname($db_file))) {
    @mkdir(dirname($db_file), 0755, true);
}

class SQLiteDB {
    private $pdo;
    public $error = null;

    public function __construct($db_file) {
        try {
            $this->pdo = new PDO('sqlite:' . $db_file);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->initializeTables();
        } catch (Exception $e) {
            $this->error = $e->getMessage();
        }
    }

    private function initializeTables() {
        $tables = [
            'parks' => "CREATE TABLE IF NOT EXISTS parks (
                idx INTEGER PRIMARY KEY,
                name TEXT,
                cname TEXT,
                description TEXT,
                location TEXT,
                photo TEXT,
                about TEXT
            )",
            'instructors' => "CREATE TABLE IF NOT EXISTS instructors (
                idx INTEGER PRIMARY KEY,
                name TEXT,
                cname TEXT,
                content TEXT,
                photo TEXT
            )",
            'articles' => "CREATE TABLE IF NOT EXISTS articles (
                idx INTEGER PRIMARY KEY,
                title TEXT,
                tags TEXT,
                article TEXT,
                keyword TEXT,
                timestamp DATETIME
            )"
        ];

        foreach ($tables as $sql) {
            try {
                $this->pdo->exec($sql);
            } catch (Exception $e) {
                $this->error = $e->getMessage();
            }
        }
    }

    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            return [];
        }
    }

    public function close() {
        $this->pdo = null;
    }
}

// Use SQLite as fallback
if (!defined('DB_TYPE')) {
    define('DB_TYPE', 'sqlite');
    define('DB_FILE', $db_file);
}
?>
