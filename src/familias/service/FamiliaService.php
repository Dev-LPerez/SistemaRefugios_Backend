<?php
require_once __DIR__ . '/../entity/Familia.php';

class FamiliaService
{
    private $db;

    public function __construct($dbConnection)
    {
        $this->db = $dbConnection;
    }

    // CREATE (POST)
    public function createFamilia(CreateFamiliaDTO $dto)
    {
        $query = "INSERT INTO familia (representante, telefono, direccion, cantidad_miembros, prioridad, id_refugio) 
                  VALUES (:representante, :telefono, :direccion, :cantidad_miembros, :prioridad, :id_refugio)";
        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':representante', $dto->representante);
        $stmt->bindParam(':telefono', $dto->telefono);
        $stmt->bindParam(':direccion', $dto->direccion);
        $stmt->bindParam(':cantidad_miembros', $dto->cantidad_miembros, PDO::PARAM_INT);
        $stmt->bindParam(':prioridad', $dto->prioridad);
        $stmt->bindParam(':id_refugio', $dto->id_refugio, PDO::PARAM_INT);

        try {
            if ($stmt->execute()) {
                return ["status" => 201, "message" => "Familia registrada exitosamente."];
            }
        } catch (PDOException $e) {
            return ["status" => 400, "message" => "Error al registrar: Verifica que el id_refugio exista en la base de datos."];
        }
        return ["status" => 500, "message" => "Error interno al registrar la familia."];
    }

    // READ ALL (GET)
    public function getAllFamilias()
    {
        $query = "SELECT * FROM familia";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return ["status" => 200, "data" => $resultados];
    }

    // READ ONE (GET por ID)
    public function getFamiliaById($id)
    {
        $query = "SELECT * FROM familia WHERE id_familia = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $familia = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($familia) {
            return ["status" => 200, "data" => $familia];
        }
        return ["status" => 404, "message" => "Familia no encontrada."];
    }

    // UPDATE (PUT)
    public function updateFamilia(UpdateFamiliaDTO $dto)
    {
        $query = "UPDATE familia 
                  SET representante = :representante, telefono = :telefono, direccion = :direccion, 
                      cantidad_miembros = :cantidad_miembros, prioridad = :prioridad, id_refugio = :id_refugio 
                  WHERE id_familia = :id";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':representante', $dto->representante);
        $stmt->bindParam(':telefono', $dto->telefono);
        $stmt->bindParam(':direccion', $dto->direccion);
        $stmt->bindParam(':cantidad_miembros', $dto->cantidad_miembros, PDO::PARAM_INT);
        $stmt->bindParam(':prioridad', $dto->prioridad);
        $stmt->bindParam(':id_refugio', $dto->id_refugio, PDO::PARAM_INT);
        $stmt->bindParam(':id', $dto->id_familia, PDO::PARAM_INT);

        try {
            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    return ["status" => 200, "message" => "Familia actualizada exitosamente."];
                }
                return ["status" => 404, "message" => "Familia no encontrada o sin cambios."];
            }
        } catch (PDOException $e) {
            return ["status" => 400, "message" => "Error al actualizar: Verifica que el id_refugio exista."];
        }
        return ["status" => 500, "message" => "Error al actualizar la familia."];
    }

    // DELETE (DELETE)
    public function deleteFamilia($id)
    {
        $query = "DELETE FROM familia WHERE id_familia = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        try {
            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    return ["status" => 200, "message" => "Familia eliminada exitosamente."];
                }
                return ["status" => 404, "message" => "Familia no encontrada."];
            }
        } catch (PDOException $e) {
            return ["status" => 409, "message" => "No se puede eliminar: Hay registros dependientes asociados a esta familia."];
        }
        return ["status" => 500, "message" => "Error al eliminar la familia."];
    }
}