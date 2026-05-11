<?php
require_once __DIR__ . '/../entity/Recurso.php';

class RecursoService
{
    private $db;

    public function __construct($dbConnection)
    {
        $this->db = $dbConnection;
    }

    // CREATE (POST)
    public function createRecurso(CreateRecursoDTO $dto)
    {
        $query = "INSERT INTO recursos (nombre, tipo, unidad, cantidad_disponible, categoria, stock) 
                  VALUES (:nombre, :tipo, :unidad, :cantidad_disponible, :categoria, :stock)";
        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':nombre', $dto->nombre);
        $stmt->bindParam(':tipo', $dto->tipo);
        $stmt->bindParam(':unidad', $dto->unidad);
        $stmt->bindParam(':cantidad_disponible', $dto->cantidad_disponible);
        $stmt->bindParam(':categoria', $dto->categoria);
        $stmt->bindParam(':stock', $dto->stock);

        if ($stmt->execute()) {
            return ["status" => 201, "message" => "Recurso creado exitosamente."];
        }
        return ["status" => 500, "message" => "Error al crear el recurso."];
    }

    // READ ALL (GET)
    public function getAllRecursos()
    {
        $query = "SELECT * FROM recursos";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return ["status" => 200, "data" => $resultados];
    }

    // READ ONE (GET por ID)
    public function getRecursoById($id)
    {
        $query = "SELECT * FROM recursos WHERE id_recurso = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $recurso = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($recurso) {
            return ["status" => 200, "data" => $recurso];
        }
        return ["status" => 404, "message" => "Recurso no encontrado."];
    }

    // UPDATE (PUT) - Para modificar nombre, tipo o hacer ajustes manuales de inventario
    public function updateRecurso(UpdateRecursoDTO $dto)
    {
        $query = "UPDATE recursos 
                  SET nombre = :nombre, tipo = :tipo, unidad = :unidad, 
                      cantidad_disponible = :cantidad_disponible, categoria = :categoria, stock = :stock
                  WHERE id_recurso = :id";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':nombre', $dto->nombre);
        $stmt->bindParam(':tipo', $dto->tipo);
        $stmt->bindParam(':unidad', $dto->unidad);
        $stmt->bindParam(':cantidad_disponible', $dto->cantidad_disponible);
        $stmt->bindParam(':categoria', $dto->categoria);
        $stmt->bindParam(':stock', $dto->stock);
        $stmt->bindParam(':id', $dto->id_recurso, PDO::PARAM_INT);

        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                return ["status" => 200, "message" => "Recurso actualizado exitosamente."];
            }
            return ["status" => 404, "message" => "Recurso no encontrado o sin cambios."];
        }
        return ["status" => 500, "message" => "Error al actualizar el recurso."];
    }

    // RF-03.03 Alertas de Stock -> devuelve artículos cuyo stock < 10
    public function getAlertasStock()
    {
        // Se asume un umbral crítico de 10 unidades/kg
        $query = "SELECT id_recurso, nombre, tipo, unidad, stock, cantidad_disponible 
                  FROM recursos 
                  WHERE (stock < 10) OR (stock IS NULL AND cantidad_disponible < 10)";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $mensaje = empty($resultados) ? "Stock saludable." : "Atención: Hay recursos con stock crítico.";
        
        return [
            "status" => 200, 
            "message" => $mensaje, 
            "data" => $resultados
        ];
    }
    
    // Calcula el total general actual
    public function getStockReal()
    {
        $query = "SELECT SUM(COALESCE(stock, cantidad_disponible, 0)) as total_almacen, COUNT(id_recurso) as total_items FROM recursos";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            "status" => 200, 
            "data" => [
                "total_kilos_unidades" => $resultado['total_almacen'],
                "tipos_de_recurso" => $resultado['total_items']
            ]
        ];
    }

    // DELETE (DELETE)
    public function deleteRecurso($id)
    {
        $query = "DELETE FROM recursos WHERE id_recurso = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        try {
            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    return ["status" => 200, "message" => "Recurso eliminado exitosamente."];
                }
                return ["status" => 404, "message" => "Recurso no encontrado."];
            }
        } catch (PDOException $e) {
            return ["status" => 409, "message" => "No se puede eliminar: El recurso está vinculado a donaciones, entregas o gestiones históricas."];
        }
        return ["status" => 500, "message" => "Error al eliminar el recurso."];
    }
}