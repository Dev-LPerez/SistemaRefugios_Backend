<?php

class ReporteService
{
    private $db;

    public function __construct($dbConnection)
    {
        $this->db = $dbConnection;
    }

    /**
     * Tarea 6.1: Trazabilidad "Origen - Destino"
     * Enlaza la salida (Entregas a Familias) con el ingreso (Donantes/Procedencia del Recurso).
     */
    public function getTrazabilidadOrigenDestino()
    {
        // Usamos una subconsulta con GROUP_CONCAT para listar los donantes u orígenes que han aportado ese tipo de recurso.
        $query = "
            SELECT 
                de.id_entrega,
                de.fecha as fecha_entrega,
                r.nombre as recurso,
                de.cantidad as cantidad_entregada,
                f.representante as familia_receptora,
                f.ubicacion_actual as ubicacion_receptora,
                (
                    SELECT GROUP_CONCAT(DISTINCT d.origen SEPARATOR ', ')
                    FROM detalle_donacion dd
                    JOIN donaciones d ON dd.id_donacion = d.id_donacion
                    WHERE dd.id_recurso = r.id_recurso
                ) as posibles_origenes
            FROM detalle_entrega de
            JOIN recursos r ON de.id_recurso = r.id_recurso
            JOIN familias f ON de.id_familia = f.id_familia
            ORDER BY de.fecha DESC
            LIMIT 100
        ";

        $stmt = $this->db->prepare($query);
        try {
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return ["status" => 200, "data" => $data];
        } catch (PDOException $e) {
            return ["status" => 500, "message" => "Error generando la trazabilidad.", "error" => $e->getMessage()];
        }
    }

    /**
     * Tarea 6.2: Endpoints de Dashboard Estadístico
     * Devuelve métricas compuestas para pintar en UI (Tarjetas, Semáforos).
     */
    public function getDashboardStats()
    {
        $stats = [];
        
        try {
            // Total familias
            $stmt = $this->db->query("SELECT COUNT(*) FROM familias");
            $stats['total_familias_registradas'] = (int) $stmt->fetchColumn();

            // Total miembros
            $stmt = $this->db->query("SELECT COUNT(*) FROM miembros");
            $stats['total_personas_damnificadas'] = (int) $stmt->fetchColumn();

            // Total despachos realizados
            $stmt = $this->db->query("SELECT COUNT(*) FROM detalle_entrega");
            $stats['total_entregas_completadas'] = (int) $stmt->fetchColumn();

            // Total tonelaje / unidades en inventario general
            $stmt = $this->db->query("SELECT SUM(cantidad_disponible) FROM recursos");
            $stats['total_unidades_kg_almacen'] = (float) $stmt->fetchColumn();

            // Alertas Críticas de Stock (menor a 50 unidades/kg)
            $stmt = $this->db->query("SELECT nombre, cantidad_disponible, unidad FROM recursos WHERE cantidad_disponible <= 50");
            $stats['recursos_alertas_stock'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return ["status" => 200, "data" => $stats];
        } catch (PDOException $e) {
            return ["status" => 500, "message" => "Error generando las estadísticas del dashboard."];
        }
    }
}