<?php
class CreateRefugioDTO
{
    public $nombre;
    public $direccion;
    public $capacidad_maxima;
    public $estado;

    public function __construct($data)
    {
        $this->nombre = $data['nombre'] ?? null;
        $this->direccion = $data['direccion'] ?? null;
        $this->capacidad_maxima = $data['capacidad_maxima'] ?? null;
        $this->estado = $data['estado'] ?? 'activo'; // Por defecto será 'activo'
    }

    public function isValid()
    {
        return !empty($this->nombre) && !empty($this->direccion) && !empty($this->capacidad_maxima);
    }
}