<?php
require_once __DIR__ . '/../dto/CreateEntregaDTO.php';
require_once __DIR__ . '/../service/EntregaService.php';

class EntregaController
{
    private $service;

    public function __construct($db)
    {
        $this->service = new EntregaService($db);
    }

    public function handleRequest($method, $data, $id = null)
    {
        switch ($method) {
            case 'GET':
                // Para simplificar, solo haremos el GET ALL con los JOINs
                $response = $this->service->getAllEntregas();
                $this->sendResponse($response);
                break;

            case 'POST':
                $dto = new CreateEntregaDTO($data);
                if (!$dto->isValid()) {
                    $this->sendResponse(["status" => 400, "error" => "Faltan datos (id_familia y al menos un id_recurso con cantidad mayor a 0)"]);
                    return;
                }
                $response = $this->service->createEntrega($dto);
                $this->sendResponse($response);
                break;

            case 'PUT':
                if (!$id) {
                    $this->sendResponse(["status" => 400, "error" => "ID requerido para actualizar la entrega"]);
                    return;
                }
                if (!isset($data['estado'])) {
                    $this->sendResponse(["status" => 400, "error" => "Estado requerido"]);
                    return;
                }
                $response = $this->service->updateEstado($id, $data['estado']);
                $this->sendResponse($response);
                break;

            case 'DELETE':
                if (!$id) {
                    $this->sendResponse(["status" => 400, "error" => "ID requerido para anular la entrega"]);
                    return;
                }
                $response = $this->service->deleteEntrega($id);
                $this->sendResponse($response);
                break;

            default:
                $this->sendResponse(["status" => 405, "error" => "Método no permitido"]);
                break;
        }
    }

    private function sendResponse($response)
    {
        http_response_code($response['status']);
        echo json_encode($response);
    }
}