<?php
class CreateUsuarioDTO
{
    public $user;
    public $password;
    public $rol;

    public function __construct($data)
    {
        $this->user = $data['user'] ?? null;
        $this->password = $data['password'] ?? null;
        $this->rol = $data['rol'] ?? null;
    }

    public function isValid()
    {
        return !empty($this->user) && !empty($this->password) && !empty($this->rol);
    }
}