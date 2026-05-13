<?php
require_once __DIR__ . '/../entity/Refugio.php';

class RefugioService
{
    private $db;

    public function __construct($dbConnection)
    {
        $this->db = $dbConnection;
    }

    // CREATE (POST)
    public function createRefugio(CreateRefugioDTO $dto)
    {
        $query = "INSERT INTO refugios (nombre, direccion, capacidad_maxima, ocupacion_actual, estado) 
                  VALUES (:nombre, :direccion, :capacidad_maxima, 0, :estado)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':nombre', $dto->nombre);
        $stmt->bindParam(':direccion', $dto->direccion);
        $stmt->bindParam(':capacidad_maxima', $dto->capacidad_maxima, PDO::PARAM_INT);
        $stmt->bindParam(':estado', $dto->estado);

        if ($stmt->execute()) {
            return ["status" => 201, "message" => "Refugio creado exitosamente."];
        }
        return ["status" => 500, "message" => "Error al crear el refugio."];
    }

    // READ ALL (GET)
    public function getAllRefugios()
    {
        $query = "SELECT * FROM refugios";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return ["status" => 200, "data" => $resultados];
    }

    // READ ONE (GET por ID)
    public function getRefugioById($id)
    {
        $query = "SELECT * FROM refugios WHERE id_refugio = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $refugio = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($refugio) {
            return ["status" => 200, "data" => $refugio];
        }
        return ["status" => 404, "message" => "Refugio no encontrado."];
    }

    // UPDATE (PUT)
    public function updateRefugio(UpdateRefugioDTO $dto)
    {
        // En un escenario real idealmente hacemos update dinámico, aquí pasaremos todo tal cual está actualmente.
        $query = "UPDATE refugios SET 
                    nombre = :nombre, 
                    direccion = :direccion, 
                    capacidad_maxima = :capacidad_maxima, 
                    ocupacion_actual = :ocupacion_actual, 
                    estado = :estado 
                  WHERE id_refugio = :id";
                  
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':nombre', $dto->nombre);
        $stmt->bindParam(':direccion', $dto->direccion);
        $stmt->bindParam(':capacidad_maxima', $dto->capacidad_maxima, PDO::PARAM_INT);
        $stmt->bindParam(':ocupacion_actual', $dto->ocupacion_actual, PDO::PARAM_INT);
        $stmt->bindParam(':estado', $dto->estado);
        $stmt->bindParam(':id', $dto->id_refugio, PDO::PARAM_INT);

        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                return ["status" => 200, "message" => "Refugio actualizado exitosamente."];
            }
            return ["status" => 404, "message" => "Refugio no encontrado o no hubo cambios."];
        }
        return ["status" => 500, "message" => "Error al actualizar el refugio."];
    }

    // DELETE (DELETE)
    public function deleteRefugio($id)
    {
        $query = "DELETE FROM refugios WHERE id_refugio = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        try {
            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    return ["status" => 200, "message" => "Refugio eliminado exitosamente."];
                }
                return ["status" => 404, "message" => "Refugio no encontrado."];
            }
        } catch (PDOException $e) {
            return ["status" => 409, "message" => "No se puede eliminar: El refugio está en uso."];
        }
        return ["status" => 500, "message" => "Error al eliminar el refugio."];
    }
}