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
        $query = "INSERT INTO familias (cedula, representante, telefono, direccion, cantidad_miembros, prioridad, id_refugio, ubicacion_actual, aceptacion_habeas_data) 
                  VALUES (:cedula, :representante, :telefono, :direccion, :cantidad_miembros, :prioridad, :id_refugio, :ubicacion_actual, :aceptacion_habeas_data)";
        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':cedula', $dto->cedula);
        $stmt->bindParam(':representante', $dto->representante);
        $stmt->bindParam(':telefono', $dto->telefono);
        $stmt->bindParam(':direccion', $dto->direccion);
        $stmt->bindParam(':cantidad_miembros', $dto->cantidad_miembros, PDO::PARAM_INT);
        $stmt->bindParam(':prioridad', $dto->prioridad);
        $stmt->bindParam(':id_refugio', $dto->id_refugio, PDO::PARAM_INT);
        $stmt->bindParam(':ubicacion_actual', $dto->ubicacion_actual);
        $stmt->bindParam(':aceptacion_habeas_data', $dto->aceptacion_habeas_data, PDO::PARAM_INT);

        try {
            if ($stmt->execute()) {
                $id_familia = $this->db->lastInsertId();

                // Crear automáticamente al representante como miembro (Cabeza de Hogar)
                $queryMiembro = "INSERT INTO miembros (
                                    nombre, edad, parentezco, tipo_documento, numero_documento, 
                                    vulnerable, tipo_vulnerabilidad, id_familia,
                                    es_embarazada, tiene_discapacidad, enfermedad_cronica
                                 ) VALUES (
                                    :nombre, :edad, :parentezco, :tipo_documento, :numero_documento, 
                                    0, '', :id_familia,
                                    0, 0, 0
                                 )";
                $stmtMiembro = $this->db->prepare($queryMiembro);
                $parentezco = 'Cabeza de Hogar';
                $tipoDoc = 'CC';
                
                $stmtMiembro->bindParam(':nombre', $dto->representante);
                $stmtMiembro->bindValue(':edad', null, PDO::PARAM_NULL);
                $stmtMiembro->bindParam(':parentezco', $parentezco);
                $stmtMiembro->bindParam(':tipo_documento', $tipoDoc);
                $stmtMiembro->bindParam(':numero_documento', $dto->cedula);
                $stmtMiembro->bindParam(':id_familia', $id_familia, PDO::PARAM_INT);
                $stmtMiembro->execute();

                return ["status" => 201, "message" => "Familia registrada exitosamente.", "id" => $id_familia];
            }
        } catch (PDOException $e) {
            // Si la tabla original es 'familia' singular y falló, hacemos un fallback simple en entorno legacy si es necesario
            error_log("Error insertando familia: " . $e->getMessage());
            return ["status" => 400, "message" => "Error DB: " . $e->getMessage()];
        }
        return ["status" => 500, "message" => "Error interno al registrar la familia."];
    }

    // ALL FAMILIAS (GET)
    public function getAllFamilias()
    {
        $query = "SELECT f.*,
                         (SELECT COUNT(*) FROM miembros m WHERE m.id_familia = f.id_familia) AS cantidad_miembros,
                         COALESCE(
                             (
                                 SELECT 10 + SUM(
                                     IF(m.edad < 5, 15, 0) +
                                     IF(m.edad > 65, 15, 0) +
                                     IF(m.es_embarazada = 1, 20, 0) +
                                     IF(m.tiene_discapacidad = 1, 20, 0) +
                                     IF(m.enfermedad_cronica = 1, 10, 0) +
                                     IF(m.vulnerable = 1 AND m.es_embarazada = 0 AND m.tiene_discapacidad = 0 AND m.enfermedad_cronica = 0, 5, 0)
                                 )
                                 FROM miembros m
                                 WHERE m.id_familia = f.id_familia
                             ),
                             10
                         ) AS prioridad
                  FROM familias f";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return ["status" => 200, "data" => $resultados];
    }

    // READ ONE (GET por ID)
    public function getFamiliaById($id)
    {
        $query = "SELECT f.*,
                         (SELECT COUNT(*) FROM miembros m WHERE m.id_familia = f.id_familia) AS cantidad_miembros,
                         COALESCE(
                             (
                                 SELECT 10 + SUM(
                                     IF(m.edad < 5, 15, 0) +
                                     IF(m.edad > 65, 15, 0) +
                                     IF(m.es_embarazada = 1, 20, 0) +
                                     IF(m.tiene_discapacidad = 1, 20, 0) +
                                     IF(m.enfermedad_cronica = 1, 10, 0) +
                                     IF(m.vulnerable = 1 AND m.es_embarazada = 0 AND m.tiene_discapacidad = 0 AND m.enfermedad_cronica = 0, 5, 0)
                                 )
                                 FROM miembros m
                                 WHERE m.id_familia = f.id_familia
                             ),
                             10
                         ) AS prioridad
                  FROM familias f WHERE f.id_familia = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $familia = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($familia) {
            return ["status" => 200, "data" => $familia];
        }
        return ["status" => 404, "message" => "Familia no encontrada."];
    }

    // BUSQUEDA AGIL (GET /search)
    public function searchFamilia($q)
    {
        $query = "SELECT f.*,
                         (SELECT COUNT(*) FROM miembros m WHERE m.id_familia = f.id_familia) AS cantidad_miembros,
                         COALESCE(
                             (
                                 SELECT 10 + SUM(
                                     IF(m.edad < 5, 15, 0) +
                                     IF(m.edad > 65, 15, 0) +
                                     IF(m.es_embarazada = 1, 20, 0) +
                                     IF(m.tiene_discapacidad = 1, 20, 0) +
                                     IF(m.enfermedad_cronica = 1, 10, 0) +
                                     IF(m.vulnerable = 1 AND m.es_embarazada = 0 AND m.tiene_discapacidad = 0 AND m.enfermedad_cronica = 0, 5, 0)
                                 )
                                 FROM miembros m
                                 WHERE m.id_familia = f.id_familia
                             ),
                             10
                         ) AS prioridad
                  FROM familias f 
                  WHERE f.cedula LIKE :q1 OR f.representante LIKE :q2 
                  LIMIT 20";
        $stmt = $this->db->prepare($query);
        $searchParam = "%{$q}%";
        $stmt->bindParam(':q1', $searchParam);
        $stmt->bindParam(':q2', $searchParam);
        $stmt->execute();
        
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return ["status" => 200, "data" => $resultados];
    }

    // UPDATE (PUT)
    public function updateFamilia(UpdateFamiliaDTO $dto)
    {
        $query = "UPDATE familias 
                  SET representante = :representante, telefono = :telefono, direccion = :direccion, 
                      cantidad_miembros = :cantidad_miembros, prioridad = :prioridad, id_refugio = :id_refugio,
                      ubicacion_actual = :ubicacion_actual, aceptacion_habeas_data = :aceptacion_habeas_data
                  WHERE id_familia = :id";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':representante', $dto->representante);
        $stmt->bindParam(':telefono', $dto->telefono);
        $stmt->bindParam(':direccion', $dto->direccion);
        $stmt->bindParam(':cantidad_miembros', $dto->cantidad_miembros, PDO::PARAM_INT);
        $stmt->bindParam(':prioridad', $dto->prioridad);
        $stmt->bindParam(':id_refugio', $dto->id_refugio, PDO::PARAM_INT);
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

                $query = "INSERT INTO familias (cedula, representante, telefono, direccion, cantidad_miembros, prioridad, id_refugio, ubicacion_actual, aceptacion_habeas_data) 
                          VALUES (:cedula, :representante, :telefono, :direccion, :cantidad_miembros, :prioridad, :id_refugio, :ubicacion_actual, :aceptacion_habeas_data)";
                
                $stmt = $this->db->prepare($query);
                
                $stmt->bindParam(':cedula', $dto->cedula);
                $stmt->bindParam(':representante', $dto->representante);
                $stmt->bindParam(':telefono', $dto->telefono);
                $stmt->bindParam(':direccion', $dto->direccion);
                $stmt->bindParam(':cantidad_miembros', $dto->cantidad_miembros, PDO::PARAM_INT);
                $stmt->bindParam(':prioridad', $dto->prioridad);
                $stmt->bindParam(':id_refugio', $dto->id_refugio, PDO::PARAM_INT);
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