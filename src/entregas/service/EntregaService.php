<?php
require_once __DIR__ . '/../entity/Entrega.php';

class EntregaService
{
    private $db;

    public function __construct($dbConnection)
    {
        $this->db = $dbConnection;
    }

    // Regla de 72 Horas (RF-05.02)
    public function checkUltimaEntrega($id_familia)
    {
        $query = "SELECT fecha FROM detalle_entrega 
                  WHERE id_familia = :id_familia 
                  ORDER BY fecha DESC LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_familia', $id_familia, PDO::PARAM_INT);
        $stmt->execute();
        $ultima = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($ultima) {
            $fechaActual = new DateTime('now', new DateTimeZone('America/Bogota'));
            $fechaUltima = new DateTime($ultima['fecha'], new DateTimeZone('America/Bogota'));
            $diferencia = $fechaActual->diff($fechaUltima);
            
            if ($diferencia->days < 3) {
                throw new Exception("Bloqueo de seguridad: La familia recibió apoyo hace menos de 72 horas (última entrega: " . $ultima['fecha'] . ").");
            }
        }
        return true;
    }

    // CREATE (POST) - Transaccional con validación de inventario (Soporta múltiples recursos)
    public function createEntrega(CreateEntregaDTO $dto)
    {
        try {
            $this->db->beginTransaction();

            // 0. Validar la regla de restricción de 3 días (solo una vez por familia)
            $this->checkUltimaEntrega($dto->id_familia);

            foreach ($dto->recursos as $item) {
                $idRecurso = (int)$item['id_recurso'];
                $cantidad = (int)$item['cantidad'];

                // 1. Verificar si hay suficiente inventario
                $queryStock = "SELECT cantidad_disponible, nombre FROM recursos WHERE id_recurso = :id_recurso FOR UPDATE";
                $stmtStock = $this->db->prepare($queryStock);
                $stmtStock->bindParam(':id_recurso', $idRecurso, PDO::PARAM_INT);
                $stmtStock->execute();
                $recurso = $stmtStock->fetch(PDO::FETCH_ASSOC);

                if (!$recurso) {
                    throw new Exception("El recurso con ID " . $idRecurso . " no existe.");
                }

                if ($recurso['cantidad_disponible'] < $cantidad) {
                    throw new Exception("Stock insuficiente. Solo quedan " . $recurso['cantidad_disponible'] . " de " . $recurso['nombre']);
                }

                // 2. Insertar el registro de la entrega
                $queryInsert = "INSERT INTO detalle_entrega (estado, fecha, cantidad, id_familia, id_recurso) 
                                VALUES (:estado, :fecha, :cantidad, :id_familia, :id_recurso)";
                $stmtInsert = $this->db->prepare($queryInsert);
                $stmtInsert->bindParam(':estado', $dto->estado);
                $stmtInsert->bindParam(':fecha', $dto->fecha);
                $stmtInsert->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
                $stmtInsert->bindParam(':id_familia', $dto->id_familia, PDO::PARAM_INT);
                $stmtInsert->bindParam(':id_recurso', $idRecurso, PDO::PARAM_INT);
                $stmtInsert->execute();

                // 3. Descontar del inventario
                $queryUpdate = "UPDATE recursos SET cantidad_disponible = cantidad_disponible - :decremento 
                WHERE id_recurso = :id_recurso";
                $stmtUpdate = $this->db->prepare($queryUpdate);
                $stmtUpdate->bindParam(':decremento', $cantidad, PDO::PARAM_INT);
                $stmtUpdate->bindParam(':id_recurso', $idRecurso, PDO::PARAM_INT);
                $stmtUpdate->execute();
            }

            $this->db->commit();
            return ["status" => 201, "message" => "Entrega registrada exitosamente y el inventario ha sido actualizado."];

        } catch (Exception $e) {
            $this->db->rollBack();
            $errorMsg = $e->getMessage();
            $status = (strpos($errorMsg, 'Stock') !== false || strpos($errorMsg, 'Bloqueo') !== false || strpos($errorMsg, 'inválidos') !== false) ? 400 : 500;
            return ["status" => $status, "message" => "Error al procesar la entrega.", "error" => $errorMsg];
        }
    }

    // READ ALL (GET) - Usamos JOIN para traer datos legibles en vez de solo IDs
    public function getAllEntregas()
    {
        $query = "SELECT de.id_entrega, de.estado, de.fecha, de.cantidad, 
                         f.representante AS familia_representante, 
                         r.nombre AS recurso_nombre, r.unidad 
                  FROM detalle_entrega de
                  JOIN familias f ON de.id_familia = f.id_familia
                  JOIN recursos r ON de.id_recurso = r.id_recurso
                  ORDER BY de.fecha DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return ["status" => 200, "data" => $resultados];
    }

    // DELETE (DELETE) - Si borramos una entrega, devolvemos el stock al inventario
    public function deleteEntrega($id)
    {
        try {
            $this->db->beginTransaction();

            // 1. Obtener los datos de la entrega antes de borrarla
            $querySelect = "SELECT id_recurso, cantidad FROM detalle_entrega WHERE id_entrega = :id";
            $stmtSelect = $this->db->prepare($querySelect);
            $stmtSelect->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtSelect->execute();
            $entrega = $stmtSelect->fetch(PDO::FETCH_ASSOC);

            if (!$entrega) {
                throw new Exception("Entrega no encontrada.");
            }

            // 2. Borrar la entrega
            $queryDelete = "DELETE FROM detalle_entrega WHERE id_entrega = :id";
            $stmtDelete = $this->db->prepare($queryDelete);
            $stmtDelete->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtDelete->execute();

            // 3. Devolver la cantidad al inventario
            // FIX: `:cantidad` no puede repetirse en la misma query → se usa `:incremento`
            $queryRefund = "UPDATE recursos SET cantidad_disponible = cantidad_disponible + :incremento WHERE id_recurso = :id_recurso";
            $stmtRefund = $this->db->prepare($queryRefund);
            $stmtRefund->bindParam(':incremento', $entrega['cantidad'], PDO::PARAM_INT);
            $stmtRefund->bindParam(':id_recurso', $entrega['id_recurso'], PDO::PARAM_INT);
            $stmtRefund->execute();

            $this->db->commit();
            return ["status" => 200, "message" => "Entrega eliminada. El inventario ha sido devuelto."];

        } catch (Exception $e) {
            $this->db->rollBack();
            return ["status" => 404, "message" => $e->getMessage()];
        }
    }
}
