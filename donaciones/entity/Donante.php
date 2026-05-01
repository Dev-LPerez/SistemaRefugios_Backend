<?php
class Donante
{
    public $id_donante;
    public $identificacion;
    public $nombre;
    public $tipo; // persona, empresa, gobierno
    public $telefono;

    public function __construct($id_donante, $identificacion, $nombre, $tipo, $telefono)
    {
        $this->id_donante = $id_donante;
        $this->identificacion = $identificacion;
        $this->nombre = $nombre;
        $this->tipo = $tipo;
        $this->telefono = $telefono;
    }
}