<?php
class Database {
    private $host = "taaltourism.database.windows.net";
    private $username = "adminoftaal";
    private $pass = "4D8b3>BB@)";
    private $dbname = "taaltourismdb";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            // Connection string for Azure SQL Database
            $connectionString = "sqlsrv:Server=" . $this->host . ",1433;Database=" . $this->dbname;
            
            // Create PDO instance
            $this->conn = new PDO($connectionString, $this->username, $this->pass);
            
            // Set error mode to exception
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Configure additional settings if needed
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->conn->setAttribute(PDO::SQLSRV_ATTR_ENCODING, PDO::SQLSRV_ENCODING_UTF8);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
   
}
?>