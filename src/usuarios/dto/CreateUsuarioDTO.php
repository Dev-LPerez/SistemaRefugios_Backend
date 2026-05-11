<?php
class CreateUsuarioDTO
{
    public $user;
    public $password;
    public $rol;

    public function __construct($data)
    {
        $this->user = $data['user'] ?? null;
        $this->password = isset($data['password']) ? password_hash($data['password'], PASSWORD_BCRYPT) : null;
        $this->rol = $data['rol'] ?? null;
    }

    public function isValid()
    {
        return !empty($this->user) && !empty($this->password) && !empty($this->rol);
    }
}