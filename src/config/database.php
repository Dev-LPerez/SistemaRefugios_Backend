<?php
class Database
{
    // Usamos getenv() para leer las variables que le pondremos a Render. 
    // Si no existen (como en tu PC local), usará los valores por defecto (localhost).
    private $host;
    private $port;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct()
    {
        $this->host = getenv('DB_HOST') ?: "127.0.0.1";
        $this->port = getenv('DB_PORT') ?: "3307";
        $this->db_name = getenv('DB_NAME') ?: "sistema_refugios";
        $this->username = getenv('DB_USER') ?: "root";
        $this->password = getenv('DB_PASS') ?: "";
    }

    public function getConnection()
    {
        $this->conn = null;

        try {
            $dsn = "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name . ";charset=utf8mb4";

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                // Aiven requiere conexiones seguras (SSL). Esto le dice a PDO que lo acepte.
                PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
            ];

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);

        } catch (PDOException $exception) {
            http_response_code(500);
            echo json_encode([
                "status" => 500,
                "error" => "Error de conexión a la Base de Datos en la nube",
                "detalle" => $exception->getMessage()
            ]);
            exit();
        }

        return $this->conn;
    }
}