<?php
class Usuario
{
    public $id_usuario;
    public $user;
    public $password;
    public $rol;

    public function __construct($id_usuario, $user, $password, $rol)
    {
        $this->id_usuario = $id_usuario;
        $this->user = $user;
        $this->password = $password;
        $this->rol = $rol;
    }
}