<?php
class Database {
    private $host;
    private $username;
    private $pass;
    private $dbname;
    public $conn;

    public function __construct() {
        $this->host = getenv('DB_HOST');
        $this->username = getenv('DB_USERNAME');
        $this->pass = getenv('DB_PASSWORD');
        $this->dbname = getenv('DB_NAME');
        
        // Validate environment variables
        if (!$this->host || !$this->username || !$this->pass || !$this->dbname) {
            throw new Exception("Database configuration incomplete. Please check environment variables.");
        }
    }

    public function getConnection() {
        $this->conn = null;
        try {
            // Simpler connection string format
            $connectionString = "sqlsrv:Server={$this->host};Database={$this->dbname}";
            
            // Optional: For Azure SQL with encryption
            // $connectionString = "sqlsrv:Server={$this->host};Database={$this->dbname};Encrypt=true;TrustServerCertificate=false";
            
            $this->conn = new PDO($connectionString, $this->username, $this->pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->conn->setAttribute(PDO::SQLSRV_ATTR_ENCODING, PDO::SQLSRV_ENCODING_UTF8);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>