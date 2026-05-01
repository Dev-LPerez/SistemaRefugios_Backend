<?php
require_once __DIR__ . '/../dto/CreateDonanteDTO.php';
require_once __DIR__ . '/../dto/UpdateDonanteDTO.php';
require_once __DIR__ . '/../service/DonanteService.php';

class DonanteController
{
    private $service;

    public function __construct($db)
    {
        $this->service = new DonanteService($db);
    }

    public function handleRequest($method, $data, $id = null)
    {
        switch ($method) {
            case 'GET':
                if ($id) {
                    $response = $this->service->getDonanteById($id);
                } else {
                    $response = $this->service->getAllDonantes();
                }
                $this->sendResponse($response);
                break;

            case 'POST':
                $dto = new CreateDonanteDTO($data);
                if (!$dto->isValid()) {
                    $this->sendResponse(["status" => 400, "error" => "Faltan datos obligatorios para registrar al donante"]);
                    return;
                }
                $response = $this->service->createDonante($dto);
                $this->sendResponse($response);
                break;

            case 'PUT':
                if (!$id) {
                    $this->sendResponse(["status" => 400, "error" => "ID requerido para actualizar al donante"]);
                    return;
                }
                $dto = new UpdateDonanteDTO($id, $data);
                if (!$dto->isValid()) {
                    $this->sendResponse(["status" => 400, "error" => "Datos inválidos para actualizar"]);
                    return;
                }
                $response = $this->service->updateDonante($dto);
                $this->sendResponse($response);
                break;

            case 'DELETE':
                if (!$id) {
                    $this->sendResponse(["status" => 400, "error" => "ID requerido para eliminar"]);
                    return;
                }
                $response = $this->service->deleteDonante($id);
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