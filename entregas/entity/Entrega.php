<?php
class Entrega
{
    public $id_entrega;
    public $estado;
    public $fecha;
    public $cantidad;
    public $id_familia;
    public $id_recurso;

    public function __construct($id_entrega, $estado, $fecha, $cantidad, $id_familia, $id_recurso)
    {
        $this->id_entrega = $id_entrega;
        $this->estado = $estado;
        $this->fecha = $fecha;
        $this->cantidad = $cantidad;
        $this->id_familia = $id_familia;
        $this->id_recurso = $id_recurso;
    }
}