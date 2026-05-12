<?php
require_once __DIR__ . '/../service/MotorPriorizacionService.php';

class PriorizacionController
{
    private $service;

    public function __construct($db)
    {
        $this->service = new MotorPriorizacionService($db);
    }

    public function handleRequest($method, $action = null, $id_familia = null)
    {
        switch ($method) {
            case 'GET':
                if ($action === 'despachos') {
                    $response = $this->service->generarDespachos();
                    $this->sendResponse($response);
                } else {
                    $this->sendResponse(["status" => 400, "error" => "Debe especificar action=despachos para obtener la prioridad"]);
                }
                break;

            case 'POST':
                if ($action === 'calcular' && $id_familia) {
                    $puntaje = $this->service->calcularPuntajePrioridad($id_familia);
                    $this->sendResponse(["status" => 200, "id_familia" => $id_familia, "puntaje_prioridad" => $puntaje]);
                } else {
                    $this->sendResponse(["status" => 400, "error" => "Se requiere un id_familia y action=calcular"]);
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
