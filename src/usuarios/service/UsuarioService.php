<?php
require_once __DIR__ . '/../entity/Usuario.php';

class UsuarioService
{
    private $db;

    public function __construct($dbConnection)
    {
        $this->db = $dbConnection;
    }

    // CREATE (POST)
    public function createUsuario(CreateUsuarioDTO $dto)
    {
        // Se asume que en el esquema viejo existía 'rol' (VARCHAR) y también agregamos 'rol_id'
        $query = "INSERT INTO usuarios (user, password, rol) VALUES (:user, :password, :rol)";
        $stmt = $this->db->prepare($query);

        // La contraseña ya viene encriptada desde el DTO
        $hashed_password = $dto->password;

        $stmt->bindParam(':user', $dto->user);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':rol', $dto->rol);

        try {
            if ($stmt->execute()) {
                return ["status" => 201, "message" => "Usuario creado exitosamente."];
            }
        } catch (PDOException $e) {
            // Manejo de error por si el campo 'user' tiene restricción UNIQUE y se intenta duplicar
            return ["status" => 400, "message" => "Error al crear: Es posible que el nombre de usuario ya exista."];
        }
        return ["status" => 500, "message" => "Error interno al crear el usuario."];
    }

    // READ ALL (GET)
    public function getAllUsuarios()
    {
        // Excluimos la contraseña en el SELECT por seguridad
        $query = "SELECT id_usuario, user, rol FROM usuarios";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return ["status" => 200, "data" => $resultados];
    }

    // READ ONE (GET por ID)
    public function getUsuarioById($id)
    {
        $query = "SELECT id_usuario, user, rol FROM usuarios WHERE id_usuario = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($usuario) {
            return ["status" => 200, "data" => $usuario];
        }
        return ["status" => 404, "message" => "Usuario no encontrado."];
    }

    // AUTHENTICATE (LOGIN)
    public function login($username, $password)
    {
        $query = "SELECT id_usuario, user, password, rol FROM usuarios WHERE user = :user LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user', $username);
        $stmt->execute();
        
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($password, $usuario['password'])) {
            require_once __DIR__ . '/../../middlewares/AuthMiddleware.php';
            $token = AuthMiddleware::generateToken($usuario);
            return [
                "status" => 200, 
                "message" => "Autenticación exitosa", 
                "token" => $token,
                "data" => [
                    "id_usuario" => $usuario['id_usuario'],
                    "user" => $usuario['user'],
                    "rol" => $usuario['rol']
                ]
            ];
        }

        return ["status" => 401, "message" => "Credenciales incorrectas."];
    }

    // UPDATE (PUT)
    public function updateUsuario(UpdateUsuarioDTO $dto)
    {
        // Armamos la consulta dinámicamente dependiendo de si enviaron o no una nueva contraseña
        if (!empty($dto->password)) {
            $query = "UPDATE usuarios SET user = :user, password = :password, rol = :rol WHERE id_usuario = :id";
            // La contraseña ya viene encriptada desde el DTO
            $hashed_password = $dto->password;
        } else {
            $query = "UPDATE usuarios SET user = :user, rol = :rol WHERE id_usuario = :id";
        }

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user', $dto->user);
        $stmt->bindParam(':rol', $dto->rol);
        $stmt->bindParam(':id', $dto->id_usuario, PDO::PARAM_INT);

        if (!empty($dto->password)) {
            $stmt->bindParam(':password', $hashed_password);
        }

        try {
            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    return ["status" => 200, "message" => "Usuario actualizado exitosamente."];
                }
                return ["status" => 404, "message" => "Usuario no encontrado o sin cambios."];
            }
        } catch (PDOException $e) {
            return ["status" => 400, "message" => "Error al actualizar. Verifique que el nombre de usuario no esté duplicado."];
        }
        return ["status" => 500, "message" => "Error al actualizar el usuario."];
    }

    // DELETE (DELETE)
    public function deleteUsuario($id)
    {
        $query = "DELETE FROM usuarios WHERE id_usuario = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        try {
            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    return ["status" => 200, "message" => "Usuario eliminado exitosamente."];
                }
                return ["status" => 404, "message" => "Usuario no encontrado."];
            }
        } catch (PDOException $e) {
            // Evita borrar usuarios si ya están vinculados a la tabla Detalle_Gestion
            return ["status" => 409, "message" => "No se puede eliminar: El usuario tiene gestiones asociadas en el sistema."];
        }
        return ["status" => 500, "message" => "Error al eliminar el usuario."];
    }
}