<?php
class Refugio
{
    public $id_refugio;
    public $nombre;
    public $ubicacion;
    public $capacidad;

    public function __construct($id_refugio, $nombre, $ubicacion, $capacidad)
    {
        $this->id_refugio = $id_refugio;
        $this->nombre = $nombre;
        $this->ubicacion = $ubicacion;
        $this->capacidad = $capacidad;
    }
}