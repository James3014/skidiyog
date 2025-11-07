<?php
// Database configuration - use SQLite (no server required)
// Database file is stored in /data/skidiyog.db

// Determine database file path
$db_dir = __DIR__ . '/../data';
if (!is_dir($db_dir)) {
    mkdir($db_dir, 0755, true);
}

define('DB_TYPE', 'sqlite'); // SQLite database type
define('DB_FILE', $db_dir . '/skidiyog.db'); // SQLite database file path

class DB{/* DBv16.09.03 By Ko - Modified for SQLite */

	private $pdo;

	public $error;
	public $sql;
	public $link;

	public function __construct($db=null){
		try {
			// Ensure database file has write permissions
			if (file_exists(DB_FILE)) {
				if (!is_writable(DB_FILE)) {
					// Try to fix permissions
					@chmod(DB_FILE, 0666);
					error_log("Database permission fixed: " . DB_FILE);
				}
			} else {
				// Ensure parent directory is writable
				$db_dir = dirname(DB_FILE);
				if (!is_writable($db_dir)) {
					@chmod($db_dir, 0777);
				}
			}

			$this->pdo = new PDO('sqlite:' . DB_FILE);
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

			// Auto-create tables if they don't exist
			$this->createTablesIfNotExist();

			$this->link = $this->pdo; // Compatibility alias
			$this->error = null;
		} catch (Exception $e) {
			$this->error = "Database connection failed: " . $e->getMessage();
			error_log("Database error: " . $this->error);
			$this->pdo = null;
			$this->link = null;
		}
	}

	private function createTablesIfNotExist() {
		if (!$this->pdo) {
			return;
		}

		try {
			// Create parks table
			$this->pdo->exec("
				CREATE TABLE IF NOT EXISTS parks (
					idx INTEGER PRIMARY KEY,
					name TEXT,
					cname TEXT,
					description TEXT,
					location TEXT,
					photo TEXT,
					about TEXT,
					photo_section TEXT,
					location_section TEXT,
					slope_section TEXT,
					ticket_section TEXT,
					time_section TEXT,
					access_section TEXT,
					live_section TEXT,
					rental_section TEXT,
					delivery_section TEXT,
					luggage_section TEXT,
					workout_section TEXT,
					remind_section TEXT,
					join_section TEXT,
					event_section TEXT,
					created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
					updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
				)
			");

			// Create instructors table
			$this->pdo->exec("
				CREATE TABLE IF NOT EXISTS instructors (
					idx INTEGER PRIMARY KEY,
					name TEXT,
					cname TEXT,
					content TEXT,
					photo TEXT,
					created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
					updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
				)
			");

			// Create articles table
			$this->pdo->exec("
				CREATE TABLE IF NOT EXISTS articles (
					idx INTEGER PRIMARY KEY,
					title TEXT,
					tags TEXT,
					article TEXT,
					keyword TEXT,
					timestamp DATETIME,
					created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
					updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
				)
			");

			// Create parkInfo view for backward compatibility with backend admin
			$this->pdo->exec("
				CREATE VIEW IF NOT EXISTS parkInfo AS
				SELECT
					idx,
					name,
					cname,
					description,
					location,
					photo,
					'' as timeslot,
					'' as active
				FROM parks
			");

			// Create instructorInfo view for backward compatibility with backend admin
			$this->pdo->exec("
				CREATE VIEW IF NOT EXISTS instructorInfo AS
				SELECT
					idx,
					name,
					cname,
					content,
					photo,
					1 as active,
					'' as jobType
				FROM instructors
			");
		} catch (Exception $e) {
			error_log("Error creating tables: " . $e->getMessage());
		}
	}
	
	public function __destruct(){
		// PDO closes automatically
		if(!empty($this->error)){
			// Only output error in CLI mode, not in web mode
			if (php_sapi_name() === 'cli') {
				echo "ERROR: {$this->error}\n";
			}
		}
	}

	public function QUERY($cmd, $sql, $params=NULL, $types=NULL){
		// Gracefully handle disconnected database
		if (!$this->pdo) {
			$this->error = "Database not connected";
			return ($cmd === 'SELECT') ? array() : null;
		}

		$this->sql = $sql;

		try {
			$stmt = $this->pdo->prepare($sql);
			if (!$stmt) {
				$this->error = "Cannot prepare statement";
				return ($cmd === 'SELECT') ? array() : null;
			}

			if($params && is_array($params)) {
				$stmt->execute($params);
			} else {
				$stmt->execute();
			}

			switch($cmd){
				case 'INSERT':
					$idx = $this->pdo->lastInsertId();
					$this->error = null;
					return $idx;
					break;

				case 'DELETE':
				case 'UPDATE':
					$affected = $stmt->rowCount();
					$this->error = null;
					return $affected;
					break;

				case 'SELECT':
					$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
					$this->error = null;
					return $rows ? $rows : array();
					break;
			}
		} catch (Exception $e) {
			$this->error = $e->getMessage();
			return ($cmd === 'SELECT') ? array() : null;
		}
	}///Query
	
	public function SELECT($table, $where){
		$whStr = '';
		$params = array();
		$i = 0;
		foreach($where as $f=>$v){
			if ($i > 0) $whStr .= " AND";
			$whStr.=" `{$f}`=?";
			$params[] = $v;
			$i++;
		}
		$sql = "SELECT * FROM `{$table}` WHERE {$whStr}";
		return $this->QUERY('SELECT', $sql, $params);
	}

	public function INSERT($table, $data){
		$params = array();
		$field = '';
		$value = '';
		$i = 0;
		foreach($data as $f=>$v){
			if ($i > 0) {
				$field .= ',';
				$value .= ',';
			}
			$field .= "`{$f}`";
			$value .= "?";
			$params[] = $v;
			$i++;
		}
		$sql = "INSERT INTO `{$table}` ($field) VALUES ($value)";
		return $this->QUERY('INSERT', $sql, $params);
	}///INSERT

	public function UPDATE($table, $data, $where){
		$params = array();
		$field = '';
		$whStr = '';
		$i = 0;
		foreach($data as $f=>$v){
			if ($i > 0) $field .= ',';
			$field .= "`{$f}`=?";
			$params[] = $v;
			$i++;
		}
		$i = 0;
		foreach($where as $f=>$v){
			if ($i > 0) $whStr .= " AND";
			$whStr .= " `{$f}`=?";
			$params[] = $v;
			$i++;
		}
		$sql = "UPDATE `{$table}` SET {$field} WHERE {$whStr}";
		error_log("DB UPDATE SQL: {$sql}");
		error_log("DB UPDATE params: " . json_encode($params));
		$result = $this->QUERY('UPDATE', $sql, $params);
		error_log("DB UPDATE result: " . var_export($result, true));
		return $result;
	}///UPDATE

	public function DELETE($table, $where){
		$params = array();
		$whStr = '';
		$i = 0;
		foreach($where as $f=>$v){
			if ($i > 0) $whStr .= " AND";
			$whStr .= " `{$f}`=?";
			$params[] = $v;
			$i++;
		}
		$sql = "DELETE FROM `{$table}` WHERE {$whStr}";
		return $this->QUERY('DELETE', $sql, $params);
	}///DELETE
	
}///class DB

?>
