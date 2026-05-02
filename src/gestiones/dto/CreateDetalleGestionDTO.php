<?php
class CreateDetalleGestionDTO
{
    public $id_usuario;
    public $id_recurso;
    public $accion;

    public function __construct($data)
    {
        $this->id_usuario = $data['id_usuario'] ?? null;
        $this->id_recurso = $data['id_recurso'] ?? null;
        $this->accion = $data['accion'] ?? null;
    }

    public function isValid()
    {
        return !empty($this->id_usuario) && !empty($this->id_recurso) && !empty($this->accion);
    }
}