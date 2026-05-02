<?php
require_once __DIR__ . '/../entity/Donacion.php';

class DonacionService
{
    private $db;

    public function __construct($dbConnection)
    {
        $this->db = $dbConnection;
    }

    // Crear la "cabecera" de la donación
    public function createDonacion(CreateDonacionDTO $dto)
    {
        $query = "INSERT INTO donacion (fecha, descripcion, id_donante) VALUES (:fecha, :descripcion, :id_donante)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':fecha', $dto->fecha);
        $stmt->bindParam(':descripcion', $dto->descripcion);
        $stmt->bindParam(':id_donante', $dto->id_donante, PDO::PARAM_INT);

        try {
            if ($stmt->execute()) {
                // Devolvemos el ID generado para poder agregarle detalles enseguida
                $id_generado = $this->db->lastInsertId();
                return ["status" => 201, "message" => "Donación registrada.", "id_donacion" => $id_generado];
            }
        } catch (PDOException $e) {
            return ["status" => 400, "message" => "Error: Verifique que el id_donante exista."];
        }
        return ["status" => 500, "message" => "Error al registrar la donación."];
    }

    // ESTO ES CLAVE: Agregar un detalle y actualizar el inventario usando una TRANSACCIÓN
    public function addDetalleDonacion($id_donacion, $id_recurso, $cantidad)
    {
        try {
            // Iniciamos la transacción (Si algo falla, no se guarda nada)
            $this->db->beginTransaction();

            // 1. Insertamos el detalle de la donación
            $queryDetalle = "INSERT INTO detalle_donacion (id_donacion, id_recurso, cantidad) VALUES (:id_donacion, :id_recurso, :cantidad)";
            $stmtDetalle = $this->db->prepare($queryDetalle);
            $stmtDetalle->bindParam(':id_donacion', $id_donacion, PDO::PARAM_INT);
            $stmtDetalle->bindParam(':id_recurso', $id_recurso, PDO::PARAM_INT);
            $stmtDetalle->bindParam(':cantidad', $cantidad);
            $stmtDetalle->execute();

            // 2. Actualizamos el inventario sumando la cantidad al recurso existente
            $queryInventario = "UPDATE recursos SET cantidad_disponible = cantidad_disponible + :cantidad WHERE id_recurso = :id_recurso";
            $stmtInventario = $this->db->prepare($queryInventario);
            $stmtInventario->bindParam(':cantidad', $cantidad);
            $stmtInventario->bindParam(':id_recurso', $id_recurso, PDO::PARAM_INT);
            $stmtInventario->execute();

            // Si ambas consultas fueron exitosas, confirmamos los cambios
            $this->db->commit();
            return ["status" => 201, "message" => "Detalle agregado y el inventario del recurso ha aumentado."];

        } catch (PDOException $e) {
            // Si algo falla (ej. el id_recurso no existe), revertimos todo
            $this->db->rollBack();
            return ["status" => 400, "message" => "Error al procesar el detalle. Transacción revertida.", "error" => $e->getMessage()];
        }
    }

    // Obtener todas las donaciones con sus respectivos detalles (JOIN)
    public function getDonacionCompleta($id_donacion)
    {
        // Cabecera
        $query = "SELECT * FROM donacion WHERE id_donacion = :id_donacion LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_donacion', $id_donacion, PDO::PARAM_INT);
        $stmt->execute();
        $donacion = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($donacion) {
            // Detalles usando JOIN con recursos para traer el nombre
            $queryDetalles = "SELECT dd.id_detalle, r.nombre, r.tipo, r.unidad, dd.cantidad 
                              FROM detalle_donacion dd 
                              JOIN recursos r ON dd.id_recurso = r.id_recurso 
                              WHERE dd.id_donacion = :id_donacion";
            $stmtDetalles = $this->db->prepare($queryDetalles);
            $stmtDetalles->bindParam(':id_donacion', $id_donacion, PDO::PARAM_INT);
            $stmtDetalles->execute();
            $donacion['detalles'] = $stmtDetalles->fetchAll(PDO::FETCH_ASSOC);

            return ["status" => 200, "data" => $donacion];
        }
        return ["status" => 404, "message" => "Donación no encontrada."];
    }
}