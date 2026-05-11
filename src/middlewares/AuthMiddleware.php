<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class AuthMiddleware
{
    private static $secret_key = "MY_SUPER_SECRET_KEY_REFUGIOS"; // Idealmente estaría en env
    private static $alg = 'HS256';

    public static function generateToken($usuario)
    {
        $payload = [
            "iat" => time(), // Tiempo que inició el token
            "exp" => time() + (60 * 60 * 24), // Expira en 24 horas
            "data" => [
                "id_usuario" => $usuario['id_usuario'],
                "user" => $usuario['user'],
                "rol" => $usuario['rol']
            ]
        ];

        return JWT::encode($payload, self::$secret_key, self::$alg);
    }

    public static function checkToken()
    {
        $headers = apache_request_headers();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            http_response_code(401);
            echo json_encode(["status" => 401, "message" => "Acceso denegado. Token no proporcionado o formato inválido."]);
            exit();
        }

        $token = $matches[1];

        try {
            $decoded = JWT::decode($token, new Key(self::$secret_key, self::$alg));
            return $decoded->data; // Devuelve los datos del usuario en el token
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(["status" => 401, "message" => "Acceso denegado. Token inválido o expirado."]);
            exit();
        }
    }

    public static function checkRole($allowedRoles)
    {
        $user_data = self::checkToken();

        if (!in_array($user_data->rol, $allowedRoles)) {
            http_response_code(403);
            echo json_encode([
                "status" => 403, 
                "message" => "Acceso denegado. Se requiere uno de los siguientes roles: " . implode(', ', $allowedRoles)
            ]);
            exit();
        }

        return $user_data;
    }
}