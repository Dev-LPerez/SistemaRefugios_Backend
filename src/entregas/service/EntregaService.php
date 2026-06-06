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
        $query = "SELECT fecha FROM entregas 
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

            // 1. Crear Header de Entrega
            $queryHeader = "INSERT INTO entregas (fecha, estado, id_familia) VALUES (:fecha, :estado, :id_familia)";
            $stmtHeader = $this->db->prepare($queryHeader);
            $stmtHeader->bindParam(':fecha', $dto->fecha);
            $stmtHeader->bindParam(':estado', $dto->estado);
            $stmtHeader->bindParam(':id_familia', $dto->id_familia, PDO::PARAM_INT);
            $stmtHeader->execute();

            $id_entrega = $this->db->lastInsertId();

            foreach ($dto->recursos as $item) {
                $idRecurso = (int)$item['id_recurso'];
                $cantidad = (int)$item['cantidad'];

                // 2. Verificar si hay suficiente inventario
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

                // 3. Insertar el registro de la entrega
                $queryInsert = "INSERT INTO detalle_entrega (id_entrega, cantidad, id_recurso) 
                                VALUES (:id_entrega, :cantidad, :id_recurso)";
                $stmtInsert = $this->db->prepare($queryInsert);
                $stmtInsert->bindParam(':id_entrega', $id_entrega, PDO::PARAM_INT);
                $stmtInsert->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
                $stmtInsert->bindParam(':id_recurso', $idRecurso, PDO::PARAM_INT);
                $stmtInsert->execute();

                // 4. Descontar del inventario
                $queryUpdate = "UPDATE recursos SET cantidad_disponible = cantidad_disponible - :decremento 
                WHERE id_recurso = :id_recurso";
                $stmtUpdate = $this->db->prepare($queryUpdate);
                $stmtUpdate->bindParam(':decremento', $cantidad, PDO::PARAM_INT);
                $stmtUpdate->bindParam(':id_recurso', $idRecurso, PDO::PARAM_INT);
                $stmtUpdate->execute();
            }

            $this->db->commit();
            return ["status" => 201, "message" => "Entrega registrada exitosamente y el inventario ha sido actualizado.", "id_entrega" => $id_entrega];

        } catch (Exception $e) {
            $this->db->rollBack();
            $errorMsg = $e->getMessage();
            $status = (strpos($errorMsg, 'Stock') !== false || strpos($errorMsg, 'Bloqueo') !== false || strpos($errorMsg, 'inválidos') !== false) ? 400 : 500;
            return ["status" => $status, "message" => "Error al procesar la entrega.", "error" => $errorMsg];
        }
    }

    // READ ALL (GET) - Usamos JOIN para traer datos legibles y los agrupamos por entrega
    public function getAllEntregas()
    {
        $query = "SELECT e.id_entrega, e.estado, e.fecha, e.id_familia,
                         f.representante AS familia_representante, 
                         de.id_detalle, r.id_recurso, r.nombre AS recurso_nombre, r.unidad, de.cantidad 
                  FROM entregas e
                  JOIN detalle_entrega de ON e.id_entrega = de.id_entrega
                  JOIN familias f ON e.id_familia = f.id_familia
                  JOIN recursos r ON de.id_recurso = r.id_recurso
                  ORDER BY e.fecha DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $agrupado = [];
        foreach ($resultados as $row) {
            $id = $row['id_entrega'];
            if (!isset($agrupado[$id])) {
                $agrupado[$id] = [
                    'id_entrega' => $id,
                    'estado' => $row['estado'],
                    'fecha' => $row['fecha'],
                    'id_familia' => $row['id_familia'],
                    'familia_representante' => $row['familia_representante'],
                    'detalles' => []
                ];
            }
            $agrupado[$id]['detalles'][] = [
                'id_detalle' => $row['id_detalle'],
                'id_recurso' => $row['id_recurso'],
                'recurso_nombre' => $row['recurso_nombre'],
                'unidad' => $row['unidad'],
                'cantidad' => $row['cantidad']
            ];
        }

        return ["status" => 200, "data" => array_values($agrupado)];
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
            $detalles = $stmtSelect->fetchAll(PDO::FETCH_ASSOC);

            if (empty($detalles)) {
                throw new Exception("Entrega no encontrada.");
            }

            // 2. Borrar la entrega (los detalles se deben borrar por cascada si está configurado, 
            // pero por seguridad borramos la cabecera, asegurando que la FK borre en detalle_entrega)
            $queryDelete = "DELETE FROM entregas WHERE id_entrega = :id";
            $stmtDelete = $this->db->prepare($queryDelete);
            $stmtDelete->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtDelete->execute();

            // 3. Devolver la cantidad al inventario por cada detalle
            foreach ($detalles as $detalle) {
                $queryRefund = "UPDATE recursos SET cantidad_disponible = cantidad_disponible + :incremento WHERE id_recurso = :id_recurso";
                $stmtRefund = $this->db->prepare($queryRefund);
                $stmtRefund->bindParam(':incremento', $detalle['cantidad'], PDO::PARAM_INT);
                $stmtRefund->bindParam(':id_recurso', $detalle['id_recurso'], PDO::PARAM_INT);
                $stmtRefund->execute();
            }

            $this->db->commit();
            return ["status" => 200, "message" => "Entrega eliminada. El inventario ha sido devuelto."];

        } catch (Exception $e) {
            $this->db->rollBack();
            return ["status" => 404, "message" => $e->getMessage()];
        }
    }

    // UPDATE STATUS (PUT) - Actualizar el estado de la entrega y gestionar el inventario en consecuencia
    public function updateEstado($id, $estado)
    {
        try {
            $this->db->beginTransaction();

            // 1. Obtener estado anterior
            $queryGet = "SELECT estado FROM entregas WHERE id_entrega = :id";
            $stmtGet = $this->db->prepare($queryGet);
            $stmtGet->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtGet->execute();
            $entrega = $stmtGet->fetch(PDO::FETCH_ASSOC);

            if (!$entrega) {
                throw new Exception("Entrega no encontrada.");
            }

            $estadoAnterior = strtolower($entrega['estado']);
            $estadoNuevo = strtolower($estado);

            if ($estadoAnterior !== $estadoNuevo) {
                // Normalizar estado para la base de datos (enum: pendiente, entregado, cancelado)
                $dbEstado = $estadoNuevo;
                if ($dbEstado === 'cancelada') $dbEstado = 'cancelado';

                // 2. Actualizar el estado de la cabecera
                $queryUpdate = "UPDATE entregas SET estado = :estado WHERE id_entrega = :id";
                $stmtUpdate = $this->db->prepare($queryUpdate);
                $stmtUpdate->bindParam(':estado', $dbEstado);
                $stmtUpdate->bindParam(':id', $id, PDO::PARAM_INT);
                $stmtUpdate->execute();

                // 3. Manejar inventario si pasa a 'cancelado' o si se reactiva desde 'cancelado'
                // Obtener los detalles
                $querySelect = "SELECT id_recurso, cantidad FROM detalle_entrega WHERE id_entrega = :id";
                $stmtSelect = $this->db->prepare($querySelect);
                $stmtSelect->bindParam(':id', $id, PDO::PARAM_INT);
                $stmtSelect->execute();
                $detalles = $stmtSelect->fetchAll(PDO::FETCH_ASSOC);

                if ($dbEstado === 'cancelado') {
                    // Devolver stock
                    foreach ($detalles as $detalle) {
                        $queryRefund = "UPDATE recursos SET cantidad_disponible = cantidad_disponible + :incremento WHERE id_recurso = :id_recurso";
                        $stmtRefund = $this->db->prepare($queryRefund);
                        $stmtRefund->bindParam(':incremento', $detalle['cantidad'], PDO::PARAM_INT);
                        $stmtRefund->bindParam(':id_recurso', $detalle['id_recurso'], PDO::PARAM_INT);
                        $stmtRefund->execute();
                    }
                } elseif ($estadoAnterior === 'cancelado') {
                    // Descontar stock (validando disponibilidad)
                    foreach ($detalles as $detalle) {
                        // Validar stock
                        $queryStock = "SELECT cantidad_disponible, nombre FROM recursos WHERE id_recurso = :id_recurso FOR UPDATE";
                        $stmtStock = $this->db->prepare($queryStock);
                        $stmtStock->bindParam(':id_recurso', $detalle['id_recurso'], PDO::PARAM_INT);
                        $stmtStock->execute();
                        $recurso = $stmtStock->fetch(PDO::FETCH_ASSOC);

                        if ($recurso['cantidad_disponible'] < $detalle['cantidad']) {
                            throw new Exception("Stock insuficiente para reactivar entrega. Solo quedan " . $recurso['cantidad_disponible'] . " de " . $recurso['nombre']);
                        }

                        // Descontar
                        $queryUpdateStock = "UPDATE recursos SET cantidad_disponible = cantidad_disponible - :decremento WHERE id_recurso = :id_recurso";
                        $stmtUpdateStock = $this->db->prepare($queryUpdateStock);
                        $stmtUpdateStock->bindParam(':decremento', $detalle['cantidad'], PDO::PARAM_INT);
                        $stmtUpdateStock->bindParam(':id_recurso', $detalle['id_recurso'], PDO::PARAM_INT);
                        $stmtUpdateStock->execute();
                    }
                }
            }

            $this->db->commit();
            return ["status" => 200, "message" => "Estado de entrega actualizado correctamente."];

        } catch (Exception $e) {
            $this->db->rollBack();
            return ["status" => 400, "message" => $e->getMessage()];
        }
    }
}
