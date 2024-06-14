<?php
require 'vendor/autoload.php';

use Sentry\SentrySdk;

SentrySdk::init(['dsn' => 'https://examplePublicKey@o0.ingest.sentry.io/0']);

// Handler untuk error PHP
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    \Sentry\captureMessage("Error: $errstr", Sentry\Severity::error());
});

// Handler untuk exception
set_exception_handler(function ($exception) {
    \Sentry\captureException($exception);
});

// Handler untuk fatal error
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null &&
        in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        \Sentry\captureMessage("Fatal Error: {$error['message']}", Sentry\Severity::fatal());
    }
});

// Contoh kode aplikasi PHP Anda dimulai di sini
// ...
?>

<?php
class ConfigDB {
    private $host = 'localhost';
    private $db_name = 'db_sepeda';
    private $username = 'root';
    private $password = '';
    public $conn;

    public function connect() {
        $this->conn = null;
        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
            if ($this->conn->connect_error) {
                throw new Exception('Connection error: ' . $this->conn->connect_error);
            }
        } catch (Exception $e) {
            die('Database connection error: ' . $e->getMessage());
        }
        return $this->conn;
    }

    public function close() {
        $this->conn->close();
    }

    public function update($table, $data, $id) {
        // Implementation of update method
    }
}
?>
