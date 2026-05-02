<?php
require_once __DIR__ . '/../entity/Recurso.php';

class RecursoService
{
    private $db;

    public function __construct($dbConnection)
    {
        $this->db = $dbConnection;
    }

    // CREATE (POST)
    public function createRecurso(CreateRecursoDTO $dto)
    {
        $query = "INSERT INTO recursos (nombre, tipo, unidad, cantidad_disponible) 
                  VALUES (:nombre, :tipo, :unidad, :cantidad_disponible)";
        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':nombre', $dto->nombre);
        $stmt->bindParam(':tipo', $dto->tipo);
        $stmt->bindParam(':unidad', $dto->unidad);
        $stmt->bindParam(':cantidad_disponible', $dto->cantidad_disponible);

        if ($stmt->execute()) {
            return ["status" => 201, "message" => "Recurso creado exitosamente."];
        }
        return ["status" => 500, "message" => "Error al crear el recurso."];
    }

    // READ ALL (GET)
    public function getAllRecursos()
    {
        $query = "SELECT * FROM recursos";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return ["status" => 200, "data" => $resultados];
    }

    // READ ONE (GET por ID)
    public function getRecursoById($id)
    {
        $query = "SELECT * FROM recursos WHERE id_recurso = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $recurso = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($recurso) {
            return ["status" => 200, "data" => $recurso];
        }
        return ["status" => 404, "message" => "Recurso no encontrado."];
    }

    // UPDATE (PUT) - Para modificar nombre, tipo o hacer ajustes manuales de inventario
    public function updateRecurso(UpdateRecursoDTO $dto)
    {
        $query = "UPDATE recursos 
                  SET nombre = :nombre, tipo = :tipo, unidad = :unidad, cantidad_disponible = :cantidad_disponible 
                  WHERE id_recurso = :id";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':nombre', $dto->nombre);
        $stmt->bindParam(':tipo', $dto->tipo);
        $stmt->bindParam(':unidad', $dto->unidad);
        $stmt->bindParam(':cantidad_disponible', $dto->cantidad_disponible);
        $stmt->bindParam(':id', $dto->id_recurso, PDO::PARAM_INT);

        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                return ["status" => 200, "message" => "Recurso actualizado exitosamente."];
            }
            return ["status" => 404, "message" => "Recurso no encontrado o sin cambios."];
        }
        return ["status" => 500, "message" => "Error al actualizar el recurso."];
    }

    // DELETE (DELETE)
    public function deleteRecurso($id)
    {
        $query = "DELETE FROM recursos WHERE id_recurso = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        try {
            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    return ["status" => 200, "message" => "Recurso eliminado exitosamente."];
                }
                return ["status" => 404, "message" => "Recurso no encontrado."];
            }
        } catch (PDOException $e) {
            return ["status" => 409, "message" => "No se puede eliminar: El recurso está vinculado a donaciones, entregas o gestiones históricas."];
        }
        return ["status" => 500, "message" => "Error al eliminar el recurso."];
    }
}