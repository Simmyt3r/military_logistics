<?php
class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;

    private $dbh;
    private $stmt;
    private $error;
    private $connected = false;

    public function __construct() {
        // Set DSN
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );

        // Create PDO instance
        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
            $this->connected = true;
        } catch(PDOException $e) {
            $this->error = $e->getMessage();
            // Log the error instead of displaying it directly
            error_log('Database Connection Error: ' . $this->error);
            
            // Create a user-friendly error message
            if (defined('DEBUG_MODE') && DEBUG_MODE) {
                // Show detailed error in debug mode
                echo '<div class="alert alert-danger">Database Connection Error: ' . $this->error . '</div>';
            } else {
                // Show generic error in production
                echo '<div class="alert alert-danger">Database connection failed. Please try again later or contact the administrator.</div>';
            }
        }
    }

    // Check if connection is established
    public function isConnected() {
        return $this->connected;
    }

    // Get error message
    public function getError() {
        return $this->error;
    }

    // Prepare statement with query
    public function query($sql) {
        if (!$this->connected) {
            $this->error = "Cannot execute query: Database connection not established.";
            error_log($this->error);
            return false;
        }
        
        try {
            $this->stmt = $this->dbh->prepare($sql);
            return true;
        } catch(PDOException $e) {
            $this->error = $e->getMessage();
            error_log('Query Preparation Error: ' . $this->error);
            return false;
        }
    }

    // Bind values
    public function bind($param, $value, $type = null) {
        if (!$this->stmt) {
            $this->error = "Cannot bind parameters: Statement not prepared.";
            error_log($this->error);
            return false;
        }
        
        if(is_null($type)) {
            switch(true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        
        try {
            $this->stmt->bindValue($param, $value, $type);
            return true;
        } catch(PDOException $e) {
            $this->error = $e->getMessage();
            error_log('Parameter Binding Error: ' . $this->error);
            return false;
        }
    }

    // Execute the prepared statement
    public function execute() {
        if (!$this->stmt) {
            $this->error = "Cannot execute: Statement not prepared.";
            error_log($this->error);
            return false;
        }
        
        try {
            return $this->stmt->execute();
        } catch(PDOException $e) {
            $this->error = $e->getMessage();
            error_log('Statement Execution Error: ' . $this->error);
            return false;
        }
    }

    // Get result set as array of objects
    public function resultSet() {
        if (!$this->stmt) {
            $this->error = "Cannot get result set: Statement not prepared.";
            error_log($this->error);
            return [];
        }
        
        try {
            $this->execute();
            return $this->stmt->fetchAll(PDO::FETCH_OBJ);
        } catch(PDOException $e) {
            $this->error = $e->getMessage();
            error_log('Result Set Fetch Error: ' . $this->error);
            return [];
        }
    }

    // Get single record as object
    public function single() {
        if (!$this->stmt) {
            $this->error = "Cannot get single record: Statement not prepared.";
            error_log($this->error);
            return null;
        }
        
        try {
            $this->execute();
            return $this->stmt->fetch(PDO::FETCH_OBJ);
        } catch(PDOException $e) {
            $this->error = $e->getMessage();
            error_log('Single Record Fetch Error: ' . $this->error);
            return null;
        }
    }

    // Get row count
    public function rowCount() {
        if (!$this->stmt) {
            $this->error = "Cannot get row count: Statement not prepared.";
            error_log($this->error);
            return 0;
        }
        
        try {
            return $this->stmt->rowCount();
        } catch(PDOException $e) {
            $this->error = $e->getMessage();
            error_log('Row Count Error: ' . $this->error);
            return 0;
        }
    }

    // Get last inserted ID
    public function lastInsertId() {
        if (!$this->connected) {
            $this->error = "Cannot get last insert ID: Database connection not established.";
            error_log($this->error);
            return null;
        }
        
        try {
            return $this->dbh->lastInsertId();
        } catch(PDOException $e) {
            $this->error = $e->getMessage();
            error_log('Last Insert ID Error: ' . $this->error);
            return null;
        }
    }

    // Transactions
    public function beginTransaction() {
        if (!$this->connected) {
            $this->error = "Cannot begin transaction: Database connection not established.";
            error_log($this->error);
            return false;
        }
        
        try {
            return $this->dbh->beginTransaction();
        } catch(PDOException $e) {
            $this->error = $e->getMessage();
            error_log('Begin Transaction Error: ' . $this->error);
            return false;
        }
    }

    public function endTransaction() {
        if (!$this->connected) {
            $this->error = "Cannot commit transaction: Database connection not established.";
            error_log($this->error);
            return false;
        }
        
        try {
            return $this->dbh->commit();
        } catch(PDOException $e) {
            $this->error = $e->getMessage();
            error_log('Commit Transaction Error: ' . $this->error);
            return false;
        }
    }

    public function cancelTransaction() {
        if (!$this->connected) {
            $this->error = "Cannot rollback transaction: Database connection not established.";
            error_log($this->error);
            return false;
        }
        
        try {
            return $this->dbh->rollBack();
        } catch(PDOException $e) {
            $this->error = $e->getMessage();
            error_log('Rollback Transaction Error: ' . $this->error);
            return false;
        }
    }

    // Debug dump parameters
    public function debugDumpParams() {
        if (!$this->stmt) {
            $this->error = "Cannot dump parameters: Statement not prepared.";
            error_log($this->error);
            return false;
        }
        
        try {
            return $this->stmt->debugDumpParams();
        } catch(PDOException $e) {
            $this->error = $e->getMessage();
            error_log('Debug Dump Params Error: ' . $this->error);
            return false;
        }
    }
}
?>