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
        $this->password = $data['password'] ?? null;
        $this->rol = $data['rol'] ?? null;
    }

    public function isValid()
    {
        return !empty($this->id_usuario) && (!empty($this->user) || !empty($this->rol) || !empty($this->password));
    }
}