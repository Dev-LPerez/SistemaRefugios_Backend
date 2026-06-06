<?php
require_once __DIR__ . '/vendor/autoload.php';

// Cargar variables de entorno desde el archivo .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// ==========================================
// 1. CONFIGURACIÓN DE CABECERAS (CORS y JSON)
// ==========================================
// Esto permite que Postman o cualquier Frontend (React, Angular, etc.) pueda comunicarse con la API
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Manejo de peticiones pre-flight (OPTIONS) necesarias para navegadores modernos
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ==========================================
// 2. CONEXIÓN A LA BASE DE DATOS
// ==========================================
require_once 'src/config/database.php';
$database = new Database();
$db = $database->getConnection();

// ==========================================
// 3. CAPTURA DE LA PETICIÓN (Ruta, Método, ID, Datos)
// ==========================================
// Obtenemos la ruta y el ID desde la URL (ej: ?route=refugios&id=1)
$route = $_GET['route'] ?? '';
$id = $_GET['id'] ?? null;
$method = $_SERVER['REQUEST_METHOD'];
$id_familia = $_GET['id_familia'] ?? null; // Nueva variable para filtrar miembros
$action = $_GET['action'] ?? null; // Nueva variable para acciones específicas (ej. agregar detalle a donación)

// Capturamos el body en formato JSON (para POST y PUT) y lo convertimos a un array asociativo
$data = json_decode(file_get_contents("php://input"), true);

// ==========================================
// 4. ENRUTADOR PRINCIPAL (Front Controller)
// ==========================================

// Importar Utils
require_once 'src/middlewares/AuthMiddleware.php';
require_once 'src/auditoria/service/AuditoriaService.php';
require_once 'src/auditoria/entity/LogAuditoria.php';

// Definimos explícitamente qué es una ruta pública (Login)
// Acepta tanto ?route=usuarios&action=login como ?route=login
$isLoginRoute = ($route === 'usuarios' && $action === 'login') || ($route === 'login');
// Validar y auditar de forma global (Excepto si es la ruta de login)
if (!$isLoginRoute) {
    // Definición de Accesos por Ruta
    $permisos = [
        'usuarios' => ['Admin'], // Ahora sí, solo un Admin logueado puede gestionar usuarios
        'auditoria' => ['Admin', 'Auditor'],
        'refugios' => ['Admin', 'Logistica', 'Operario'],
        'familias' => ['Admin', 'Operario', 'Voluntario'],
        'familias/miembros' => ['Admin', 'Operario', 'Voluntario'],
        'recursos' => ['Admin', 'Logistica'],
        'donantes' => ['Admin', 'Logistica', 'Operario'],
        'donaciones' => ['Admin', 'Logistica', 'Operario'],
        'entregas' => ['Admin', 'Logistica', 'Operario', 'Voluntario'],
        'gestiones' => ['Admin', 'Logistica', 'Operario'],
        'priorizacion' => ['Admin', 'Logistica', 'Operario'],
        'reportes' => ['Admin', 'Auditor', 'Logistica', 'Operario']
    ];

    // Chequeo de Roles.
    if (isset($permisos[$route])) {
        $user = AuthMiddleware::checkRole($permisos[$route]);
    } else {
        $user = AuthMiddleware::checkToken();
    }

    // Auditoría
    if (in_array($method, ['POST', 'PUT', 'DELETE'])) {
        $detalle = '';
        $redactedData = $data;
        if (is_array($redactedData) && isset($redactedData['password'])) {
            $redactedData['password'] = '********';
        }
        
        $dataStr = is_array($redactedData) ? json_encode($redactedData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '';
        
        if ($method === 'DELETE') {
            $motivo = $_GET['motivo'] ?? '';
            $motivoStr = $motivo ? " (Motivo: $motivo)" : "";
            if ($id) {
                $detalle = "Eliminó registro con ID: $id$motivoStr";
            } elseif ($id_familia) {
                $detalle = "Eliminó miembro de familia ID: $id_familia$motivoStr";
            } else {
                $detalle = "Eliminó registro$motivoStr";
            }
        } elseif ($method === 'PUT') {
            $targetId = $id ?? $id_familia ?? '';
            $detalle = "Actualizó registro" . ($targetId ? " con ID: $targetId" : "") . ". Datos: $dataStr";
        } elseif ($method === 'POST') {
            $detalle = "Creó nuevo registro. Datos: $dataStr";
        }

        $log = new LogAuditoria($user->id_usuario, $method . ($action ? " $action" : ""), $route, $_SERVER['REMOTE_ADDR'], $detalle);
        $auditoriaService = new AuditoriaService($db);
        $auditoriaService->log($log);
    }
}

switch ($route) {
    // Nueva ruta explícita y limpia para el login
    case 'login':
        require_once 'src/usuarios/controller/UsuarioController.php';
        $controller = new UsuarioController($db);
        // Forzamos la acción 'login' hacia el controlador
        $controller->handleRequest($method, $data, null, 'login');
        break;

    case 'usuarios':
        require_once 'src/usuarios/controller/UsuarioController.php';
        $controller = new UsuarioController($db);
        $controller->handleRequest($method, $data, $id, $action);
        break;

    case 'refugios':
        require_once 'src/refugios/controller/RefugioController.php';
        $controller = new RefugioController($db);
        $controller->handleRequest($method, $data, $id);
        break;

    case 'familias':
        require_once 'src/familias/controller/FamiliaController.php';
        $controller = new FamiliaController($db);
        $controller->handleRequest($method, $data, $id, $action);
        break;

    case 'familias/miembros':
        require_once 'src/familias/controller/MiembroController.php';
        $controller = new MiembroController($db);
        $controller->handleRequest($method, $data, $id, $id_familia);
        break;

    case 'recursos':
        require_once 'src/recursos/controller/RecursoController.php';
        $controller = new RecursoController($db);
        $controller->handleRequest($method, $data, $id, $action);
        break;

    case 'donantes':
        require_once 'src/donaciones/controller/DonanteController.php';
        $controller = new DonanteController($db);
        $controller->handleRequest($method, $data, $id);
        break;

    case 'donaciones':
        require_once 'src/donaciones/controller/DonacionController.php';
        $controller = new DonacionController($db);
        $controller->handleRequest($method, $data, $id, $action);
        break;

    case 'entregas':
        require_once 'src/entregas/controller/EntregaController.php';
        $controller = new EntregaController($db);
        $controller->handleRequest($method, $data, $id);
        break;

    case 'gestiones':
        require_once 'src/gestiones/controller/GestionController.php';
        $controller = new GestionController($db);
        $controller->handleRequest($method, $data);
        break;

    case 'priorizacion':
        require_once 'src/priorizacion/controller/PriorizacionController.php';
        $controller = new PriorizacionController($db);
        $controller->handleRequest($method, $action, $id_familia);
        break;

    case 'reportes':
        require_once 'src/reportes/controller/ReporteController.php';
        $controller = new ReporteController($db);
        $controller->handleRequest($method, $action);
        break;

    case 'auditoria':
        require_once 'src/auditoria/controller/AuditoriaController.php';
        $controller = new AuditoriaController($db);
        $controller->handleRequest($method);
        break;

    default:
        http_response_code(404);
        echo json_encode([
            "status" => 404,
            "error" => "Ruta no encontrada.",
            "hint" => "Asegúrate de usar ?route=nombre_del_modulo"
        ]);
        break;
}