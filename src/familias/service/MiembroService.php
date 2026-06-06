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
        $query = "INSERT INTO miembros (
                    nombre, edad, parentezco, tipo_documento, numero_documento, 
                    vulnerable, tipo_vulnerabilidad, id_familia,
                    es_embarazada, tiene_discapacidad, enfermedad_cronica
                  ) VALUES (
                    :nombre, :edad, :parentezco, :tipo_documento, :numero_documento, 
                    :vulnerable, :tipo_vulnerabilidad, :id_familia,
                    :es_embarazada, :tiene_discapacidad, :enfermedad_cronica
                  )";
        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':nombre', $dto->nombre);
        $stmt->bindParam(':edad', $dto->edad, PDO::PARAM_INT);
        $stmt->bindParam(':parentezco', $dto->parentezco);
        $stmt->bindParam(':tipo_documento', $dto->tipo_documento);
        $stmt->bindParam(':numero_documento', $dto->numero_documento);
        $stmt->bindParam(':vulnerable', $dto->vulnerable, PDO::PARAM_INT);
        $stmt->bindParam(':tipo_vulnerabilidad', $dto->tipo_vulnerabilidad);
        $stmt->bindParam(':id_familia', $dto->id_familia, PDO::PARAM_INT);
        
        // Bind de los nuevos campos booleanos
        $stmt->bindParam(':es_embarazada', $dto->es_embarazada, PDO::PARAM_INT);
        $stmt->bindParam(':tiene_discapacidad', $dto->tiene_discapacidad, PDO::PARAM_INT);
        $stmt->bindParam(':enfermedad_cronica', $dto->enfermedad_cronica, PDO::PARAM_INT);

        try {
            if ($stmt->execute()) {
                // Actualizar cantidad_miembros en la familia correspondiente
                $updateQuery = "UPDATE familias SET cantidad_miembros = (SELECT COUNT(*) FROM miembros WHERE id_familia = :id_familia) WHERE id_familia = :id_familia2";
                $updateStmt = $this->db->prepare($updateQuery);
                $updateStmt->bindParam(':id_familia', $dto->id_familia, PDO::PARAM_INT);
                $updateStmt->bindParam(':id_familia2', $dto->id_familia, PDO::PARAM_INT);
                $updateStmt->execute();

                return ["status" => 201, "message" => "Miembro registrado exitosamente."];
            }
        } catch (PDOException $e) {
            return ["status" => 400, "message" => "Error de BD: Asegúrate de que el id_familia exista. Detalle: " . $e->getMessage()];
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

    public function getMiembrosByFamilia($id_familia)
    {
        $query = "SELECT * FROM miembros WHERE id_familia = :id_familia";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_familia', $id_familia, PDO::PARAM_INT);
        $stmt->execute();

        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return ["status" => 200, "data" => $resultados];
    }

    public function updateMiembro($dto) // Puedes tiparlo con UpdateMiembroDTO si ya lo tienes listo con los campos
    {
        $query = "UPDATE miembros 
                  SET nombre = :nombre, edad = :edad, parentezco = :parentezco, 
                      tipo_documento = :tipo_documento, numero_documento = :numero_documento, 
                      vulnerable = :vulnerable, tipo_vulnerabilidad = :tipo_vulnerabilidad, id_familia = :id_familia,
                      es_embarazada = :es_embarazada, tiene_discapacidad = :tiene_discapacidad, enfermedad_cronica = :enfermedad_cronica
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
        
        // Bind de los nuevos campos booleanos
        $stmt->bindParam(':es_embarazada', $dto->es_embarazada, PDO::PARAM_INT);
        $stmt->bindParam(':tiene_discapacidad', $dto->tiene_discapacidad, PDO::PARAM_INT);
        $stmt->bindParam(':enfermedad_cronica', $dto->enfermedad_cronica, PDO::PARAM_INT);
        
        $stmt->bindParam(':id', $dto->id_persona, PDO::PARAM_INT);

        try {
            // Consultar familia anterior para actualizarla si cambia de familia
            $getOldFamQuery = "SELECT id_familia FROM miembros WHERE id_persona = :id LIMIT 1";
            $getOldFamStmt = $this->db->prepare($getOldFamQuery);
            $getOldFamStmt->bindParam(':id', $dto->id_persona, PDO::PARAM_INT);
            $getOldFamStmt->execute();
            $oldMiembro = $getOldFamStmt->fetch(PDO::FETCH_ASSOC);
            $old_familia_id = $oldMiembro ? (int)$oldMiembro['id_familia'] : null;

            if ($stmt->execute()) {
                // Actualizar nueva familia
                $updateQuery = "UPDATE familias SET cantidad_miembros = (SELECT COUNT(*) FROM miembros WHERE id_familia = :id_familia) WHERE id_familia = :id_familia2";
                $updateStmt = $this->db->prepare($updateQuery);
                $updateStmt->bindParam(':id_familia', $dto->id_familia, PDO::PARAM_INT);
                $updateStmt->bindParam(':id_familia2', $dto->id_familia, PDO::PARAM_INT);
                $updateStmt->execute();

                // Si cambió de familia, actualizar la familia anterior también
                if ($old_familia_id && $old_familia_id !== (int)$dto->id_familia) {
                    $updateStmt2 = $this->db->prepare($updateQuery);
                    $updateStmt2->bindParam(':id_familia', $old_familia_id, PDO::PARAM_INT);
                    $updateStmt2->bindParam(':id_familia2', $old_familia_id, PDO::PARAM_INT);
                    $updateStmt2->execute();
                }

                return ["status" => 200, "message" => "Miembro actualizado exitosamente."];
            }
            return ["status" => 404, "message" => "Miembro no encontrado o sin cambios."];
        } catch (PDOException $e) {
            return ["status" => 400, "message" => "Error al actualizar."];
        }
    }

    public function deleteMiembro($id)
    {
        // Consultar familia del miembro a eliminar para recalcular
        $getFamQuery = "SELECT id_familia FROM miembros WHERE id_persona = :id LIMIT 1";
        $getFamStmt = $this->db->prepare($getFamQuery);
        $getFamStmt->bindParam(':id', $id, PDO::PARAM_INT);
        $getFamStmt->execute();
        $miembro = $getFamStmt->fetch(PDO::FETCH_ASSOC);
        $id_familia = $miembro ? (int)$miembro['id_familia'] : null;

        $query = "DELETE FROM miembros WHERE id_persona = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute() && $stmt->rowCount() > 0) {
            if ($id_familia) {
                $updateQuery = "UPDATE familias SET cantidad_miembros = (SELECT COUNT(*) FROM miembros WHERE id_familia = :id_familia) WHERE id_familia = :id_familia2";
                $updateStmt = $this->db->prepare($updateQuery);
                $updateStmt->bindParam(':id_familia', $id_familia, PDO::PARAM_INT);
                $updateStmt->bindParam(':id_familia2', $id_familia, PDO::PARAM_INT);
                $updateStmt->execute();
            }
            return ["status" => 200, "message" => "Miembro eliminado exitosamente."];
        }
        return ["status" => 404, "message" => "Miembro no encontrado."];
    }
}