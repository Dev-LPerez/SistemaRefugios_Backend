<?php
require_once __DIR__ . '/../entity/Donante.php';

class DonanteService
{
    private $db;

    public function __construct($dbConnection)
    {
        $this->db = $dbConnection;
    }

    // CREATE (POST)
    public function createDonante(CreateDonanteDTO $dto)
    {
        $query = "INSERT INTO donante (identificacion, nombre, tipo, telefono) VALUES (:identificacion, :nombre, :tipo, :telefono)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':identificacion', $dto->identificacion);
        $stmt->bindParam(':nombre', $dto->nombre);
        $stmt->bindParam(':tipo', $dto->tipo);
        $stmt->bindParam(':telefono', $dto->telefono);

        try {
            if ($stmt->execute()) {
                return ["status" => 201, "message" => "Donante registrado exitosamente."];
            }
        } catch (PDOException $e) {
            return ["status" => 400, "message" => "Error: La identificación ya podría estar registrada."];
        }
        return ["status" => 500, "message" => "Error al registrar el donante."];
    }

    // READ ALL (GET)
    public function getAllDonantes()
    {
        $query = "SELECT * FROM donante";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return ["status" => 200, "data" => $resultados];
    }

    // READ ONE (GET por ID)
    public function getDonanteById($id)
    {
        $query = "SELECT * FROM donante WHERE id_donante = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $donante = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($donante) {
            return ["status" => 200, "data" => $donante];
        }
        return ["status" => 404, "message" => "Donante no encontrado."];
    }

    // UPDATE (PUT)
    public function updateDonante(UpdateDonanteDTO $dto)
    {
        $query = "UPDATE donante 
                  SET identificacion = :identificacion, nombre = :nombre, tipo = :tipo, telefono = :telefono 
                  WHERE id_donante = :id";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':identificacion', $dto->identificacion);
        $stmt->bindParam(':nombre', $dto->nombre);
        $stmt->bindParam(':tipo', $dto->tipo);
        $stmt->bindParam(':telefono', $dto->telefono);
        $stmt->bindParam(':id', $dto->id_donante, PDO::PARAM_INT);

        try {
            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    return ["status" => 200, "message" => "Donante actualizado exitosamente."];
                }
                return ["status" => 404, "message" => "Donante no encontrado o sin cambios."];
            }
        } catch (PDOException $e) {
            return ["status" => 400, "message" => "Error al actualizar. Posible identificación duplicada."];
        }
        return ["status" => 500, "message" => "Error al actualizar el donante."];
    }

    // DELETE (DELETE)
    public function deleteDonante($id)
    {
        $query = "DELETE FROM donante WHERE id_donante = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        try {
            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    return ["status" => 200, "message" => "Donante eliminado exitosamente."];
                }
                return ["status" => 404, "message" => "Donante no encontrado."];
            }
        } catch (PDOException $e) {
            return ["status" => 409, "message" => "No se puede eliminar: El donante tiene donaciones registradas en el sistema."];
        }
        return ["status" => 500, "message" => "Error al eliminar el donante."];
    }
}