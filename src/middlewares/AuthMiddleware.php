<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class AuthMiddleware
{
    private static $alg = 'HS256';

    private static function getSecretKey()
    {
        return getenv('JWT_SECRET') ?: "MY_SUPER_SECRET_KEY_REFUGIOS_2026_SISTEMA";
    }

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

        return JWT::encode($payload, self::getSecretKey(), self::$alg);
    }

    public static function checkToken()
    {
        $token = null;

        // 1. Intentar obtener el token desde la cookie HttpOnly
        if (isset($_COOKIE['token'])) {
            $token = $_COOKIE['token'];
        } 
        // 2. Si no está en la cookie, intentar desde el header Authorization (retrocompatibilidad)
        else {
            $headers = apache_request_headers();
            $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;
            if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                $token = $matches[1];
            }
        }

        if (!$token) {
            http_response_code(401);
            echo json_encode(["status" => 401, "message" => "Acceso denegado. Token no proporcionado o formato inválido."]);
            exit();
        }

        try {
            $decoded = JWT::decode($token, new Key(self::getSecretKey(), self::$alg));
            return $decoded->data; // Devuelve los datos del usuario en el token
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(["status" => 401, "message" => "Acceso denegado. Token inválido o expirado."]);
            exit();
        }
    }

    public static function setHttpOnlyCookie($token)
    {
        $isSecure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
        $options = [
            'expires' => time() + (60 * 60 * 24), // 24 horas
            'path' => '/',
            'httponly' => true,
        ];
        
        if ($isSecure) {
            $options['secure'] = true;
            $options['samesite'] = 'None';
        } else {
            $options['secure'] = false;
            $options['samesite'] = 'Lax';
        }

        setcookie('token', $token, $options);
    }

    public static function clearHttpOnlyCookie()
    {
        $isSecure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
        $options = [
            'expires' => time() - 3600, // Expira en el pasado
            'path' => '/',
            'httponly' => true,
        ];

        if ($isSecure) {
            $options['secure'] = true;
            $options['samesite'] = 'None';
        } else {
            $options['secure'] = false;
            $options['samesite'] = 'Lax';
        }

        setcookie('token', '', $options);
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