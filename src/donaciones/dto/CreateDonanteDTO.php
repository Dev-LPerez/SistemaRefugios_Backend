<?php
class CreateDonanteDTO
{
    public $identificacion;
    public $nombre;
    public $tipo;
    public $telefono;

    public function __construct($data)
    {
        $this->identificacion = $data['identificacion'] ?? null;
        $this->nombre = $data['nombre'] ?? null;
        $this->tipo = $data['tipo'] ?? null;
        $this->telefono = $data['telefono'] ?? null;
    }

    public function isValid()
    {
        return !empty($this->identificacion) && !empty($this->nombre) && !empty($this->tipo);
    }
}