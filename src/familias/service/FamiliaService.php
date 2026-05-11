<?php
require_once __DIR__ . '/../entity/Familia.php';

class FamiliaService
{
    private $db;

    public function __construct($dbConnection)
    {
        $this->db = $dbConnection;
    }

    public function checkDuplicidad($cedula)
    {
        $query = "SELECT id_familia FROM familias WHERE cedula = :cedula LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':cedula', $cedula);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    }
    public function createFamilia(CreateFamiliaDTO $dto)
    {
        // Validar Duplicidad por Cedula si se proporciona (requerimiento fase 2)
        if (!empty($dto->cedula) && $this->checkDuplicidad($dto->cedula)) {
             return ["status" => 409, "message" => "Error: La familia o representante con esta cédula ya existe."];
        }

        // Manejo retrospectivo a nivel de tabla
        // Asumiendo que la tabla se llama 'familias' (plural) o 'familia' (depende del esquema original)
        // Usaremos el esquema de query base pero añadiendo los nuevos campos.
        $query = "INSERT INTO familias (cedula, representante, telefono, direccion, cantidad_miembros, prioridad, refugio_id, ubicacion_actual, aceptacion_habeas_data) 
                  VALUES (:cedula, :representante, :telefono, :direccion, :cantidad_miembros, :prioridad, :refugio_id, :ubicacion_actual, :aceptacion_habeas_data)";
        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':cedula', $dto->cedula);
        $stmt->bindParam(':representante', $dto->representante);
        $stmt->bindParam(':telefono', $dto->telefono);
        $stmt->bindParam(':direccion', $dto->direccion);
        $stmt->bindParam(':cantidad_miembros', $dto->cantidad_miembros, PDO::PARAM_INT);
        $stmt->bindParam(':prioridad', $dto->prioridad);
        $stmt->bindParam(':refugio_id', $dto->refugio_id, PDO::PARAM_INT);
        $stmt->bindParam(':ubicacion_actual', $dto->ubicacion_actual);
        $stmt->bindParam(':aceptacion_habeas_data', $dto->aceptacion_habeas_data, PDO::PARAM_INT);

        try {
            if ($stmt->execute()) {
                return ["status" => 201, "message" => "Familia registrada exitosamente.", "id" => $this->db->lastInsertId()];
            }
        } catch (PDOException $e) {
            // Si la tabla original es 'familia' singular y falló, hacemos un fallback simple en entorno legacy si es necesario
            error_log("Error insertando familia: " . $e->getMessage());
            return ["status" => 400, "message" => "Error al registrar: Verifica los datos provistos."];
        }
        return ["status" => 500, "message" => "Error interno al registrar la familia."];
    }

    // ALL FAMILIAS (GET)
    public function getAllFamilias()
    {
        $query = "SELECT * FROM familias";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return ["status" => 200, "data" => $resultados];
    }

    // READ ONE (GET por ID)
    public function getFamiliaById($id)
    {
        $query = "SELECT * FROM familias WHERE id_familia = :id LIMIT 1";
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
        $query = "UPDATE familias 
                  SET representante = :representante, telefono = :telefono, direccion = :direccion, 
                      cantidad_miembros = :cantidad_miembros, prioridad = :prioridad, refugio_id = :refugio_id,
                      ubicacion_actual = :ubicacion_actual, aceptacion_habeas_data = :aceptacion_habeas_data
                  WHERE id_familia = :id";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':representante', $dto->representante);
        $stmt->bindParam(':telefono', $dto->telefono);
        $stmt->bindParam(':direccion', $dto->direccion);
        $stmt->bindParam(':cantidad_miembros', $dto->cantidad_miembros, PDO::PARAM_INT);
        $stmt->bindParam(':prioridad', $dto->prioridad);
        $stmt->bindParam(':refugio_id', $dto->refugio_id, PDO::PARAM_INT);
        $stmt->bindParam(':ubicacion_actual', $dto->ubicacion_actual);
        $stmt->bindParam(':aceptacion_habeas_data', $dto->aceptacion_habeas_data, PDO::PARAM_INT);
        $stmt->bindParam(':id', $dto->id_familia, PDO::PARAM_INT);

        try {
            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    return ["status" => 200, "message" => "Familia actualizada exitosamente."];
                }
                return ["status" => 404, "message" => "Familia no encontrada o sin cambios."];
            }
        } catch (PDOException $e) {
            return ["status" => 400, "message" => "Error al actualizar: Verifica los datos."];
        }
        return ["status" => 500, "message" => "Error al actualizar la familia."];
    }

    // DELETE (DELETE)
    public function deleteFamilia($id)
    {
        $query = "DELETE FROM familias WHERE id_familia = :id";
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

    // SINCRONIZACIÓN MASIVA (POST /sync)
    public function syncMasivo(array $datosFamilias)
    {
        $this->db->beginTransaction();

        $creadas = 0;
        $omitidas = 0;

        try {
            foreach ($datosFamilias as $datos) {
                // Verificar si tiene cedula para no crear conflictos
                if (empty($datos['cedula'])) {
                    $omitidas++;
                    continue;
                }

                if ($this->checkDuplicidad($datos['cedula'])) {
                    $omitidas++;
                    continue; // Skip ya que existe
                }

                // Usamos el DTO para estandarizar
                $dto = new CreateFamiliaDTO($datos);
                
                // Si el offline sync forzó algunos saltos, validamos igual
                if (!$dto->isValid()) {
                    $omitidas++;
                    continue;
                }

                $query = "INSERT INTO familias (cedula, representante, telefono, direccion, cantidad_miembros, prioridad, refugio_id, ubicacion_actual, aceptacion_habeas_data) 
                          VALUES (:cedula, :representante, :telefono, :direccion, :cantidad_miembros, :prioridad, :refugio_id, :ubicacion_actual, :aceptacion_habeas_data)";
                
                $stmt = $this->db->prepare($query);
                
                $stmt->bindParam(':cedula', $dto->cedula);
                $stmt->bindParam(':representante', $dto->representante);
                $stmt->bindParam(':telefono', $dto->telefono);
                $stmt->bindParam(':direccion', $dto->direccion);
                $stmt->bindParam(':cantidad_miembros', $dto->cantidad_miembros, PDO::PARAM_INT);
                $stmt->bindParam(':prioridad', $dto->prioridad);
                $stmt->bindParam(':refugio_id', $dto->refugio_id, PDO::PARAM_INT);
                $stmt->bindParam(':ubicacion_actual', $dto->ubicacion_actual);
                $stmt->bindParam(':aceptacion_habeas_data', $dto->aceptacion_habeas_data, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    $creadas++;
                } else {
                    $omitidas++;
                }
            }

            $this->db->commit();
            return ["status" => 200, "message" => "Sincronización completada", "creadas" => $creadas, "duplicadas_o_invalidas" => $omitidas];

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Rollback Sync Masivo: " . $e->getMessage());
            return ["status" => 500, "message" => "Error crítico en sincronización masiva, cambios revertidos.", "error" => $e->getMessage()];
        }
    }
}