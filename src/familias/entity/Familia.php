<?php
class Familia
{
    public $id_familia;
    public $representante;
    public $telefono;
    public $direccion;
    public $cantidad_miembros;
    public $prioridad;
    public $id_refugio;

    public function __construct($id_familia, $representante, $telefono, $direccion, $cantidad_miembros, $prioridad, $id_refugio)
    {
        $this->id_familia = $id_familia;
        $this->representante = $representante;
        $this->telefono = $telefono;
        $this->direccion = $direccion;
        $this->cantidad_miembros = $cantidad_miembros;
        $this->prioridad = $prioridad;
        $this->id_refugio = $id_refugio;
    }
}