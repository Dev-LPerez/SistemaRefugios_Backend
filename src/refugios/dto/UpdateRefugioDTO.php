<?php
class UpdateRefugioDTO
{
    public $id_refugio;
    public $nombre;
    public $ubicacion;
    public $capacidad;

    public function __construct($id, $data)
    {
        $this->id_refugio = $id;
        $this->nombre = $data['nombre'] ?? null;
        $this->ubicacion = $data['ubicacion'] ?? null;
        $this->capacidad = $data['capacidad'] ?? null;
    }

    public function isValid()
    {
        return !empty($this->id_refugio) && (!empty($this->nombre) || !empty($this->ubicacion) || !empty($this->capacidad));
    }
}