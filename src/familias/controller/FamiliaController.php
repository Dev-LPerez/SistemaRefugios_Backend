<?php
require_once __DIR__ . '/../dto/CreateFamiliaDTO.php';
require_once __DIR__ . '/../dto/UpdateFamiliaDTO.php';
require_once __DIR__ . '/../service/FamiliaService.php';

class FamiliaController
{
    private $service;

    public function __construct($db)
    {
        $this->service = new FamiliaService($db);
    }

    public function handleRequest($method, $data, $id = null)
    {
        switch ($method) {
            case 'GET':
                if ($id) {
                    $response = $this->service->getFamiliaById($id);
                } else {
                    $response = $this->service->getAllFamilias();
                }
                $this->sendResponse($response);
                break;

            case 'POST':
                $dto = new CreateFamiliaDTO($data);
                if (!$dto->isValid()) {
                    $this->sendResponse(["status" => 400, "error" => "Faltan datos obligatorios para crear la familia"]);
                    return;
                }
                $response = $this->service->createFamilia($dto);
                $this->sendResponse($response);
                break;

            case 'PUT':
                if (!$id) {
                    $this->sendResponse(["status" => 400, "error" => "ID requerido para actualizar la familia"]);
                    return;
                }
                $dto = new UpdateFamiliaDTO($id, $data);
                if (!$dto->isValid()) {
                    $this->sendResponse(["status" => 400, "error" => "Datos inválidos para actualizar"]);
                    return;
                }
                $response = $this->service->updateFamilia($dto);
                $this->sendResponse($response);
                break;

            case 'DELETE':
                if (!$id) {
                    $this->sendResponse(["status" => 400, "error" => "ID requerido para eliminar"]);
                    return;
                }
                $response = $this->service->deleteFamilia($id);
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