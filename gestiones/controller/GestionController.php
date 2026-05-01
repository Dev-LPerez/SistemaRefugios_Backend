<?php
require_once __DIR__ . '/../dto/CreateDetalleGestionDTO.php';
require_once __DIR__ . '/../service/GestionService.php';

class GestionController
{
    private $service;

    public function __construct($db)
    {
        $this->service = new GestionService($db);
    }

    public function handleRequest($method, $data)
    {
        switch ($method) {
            case 'GET':
                $response = $this->service->getAllGestiones();
                $this->sendResponse($response);
                break;

            case 'POST':
                $dto = new CreateDetalleGestionDTO($data);
                if (!$dto->isValid()) {
                    $this->sendResponse(["status" => 400, "error" => "Faltan datos (id_usuario, id_recurso, accion)"]);
                    return;
                }
                $response = $this->service->createGestion($dto);
                $this->sendResponse($response);
                break;

            default:
                // Bloqueamos PUT y DELETE por seguridad de la auditoría
                $this->sendResponse(["status" => 405, "error" => "Método no permitido para el historial de gestiones"]);
                break;
        }
    }

    private function sendResponse($response)
    {
        http_response_code($response['status']);
        echo json_encode($response);
    }
}