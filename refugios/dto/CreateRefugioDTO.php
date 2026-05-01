<?php
class CreateRefugioDTO
{
    public $nombre;
    public $ubicacion;
    public $capacidad;

    public function __construct($data)
    {
        $this->nombre = $data['nombre'] ?? null;
        $this->ubicacion = $data['ubicacion'] ?? null;
        $this->capacidad = $data['capacidad'] ?? null;
    }

    public function isValid()
    {
        return !empty($this->nombre) && !empty($this->ubicacion) && !empty($this->capacidad);
    }
}