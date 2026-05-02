<?php
class Donacion
{
    public $id_donacion;
    public $fecha;
    public $descripcion;
    public $id_donante;

    public function __construct($id_donacion, $fecha, $descripcion, $id_donante)
    {
        $this->id_donacion = $id_donacion;
        $this->fecha = $fecha;
        $this->descripcion = $descripcion;
        $this->id_donante = $id_donante;
    }
}