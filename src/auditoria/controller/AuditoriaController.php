<?php
require_once __DIR__ . '/../service/AuditoriaService.php';
require_once __DIR__ . '/../../middlewares/AuthMiddleware.php';

class AuditoriaController
{
    private $service;

    public function __construct($db)
    {
        $this->service = new AuditoriaService($db);
    }

    public function handleRequest($method)
    {
        // Solo Admin o Auditor pueden leer los logs
        AuthMiddleware::checkRole(['Admin', 'Auditor']);

        if ($method === 'GET') {
            $response = $this->service->getAllLogs();
            $this->sendResponse($response);
        } else {
            $this->sendResponse(["status" => 405, "error" => "Método no permitido"]);
        }
    }

    private function sendResponse($response)
    {
        http_response_code($response['status']);
        echo json_encode($response);
    }
}