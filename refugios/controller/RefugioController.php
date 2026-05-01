<?php
require_once __DIR__ . '/../dto/CreateRefugioDTO.php';
require_once __DIR__ . '/../dto/UpdateRefugioDTO.php';
require_once __DIR__ . '/../service/RefugioService.php';

class RefugioController
{
    private $service;

    public function __construct($db)
    {
        $this->service = new RefugioService($db);
    }

    public function handleRequest($method, $data, $id = null)
    {
        switch ($method) {
            case 'GET':
                if ($id) {
                    $response = $this->service->getRefugioById($id);
                } else {
                    $response = $this->service->getAllRefugios();
                }
                $this->sendResponse($response);
                break;

            case 'POST':
                $dto = new CreateRefugioDTO($data);
                if (!$dto->isValid()) {
                    $this->sendResponse(["status" => 400, "error" => "Datos incompletos"]);
                    return;
                }
                $response = $this->service->createRefugio($dto);
                $this->sendResponse($response);
                break;

            case 'PUT':
                if (!$id) {
                    $this->sendResponse(["status" => 400, "error" => "ID requerido para actualizar"]);
                    return;
                }
                $dto = new UpdateRefugioDTO($id, $data);
                if (!$dto->isValid()) {
                    $this->sendResponse(["status" => 400, "error" => "Datos inválidos para actualizar"]);
                    return;
                }
                $response = $this->service->updateRefugio($dto);
                $this->sendResponse($response);
                break;

            case 'DELETE':
                if (!$id) {
                    $this->sendResponse(["status" => 400, "error" => "ID requerido para eliminar"]);
                    return;
                }
                $response = $this->service->deleteRefugio($id);
                $this->sendResponse($response);
                break;

            default:
                $this->sendResponse(["status" => 405, "error" => "Método no permitido"]);
                break;
        }
    }

    // Función auxiliar para imprimir la respuesta JSON
    private function sendResponse($response)
    {
        http_response_code($response['status']);
        echo json_encode($response);
    }
}