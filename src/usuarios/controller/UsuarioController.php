<?php
require_once __DIR__ . '/../dto/CreateUsuarioDTO.php';
require_once __DIR__ . '/../dto/UpdateUsuarioDTO.php';
require_once __DIR__ . '/../service/UsuarioService.php';

class UsuarioController
{
    private $service;

    public function __construct($db)
    {
        $this->service = new UsuarioService($db);
    }

    public function handleRequest($method, $data, $id = null, $action = null)
    {
        switch ($method) {
            case 'GET':
                if ($id) {
                    $response = $this->service->getUsuarioById($id);
                } else {
                    $response = $this->service->getAllUsuarios();
                }
                $this->sendResponse($response);
                break;

            case 'POST':
                if ($action === 'login') {
                    if (empty($data['user']) || empty($data['password'])) {
                        $this->sendResponse(["status" => 400, "error" => "Faltan credenciales (user, password)"]);
                        return;
                    }
                    $response = $this->service->login($data['user'], $data['password']);
                } else if ($action === 'logout') {
                    // Para JWT, el logout normalmente se maneja eliminando el token del lado del cliente
                    // Pero proveemos el endpoint por completitud
                    $this->sendResponse(["status" => 200, "message" => "Logout exitoso. Descarte el token en el cliente."]);
                    return;
                } else {
                    $dto = new CreateUsuarioDTO($data);
                    if (!$dto->isValid()) {
                        $this->sendResponse(["status" => 400, "error" => "Faltan datos obligatorios (user, password, rol)"]);
                        return;
                    }
                    $response = $this->service->createUsuario($dto);
                }
                $this->sendResponse($response);
                break;

            case 'PUT':
                if (!$id) {
                    $this->sendResponse(["status" => 400, "error" => "ID requerido para actualizar"]);
                    return;
                }
                $dto = new UpdateUsuarioDTO($id, $data);
                if (!$dto->isValid()) {
                    $this->sendResponse(["status" => 400, "error" => "Datos inválidos para actualizar"]);
                    return;
                }
                $response = $this->service->updateUsuario($dto);
                $this->sendResponse($response);
                break;

            case 'DELETE':
                if (!$id) {
                    $this->sendResponse(["status" => 400, "error" => "ID requerido para eliminar"]);
                    return;
                }
                $response = $this->service->deleteUsuario($id);
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