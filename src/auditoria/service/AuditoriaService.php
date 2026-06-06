<?php
require_once __DIR__ . '/../entity/LogAuditoria.php';

class AuditoriaService
{
    private $db;

    public function __construct($dbConnection)
    {
        $this->db = $dbConnection;
    }

    public function log(LogAuditoria $log)
    {
        $query = "INSERT INTO auditoria_logs (usuario_id, accion, entidad, ip, detalle) VALUES (:usuario_id, :accion, :entidad, :ip, :detalle)";
        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':usuario_id', $log->usuario_id, PDO::PARAM_INT);
        $stmt->bindParam(':accion', $log->accion);
        $stmt->bindParam(':entidad', $log->entidad);
        $stmt->bindParam(':ip', $log->ip);
        $stmt->bindParam(':detalle', $log->detalle);

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            // Un fallo en el logging no debería necesariamente romper el sistema completo, 
            // pero podemos registrar el error en error_log de php
            error_log("Fallo en Auditoria: " . $e->getMessage());
        }
    }

    public function getAllLogs()
    {
        $query = "SELECT l.*, u.user as username, u.rol as rol
                  FROM auditoria_logs l 
                  LEFT JOIN usuarios u ON l.usuario_id = u.id_usuario 
                  ORDER BY l.id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return ["status" => 200, "data" => $resultados];
    }
}