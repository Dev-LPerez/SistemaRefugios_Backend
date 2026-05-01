<?php
class Database
{
    private $host = "127.0.0.1";      // Cambiado a IP para mejor resolución con puertos
    private $port = "3307";           // <--- EL PUERTO QUE VIMOS EN TU IMAGEN
    private $db_name = "sistema_refugios";
    private $username = "root";
    private $password = "";           // Confirmado que está vacío

    public $conn;

    public function getConnection()
    {
        $this->conn = null;

        try {
            // Agregamos el parámetro port= al DSN
            $dsn = "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name . ";charset=utf8mb4";

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);

        } catch (PDOException $exception) {
            http_response_code(500);
            echo json_encode([
                "status" => 500,
                "error" => "Error de conexión a la Base de Datos",
                "detalle" => $exception->getMessage()
            ]);
            exit();
        }

        return $this->conn;
    }
}