<?php
require_once __DIR__ . '/../dto/CreateMiembroDTO.php';
require_once __DIR__ . '/../dto/UpdateMiembroDTO.php';
require_once __DIR__ . '/../service/MiembroService.php';

class MiembroController
{
    private $service;

    public function __construct($db)
    {
        $this->service = new MiembroService($db);
    }

    public function handleRequest($method, $data, $id = null, $id_familia = null)
    {
        switch ($method) {
            case 'GET':
                if ($id) {
                    // Buscar a una persona en específico
                    $response = $this->service->getMiembroById($id);
                } elseif ($id_familia) {
                    // Buscar a todos los miembros de una familia
                    $response = $this->service->getMiembrosByFamilia($id_familia);
                } else {
                    $response = ["status" => 400, "error" => "Debes proporcionar un ID de persona o un ID de familia"];
                }
                $this->sendResponse($response);
                break;

            case 'POST':
                $dto = new CreateMiembroDTO($data);
                if (!$dto->isValid()) {
                    $this->sendResponse(["status" => 400, "error" => "Faltan datos obligatorios"]);
                    return;
                }
                $response = $this->service->createMiembro($dto);
                $this->sendResponse($response);
                break;

            case 'PUT':
                if (!$id) {
                    $this->sendResponse(["status" => 400, "error" => "ID de persona requerido para actualizar"]);
                    return;
                }
                $dto = new UpdateMiembroDTO($id, $data);
                $response = $this->service->updateMiembro($dto);
                $this->sendResponse($response);
                break;

            case 'DELETE':
                if (!$id) {
                    $this->sendResponse(["status" => 400, "error" => "ID de persona requerido para eliminar"]);
                    return;
                }
                $response = $this->service->deleteMiembro($id);
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