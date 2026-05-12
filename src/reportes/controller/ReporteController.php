<?php
require_once __DIR__ . '/../service/ReporteService.php';

class ReporteController
{
    private $service;

    public function __construct($db)
    {
        $this->service = new ReporteService($db);
    }

    public function handleRequest($method, $action)
    {
        // Los reportes son estrictamente de solo lectura
        if ($method !== 'GET') {
            http_response_code(405);
            echo json_encode(["status" => 405, "error" => "Método no permitido. Use GET."]);
            return;
        }

        switch ($action) {
            case 'origen-destino':
                $response = $this->service->getTrazabilidadOrigenDestino();
                break;

            case 'dashboard':
                $response = $this->service->getDashboardStats();
                break;

            default:
                $response = [
                    "status" => 404, 
                    "error" => "Acción de reporte no encontrada.",
                    "hint" => "Acciones disponibles: &action=origen-destino o &action=dashboard"
                ];
                break;
        }

        http_response_code($response['status']);
        echo json_encode($response);
    }
}