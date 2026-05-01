<?php
require_once __DIR__ . '/../dto/CreateDonacionDTO.php';
require_once __DIR__ . '/../service/DonacionService.php';

class DonacionController
{
    private $service;

    public function __construct($db)
    {
        $this->service = new DonacionService($db);
    }

    public function handleRequest($method, $data, $id = null, $action = null)
    {
        switch ($method) {
            case 'GET':
                if ($id) {
                    $response = $this->service->getDonacionCompleta($id);
                    $this->sendResponse($response);
                }
                break;

            case 'POST':
                // Si la URL es ?route=donaciones&action=agregar_detalle
                if ($action === 'agregar_detalle') {
                    if (!isset($data['id_donacion'], $data['id_recurso'], $data['cantidad'])) {
                        $this->sendResponse(["status" => 400, "error" => "Faltan datos (id_donacion, id_recurso, cantidad)"]);
                        return;
                    }
                    $response = $this->service->addDetalleDonacion($data['id_donacion'], $data['id_recurso'], $data['cantidad']);
                    $this->sendResponse($response);
                }
                // Creación normal de la cabecera de la donación
                else {
                    $dto = new CreateDonacionDTO($data);
                    if (!$dto->isValid()) {
                        $this->sendResponse(["status" => 400, "error" => "Faltan datos obligatorios"]);
                        return;
                    }
                    $response = $this->service->createDonacion($dto);
                    $this->sendResponse($response);
                }
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