<?php
class Recurso
{
    public $id_recurso;
    public $nombre;
    public $tipo;
    public $unidad;
    public $cantidad_disponible;

    public function __construct($id_recurso, $nombre, $tipo, $unidad, $cantidad_disponible)
    {
        $this->id_recurso = $id_recurso;
        $this->nombre = $nombre;
        $this->tipo = $tipo;
        $this->unidad = $unidad;
        $this->cantidad_disponible = $cantidad_disponible;
    }
}