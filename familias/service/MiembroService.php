<?php
require_once __DIR__ . '/../entity/Miembro.php';

class MiembroService
{
    private $db;

    public function __construct($dbConnection)
    {
        $this->db = $dbConnection;
    }

    public function createMiembro(CreateMiembroDTO $dto)
    {
        $query = "INSERT INTO miembros (nombre, edad, parentezco, tipo_documento, numero_documento, vulnerable, tipo_vulnerabilidad, id_familia) 
                  VALUES (:nombre, :edad, :parentezco, :tipo_documento, :numero_documento, :vulnerable, :tipo_vulnerabilidad, :id_familia)";
        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':nombre', $dto->nombre);
        $stmt->bindParam(':edad', $dto->edad, PDO::PARAM_INT);
        $stmt->bindParam(':parentezco', $dto->parentezco);
        $stmt->bindParam(':tipo_documento', $dto->tipo_documento);
        $stmt->bindParam(':numero_documento', $dto->numero_documento);
        $stmt->bindParam(':vulnerable', $dto->vulnerable, PDO::PARAM_INT);
        $stmt->bindParam(':tipo_vulnerabilidad', $dto->tipo_vulnerabilidad);
        $stmt->bindParam(':id_familia', $dto->id_familia, PDO::PARAM_INT);

        try {
            if ($stmt->execute()) {
                return ["status" => 201, "message" => "Miembro registrado exitosamente."];
            }
        } catch (PDOException $e) {
            return ["status" => 400, "message" => "Error de BD: Asegúrate de que el id_familia exista."];
        }
        return ["status" => 500, "message" => "Error al registrar el miembro."];
    }

    public function getMiembroById($id)
    {
        $query = "SELECT * FROM miembros WHERE id_persona = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $miembro = $stmt->fetch(PDO::FETCH_ASSOC);
        return $miembro ? ["status" => 200, "data" => $miembro] : ["status" => 404, "message" => "Miembro no encontrado."];
    }

    // Nuevo método: Traer todos los miembros que pertenecen a una familia específica
    public function getMiembrosByFamilia($id_familia)
    {
        $query = "SELECT * FROM miembros WHERE id_familia = :id_familia";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_familia', $id_familia, PDO::PARAM_INT);
        $stmt->execute();

        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return ["status" => 200, "data" => $resultados];
    }

    public function updateMiembro(UpdateMiembroDTO $dto)
    {
        $query = "UPDATE miembros 
                  SET nombre = :nombre, edad = :edad, parentezco = :parentezco, 
                      tipo_documento = :tipo_documento, numero_documento = :numero_documento, 
                      vulnerable = :vulnerable, tipo_vulnerabilidad = :tipo_vulnerabilidad, id_familia = :id_familia 
                  WHERE id_persona = :id";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':nombre', $dto->nombre);
        $stmt->bindParam(':edad', $dto->edad, PDO::PARAM_INT);
        $stmt->bindParam(':parentezco', $dto->parentezco);
        $stmt->bindParam(':tipo_documento', $dto->tipo_documento);
        $stmt->bindParam(':numero_documento', $dto->numero_documento);
        $stmt->bindParam(':vulnerable', $dto->vulnerable, PDO::PARAM_INT);
        $stmt->bindParam(':tipo_vulnerabilidad', $dto->tipo_vulnerabilidad);
        $stmt->bindParam(':id_familia', $dto->id_familia, PDO::PARAM_INT);
        $stmt->bindParam(':id', $dto->id_persona, PDO::PARAM_INT);

        try {
            if ($stmt->execute() && $stmt->rowCount() > 0) {
                return ["status" => 200, "message" => "Miembro actualizado exitosamente."];
            }
            return ["status" => 404, "message" => "Miembro no encontrado o sin cambios."];
        } catch (PDOException $e) {
            return ["status" => 400, "message" => "Error al actualizar."];
        }
    }

    public function deleteMiembro($id)
    {
        $query = "DELETE FROM miembros WHERE id_persona = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute() && $stmt->rowCount() > 0) {
            return ["status" => 200, "message" => "Miembro eliminado exitosamente."];
        }
        return ["status" => 404, "message" => "Miembro no encontrado."];
    }
}