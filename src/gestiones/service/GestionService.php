<?php
require_once __DIR__ . '/../entity/DetalleGestion.php';

class GestionService
{
    private $db;

    public function __construct($dbConnection)
    {
        $this->db = $dbConnection;
    }

    // CREATE (POST) - Registrar una acción en la bitácora
    public function createGestion(CreateDetalleGestionDTO $dto)
    {
        $query = "INSERT INTO detalle_gestion (id_usuario, id_recurso, accion) 
                  VALUES (:id_usuario, :id_recurso, :accion)";
        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':id_usuario', $dto->id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':id_recurso', $dto->id_recurso, PDO::PARAM_INT);
        $stmt->bindParam(':accion', $dto->accion);

        try {
            if ($stmt->execute()) {
                return ["status" => 201, "message" => "Gestión registrada en el historial."];
            }
        } catch (PDOException $e) {
            return ["status" => 400, "message" => "Error: Verifica que el usuario y el recurso existan."];
        }
        return ["status" => 500, "message" => "Error al registrar la gestión."];
    }

    // READ ALL (GET) - Ver todo el historial con los nombres de usuario y recurso
    public function getAllGestiones()
    {
        $query = "SELECT dg.id_detalle, dg.accion, 
                         u.user AS nombre_usuario, 
                         r.nombre AS nombre_recurso, r.tipo
                  FROM detalle_gestion dg
                  JOIN usuario u ON dg.id_usuario = u.id_usuario
                  JOIN recursos r ON dg.id_recurso = r.id_recurso
                  ORDER BY dg.id_detalle DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return ["status" => 200, "data" => $resultados];
    }
}