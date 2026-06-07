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

    /**
     * Detecta si la petición viene por HTTPS.
     * Render termina TLS en el proxy y reenvía la petición al contenedor
     * en HTTP plano, por eso $_SERVER['HTTPS'] nunca es 'on'.
     * El proxy añade X-Forwarded-Proto: https, que es la señal fiable.
     */
    private static function isHttps(): bool
    {
        // Render (y la mayoría de proxies) envía este header
        if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
            return strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https';
        }
        // Fallback para entornos donde Apache sí maneja TLS directamente
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            return true;
        }
        // Fallback adicional: Render también puede enviar X-Forwarded-Ssl
        if (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on') {
            return true;
        }
        return false;
    }

    public static function generateToken($usuario)
    {
        $payload = [
            "iat" => time(),
            "exp" => time() + (60 * 60 * 24), // 24 horas
            "data" => [
                "id_usuario" => $usuario['id_usuario'],
                "user"       => $usuario['user'],
                "rol"        => $usuario['rol']
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
        // 2. Fallback: header Authorization (útil para Postman / tests)
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
            return $decoded->data;
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(["status" => 401, "message" => "Acceso denegado. Token inválido o expirado."]);
            exit();
        }
    }

    public static function setHttpOnlyCookie($token)
    {
        $secure   = self::isHttps();
        $samesite = $secure ? 'None' : 'Lax';

        setcookie('token', $token, [
            'expires'  => time() + (60 * 60 * 24),
            'path'     => '/',
            'httponly' => true,
            'secure'   => $secure,
            'samesite' => $samesite,
        ]);
    }

    public static function clearHttpOnlyCookie()
    {
        $secure   = self::isHttps();
        $samesite = $secure ? 'None' : 'Lax';

        setcookie('token', '', [
            'expires'  => time() - 3600,
            'path'     => '/',
            'httponly' => true,
            'secure'   => $secure,
            'samesite' => $samesite,
        ]);
    }

    public static function checkRole($allowedRoles)
    {
        $user_data = self::checkToken();

        if (!in_array($user_data->rol, $allowedRoles)) {
            http_response_code(403);
            echo json_encode([
                "status"  => 403,
                "message" => "Acceso denegado. Se requiere uno de los siguientes roles: " . implode(', ', $allowedRoles)
            ]);
            exit();
        }

        return $user_data;
    }
}