<?php
require_once __DIR__ . '/../dto/CreateRecursoDTO.php';
require_once __DIR__ . '/../dto/UpdateRecursoDTO.php';
require_once __DIR__ . '/../service/RecursoService.php';

class RecursoController
{
    private $service;

    public function __construct($db)
    {
        $this->service = new RecursoService($db);
    }

    public function handleRequest($method, $data, $id = null, $action = null)
    {
        switch ($method) {
            case 'GET':
                if ($action === 'alertas') {
                    $response = $this->service->getAlertasStock();
                } elseif ($action === 'stock-real') {
                    $response = $this->service->getStockReal();
                } elseif ($id) {
                    $response = $this->service->getRecursoById($id);
                } else {
                    $response = $this->service->getAllRecursos();
                }
                $this->sendResponse($response);
                break;

            case 'POST':
                $dto = new CreateRecursoDTO($data);
                if (!$dto->isValid()) {
                    $this->sendResponse(["status" => 400, "error" => "Faltan datos obligatorios para crear el recurso."]);
                    return;
                }
                $response = $this->service->createRecurso($dto);
                $this->sendResponse($response);
                break;

            case 'PUT':
                if (!$id) {
                    $this->sendResponse(["status" => 400, "error" => "ID requerido para actualizar."]);
                    return;
                }
                $dto = new UpdateRecursoDTO($id, $data);
                if (!$dto->isValid()) {
                    $this->sendResponse(["status" => 400, "error" => "Datos inválidos para actualizar."]);
                    return;
                }
                $response = $this->service->updateRecurso($dto);
                $this->sendResponse($response);
                break;

            case 'DELETE':
                if (!$id) {
                    $this->sendResponse(["status" => 400, "error" => "ID requerido para eliminar."]);
                    return;
                }
                $response = $this->service->deleteRecurso($id);
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