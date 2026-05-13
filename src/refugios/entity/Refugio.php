<?php
class Refugio
{
    public $id_refugio;
    public $nombre;
    public $direccion;
    public $capacidad_maxima;
    public $ocupacion_actual;
    public $estado;
    public $created_at;

    public function __construct($id_refugio, $nombre, $direccion, $capacidad_maxima, $ocupacion_actual, $estado, $created_at = null)
    {
        $this->id_refugio = $id_refugio;
        $this->nombre = $nombre;
        $this->direccion = $direccion;
        $this->capacidad_maxima = $capacidad_maxima;
        $this->ocupacion_actual = $ocupacion_actual;
        $this->estado = $estado;
        $this->created_at = $created_at;
    }
}