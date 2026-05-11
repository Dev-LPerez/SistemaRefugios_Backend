<?php
class UpdateUsuarioDTO
{
    public $id_usuario;
    public $user;
    public $password; // Opcional en la actualización
    public $rol;

    public function __construct($id, $data)
    {
        $this->id_usuario = $id;
        $this->user = $data['user'] ?? null;
        $this->password = isset($data['password']) && !empty($data['password']) ? password_hash($data['password'], PASSWORD_BCRYPT) : null;
        $this->rol = $data['rol'] ?? null;
    }

    public function isValid()
    {
        return !empty($this->id_usuario) && (!empty($this->user) || !empty($this->rol) || !empty($this->password));
    }
}